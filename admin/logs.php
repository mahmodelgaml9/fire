<?php
require_once '../config.php';
require_once 'includes/auth.php';
require_once '../includes/functions.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف السجلات القديمة
if (isset($_GET['cleanup']) && $_GET['cleanup'] === 'old') {
    $days = (int)($_GET['days'] ?? 30);
    $deleted = cleanupOldLogs($days);
    
    if ($deleted !== false) {
        $message = "تم حذف السجلات الأقدم من $days يوم بنجاح";
        logAdminAction('cleanup_logs', 'logs', "حذف سجلات أقدم من $days يوم");
    } else {
        $error = 'حدث خطأ أثناء حذف السجلات';
    }
}

// معالجة حذف سجل محدد
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $log_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM admin_logs WHERE id = ?");
        $stmt->execute([$log_id]);
        $message = 'تم حذف السجل بنجاح';
        logAdminAction('delete_log', 'logs', "حذف سجل رقم $log_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف السجل';
        error_log('Delete Log Error: ' . $e->getMessage());
    }
}

// جلب السجلات مع الصفحات
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 50;
$offset = ($page - 1) * $limit;

$logs = getActivityLogs($limit, $offset);

// جلب إجمالي عدد السجلات
$total_logs = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_logs");
    $total_logs = $stmt->fetch()['count'];
} catch (PDOException $e) {
    error_log('Count Logs Error: ' . $e->getMessage());
}

$total_pages = ceil($total_logs / $limit);

$page_title = 'سجلات النشاط';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">سجلات النشاط</h1>
                <p class="text-gray-600">مراقبة نشاطات المستخدمين والعمليات</p>
            </div>
            <div class="flex space-x-2 space-x-reverse">
                <button onclick="exportLogs()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-download ml-2"></i>
                    تصدير السجلات
                </button>
                <button onclick="showCleanupModal()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fas fa-broom ml-2"></i>
                    تنظيف السجلات
                </button>
            </div>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-500">إجمالي السجلات</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo number_format($total_logs); ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-500">المستخدمين النشطين</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php 
                        try {
                            $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) as count FROM admin_logs WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)");
                            echo $stmt->fetch()['count'];
                        } catch (PDOException $e) {
                            echo '0';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-500">اليوم</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php 
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_logs WHERE DATE(created_at) = CURDATE()");
                            echo $stmt->fetch()['count'];
                        } catch (PDOException $e) {
                            echo '0';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-500">الأخطاء</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?php 
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_logs WHERE action LIKE '%error%' OR action LIKE '%delete%'");
                            echo $stmt->fetch()['count'];
                        } catch (PDOException $e) {
                            echo '0';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الفلاتر</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع النشاط</label>
                    <select id="action_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الأنشطة</option>
                        <option value="login">تسجيل دخول</option>
                        <option value="logout">تسجيل خروج</option>
                        <option value="create">إنشاء</option>
                        <option value="update">تحديث</option>
                        <option value="delete">حذف</option>
                        <option value="upload">رفع ملف</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المستخدم</label>
                    <select id="user_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع المستخدمين</option>
                        <?php 
                        try {
                            $stmt = $pdo->query("SELECT DISTINCT au.id, au.first_name, au.last_name FROM admin_logs al JOIN admin_users au ON al.user_id = au.id ORDER BY au.first_name");
                            while ($user = $stmt->fetch()) {
                                echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '</option>';
                            }
                        } catch (PDOException $e) {
                            // ignore
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التاريخ</label>
                    <select id="date_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الأوقات</option>
                        <option value="today">اليوم</option>
                        <option value="week">الأسبوع</option>
                        <option value="month">الشهر</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <input type="text" id="search_filter" placeholder="البحث في الوصف" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">سجلات النشاط</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النشاط
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المستخدم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الوصف
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التاريخ
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد سجلات</p>
                                    <p class="text-sm">ستظهر السجلات هنا عند حدوث نشاطات</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50" 
                                data-action="<?php echo $log['action']; ?>"
                                data-user="<?php echo $log['user_id']; ?>"
                                data-date="<?php echo strtotime($log['created_at']); ?>"
                                data-description="<?php echo strtolower($log['description']); ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-lg <?php 
                                                echo match($log['action']) {
                                                    'login' => 'bg-green-100',
                                                    'logout' => 'bg-blue-100',
                                                    'create' => 'bg-purple-100',
                                                    'update' => 'bg-yellow-100',
                                                    'delete' => 'bg-red-100',
                                                    'upload' => 'bg-indigo-100',
                                                    default => 'bg-gray-100'
                                                };
                                            ?> flex items-center justify-center">
                                                <i class="<?php 
                                                    echo match($log['action']) {
                                                        'login' => 'fas fa-sign-in-alt text-green-600',
                                                        'logout' => 'fas fa-sign-out-alt text-blue-600',
                                                        'create' => 'fas fa-plus text-purple-600',
                                                        'update' => 'fas fa-edit text-yellow-600',
                                                        'delete' => 'fas fa-trash text-red-600',
                                                        'upload' => 'fas fa-upload text-indigo-600',
                                                        default => 'fas fa-info text-gray-600'
                                                    };
                                                ?>"></i>
                                            </div>
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo ucfirst($log['action']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo ucfirst($log['module'] ?? 'system'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?>
                                    <br>
                                    <span class="text-xs text-gray-500"><?php echo htmlspecialchars($log['username']); ?></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($log['description']); ?>">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('Y/m/d H:i', strtotime($log['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="viewLogDetails(<?php echo $log['id']; ?>)" 
                                                class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="deleteLog(<?php echo $log['id']; ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        عرض <?php echo ($offset + 1); ?> إلى <?php echo min($offset + $limit, $total_logs); ?> من <?php echo $total_logs; ?> سجل
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                السابق
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="px-3 py-2 text-sm border border-gray-300 rounded-lg <?php echo $i === $page ? 'bg-primary-600 text-white' : 'hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                التالي
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Cleanup Modal -->
<div id="cleanup-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center mb-4">
            <i class="fas fa-broom text-yellow-500 text-2xl ml-3"></i>
            <h3 class="text-lg font-semibold text-gray-900">تنظيف السجلات</h3>
        </div>
        
        <p class="text-gray-600 mb-4">سيتم حذف جميع السجلات الأقدم من الفترة المحددة. هذا الإجراء لا يمكن التراجع عنه.</p>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">حذف السجلات الأقدم من</label>
            <select id="cleanup_days" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="7">7 أيام</option>
                <option value="30" selected>30 يوم</option>
                <option value="90">90 يوم</option>
                <option value="180">6 أشهر</option>
                <option value="365">سنة</option>
            </select>
        </div>
        
        <div class="flex space-x-3 space-x-reverse">
            <button onclick="confirmCleanup()" class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors duration-200">
                تأكيد الحذف
            </button>
            <button onclick="document.getElementById('cleanup-modal').classList.add('hidden')" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                إلغاء
            </button>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const actionFilter = document.getElementById('action_filter');
    const userFilter = document.getElementById('user_filter');
    const dateFilter = document.getElementById('date_filter');
    const searchFilter = document.getElementById('search_filter');
    const tableRows = document.querySelectorAll('tbody tr[data-action]');

    function filterTable() {
        const actionValue = actionFilter.value;
        const userValue = userFilter.value;
        const dateValue = dateFilter.value;
        const searchValue = searchFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const action = row.getAttribute('data-action');
            const user = row.getAttribute('data-user');
            const date = parseInt(row.getAttribute('data-date'));
            const description = row.getAttribute('data-description');

            let showRow = true;

            if (actionValue && action !== actionValue) showRow = false;
            if (userValue && user !== userValue) showRow = false;
            if (searchValue && !description.includes(searchValue)) showRow = false;

            // Date filtering
            if (dateValue) {
                const now = Math.floor(Date.now() / 1000);
                const dayInSeconds = 24 * 60 * 60;
                
                switch (dateValue) {
                    case 'today':
                        if (date < now - dayInSeconds) showRow = false;
                        break;
                    case 'week':
                        if (date < now - (7 * dayInSeconds)) showRow = false;
                        break;
                    case 'month':
                        if (date < now - (30 * dayInSeconds)) showRow = false;
                        break;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    actionFilter.addEventListener('change', filterTable);
    userFilter.addEventListener('change', filterTable);
    dateFilter.addEventListener('change', filterTable);
    searchFilter.addEventListener('input', filterTable);
});

// Show cleanup modal
function showCleanupModal() {
    document.getElementById('cleanup-modal').classList.remove('hidden');
}

// Confirm cleanup
function confirmCleanup() {
    const days = document.getElementById('cleanup_days').value;
    window.location.href = `logs.php?cleanup=old&days=${days}`;
}

// Export logs
function exportLogs() {
    window.location.href = 'export_logs.php';
}

// View log details
function viewLogDetails(logId) {
    // يمكن إضافة modal لعرض تفاصيل السجل
    showSuccessToast('عرض تفاصيل السجل رقم ' + logId);
}

// Delete log
function deleteLog(logId) {
    showConfirmation('هل أنت متأكد من حذف هذا السجل؟', (confirmed) => {
        if (confirmed) {
            window.location.href = `logs.php?delete=${logId}`;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?> 