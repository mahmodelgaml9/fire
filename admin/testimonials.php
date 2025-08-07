<?php
require_once '../config.php';
require_once 'includes/auth.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف الشهادة
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $testimonial_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$testimonial_id]);
        $message = 'تم حذف الشهادة بنجاح';
        logAdminAction('delete_testimonial', 'testimonials', "حذف شهادة رقم $testimonial_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف الشهادة';
        error_log('Delete Testimonial Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة الشهادة
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $testimonial_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET is_published = NOT is_published WHERE id = ?");
        $stmt->execute([$testimonial_id]);
        $message = 'تم تحديث حالة الشهادة بنجاح';
        logAdminAction('toggle_testimonial_status', 'testimonials', "تغيير حالة شهادة رقم $testimonial_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة الشهادة';
        error_log('Toggle Testimonial Status Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة التميز
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $testimonial_id = (int)$_GET['toggle_featured'];
    try {
        $stmt = $pdo->prepare("UPDATE testimonials SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$testimonial_id]);
        $message = 'تم تحديث حالة التميز بنجاح';
        logAdminAction('toggle_testimonial_featured', 'testimonials', "تغيير حالة التميز لشهادة رقم $testimonial_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة التميز';
        error_log('Toggle Testimonial Featured Error: ' . $e->getMessage());
    }
}

// جلب قائمة الشهادات
$testimonials = [];
try {
    $stmt = $pdo->query("
        SELECT t.*, 
               CONCAT(au.first_name, ' ', au.last_name) as created_by_name,
               p.title as project_title,
               s.name as service_name,
               l.name as location_name
        FROM testimonials t
        LEFT JOIN admin_users au ON t.created_by = au.id
        LEFT JOIN projects p ON t.project_id = p.id
        LEFT JOIN services s ON t.service_id = s.id
        LEFT JOIN locations l ON t.location_id = l.id
        ORDER BY t.created_at DESC
    ");
    $testimonials = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب الشهادات';
    error_log('Fetch Testimonials Error: ' . $e->getMessage());
}

$page_title = 'إدارة الشهادات';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة الشهادات</h1>
                <p class="text-gray-600">إدارة شهادات العملاء وإعداداتها</p>
            </div>
            <a href="testimonials.php?action=create" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة شهادة جديدة
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select id="status_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الحالات</option>
                        <option value="1">منشور</option>
                        <option value="0">غير منشور</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التميز</label>
                    <select id="featured_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الشهادات</option>
                        <option value="1">مميز</option>
                        <option value="0">غير مميز</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التقييم</label>
                    <select id="rating_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع التقييمات</option>
                        <option value="5">5 نجوم</option>
                        <option value="4">4 نجوم</option>
                        <option value="3">3 نجوم</option>
                        <option value="2">2 نجوم</option>
                        <option value="1">1 نجمة</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة الشهادات</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العميل
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التقييم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التميز
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            منشئ الشهادة
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
                    <?php if (empty($testimonials)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-quote-right text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد شهادات</p>
                                    <p class="text-sm">ابدأ بإضافة شهادة جديدة</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <tr class="hover:bg-gray-50" data-status="<?php echo $testimonial['is_published']; ?>" data-featured="<?php echo $testimonial['is_featured']; ?>" data-rating="<?php echo $testimonial['rating']; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center">
                                                <i class="fas fa-quote-right text-orange-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($testimonial['client_name'] ?? 'بدون اسم'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($testimonial['client_company'] ?? ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="mr-2 text-sm text-gray-500">(<?php echo $testimonial['rating']; ?>)</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($testimonial['is_published'] ?? false): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            منشور
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            غير منشور
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($testimonial['is_featured'] ?? false): ?>
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
                                    <?php echo htmlspecialchars($testimonial['created_by_name'] ?? 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('Y/m/d', strtotime($testimonial['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="testimonials.php?action=edit&id=<?php echo $testimonial['id']; ?>" 
                                           class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="testimonials.php?toggle_status=<?php echo $testimonial['id']; ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذه الشهادة؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="testimonials.php?toggle_featured=<?php echo $testimonial['id']; ?>" 
                                           class="text-purple-600 hover:text-purple-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة التميز لهذه الشهادة؟')">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="testimonials.php?delete=<?php echo $testimonial['id']; ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذه الشهادة؟')">
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
    const statusFilter = document.getElementById('status_filter');
    const featuredFilter = document.getElementById('featured_filter');
    const ratingFilter = document.getElementById('rating_filter');
    const tableRows = document.querySelectorAll('tbody tr[data-status]');

    function filterTable() {
        const statusValue = statusFilter.value;
        const featuredValue = featuredFilter.value;
        const ratingValue = ratingFilter.value;

        tableRows.forEach(row => {
            const status = row.getAttribute('data-status');
            const featured = row.getAttribute('data-featured');
            const rating = row.getAttribute('data-rating');

            let showRow = true;

            if (statusValue !== '' && status !== statusValue) showRow = false;
            if (featuredValue !== '' && featured !== featuredValue) showRow = false;
            if (ratingValue && rating !== ratingValue) showRow = false;

            row.style.display = showRow ? '' : 'none';
        });
    }

    statusFilter.addEventListener('change', filterTable);
    featuredFilter.addEventListener('change', filterTable);
    ratingFilter.addEventListener('change', filterTable);
});
</script>

<?php include 'includes/footer.php'; ?> 