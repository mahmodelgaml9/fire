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

// معالجة حذف المشروع
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $project_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $message = 'تم حذف المشروع بنجاح';
        logAdminAction('delete_project', 'projects', "حذف مشروع رقم $project_id");
        
        // إعادة التوجيه للقائمة
        header('Location: projects.php?deleted=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف المشروع';
        error_log('Delete Project Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة المشروع
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $project_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE projects SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$project_id]);
        $message = 'تم تحديث حالة المشروع بنجاح';
        logAdminAction('toggle_project_status', 'projects', "تغيير حالة مشروع رقم $project_id");
        
        // إعادة التوجيه للقائمة
        header('Location: projects.php?status_updated=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة المشروع';
        error_log('Toggle Project Status Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة التميز
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $project_id = (int)$_GET['toggle_featured'];
    try {
        $stmt = $pdo->prepare("UPDATE projects SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$project_id]);
        $message = 'تم تحديث حالة التميز بنجاح';
        logAdminAction('toggle_project_featured', 'projects', "تغيير حالة التميز لمشروع رقم $project_id");
        
        // إعادة التوجيه للقائمة
        header('Location: projects.php?featured_updated=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة التميز';
        error_log('Toggle Project Featured Error: ' . $e->getMessage());
    }
}

// جلب قائمة المشاريع
$projects = [];
try {
    // التحقق من وجود جدول projects
    $tableExists = $pdo->query("SHOW TABLES LIKE 'projects'")->fetch();
    if ($tableExists) {
        // التحقق من أعمدة جدول project_categories
        $categoryColumns = $pdo->query("SHOW COLUMNS FROM project_categories")->fetchAll(PDO::FETCH_COLUMN);
        $categoryNameField = in_array('name', $categoryColumns) ? 'pc.name' : 
                           (in_array('title', $categoryColumns) ? 'pc.title' : 'pc.slug');
        
        // التحقق من وجود جدول project_translations
        $translationTableExists = $pdo->query("SHOW TABLES LIKE 'project_translations'")->fetch();
        
        if ($translationTableExists) {
            // التحقق من أعمدة جدول الترجمات
            $translationColumns = $pdo->query("SHOW COLUMNS FROM project_translations")->fetchAll(PDO::FETCH_COLUMN);
            
            $stmt = $pdo->query("
                SELECT p.*, 
                       au.first_name, au.last_name,
                       $categoryNameField as category_name,
                       pt.title as ar_title,
                       pt.subtitle as ar_subtitle,
                       pt.description as ar_description,
                       pt.challenge as ar_challenge,
                       pt.solution as ar_solution,
                       pt.results as ar_results,
                       pt.testimonial as ar_testimonial,
                       pt.testimonial_author as ar_testimonial_author,
                       pt.testimonial_position as ar_testimonial_position,
                       p.featured_image as ar_featured_image,
                       pt.meta_title as ar_meta_title,
                       pt.meta_description as ar_meta_description,
                       p.og_title as ar_og_title,
                       p.og_description as ar_og_description,
                       p.og_image as ar_og_image,
                       p.canonical_url as ar_canonical_url
                FROM projects p
                LEFT JOIN admin_users au ON p.created_by = au.id
                LEFT JOIN project_categories pc ON p.category_id = pc.id
                LEFT JOIN project_translations pt ON p.id = pt.project_id AND pt.language_id = 1
                ORDER BY p.created_at DESC
            ");
        } else {
            // إذا لم يكن جدول الترجمات موجود، استخدم البيانات الأساسية فقط
            $stmt = $pdo->query("
                SELECT p.*, 
                       au.first_name, au.last_name,
                       $categoryNameField as category_name,
                       '' as ar_title,
                       '' as ar_subtitle,
                       '' as ar_description,
                       '' as ar_challenge,
                       '' as ar_solution,
                       '' as ar_results,
                       '' as ar_testimonial,
                       '' as ar_testimonial_author,
                       '' as ar_testimonial_position,
                       p.featured_image as ar_featured_image,
                       p.meta_title as ar_meta_title,
                       p.meta_description as ar_meta_description,
                       p.og_title as ar_og_title,
                       p.og_description as ar_og_description,
                       p.og_image as ar_og_image,
                       p.canonical_url as ar_canonical_url
                FROM projects p
                LEFT JOIN admin_users au ON p.created_by = au.id
                LEFT JOIN project_categories pc ON p.category_id = pc.id
                ORDER BY p.created_at DESC
            ");
        }
        
        $projects = $stmt->fetchAll();
        
        // جلب الترجمات الإنجليزية
        if ($translationTableExists) {
            foreach ($projects as &$project) {
                // جلب الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    SELECT title, subtitle, description, challenge, solution, results, testimonial, 
                           testimonial_author, testimonial_position, meta_title, meta_description
                    FROM project_translations 
                    WHERE project_id = ? AND language_id = 2
                ");
                $stmt->execute([$project['id']]);
                $en_translation = $stmt->fetch();
                
                $project['en_title'] = $en_translation['title'] ?? '';
                $project['en_subtitle'] = $en_translation['subtitle'] ?? '';
                $project['en_description'] = $en_translation['description'] ?? '';
                $project['en_challenge'] = $en_translation['challenge'] ?? '';
                $project['en_solution'] = $en_translation['solution'] ?? '';
                $project['en_results'] = $en_translation['results'] ?? '';
                $project['en_testimonial'] = $en_translation['testimonial'] ?? '';
                $project['en_testimonial_author'] = $en_translation['testimonial_author'] ?? '';
                $project['en_testimonial_position'] = $en_translation['testimonial_position'] ?? '';
                $project['en_featured_image'] = $project['featured_image'] ?? '';
                $project['en_meta_title'] = $en_translation['meta_title'] ?? '';
                $project['en_meta_description'] = $en_translation['meta_description'] ?? '';
                $project['en_og_title'] = $project['og_title'] ?? '';
                $project['en_og_description'] = $project['og_description'] ?? '';
                $project['en_og_image'] = $project['og_image'] ?? '';
                $project['en_canonical_url'] = $project['canonical_url'] ?? '';
            }
        } else {
            // إذا لم يكن جدول الترجمات موجود، املأ البيانات الإنجليزية فارغة
            foreach ($projects as &$project) {
                $project['en_title'] = '';
                $project['en_subtitle'] = '';
                $project['en_description'] = '';
                $project['en_challenge'] = '';
                $project['en_solution'] = '';
                $project['en_results'] = '';
                $project['en_testimonial'] = '';
                $project['en_testimonial_author'] = '';
                $project['en_testimonial_position'] = '';
                $project['en_featured_image'] = $project['featured_image'] ?? '';
                $project['en_meta_title'] = '';
                $project['en_meta_description'] = '';
                $project['en_og_title'] = $project['og_title'] ?? '';
                $project['en_og_description'] = $project['og_description'] ?? '';
                $project['en_og_image'] = $project['og_image'] ?? '';
                $project['en_canonical_url'] = $project['canonical_url'] ?? '';
            }
        }
    } else {
        $projects = [];
        error_log('Projects table does not exist');
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب المشاريع: ' . $e->getMessage();
    error_log('Fetch Projects Error: ' . $e->getMessage());
    $projects = [];
}

// جلب قائمة الفئات للإضافة
$categories = [];
try {
    // التحقق من وجود جدول project_categories
    $tableExists = $pdo->query("SHOW TABLES LIKE 'project_categories'")->fetch();
    if ($tableExists) {
        // التحقق من أعمدة الجدول
        $columns = $pdo->query("SHOW COLUMNS FROM project_categories")->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('name', $columns)) {
            $stmt = $pdo->query("
                SELECT id, name, description
                FROM project_categories
                WHERE is_active = 1
                ORDER BY name
            ");
        } elseif (in_array('title', $columns)) {
            $stmt = $pdo->query("
                SELECT id, title as name, description
                FROM project_categories
                WHERE is_active = 1
                ORDER BY title
            ");
        } else {
            // إذا لم يكن هناك عمود name أو title، استخدم slug
            $stmt = $pdo->query("
                SELECT id, slug as name, '' as description
                FROM project_categories
                WHERE is_active = 1
                ORDER BY slug
            ");
        }
        $categories = $stmt->fetchAll();
    } else {
        // إذا لم يكن الجدول موجود، استخدم مصفوفة فارغة
        $categories = [];
        error_log('Project Categories table does not exist');
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

$page_title = 'إدارة المشاريع';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة المشاريع</h1>
                <p class="text-gray-600">إدارة مشاريع الموقع ومحتواها</p>
            </div>
            <button onclick="openAddModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة مشروع جديد
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
            تم إضافة المشروع بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث المشروع بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم حذف المشروع بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['status_updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث حالة المشروع بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['featured_updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث حالة التميز بنجاح!
        </div>
    <?php endif; ?>
    
    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة المشاريع</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المشروع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الفئة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            منشئ المشروع
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
                    <?php if (empty($projects)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-project-diagram text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد مشاريع</p>
                                    <p class="text-sm">ابدأ بإضافة مشروع جديد</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($projects as $project): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                                <i class="fas fa-project-diagram text-purple-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($project['ar_title'] ?: $project['en_title'] ?: $project['slug'] ?: 'بدون عنوان'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($project['slug'] ?: ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($project['category_name'] ?: 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col space-y-1">
                                        <?php if ($project['is_active']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                نشط
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                غير نشط
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($project['is_featured']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                مميز
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name'] ?: 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $project['created_at'] ? date('Y/m/d', strtotime($project['created_at'])) : 'غير محدد'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($project)); ?>)" 
                                                class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="projects.php?toggle_status=<?php echo $project['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذا المشروع؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="projects.php?toggle_featured=<?php echo $project['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-blue-600 hover:text-blue-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة التميز لهذا المشروع؟')">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="projects.php?delete=<?php echo $project['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
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

<!-- Add Project Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">إضافة مشروع جديد</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addProjectForm" method="POST" action="projects.php">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رابط المشروع</label>
                        <input type="text" name="slug" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="مثال: fire-protection-system">
                    </div>
                    
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">فئة المشروع</label>
                        <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر الفئة</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Client -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">العميل</label>
                        <input type="text" name="client" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="اسم العميل">
                    </div>
                    
                    <!-- Created By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">منشئ المشروع</label>
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
                        إضافة المشروع
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Project Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">تعديل المشروع</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editProjectForm" method="POST" action="projects.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="project_id" id="edit_project_id">
                
                <!-- Project Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">معلومات المشروع</h4>
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
                            <span class="font-medium">العميل:</span> 
                            <span id="edit_client"></span>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى المشروع</label>
                            <textarea name="ar_content" id="edit_ar_content" rows="8" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="محتوى المشروع..."></textarea>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Content</label>
                            <textarea name="en_content" id="edit_en_content" rows="8" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="Project content..."></textarea>
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

function openEditModal(projectData) {
    // تعبئة البيانات في النموذج
    document.getElementById('edit_project_id').value = projectData.id;
    document.getElementById('edit_slug').textContent = projectData.slug;
    document.getElementById('edit_category').textContent = projectData.category_name || 'غير محدد';
    document.getElementById('edit_client').textContent = projectData.client || 'غير محدد';
    document.getElementById('edit_created_by').textContent = (projectData.first_name || '') + ' ' + (projectData.last_name || '');
    
    // تعبئة المحتوى العربي
    document.getElementById('edit_ar_title').value = projectData.ar_title || '';
    document.getElementById('edit_ar_description').value = projectData.ar_description || '';
    document.getElementById('edit_ar_content').value = projectData.ar_content || '';
    document.getElementById('edit_ar_featured_image').value = projectData.ar_featured_image || '';
    document.getElementById('edit_ar_meta_title').value = projectData.ar_meta_title || '';
    document.getElementById('edit_ar_meta_description').value = projectData.ar_meta_description || '';
    document.getElementById('edit_ar_og_title').value = projectData.ar_og_title || '';
    document.getElementById('edit_ar_og_description').value = projectData.ar_og_description || '';
    document.getElementById('edit_ar_og_image').value = projectData.ar_og_image || '';
    document.getElementById('edit_ar_canonical_url').value = projectData.ar_canonical_url || '';
    
    // تعبئة المحتوى الإنجليزي
    document.getElementById('edit_en_title').value = projectData.en_title || '';
    document.getElementById('edit_en_description').value = projectData.en_description || '';
    document.getElementById('edit_en_content').value = projectData.en_content || '';
    document.getElementById('edit_en_featured_image').value = projectData.en_featured_image || '';
    document.getElementById('edit_en_meta_title').value = projectData.en_meta_title || '';
    document.getElementById('edit_en_meta_description').value = projectData.en_meta_description || '';
    document.getElementById('edit_en_og_title').value = projectData.en_og_title || '';
    document.getElementById('edit_en_og_description').value = projectData.en_og_description || '';
    document.getElementById('edit_en_og_image').value = projectData.en_og_image || '';
    document.getElementById('edit_en_canonical_url').value = projectData.en_canonical_url || '';
    
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
        // إضافة مشروع جديد
        $slug = $_POST['slug'];
        $category_id = (int)$_POST['category_id'];
        $client = $_POST['client'] ?? '';
        $created_by = (int)$_POST['created_by'];
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO projects (slug, category_id, client, created_by, is_active, is_featured, sort_order)
                VALUES (?, ?, ?, ?, 1, 0, 0)
            ");
            $stmt->execute([$slug, $category_id, $client, $created_by]);
            $project_id = $pdo->lastInsertId();
            
            $message = 'تم إضافة المشروع بنجاح';
            logAdminAction('create_project', 'projects', "إضافة مشروع جديد: $slug");
            
            // إعادة التوجيه للقائمة
            header('Location: projects.php?created=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء إضافة المشروع: ' . $e->getMessage();
            error_log('Create Project Error: ' . $e->getMessage());
        }
    } elseif ($action === 'edit') {
        // تحديث مشروع موجود
        $project_id = (int)$_POST['project_id'];
        
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
            // التحقق من وجود جدول project_translations
            $tableExists = $pdo->query("SHOW TABLES LIKE 'project_translations'")->fetch();
            
            if ($tableExists) {
                // تحديث الترجمة العربية
                $stmt = $pdo->prepare("
                    INSERT INTO project_translations (project_id, language_id, title, description, content, featured_image, meta_title, meta_description, og_title, og_description, og_image, canonical_url)
                    VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), description = VALUES(description), content = VALUES(content),
                    featured_image = VALUES(featured_image), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description),
                    og_title = VALUES(og_title), og_description = VALUES(og_description), og_image = VALUES(og_image), canonical_url = VALUES(canonical_url)
                ");
                $stmt->execute([$project_id, $ar_title, $ar_description, $ar_content, $ar_featured_image, $ar_meta_title, $ar_meta_description, $ar_og_title, $ar_og_description, $ar_og_image, $ar_canonical_url]);
                
                // تحديث الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    INSERT INTO project_translations (project_id, language_id, title, description, content, featured_image, meta_title, meta_description, og_title, og_description, og_image, canonical_url)
                    VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), description = VALUES(description), content = VALUES(content),
                    featured_image = VALUES(featured_image), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description),
                    og_title = VALUES(og_title), og_description = VALUES(og_description), og_image = VALUES(og_image), canonical_url = VALUES(canonical_url)
                ");
                $stmt->execute([$project_id, $en_title, $en_description, $en_content, $en_featured_image, $en_meta_title, $en_meta_description, $en_og_title, $en_og_description, $en_og_image, $en_canonical_url]);
            }
            
            $message = 'تم تحديث المشروع بنجاح';
            logAdminAction('update_project', 'projects', "تحديث مشروع رقم $project_id");
            
            // إعادة التوجيه للقائمة
            header('Location: projects.php?updated=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء تحديث المشروع: ' . $e->getMessage();
            error_log('Update Project Error: ' . $e->getMessage());
        }
    }
}
?>

<?php include 'includes/footer.php'; ?> 