<?php
require_once '../config.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
checkAdminLogin();

// الحصول على معلومات المستخدم
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف الخدمة
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $service_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $message = 'تم حذف الخدمة بنجاح';
        logAdminAction('delete_service', 'services', "حذف خدمة رقم $service_id");
        
        // إعادة التوجيه للقائمة
        header('Location: services.php?deleted=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف الخدمة';
        error_log('Delete Service Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة الخدمة
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $service_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE services SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$service_id]);
        $message = 'تم تحديث حالة الخدمة بنجاح';
        logAdminAction('toggle_service_status', 'services', "تغيير حالة خدمة رقم $service_id");
        
        // إعادة التوجيه للقائمة
        header('Location: services.php?status_updated=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة الخدمة';
        error_log('Toggle Service Status Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة التميز
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $service_id = (int)$_GET['toggle_featured'];
    try {
        $stmt = $pdo->prepare("UPDATE services SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$service_id]);
        $message = 'تم تحديث حالة التميز بنجاح';
        logAdminAction('toggle_service_featured', 'services', "تغيير حالة التميز لخدمة رقم $service_id");
        
        // إعادة التوجيه للقائمة
        header('Location: services.php?featured_updated=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة التميز';
        error_log('Toggle Service Featured Error: ' . $e->getMessage());
    }
}

// جلب قائمة الخدمات
$services = [];
try {
    // التحقق من وجود جدول services
    $tableExists = $pdo->query("SHOW TABLES LIKE 'services'")->fetch();
    if ($tableExists) {
        // التحقق من أعمدة جدول service_categories
        $categoryColumns = $pdo->query("SHOW COLUMNS FROM service_categories")->fetchAll(PDO::FETCH_COLUMN);
        $categoryNameField = in_array('name', $categoryColumns) ? 'sc.name' : 
                           (in_array('title', $categoryColumns) ? 'sc.title' : 'sc.slug');
        
        // التحقق من وجود جدول service_translations
        $translationTableExists = $pdo->query("SHOW TABLES LIKE 'service_translations'")->fetch();
        
        if ($translationTableExists) {
            // التحقق من أعمدة جدول الترجمات
            $translationColumns = $pdo->query("SHOW COLUMNS FROM service_translations")->fetchAll(PDO::FETCH_COLUMN);
            
            $stmt = $pdo->query("
                SELECT s.*, 
                       au.first_name, au.last_name,
                       $categoryNameField as category_name,
                       st.name as ar_title,
                       st.short_description as ar_description,
                       st.full_description as ar_content,
                       s.featured_image as ar_featured_image,
                       st.meta_title as ar_meta_title,
                       st.meta_description as ar_meta_description,
                       s.og_title as ar_og_title,
                       s.og_description as ar_og_description,
                       s.og_image as ar_og_image,
                       s.canonical_url as ar_canonical_url
                FROM services s
                LEFT JOIN admin_users au ON s.created_by = au.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN service_translations st ON s.id = st.service_id AND st.language_id = 1
                ORDER BY s.created_at DESC
            ");
        } else {
            // إذا لم يكن جدول الترجمات موجود، استخدم البيانات الأساسية فقط
            $stmt = $pdo->query("
                SELECT s.*, 
                       au.first_name, au.last_name,
                       $categoryNameField as category_name,
                       '' as ar_title,
                       '' as ar_description,
                       '' as ar_content,
                       s.featured_image as ar_featured_image,
                       s.meta_title as ar_meta_title,
                       s.meta_description as ar_meta_description,
                       s.og_title as ar_og_title,
                       s.og_description as ar_og_description,
                       s.og_image as ar_og_image,
                       s.canonical_url as ar_canonical_url
                FROM services s
                LEFT JOIN admin_users au ON s.created_by = au.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                ORDER BY s.created_at DESC
            ");
        }
        
        $services = $stmt->fetchAll();
        
        // جلب الترجمات الإنجليزية
        if ($translationTableExists) {
            foreach ($services as &$service) {
                // جلب الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    SELECT name, short_description, full_description, meta_title, meta_description
                    FROM service_translations 
                    WHERE service_id = ? AND language_id = 2
                ");
                $stmt->execute([$service['id']]);
                $en_translation = $stmt->fetch();
                
                $service['en_title'] = $en_translation['name'] ?? '';
                $service['en_description'] = $en_translation['short_description'] ?? '';
                $service['en_content'] = $en_translation['full_description'] ?? '';
                $service['en_featured_image'] = $service['featured_image'] ?? '';
                $service['en_meta_title'] = $en_translation['meta_title'] ?? '';
                $service['en_meta_description'] = $en_translation['meta_description'] ?? '';
                $service['en_og_title'] = $service['og_title'] ?? '';
                $service['en_og_description'] = $service['og_description'] ?? '';
                $service['en_og_image'] = $service['og_image'] ?? '';
                $service['en_canonical_url'] = $service['canonical_url'] ?? '';
            }
        } else {
            // إذا لم يكن جدول الترجمات موجود، املأ البيانات الإنجليزية فارغة
            foreach ($services as &$service) {
                $service['en_title'] = '';
                $service['en_description'] = '';
                $service['en_content'] = '';
                $service['en_featured_image'] = $service['featured_image'] ?? '';
                $service['en_meta_title'] = '';
                $service['en_meta_description'] = '';
                $service['en_og_title'] = $service['og_title'] ?? '';
                $service['en_og_description'] = $service['og_description'] ?? '';
                $service['en_og_image'] = $service['og_image'] ?? '';
                $service['en_canonical_url'] = $service['canonical_url'] ?? '';
            }
        }
    } else {
        $services = [];
        error_log('Services table does not exist');
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب الخدمات: ' . $e->getMessage();
    error_log('Fetch Services Error: ' . $e->getMessage());
    $services = [];
}

// جلب قائمة الفئات للإضافة
$categories = [];
try {
    // التحقق من وجود جدول service_categories
    $tableExists = $pdo->query("SHOW TABLES LIKE 'service_categories'")->fetch();
    if ($tableExists) {
        // التحقق من أعمدة الجدول
        $columns = $pdo->query("SHOW COLUMNS FROM service_categories")->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('name', $columns)) {
            $stmt = $pdo->query("
                SELECT id, name, description
                FROM service_categories
                WHERE is_active = 1
                ORDER BY name
            ");
        } elseif (in_array('title', $columns)) {
            $stmt = $pdo->query("
                SELECT id, title as name, description
                FROM service_categories
                WHERE is_active = 1
                ORDER BY title
            ");
        } else {
            // إذا لم يكن هناك عمود name أو title، استخدم slug
            $stmt = $pdo->query("
                SELECT id, slug as name, '' as description
                FROM service_categories
                WHERE is_active = 1
                ORDER BY slug
            ");
        }
        $categories = $stmt->fetchAll();
    } else {
        // إذا لم يكن الجدول موجود، استخدم مصفوفة فارغة
        $categories = [];
        error_log('Service Categories table does not exist');
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب قائمة الفئات: ' . $e->getMessage();
    error_log('Fetch Categories Error: ' . $e->getMessage());
    $categories = [];
}

// جلب قائمة المستخدمين للإضافة
$users = [];
try {
    // التحقق من وجود جدول admin_users
    $tableExists = $pdo->query("SHOW TABLES LIKE 'admin_users'")->fetch();
    if ($tableExists) {
        $stmt = $pdo->query("
            SELECT id, first_name, last_name, email
            FROM admin_users
            ORDER BY first_name, last_name
        ");
        $users = $stmt->fetchAll();
    } else {
        // إذا لم يكن الجدول موجود، استخدم مصفوفة فارغة
        $users = [];
        error_log('Admin Users table does not exist');
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب قائمة المستخدمين: ' . $e->getMessage();
    error_log('Fetch Users Error: ' . $e->getMessage());
    $users = [];
}

$page_title = 'إدارة الخدمات';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة الخدمات</h1>
                <p class="text-gray-600">إدارة خدمات الموقع ومحتواها</p>
            </div>
            <button onclick="openAddModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة خدمة جديدة
            </button>
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
    
    <?php if (isset($_GET['created'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم إضافة الخدمة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث الخدمة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم حذف الخدمة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['status_updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث حالة الخدمة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['featured_updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث حالة التميز بنجاح!
        </div>
    <?php endif; ?>
    
    <!-- Services Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة الخدمات</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الخدمة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الفئة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            منشئ الخدمة
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
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-cogs text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد خدمات</p>
                                    <p class="text-sm">ابدأ بإضافة خدمة جديدة</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-cogs text-green-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($service['ar_title'] ?: $service['en_title'] ?: $service['slug'] ?: 'بدون عنوان'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($service['slug'] ?: ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($service['category_name'] ?: 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <?php if ($service['is_active']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                نشط
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                غير نشط
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($service['is_featured']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                مميز
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($service['first_name'] . ' ' . $service['last_name'] ?: 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $service['created_at'] ? date('Y/m/d', strtotime($service['created_at'])) : 'غير محدد'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($service)); ?>)" 
                                                class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="services.php?toggle_status=<?php echo $service['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذه الخدمة؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="services.php?toggle_featured=<?php echo $service['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-blue-600 hover:text-blue-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة التميز لهذه الخدمة؟')">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="services.php?delete=<?php echo $service['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
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

<!-- Add Service Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">إضافة خدمة جديدة</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addServiceForm" method="POST" action="services.php">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رابط الخدمة</label>
                        <input type="text" name="slug" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="مثال: fire-protection">
                    </div>
                    
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">فئة الخدمة</label>
                        <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر الفئة</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">السعر</label>
                        <input type="number" name="price" step="0.01" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="0.00">
                    </div>
                    
                    <!-- Created By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">منشئ الخدمة</label>
                        <select name="created_by" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر المستخدم</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 space-x-reverse">
                    <button type="button" onclick="closeModal('addModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                        إضافة الخدمة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">تعديل الخدمة</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editServiceForm" method="POST" action="services.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="service_id" id="edit_service_id">
                
                <!-- Service Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">معلومات الخدمة</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="font-medium">الرابط:</span> 
                            <span id="edit_slug"></span>
                        </div>
                        <div>
                            <span class="font-medium">الفئة:</span> 
                            <span id="edit_category"></span>
                        </div>
                        <div>
                            <span class="font-medium">السعر:</span> 
                            <span id="edit_price"></span>
                        </div>
                        <div>
                            <span class="font-medium">المنشئ:</span> 
                            <span id="edit_created_by"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Arabic Content -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">المحتوى العربي</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                            <input type="text" name="ar_title" id="edit_ar_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الوصف المختصر</label>
                            <input type="text" name="ar_description" id="edit_ar_description" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">عنوان SEO</label>
                            <input type="text" name="ar_meta_title" id="edit_ar_meta_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">وصف SEO</label>
                            <input type="text" name="ar_meta_description" id="edit_ar_meta_description" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">صورة مميزة</label>
                            <input type="text" name="ar_featured_image" id="edit_ar_featured_image" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">عنوان Open Graph</label>
                            <input type="text" name="ar_og_title" id="edit_ar_og_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">وصف Open Graph</label>
                            <input type="text" name="ar_og_description" id="edit_ar_og_description" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">صورة Open Graph</label>
                            <input type="text" name="ar_og_image" id="edit_ar_og_image" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط Canonical</label>
                            <input type="url" name="ar_canonical_url" id="edit_ar_canonical_url" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الخدمة</label>
                            <textarea name="ar_content" id="edit_ar_content" rows="8" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="محتوى الخدمة..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- English Content -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">المحتوى الإنجليزي</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="en_title" id="edit_en_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                            <input type="text" name="en_description" id="edit_en_description" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SEO Title</label>
                            <input type="text" name="en_meta_title" id="edit_en_meta_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SEO Description</label>
                            <input type="text" name="en_meta_description" id="edit_en_meta_description" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                            <input type="text" name="en_featured_image" id="edit_en_featured_image" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Open Graph Title</label>
                            <input type="text" name="en_og_title" id="edit_en_og_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Open Graph Description</label>
                            <input type="text" name="en_og_description" id="edit_en_og_description" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Open Graph Image</label>
                            <input type="text" name="en_og_image" id="edit_en_og_image" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Canonical URL</label>
                            <input type="url" name="en_canonical_url" id="edit_en_canonical_url" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Content</label>
                            <textarea name="en_content" id="edit_en_content" rows="8" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="Service content..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 space-x-reverse">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function openEditModal(serviceData) {
    // تعبئة البيانات في النموذج
    document.getElementById('edit_service_id').value = serviceData.id;
    document.getElementById('edit_slug').textContent = serviceData.slug;
    document.getElementById('edit_category').textContent = serviceData.category_name || 'غير محدد';
    document.getElementById('edit_price').textContent = serviceData.price ? serviceData.price + ' ريال' : 'غير محدد';
    document.getElementById('edit_created_by').textContent = (serviceData.first_name || '') + ' ' + (serviceData.last_name || '');
    
    // تعبئة المحتوى العربي
    document.getElementById('edit_ar_title').value = serviceData.ar_title || '';
    document.getElementById('edit_ar_description').value = serviceData.ar_description || '';
    document.getElementById('edit_ar_content').value = serviceData.ar_content || '';
    document.getElementById('edit_ar_featured_image').value = serviceData.ar_featured_image || '';
    document.getElementById('edit_ar_meta_title').value = serviceData.ar_meta_title || '';
    document.getElementById('edit_ar_meta_description').value = serviceData.ar_meta_description || '';
    document.getElementById('edit_ar_og_title').value = serviceData.ar_og_title || '';
    document.getElementById('edit_ar_og_description').value = serviceData.ar_og_description || '';
    document.getElementById('edit_ar_og_image').value = serviceData.ar_og_image || '';
    document.getElementById('edit_ar_canonical_url').value = serviceData.ar_canonical_url || '';
    
    // تعبئة المحتوى الإنجليزي
    document.getElementById('edit_en_title').value = serviceData.en_title || '';
    document.getElementById('edit_en_description').value = serviceData.en_description || '';
    document.getElementById('edit_en_content').value = serviceData.en_content || '';
    document.getElementById('edit_en_featured_image').value = serviceData.en_featured_image || '';
    document.getElementById('edit_en_meta_title').value = serviceData.en_meta_title || '';
    document.getElementById('edit_en_meta_description').value = serviceData.en_meta_description || '';
    document.getElementById('edit_en_og_title').value = serviceData.en_og_title || '';
    document.getElementById('edit_en_og_description').value = serviceData.en_og_description || '';
    document.getElementById('edit_en_og_image').value = serviceData.en_og_image || '';
    document.getElementById('edit_en_canonical_url').value = serviceData.en_canonical_url || '';
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// إغلاق النوافذ عند النقر خارجها
window.onclick = function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
}
</script>

<?php
// معالجة النماذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        // إضافة خدمة جديدة
        $slug = $_POST['slug'];
        $category_id = (int)$_POST['category_id'];
        $price = $_POST['price'] ? (float)$_POST['price'] : null;
        $created_by = (int)$_POST['created_by'];
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO services (slug, category_id, price, created_by, is_active, is_featured, sort_order)
                VALUES (?, ?, ?, ?, 1, 0, 0)
            ");
            $stmt->execute([$slug, $category_id, $price, $created_by]);
            $service_id = $pdo->lastInsertId();
            
            $message = 'تم إضافة الخدمة بنجاح';
            logAdminAction('create_service', 'services', "إضافة خدمة جديدة: $slug");
            
            // إعادة التوجيه للقائمة
            header('Location: services.php?created=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء إضافة الخدمة: ' . $e->getMessage();
            error_log('Create Service Error: ' . $e->getMessage());
        }
    } elseif ($action === 'edit') {
        // تحديث خدمة موجودة
        $service_id = (int)$_POST['service_id'];
        
        // المحتوى العربي
        $ar_title = $_POST['ar_title'] ?? '';
        $ar_description = $_POST['ar_description'] ?? '';
        $ar_content = $_POST['ar_content'] ?? '';
        $ar_featured_image = $_POST['ar_featured_image'] ?? '';
        $ar_meta_title = $_POST['ar_meta_title'] ?? '';
        $ar_meta_description = $_POST['ar_meta_description'] ?? '';
        $ar_og_title = $_POST['ar_og_title'] ?? '';
        $ar_og_description = $_POST['ar_og_description'] ?? '';
        $ar_og_image = $_POST['ar_og_image'] ?? '';
        $ar_canonical_url = $_POST['ar_canonical_url'] ?? '';
        
        // المحتوى الإنجليزي
        $en_title = $_POST['en_title'] ?? '';
        $en_description = $_POST['en_description'] ?? '';
        $en_content = $_POST['en_content'] ?? '';
        $en_featured_image = $_POST['en_featured_image'] ?? '';
        $en_meta_title = $_POST['en_meta_title'] ?? '';
        $en_meta_description = $_POST['en_meta_description'] ?? '';
        $en_og_title = $_POST['en_og_title'] ?? '';
        $en_og_description = $_POST['en_og_description'] ?? '';
        $en_og_image = $_POST['en_og_image'] ?? '';
        $en_canonical_url = $_POST['en_canonical_url'] ?? '';
        
        try {
            // التحقق من وجود جدول service_translations
            $tableExists = $pdo->query("SHOW TABLES LIKE 'service_translations'")->fetch();
            
            if ($tableExists) {
                // تحديث الترجمة العربية
                $stmt = $pdo->prepare("
                    INSERT INTO service_translations (service_id, language_id, title, description, content, featured_image, meta_title, meta_description, og_title, og_description, og_image, canonical_url)
                    VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), description = VALUES(description), content = VALUES(content),
                    featured_image = VALUES(featured_image), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description),
                    og_title = VALUES(og_title), og_description = VALUES(og_description), og_image = VALUES(og_image), canonical_url = VALUES(canonical_url)
                ");
                $stmt->execute([$service_id, $ar_title, $ar_description, $ar_content, $ar_featured_image, $ar_meta_title, $ar_meta_description, $ar_og_title, $ar_og_description, $ar_og_image, $ar_canonical_url]);
                
                // تحديث الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    INSERT INTO service_translations (service_id, language_id, title, description, content, featured_image, meta_title, meta_description, og_title, og_description, og_image, canonical_url)
                    VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), description = VALUES(description), content = VALUES(content),
                    featured_image = VALUES(featured_image), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description),
                    og_title = VALUES(og_title), og_description = VALUES(og_description), og_image = VALUES(og_image), canonical_url = VALUES(canonical_url)
                ");
                $stmt->execute([$service_id, $en_title, $en_description, $en_content, $en_featured_image, $en_meta_title, $en_meta_description, $en_og_title, $en_og_description, $en_og_image, $en_canonical_url]);
            }
            
            $message = 'تم تحديث الخدمة بنجاح';
            logAdminAction('update_service', 'services', "تحديث خدمة رقم $service_id");
            
            // إعادة التوجيه للقائمة
            header('Location: services.php?updated=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء تحديث الخدمة: ' . $e->getMessage();
            error_log('Update Service Error: ' . $e->getMessage());
        }
    }
}
?>

<?php include 'includes/footer.php'; ?> 