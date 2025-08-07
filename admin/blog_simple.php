<?php
require_once '../config.php';

// إنشاء جلسة تجريبية
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_user_id'] = 1;
$_SESSION['admin_username'] = 'fireadmin';
$_SESSION['admin_email'] = 'admin@sphinxfire.com';
$_SESSION['admin_first_name'] = 'Admin';
$_SESSION['admin_last_name'] = 'User';
$_SESSION['admin_role_id'] = 1;
$_SESSION['admin_role_name'] = 'Super Admin';
$_SESSION['admin_permissions'] = ['all' => true];

$current_user = [
    'id' => 1,
    'username' => 'fireadmin',
    'email' => 'admin@sphinxfire.com',
    'first_name' => 'Admin',
    'last_name' => 'User',
    'role_id' => 1,
    'role_name' => 'Super Admin',
    'permissions' => ['all' => true]
];

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// جلب قائمة المقالات مع الترجمات
$posts = [];
try {
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

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Sphinx Fire</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="p-6">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة المدونة</h1>
                    <p class="text-gray-600">إدارة مقالات المدونة وإعداداتها</p>
                </div>
                <button onclick="alert('إضافة مقال جديد')" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
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
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-blog text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">لا توجد مقالات</p>
                                        <p class="text-sm">ابدأ بإضافة مقال جديد</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <tr class="hover:bg-gray-50">
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
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 