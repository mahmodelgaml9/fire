<?php
require_once '../config.php';
require_once 'includes/auth.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف الموقع
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $location_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM locations WHERE id = ?");
        $stmt->execute([$location_id]);
        $message = 'تم حذف الموقع بنجاح';
        logAdminAction('delete_location', 'locations', "حذف موقع رقم $location_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف الموقع';
        error_log('Delete Location Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة الموقع
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $location_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE locations SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$location_id]);
        $message = 'تم تحديث حالة الموقع بنجاح';
        logAdminAction('toggle_location_status', 'locations', "تغيير حالة موقع رقم $location_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة الموقع';
        error_log('Toggle Location Status Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة التميز
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $location_id = (int)$_GET['toggle_featured'];
    try {
        $stmt = $pdo->prepare("UPDATE locations SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$location_id]);
        $message = 'تم تحديث حالة التميز بنجاح';
        logAdminAction('toggle_location_featured', 'locations', "تغيير حالة التميز لموقع رقم $location_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة التميز';
        error_log('Toggle Location Featured Error: ' . $e->getMessage());
    }
}

// جلب قائمة المواقع
$locations = [];
try {
    $stmt = $pdo->query("
        SELECT l.*, 
               CONCAT(au.first_name, ' ', au.last_name) as created_by_name
        FROM locations l
        LEFT JOIN admin_users au ON l.created_by = au.id
        ORDER BY l.created_at DESC
    ");
    $locations = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب المواقع';
    error_log('Fetch Locations Error: ' . $e->getMessage());
}

$page_title = 'إدارة المواقع';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة المواقع</h1>
                <p class="text-gray-600">إدارة مواقع العمل وإعداداتها</p>
            </div>
            <a href="locations.php?action=create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة موقع جديد
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الموقع</label>
                    <select id="type_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الأنواع</option>
                        <option value="city">مدينة</option>
                        <option value="district">حي</option>
                        <option value="area">منطقة</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">التميز</label>
                    <select id="featured_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع المواقع</option>
                        <option value="1">مميز</option>
                        <option value="0">غير مميز</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة المواقع</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الموقع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التميز
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            منشئ الموقع
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
                    <?php if (empty($locations)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-map-marker-alt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد مواقع</p>
                                    <p class="text-sm">ابدأ بإضافة موقع جديد</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($locations as $location): ?>
                            <tr class="hover:bg-gray-50" data-type="<?php echo $location['type']; ?>" data-status="<?php echo $location['is_active']; ?>" data-featured="<?php echo $location['is_featured']; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                                                <i class="fas fa-map-marker-alt text-red-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($location['name'] ?? 'بدون اسم'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($location['slug'] ?? ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $type_text = [
                                        'city' => 'مدينة',
                                        'district' => 'حي',
                                        'area' => 'منطقة'
                                    ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo $type_text[$location['type']] ?? 'غير محدد'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($location['is_active'] ?? false): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            نشط
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            غير نشط
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($location['is_featured'] ?? false): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            مميز
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            عادي
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($location['created_by_name'] ?? 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('Y/m/d', strtotime($location['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="locations.php?action=edit&id=<?php echo $location['id']; ?>" 
                                           class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="locations.php?toggle_status=<?php echo $location['id']; ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذا الموقع؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="locations.php?toggle_featured=<?php echo $location['id']; ?>" 
                                           class="text-purple-600 hover:text-purple-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة التميز لهذا الموقع؟')">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="locations.php?delete=<?php echo $location['id']; ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذا الموقع؟')">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
    const typeFilter = document.getElementById('type_filter');
    const statusFilter = document.getElementById('status_filter');
    const featuredFilter = document.getElementById('featured_filter');
    const tableRows = document.querySelectorAll('tbody tr[data-type]');

    function filterTable() {
        const typeValue = typeFilter.value;
        const statusValue = statusFilter.value;
        const featuredValue = featuredFilter.value;

        tableRows.forEach(row => {
            const type = row.getAttribute('data-type');
            const status = row.getAttribute('data-status');
            const featured = row.getAttribute('data-featured');

            let showRow = true;

            if (typeValue && type !== typeValue) showRow = false;
            if (statusValue !== '' && status !== statusValue) showRow = false;
            if (featuredValue !== '' && featured !== featuredValue) showRow = false;

            row.style.display = showRow ? '' : 'none';
        });
    }

    typeFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    featuredFilter.addEventListener('change', filterTable);
});
</script>

<?php include 'includes/footer.php'; ?> 