<?php
require_once '../config.php';
require_once 'includes/auth.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف المقال
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $message = 'تم حذف المقال بنجاح';
        logAdminAction('delete_blog_post', 'blog_posts', "حذف مقال رقم $post_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف المقال';
        error_log('Delete Blog Post Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة المقال
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $post_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE blog_posts SET status = CASE WHEN status = 'published' THEN 'draft' ELSE 'published' END WHERE id = ?");
        $stmt->execute([$post_id]);
        $message = 'تم تحديث حالة المقال بنجاح';
        logAdminAction('toggle_blog_post_status', 'blog_posts', "تغيير حالة مقال رقم $post_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة المقال';
        error_log('Toggle Blog Post Status Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة التميز
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $post_id = (int)$_GET['toggle_featured'];
    try {
        $stmt = $pdo->prepare("UPDATE blog_posts SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$post_id]);
        $message = 'تم تحديث حالة التميز بنجاح';
        logAdminAction('toggle_blog_post_featured', 'blog_posts', "تغيير حالة التميز لمقال رقم $post_id");
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة التميز';
        error_log('Toggle Blog Post Featured Error: ' . $e->getMessage());
    }
}

// معالجة إضافة/تعديل المقال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST request received: ' . print_r($_POST, true));
    
    $post_id = $_POST['post_id'] ?? null;
    $slug = $_POST['slug'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $reading_time = $_POST['reading_time'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // المحتوى العربي
    $ar_title = $_POST['ar_title'] ?? '';
    $ar_excerpt = $_POST['ar_excerpt'] ?? '';
    $ar_content = $_POST['ar_content'] ?? '';
    $ar_meta_title = $_POST['ar_meta_title'] ?? '';
    $ar_meta_description = $_POST['ar_meta_description'] ?? '';
    $ar_tags = $_POST['ar_tags'] ?? '[]';
    
    // المحتوى الإنجليزي
    $en_title = $_POST['en_title'] ?? '';
    $en_excerpt = $_POST['en_excerpt'] ?? '';
    $en_content = $_POST['en_content'] ?? '';
    $en_meta_title = $_POST['en_meta_title'] ?? '';
    $en_meta_description = $_POST['en_meta_description'] ?? '';
    $en_tags = $_POST['en_tags'] ?? '[]';
    
    // معالجة الـ tags - تأكد من أنها JSON صحيح
    if (!empty($ar_tags) && !is_array($ar_tags)) {
        $ar_tags = json_encode(explode(',', str_replace(['[', ']', '"'], '', $ar_tags)));
    }
    if (empty($ar_tags) || $ar_tags === '[]') {
        $ar_tags = '[]';
    }
    
    if (!empty($en_tags) && !is_array($en_tags)) {
        $en_tags = json_encode(explode(',', str_replace(['[', ']', '"'], '', $en_tags)));
    }
    if (empty($en_tags) || $en_tags === '[]') {
        $en_tags = '[]';
    }
    
    error_log('Processing post_id: ' . $post_id);
    error_log('Arabic title: ' . $ar_title);
    error_log('English title: ' . $en_title);
    error_log('Arabic content length: ' . strlen($ar_content));
    error_log('English content length: ' . strlen($en_content));
    error_log('Arabic tags: ' . $ar_tags);
    error_log('English tags: ' . $en_tags);
    
    try {
        $pdo->beginTransaction();
        
        // معالجة الصورة المميزة
        $featured_image = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
            $featured_image = 'blog_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $featured_image;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $upload_path)) {
                // تحويل إلى WebP إذا كان ممكناً
                if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png'])) {
                    $webp_path = $upload_dir . 'blog_' . time() . '.webp';
                    convertToWebP($upload_path, $webp_path);
                    $featured_image = 'blog_' . time() . '.webp';
                }
            }
        }
        
        if ($post_id) {
            error_log('Updating existing post: ' . $post_id);
            
            // تحديث مقال موجود
            $stmt = $pdo->prepare("
                UPDATE blog_posts SET 
                    slug = ?, category_id = ?, reading_time = ?, status = ?, 
                    is_featured = ?, featured_image = COALESCE(?, featured_image),
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([$slug, $category_id, $reading_time, $status, $is_featured, $featured_image, $post_id]);
            
            // تحديث الترجمات العربية
            $stmt = $pdo->prepare("
                INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, meta_title, meta_description, tags)
                VALUES (?, 1, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content),
                meta_title = VALUES(meta_title), meta_description = VALUES(meta_description), tags = VALUES(tags)
            ");
            $stmt->execute([$post_id, $ar_title, $ar_excerpt, $ar_content, $ar_meta_title, $ar_meta_description, $ar_tags]);
            
            // تحديث الترجمات الإنجليزية
            $stmt = $pdo->prepare("
                INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, meta_title, meta_description, tags)
                VALUES (?, 2, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content),
                meta_title = VALUES(meta_title), meta_description = VALUES(meta_description), tags = VALUES(tags)
            ");
            $stmt->execute([$post_id, $en_title, $en_excerpt, $en_content, $en_meta_title, $en_meta_description, $en_tags]);
            
            $message = 'تم تحديث المقال بنجاح';
            logAdminAction('update_blog_post', 'blog_posts', "تحديث مقال رقم $post_id");
        } else {
            error_log('Creating new post');
            
            // إضافة مقال جديد
            $stmt = $pdo->prepare("
                INSERT INTO blog_posts (slug, category_id, author_id, reading_time, status, is_featured, featured_image, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
            ");
            $stmt->execute([$slug, $category_id, $current_user['id'], $reading_time, $status, $is_featured, $featured_image]);
            $post_id = $pdo->lastInsertId();
            
            // إضافة الترجمات العربية
            $stmt = $pdo->prepare("
                INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, meta_title, meta_description, tags)
                VALUES (?, 1, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$post_id, $ar_title, $ar_excerpt, $ar_content, $ar_meta_title, $ar_meta_description, $ar_tags]);
            
            // إضافة الترجمات الإنجليزية
            $stmt = $pdo->prepare("
                INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, meta_title, meta_description, tags)
                VALUES (?, 2, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$post_id, $en_title, $en_excerpt, $en_content, $en_meta_title, $en_meta_description, $en_tags]);
            
            $message = 'تم إضافة المقال بنجاح';
            logAdminAction('create_blog_post', 'blog_posts', "إضافة مقال جديد رقم $post_id");
        }
        
        $pdo->commit();
        error_log('Post saved successfully');
        
        // إعادة توجيه لتجنب إعادة إرسال النموذج
        header("Location: blog.php?t=" . time());
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'حدث خطأ أثناء حفظ المقال: ' . $e->getMessage();
        error_log('Save Blog Post Error: ' . $e->getMessage());
    }
}

// جلب قائمة المقالات مع الترجمات
$posts = [];
try {
    // التحقق من وجود الجداول
    $tables_exist = true;
    $required_tables = ['blog_posts', 'blog_categories', 'blog_post_translations', 'users'];
    
    foreach ($required_tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if (!$stmt->fetch()) {
                $tables_exist = false;
                break;
            }
        } catch (PDOException $e) {
            $tables_exist = false;
            break;
        }
    }
    
    if ($tables_exist) {
        // استعلام محسن لجلب المقالات
        $stmt = $pdo->query("
            SELECT 
                bp.id,
                bp.slug,
                bp.category_id,
                bp.author_id,
                bp.featured_image,
                bp.reading_time,
                bp.status,
                bp.is_featured,
                bp.views_count,
                bp.published_at,
                bp.created_at,
                bp.updated_at,
                CONCAT(u.first_name, ' ', u.last_name) as created_by_name,
                bc.slug as category_slug,
                bpt.title as ar_title,
                bpt.excerpt as ar_excerpt,
                bpt.content as ar_content,
                bpt.meta_title as ar_meta_title,
                bpt.meta_description as ar_meta_description,
                bpt.tags as ar_tags
            FROM blog_posts bp
            LEFT JOIN users u ON bp.author_id = u.id
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.post_id AND bpt.language_id = 1
            ORDER BY bp.created_at DESC
        ");
        $posts = $stmt->fetchAll();
        
        // جلب الترجمات الإنجليزية لكل مقال
        foreach ($posts as &$post) {
            try {
                $stmt = $pdo->prepare("
                    SELECT title, excerpt, content, meta_title, meta_description, tags
                    FROM blog_post_translations
                    WHERE post_id = ? AND language_id = 2
                ");
                $stmt->execute([$post['id']]);
                $en_translation = $stmt->fetch();
                
                $post['en_title'] = $en_translation['title'] ?? '';
                $post['en_excerpt'] = $en_translation['excerpt'] ?? '';
                $post['en_content'] = $en_translation['content'] ?? '';
                $post['en_featured_image'] = $post['featured_image'] ?? '';
                $post['en_meta_title'] = $en_translation['meta_title'] ?? '';
                $post['en_meta_description'] = $en_translation['meta_description'] ?? '';
                $post['en_tags'] = $en_translation['tags'] ?? '';
            } catch (PDOException $e) {
                // إذا لم توجد ترجمة إنجليزية، استخدم قيم فارغة
                $post['en_title'] = '';
                $post['en_excerpt'] = '';
                $post['en_content'] = '';
                $post['en_featured_image'] = $post['featured_image'] ?? '';
                $post['en_meta_title'] = '';
                $post['en_meta_description'] = '';
                $post['en_tags'] = '';
            }
        }
    } else {
        $error = 'بعض الجداول المطلوبة غير موجودة في قاعدة البيانات';
        error_log('Blog Tables Missing: ' . implode(', ', $required_tables));
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب المقالات: ' . $e->getMessage();
    error_log('Fetch Blog Posts Error: ' . $e->getMessage());
}

// جلب فئات المدونة للفلتر
$categories = [];
try {
    $stmt = $pdo->query("SELECT id, slug FROM blog_categories WHERE is_active = 1 ORDER BY sort_order");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Fetch Categories Error: ' . $e->getMessage());
}

$page_title = 'إدارة المدونة';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة المدونة</h1>
                <p class="text-gray-600">إدارة مقالات المدونة وإعداداتها</p>
            </div>
            <button onclick="openAddModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة مقال جديد
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الفلاتر</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">فئة المقال</label>
                    <select id="category_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الفئات</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['slug']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select id="status_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الحالات</option>
                        <option value="published">منشور</option>
                        <option value="draft">مسودة</option>
                        <option value="archived">مؤرشف</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التميز</label>
                    <select id="featured_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع المقالات</option>
                        <option value="1">مميز</option>
                        <option value="0">غير مميز</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Posts Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة المقالات</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المقال
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الفئة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التميز
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الكاتب
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
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-blog text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد مقالات</p>
                                    <p class="text-sm">ابدأ بإضافة مقال جديد</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <tr class="hover:bg-gray-50" data-category="<?php echo $post['category_id']; ?>" data-status="<?php echo $post['status']; ?>" data-featured="<?php echo $post['is_featured']; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                                <i class="fas fa-blog text-purple-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($post['ar_title'] ?? $post['en_title'] ?? 'بدون عنوان'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($post['slug'] ?? ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($post['category_slug'] ?? 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($post['status'] === 'published'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            منشور
                                        </span>
                                    <?php elseif ($post['status'] === 'draft'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            مسودة
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            مؤرشف
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($post['is_featured'] ?? false): ?>
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
                                    <?php echo htmlspecialchars($post['created_by_name'] ?? 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('Y/m/d', strtotime($post['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($post)); ?>)" 
                                                class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="blog.php?toggle_status=<?php echo $post['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذا المقال؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="blog.php?toggle_featured=<?php echo $post['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-purple-600 hover:text-purple-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة التميز لهذا المقال؟')">
                                            <i class="fas fa-star"></i>
                                        </a>
                                        <a href="blog.php?delete=<?php echo $post['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذا المقال؟')">
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

<!-- Add/Edit Modal -->
<div id="blogModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">إضافة مقال جديد</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="blogForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="post_id" name="post_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Arabic Content -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-700 border-b pb-2">المحتوى العربي</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان العربي</label>
                            <input type="text" id="ar_title" name="ar_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الملخص العربي</label>
                            <textarea id="ar_excerpt" name="ar_excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى العربي</label>
                            <textarea id="ar_content" name="ar_content" rows="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title العربي</label>
                            <input type="text" id="ar_meta_title" name="ar_meta_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description العربي</label>
                            <textarea id="ar_meta_description" name="ar_meta_description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الوسوم العربي (JSON)</label>
                            <input type="text" id="ar_tags" name="ar_tags" placeholder='["tag1", "tag2"]' class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    
                    <!-- English Content -->
                    <div class="space-y-4">
                        <h4 class="text-md font-semibold text-gray-700 border-b pb-2">المحتوى الإنجليزي</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان الإنجليزي</label>
                            <input type="text" id="en_title" name="en_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الملخص الإنجليزي</label>
                            <textarea id="en_excerpt" name="en_excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى الإنجليزي</label>
                            <textarea id="en_content" name="en_content" rows="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title الإنجليزي</label>
                            <input type="text" id="en_meta_title" name="en_meta_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description الإنجليزي</label>
                            <textarea id="en_meta_description" name="en_meta_description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الوسوم الإنجليزي (JSON)</label>
                            <input type="text" id="en_tags" name="en_tags" placeholder='["tag1", "tag2"]' class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
                
                <!-- General Settings -->
                <div class="mt-6 space-y-4">
                    <h4 class="text-md font-semibold text-gray-700 border-b pb-2">الإعدادات العامة</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" id="slug" name="slug" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                            <select id="category_id" name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                <option value="">اختر الفئة</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['slug']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">وقت القراءة (دقائق)</label>
                            <input type="number" id="reading_time" name="reading_time" min="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                            <select id="status" name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="draft">مسودة</option>
                                <option value="published">منشور</option>
                                <option value="archived">مؤرشف</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الصورة المميزة</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <label class="flex items-center">
                                <input type="checkbox" id="is_featured" name="is_featured" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="mr-2 text-sm font-medium text-gray-700">مميز</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 space-x-reverse mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        حفظ المقال
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>

<script>
// Initialize TinyMCE
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing TinyMCE...');
    
    // Initialize Arabic content editor
    tinymce.init({
        selector: '#ar_content',
        directionality: 'rtl',
        language: 'ar',
        height: 400,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | preview',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('change', function() {
                console.log('Arabic content changed');
                editor.save();
            });
            editor.on('init', function() {
                console.log('Arabic editor initialized');
            });
        }
    });
    
    // Initialize English content editor
    tinymce.init({
        selector: '#en_content',
        directionality: 'ltr',
        language: 'en',
        height: 400,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | preview',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('change', function() {
                console.log('English content changed');
                editor.save();
            });
            editor.on('init', function() {
                console.log('English editor initialized');
            });
        }
    });
});

// Modal functions
function openAddModal() {
    console.log('Opening add modal');
    
    document.getElementById('modalTitle').textContent = 'إضافة مقال جديد';
    document.getElementById('blogForm').reset();
    document.getElementById('post_id').value = '';
    
    // Clear TinyMCE content safely
    setTimeout(() => {
        if (typeof tinymce !== 'undefined') {
            if (tinymce.get('ar_content')) {
                tinymce.get('ar_content').setContent('');
                console.log('Cleared Arabic content');
            }
            if (tinymce.get('en_content')) {
                tinymce.get('en_content').setContent('');
                console.log('Cleared English content');
            }
        }
    }, 500);
    
    document.getElementById('blogModal').classList.remove('hidden');
}

function openEditModal(post) {
    console.log('Opening edit modal for post:', post);
    
    document.getElementById('modalTitle').textContent = 'تعديل المقال';
    document.getElementById('post_id').value = post.id;
    document.getElementById('slug').value = post.slug || '';
    document.getElementById('category_id').value = post.category_id || '';
    document.getElementById('reading_time').value = post.reading_time || '';
    document.getElementById('status').value = post.status || 'draft';
    document.getElementById('is_featured').checked = post.is_featured == 1;
    
    // Arabic content
    document.getElementById('ar_title').value = post.ar_title || '';
    document.getElementById('ar_excerpt').value = post.ar_excerpt || '';
    document.getElementById('ar_meta_title').value = post.ar_meta_title || '';
    document.getElementById('ar_meta_description').value = post.ar_meta_description || '';
    document.getElementById('ar_tags').value = post.ar_tags || '';
    
    // English content
    document.getElementById('en_title').value = post.en_title || '';
    document.getElementById('en_excerpt').value = post.en_excerpt || '';
    document.getElementById('en_meta_title').value = post.en_meta_title || '';
    document.getElementById('en_meta_description').value = post.en_meta_description || '';
    document.getElementById('en_tags').value = post.en_tags || '';
    
    // Update TinyMCE content safely
    setTimeout(() => {
        if (typeof tinymce !== 'undefined') {
            if (tinymce.get('ar_content')) {
                tinymce.get('ar_content').setContent(post.ar_content || '');
                console.log('Set Arabic content:', post.ar_content);
            }
            if (tinymce.get('en_content')) {
                tinymce.get('en_content').setContent(post.en_content || '');
                console.log('Set English content:', post.en_content);
            }
        }
    }, 500);
    
    document.getElementById('blogModal').classList.remove('hidden');
}

function closeModal() {
    console.log('Closing modal');
    document.getElementById('blogModal').classList.add('hidden');
}

// Form submission
document.getElementById('blogForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Form submitted');
    
    // Add content from TinyMCE
    if (typeof tinymce !== 'undefined') {
        if (tinymce.get('ar_content')) {
            const arContent = tinymce.get('ar_content').getContent();
            document.getElementById('ar_content').value = arContent;
            console.log('Arabic content:', arContent);
        }
        if (tinymce.get('en_content')) {
            const enContent = tinymce.get('en_content').getContent();
            document.getElementById('en_content').value = enContent;
            console.log('English content:', enContent);
        }
    }
    
    // Submit form directly
    console.log('Submitting form...');
    this.submit();
});

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category_filter');
    const statusFilter = document.getElementById('status_filter');
    const featuredFilter = document.getElementById('featured_filter');
    const tableRows = document.querySelectorAll('tbody tr[data-category]');

    function filterTable() {
        const categoryValue = categoryFilter.value;
        const statusValue = statusFilter.value;
        const featuredValue = featuredFilter.value;

        tableRows.forEach(row => {
            const category = row.getAttribute('data-category');
            const status = row.getAttribute('data-status');
            const featured = row.getAttribute('data-featured');

            let showRow = true;

            if (categoryValue && category !== categoryValue) showRow = false;
            if (statusValue && status !== statusValue) showRow = false;
            if (featuredValue !== '' && featured !== featuredValue) showRow = false;

            row.style.display = showRow ? '' : 'none';
        });
    }

    categoryFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
    featuredFilter.addEventListener('change', filterTable);
});
</script>

<?php include 'includes/footer.php'; ?> 