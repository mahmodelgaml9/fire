<?php
require_once '../config.php';
require_once 'includes/auth.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف المستخدم
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = 'تم حذف المستخدم بنجاح';
        logAdminAction('delete_admin_user', 'admin_users', "حذف مستخدم رقم $user_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف المستخدم';
        error_log('Delete Admin User Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة المستخدم
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $user_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE admin_users SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$user_id]);
        $message = 'تم تحديث حالة المستخدم بنجاح';
        logAdminAction('toggle_admin_user_status', 'admin_users', "تغيير حالة مستخدم رقم $user_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة المستخدم';
        error_log('Toggle Admin User Status Error: ' . $e->getMessage());
    }
}

// جلب قائمة المستخدمين
$users = [];
try {
    $stmt = $pdo->query("
        SELECT au.*, ar.name as role_name
        FROM admin_users au
        LEFT JOIN admin_roles ar ON au.role_id = ar.id
        ORDER BY au.created_at DESC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب المستخدمين';
    error_log('Fetch Admin Users Error: ' . $e->getMessage());
}

// جلب الأدوار للفلتر
$roles = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM admin_roles ORDER BY name");
    $roles = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Fetch Roles Error: ' . $e->getMessage());
}

$page_title = 'إدارة المستخدمين';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة المستخدمين</h1>
                <p class="text-gray-600">إدارة مستخدمي لوحة التحكم وإعداداتها</p>
            </div>
            <a href="users.php?action=create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة مستخدم جديد
            </a>
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الفلاتر</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الدور</label>
                    <select id="role_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الأدوار</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select id="status_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الحالات</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <input type="text" id="search_filter" placeholder="البحث بالاسم أو البريد الإلكتروني" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة المستخدمين</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المستخدم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الدور
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            آخر تسجيل دخول
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ الإنشاء
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد مستخدمين</p>
                                    <p class="text-sm">ابدأ بإضافة مستخدم جديد</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50" data-role="<?php echo $user['role_id']; ?>" data-status="<?php echo $user['is_active']; ?>" data-name="<?php echo strtolower($user['first_name'] . ' ' . $user['last_name'] . ' ' . $user['email']); ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-user text-indigo-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($user['email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($user['role_name'] ?? 'غير محدد'); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['is_active'] ?? false): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            نشط
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            غير نشط
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $user['last_login'] ? date('Y/m/d H:i', strtotime($user['last_login'])) : 'لم يسجل دخول'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('Y/m/d', strtotime($user['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" 
                                           class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="users.php?toggle_status=<?php echo $user['id']; ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذا المستخدم؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <?php if ($user['id'] != $current_user['id']): ?>
                                            <a href="users.php?delete=<?php echo $user['id']; ?>" 
                                               class="text-red-600 hover:text-red-900"
                                               onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const roleFilter = document.getElementById('role_filter');
    const statusFilter = document.getElementById('status_filter');
    const searchFilter = document.getElementById('search_filter');
    const tableRows = document.querySelectorAll('tbody tr[data-role]');

    function filterTable() {
        const roleValue = roleFilter.value;
        const statusValue = statusFilter.value;
        const searchValue = searchFilter.value.toLowerCase();

        tableRows.forEach(row => {
            const role = row.getAttribute('data-role');
            const status = row.getAttribute('data-status');
            const name = row.getAttribute('data-name');

            let showRow = true;

            if (roleValue && role !== roleValue) showRow = false;
            if (statusValue !== '' && status !== statusValue) showRow = false;
            if (searchValue && !name.includes(searchValue)) showRow = false;

            row.style.display = showRow ? '' : 'none';
        });
    }

    roleFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    searchFilter.addEventListener('input', filterTable);
});
</script>

<?php include 'includes/footer.php'; ?> 