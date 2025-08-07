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

// جلب قائمة المقالات
$posts = [];
try {
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
        LIMIT 5
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
}

$page_title = 'اختبار المدونة';
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
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">اختبار المدونة</h1>
                    <p class="text-gray-600">اختبار Modal والوظائف</p>
                </div>
                <button onclick="openAddModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة مقال جديد
                </button>
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
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($posts)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="fas fa-blog text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">لا توجد مقالات</p>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($post)); ?>)" 
                                                class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-lg transition-colors">
                                            <i class="fas fa-edit ml-1"></i>
                                            تعديل
                                        </button>
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
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">إضافة مقال جديد</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="blogForm" method="POST">
                    <input type="hidden" id="post_id" name="post_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Arabic Content -->
                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-gray-700 border-b pb-2">المحتوى العربي</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان العربي</label>
                                <input type="text" id="ar_title" name="ar_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الملخص العربي</label>
                                <textarea id="ar_excerpt" name="ar_excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                        
                        <!-- English Content -->
                        <div class="space-y-4">
                            <h4 class="text-md font-semibold text-gray-700 border-b pb-2">المحتوى الإنجليزي</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان الإنجليزي</label>
                                <input type="text" id="en_title" name="en_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">الملخص الإنجليزي</label>
                                <textarea id="en_excerpt" name="en_excerpt" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 space-x-reverse mt-6">
                        <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddModal() {
            console.log('Opening add modal');
            document.getElementById('modalTitle').textContent = 'إضافة مقال جديد';
            document.getElementById('post_id').value = '';
            document.getElementById('blogForm').reset();
            document.getElementById('blogModal').classList.remove('hidden');
        }

        function openEditModal(post) {
            console.log('Opening edit modal for post:', post);
            document.getElementById('modalTitle').textContent = 'تعديل المقال';
            document.getElementById('post_id').value = post.id;
            document.getElementById('ar_title').value = post.ar_title || '';
            document.getElementById('ar_excerpt').value = post.ar_excerpt || '';
            document.getElementById('en_title').value = post.en_title || '';
            document.getElementById('en_excerpt').value = post.en_excerpt || '';
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
            
            const formData = new FormData(this);
            const postId = document.getElementById('post_id').value;
            
            if (postId) {
                alert('تم تحديث المقال بنجاح!');
            } else {
                alert('تم إضافة المقال بنجاح!');
            }
            
            closeModal();
        });

        // Test on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, modal functions are ready');
        });
    </script>
</body>
</html> 