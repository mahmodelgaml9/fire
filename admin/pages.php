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

// معالجة حذف الصفحة
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $page_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
        $stmt->execute([$page_id]);
        $message = 'تم حذف الصفحة بنجاح';
        logAdminAction('delete_page', 'pages', "حذف صفحة رقم $page_id");
        
        // إعادة التوجيه للقائمة
        header('Location: pages.php?deleted=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف الصفحة';
        error_log('Delete Page Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة الصفحة
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $page_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE pages SET status = CASE WHEN status = 'published' THEN 'draft' ELSE 'published' END WHERE id = ?");
        $stmt->execute([$page_id]);
        $message = 'تم تحديث حالة الصفحة بنجاح';
        logAdminAction('toggle_page_status', 'pages', "تغيير حالة صفحة رقم $page_id");
        
        // إعادة التوجيه للقائمة
        header('Location: pages.php?status_updated=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة الصفحة';
        error_log('Toggle Page Status Error: ' . $e->getMessage());
    }
}

// جلب قائمة الصفحات
$pages = [];
try {
    // التحقق من وجود جدول pages
    $tableExists = $pdo->query("SHOW TABLES LIKE 'pages'")->fetch();
    if ($tableExists) {
        // التحقق من وجود جدول page_translations
        $translationTableExists = $pdo->query("SHOW TABLES LIKE 'page_translations'")->fetch();
        
        if ($translationTableExists) {
            // التحقق من أعمدة جدول الترجمات
            $translationColumns = $pdo->query("SHOW COLUMNS FROM page_translations")->fetchAll(PDO::FETCH_COLUMN);
            
            $stmt = $pdo->query("
                SELECT p.*, 
                       u.first_name, u.last_name,
                       pt.title as ar_title,
                       pt.meta_title as ar_meta_title,
                       pt.meta_description as ar_meta_description,
                       pt.content as ar_content,
                       pt.excerpt as ar_excerpt,
                       pt.featured_image as ar_featured_image,
                       pt.og_title as ar_og_title,
                       pt.og_description as ar_og_description,
                       pt.og_image as ar_og_image,
                       pt.canonical_url as ar_canonical_url
                FROM pages p
                LEFT JOIN users u ON p.created_by = u.id
                LEFT JOIN page_translations pt ON p.id = pt.page_id AND pt.language_id = 1
                ORDER BY p.created_at DESC
            ");
        } else {
            // إذا لم يكن جدول الترجمات موجود، استخدم البيانات الأساسية فقط
            $stmt = $pdo->query("
                SELECT p.*, 
                       u.first_name, u.last_name,
                       '' as ar_title,
                       '' as ar_meta_title,
                       '' as ar_meta_description,
                       '' as ar_content,
                       '' as ar_excerpt,
                       '' as ar_featured_image,
                       '' as ar_og_title,
                       '' as ar_og_description,
                       '' as ar_og_image,
                       '' as ar_canonical_url
                FROM pages p
                LEFT JOIN users u ON p.created_by = u.id
                ORDER BY p.created_at DESC
            ");
        }
        
        $pages = $stmt->fetchAll();
        
        // جلب الترجمات الإنجليزية
        if ($translationTableExists) {
            foreach ($pages as &$page) {
                // جلب الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    SELECT title, meta_title, meta_description, content, excerpt, featured_image, 
                           og_title, og_description, og_image, canonical_url
                    FROM page_translations 
                    WHERE page_id = ? AND language_id = 2
                ");
                $stmt->execute([$page['id']]);
                $en_translation = $stmt->fetch();
                
                $page['en_title'] = $en_translation['title'] ?? '';
                $page['en_meta_title'] = $en_translation['meta_title'] ?? '';
                $page['en_meta_description'] = $en_translation['meta_description'] ?? '';
                $page['en_content'] = $en_translation['content'] ?? '';
                $page['en_excerpt'] = $en_translation['excerpt'] ?? '';
                $page['en_featured_image'] = $en_translation['featured_image'] ?? '';
                $page['en_og_title'] = $en_translation['og_title'] ?? '';
                $page['en_og_description'] = $en_translation['og_description'] ?? '';
                $page['en_og_image'] = $en_translation['og_image'] ?? '';
                $page['en_canonical_url'] = $en_translation['canonical_url'] ?? '';
            }
        } else {
            // إذا لم يكن جدول الترجمات موجود، املأ البيانات الإنجليزية فارغة
            foreach ($pages as &$page) {
                $page['en_title'] = '';
                $page['en_meta_title'] = '';
                $page['en_meta_description'] = '';
                $page['en_content'] = '';
                $page['en_excerpt'] = '';
                $page['en_featured_image'] = '';
                $page['en_og_title'] = '';
                $page['en_og_description'] = '';
                $page['en_og_image'] = '';
                $page['en_canonical_url'] = '';
            }
        }
    } else {
        $pages = [];
        error_log('Pages table does not exist');
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب الصفحات: ' . $e->getMessage();
    error_log('Fetch Pages Error: ' . $e->getMessage());
    $pages = [];
}

// جلب قائمة المستخدمين للإضافة
$users = [];
try {
    // التحقق من وجود جدول users
    $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
    if ($tableExists) {
        $stmt = $pdo->query("
            SELECT id, first_name, last_name, email
            FROM users
            ORDER BY first_name, last_name
        ");
        $users = $stmt->fetchAll();
    } else {
        // إذا لم يكن الجدول موجود، استخدم مصفوفة فارغة
        $users = [];
        error_log('Users table does not exist');
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب قائمة المستخدمين: ' . $e->getMessage();
    error_log('Fetch Users Error: ' . $e->getMessage());
    $users = [];
}

$page_title = 'إدارة الصفحات';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة الصفحات</h1>
                <p class="text-gray-600">إدارة صفحات الموقع ومحتواها</p>
            </div>
            <button onclick="openAddModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة صفحة جديدة
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
            تم إضافة الصفحة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث الصفحة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم حذف الصفحة بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['status_updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث حالة الصفحة بنجاح!
        </div>
    <?php endif; ?>
    
    <!-- Pages Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة الصفحات</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الصفحة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            منشئ الصفحة
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
                    <?php if (empty($pages)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد صفحات</p>
                                    <p class="text-sm">ابدأ بإضافة صفحة جديدة</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $page): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-file-alt text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($page['ar_title'] ?: $page['en_title'] ?: $page['slug'] ?: 'بدون عنوان'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($page['slug'] ?: ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($page['status'] === 'published'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            منشور
                                        </span>
                                    <?php elseif ($page['status'] === 'draft'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            مسودة
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            مؤرشف
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($page['first_name'] . ' ' . $page['last_name'] ?: 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $page['created_at'] ? date('Y/m/d', strtotime($page['created_at'])) : 'غير محدد'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($page)); ?>)" 
                                                class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="pages.php?toggle_status=<?php echo $page['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذه الصفحة؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="pages.php?delete=<?php echo $page['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذه الصفحة؟')">
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

<!-- Add Page Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">إضافة صفحة جديدة</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addPageForm" method="POST" action="pages.php">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رابط الصفحة</label>
                        <input type="text" name="slug" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="مثال: about-us">
                    </div>
                    
                    <!-- Template -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">قالب الصفحة</label>
                        <select name="template" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="default">افتراضي</option>
                            <option value="home">الرئيسية</option>
                            <option value="about">حول</option>
                            <option value="contact">اتصال</option>
                            <option value="services">خدمات</option>
                            <option value="projects">مشاريع</option>
                            <option value="blog">المدونة</option>
                        </select>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الصفحة</label>
                        <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="draft">مسودة</option>
                            <option value="published">منشور</option>
                            <option value="archived">مؤرشف</option>
                        </select>
                    </div>
                    
                    <!-- Created By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">منشئ الصفحة</label>
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
                        إضافة الصفحة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Page Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">تعديل الصفحة</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editPageForm" method="POST" action="pages.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="page_id" id="edit_page_id">
                
                <!-- Page Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">معلومات الصفحة</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="font-medium">الرابط:</span> 
                            <span id="edit_slug"></span>
                        </div>
                        <div>
                            <span class="font-medium">القالب:</span> 
                            <span id="edit_template"></span>
                        </div>
                        <div>
                            <span class="font-medium">الحالة:</span> 
                            <span id="edit_status"></span>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">ملخص الصفحة</label>
                            <textarea name="ar_excerpt" id="edit_ar_excerpt" rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="ملخص مختصر للصفحة..."></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">محتوى الصفحة</label>
                            <textarea name="ar_content" id="edit_ar_content" rows="8" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="محتوى الصفحة..."></textarea>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Page Excerpt</label>
                            <textarea name="en_excerpt" id="edit_en_excerpt" rows="3" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="Page excerpt..."></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Page Content</label>
                            <textarea name="en_content" id="edit_en_content" rows="8" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="Page content..."></textarea>
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

function openEditModal(pageData) {
    // تعبئة البيانات في النموذج
    document.getElementById('edit_page_id').value = pageData.id;
    document.getElementById('edit_slug').textContent = pageData.slug;
    document.getElementById('edit_template').textContent = pageData.template;
    document.getElementById('edit_status').textContent = pageData.status === 'published' ? 'منشور' : (pageData.status === 'draft' ? 'مسودة' : 'مؤرشف');
    document.getElementById('edit_created_by').textContent = (pageData.first_name || '') + ' ' + (pageData.last_name || '');
    
    // تعبئة المحتوى العربي
    document.getElementById('edit_ar_title').value = pageData.ar_title || '';
    document.getElementById('edit_ar_meta_title').value = pageData.ar_meta_title || '';
    document.getElementById('edit_ar_meta_description').value = pageData.ar_meta_description || '';
    document.getElementById('edit_ar_content').value = pageData.ar_content || '';
    document.getElementById('edit_ar_excerpt').value = pageData.ar_excerpt || '';
    document.getElementById('edit_ar_featured_image').value = pageData.ar_featured_image || '';
    document.getElementById('edit_ar_og_title').value = pageData.ar_og_title || '';
    document.getElementById('edit_ar_og_description').value = pageData.ar_og_description || '';
    document.getElementById('edit_ar_og_image').value = pageData.ar_og_image || '';
    document.getElementById('edit_ar_canonical_url').value = pageData.ar_canonical_url || '';
    
    // تعبئة المحتوى الإنجليزي
    document.getElementById('edit_en_title').value = pageData.en_title || '';
    document.getElementById('edit_en_meta_title').value = pageData.en_meta_title || '';
    document.getElementById('edit_en_meta_description').value = pageData.en_meta_description || '';
    document.getElementById('edit_en_content').value = pageData.en_content || '';
    document.getElementById('edit_en_excerpt').value = pageData.en_excerpt || '';
    document.getElementById('edit_en_featured_image').value = pageData.en_featured_image || '';
    document.getElementById('edit_en_og_title').value = pageData.en_og_title || '';
    document.getElementById('edit_en_og_description').value = pageData.en_og_description || '';
    document.getElementById('edit_en_og_image').value = pageData.en_og_image || '';
    document.getElementById('edit_en_canonical_url').value = pageData.en_canonical_url || '';
    
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
        // إضافة صفحة جديدة
        $slug = $_POST['slug'];
        $template = $_POST['template'];
        $status = $_POST['status'];
        $created_by = (int)$_POST['created_by'];
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO pages (slug, template, status, created_by, is_homepage, sort_order)
                VALUES (?, ?, ?, ?, 0, 0)
            ");
            $stmt->execute([$slug, $template, $status, $created_by]);
            $page_id = $pdo->lastInsertId();
            
            $message = 'تم إضافة الصفحة بنجاح';
            logAdminAction('create_page', 'pages', "إضافة صفحة جديدة: $slug");
            
            // إعادة التوجيه للقائمة
            header('Location: pages.php?created=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء إضافة الصفحة: ' . $e->getMessage();
            error_log('Create Page Error: ' . $e->getMessage());
        }
    } elseif ($action === 'edit') {
        // تحديث صفحة موجودة
        $page_id = (int)$_POST['page_id'];
        
        // المحتوى العربي
        $ar_title = $_POST['ar_title'] ?? '';
        $ar_meta_title = $_POST['ar_meta_title'] ?? '';
        $ar_meta_description = $_POST['ar_meta_description'] ?? '';
        $ar_content = $_POST['ar_content'] ?? '';
        $ar_excerpt = $_POST['ar_excerpt'] ?? '';
        $ar_featured_image = $_POST['ar_featured_image'] ?? '';
        $ar_og_title = $_POST['ar_og_title'] ?? '';
        $ar_og_description = $_POST['ar_og_description'] ?? '';
        $ar_og_image = $_POST['ar_og_image'] ?? '';
        $ar_canonical_url = $_POST['ar_canonical_url'] ?? '';
        
        // المحتوى الإنجليزي
        $en_title = $_POST['en_title'] ?? '';
        $en_meta_title = $_POST['en_meta_title'] ?? '';
        $en_meta_description = $_POST['en_meta_description'] ?? '';
        $en_content = $_POST['en_content'] ?? '';
        $en_excerpt = $_POST['en_excerpt'] ?? '';
        $en_featured_image = $_POST['en_featured_image'] ?? '';
        $en_og_title = $_POST['en_og_title'] ?? '';
        $en_og_description = $_POST['en_og_description'] ?? '';
        $en_og_image = $_POST['en_og_image'] ?? '';
        $en_canonical_url = $_POST['en_canonical_url'] ?? '';
        
        try {
            // التحقق من وجود جدول page_translations
            $tableExists = $pdo->query("SHOW TABLES LIKE 'page_translations'")->fetch();
            
            if ($tableExists) {
                // تحديث الترجمة العربية
                $stmt = $pdo->prepare("
                    INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description, content, excerpt, featured_image, og_title, og_description, og_image, canonical_url)
                    VALUES (?, 1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description),
                    content = VALUES(content), excerpt = VALUES(excerpt), featured_image = VALUES(featured_image),
                    og_title = VALUES(og_title), og_description = VALUES(og_description), og_image = VALUES(og_image), canonical_url = VALUES(canonical_url)
                ");
                $stmt->execute([$page_id, $ar_title, $ar_meta_title, $ar_meta_description, $ar_content, $ar_excerpt, $ar_featured_image, $ar_og_title, $ar_og_description, $ar_og_image, $ar_canonical_url]);
                
                // تحديث الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description, content, excerpt, featured_image, og_title, og_description, og_image, canonical_url)
                    VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), meta_title = VALUES(meta_title), meta_description = VALUES(meta_description),
                    content = VALUES(content), excerpt = VALUES(excerpt), featured_image = VALUES(featured_image),
                    og_title = VALUES(og_title), og_description = VALUES(og_description), og_image = VALUES(og_image), canonical_url = VALUES(canonical_url)
                ");
                $stmt->execute([$page_id, $en_title, $en_meta_title, $en_meta_description, $en_content, $en_excerpt, $en_featured_image, $en_og_title, $en_og_description, $en_og_image, $en_canonical_url]);
            }
            
            $message = 'تم تحديث الصفحة بنجاح';
            logAdminAction('update_page', 'pages', "تحديث صفحة رقم $page_id");
            
            // إعادة التوجيه للقائمة
            header('Location: pages.php?updated=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء تحديث الصفحة: ' . $e->getMessage();
            error_log('Update Page Error: ' . $e->getMessage());
        }
    }
}
?>

<?php include 'includes/footer.php'; ?> 