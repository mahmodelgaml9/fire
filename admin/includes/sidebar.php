<?php
// الحصول على الإحصائيات السريعة
$quick_stats = getQuickStats();

// تحديد الصفحة الحالية
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// قائمة التنقل
$nav_items = [
    [
        'id' => 'dashboard',
        'title' => 'لوحة التحكم',
        'icon' => 'fas fa-tachometer-alt',
        'url' => 'dashboard.php',
        'permission' => 'dashboard'
    ],
    [
        'id' => 'pages',
        'title' => 'إدارة الصفحات',
        'icon' => 'fas fa-file-alt',
        'url' => 'pages.php',
        'permission' => 'pages',
        'badge' => $quick_stats['pages'] ?? 0
    ],
    [
        'id' => 'sections',
        'title' => 'إدارة السكاشنات',
        'icon' => 'fas fa-th-large',
        'url' => 'sections.php',
        'permission' => 'sections'
    ],
    [
        'id' => 'services',
        'title' => 'إدارة الخدمات',
        'icon' => 'fas fa-cogs',
        'url' => 'services.php',
        'permission' => 'services',
        'badge' => $quick_stats['services'] ?? 0
    ],
    [
        'id' => 'projects',
        'title' => 'إدارة المشاريع',
        'icon' => 'fas fa-project-diagram',
        'url' => 'projects.php',
        'permission' => 'projects',
        'badge' => $quick_stats['projects'] ?? 0
    ],
    [
        'id' => 'blog',
        'title' => 'إدارة المدونة',
        'icon' => 'fas fa-blog',
        'url' => 'blog.php',
        'permission' => 'blog',
        'badge' => $quick_stats['blog_posts'] ?? 0
    ],
    [
        'id' => 'testimonials',
        'title' => 'إدارة الشهادات',
        'icon' => 'fas fa-quote-right',
        'url' => 'testimonials.php',
        'permission' => 'testimonials',
        'badge' => $quick_stats['testimonials'] ?? 0
    ],
    [
        'id' => 'locations',
        'title' => 'إدارة المناطق',
        'icon' => 'fas fa-map-marker-alt',
        'url' => 'locations.php',
        'permission' => 'locations',
        'badge' => $quick_stats['locations'] ?? 0
    ],
    [
        'id' => 'media',
        'title' => 'مكتبة الوسائط',
        'icon' => 'fas fa-images',
        'url' => 'media.php',
        'permission' => 'media'
    ],
    [
        'id' => 'users',
        'title' => 'إدارة المستخدمين',
        'icon' => 'fas fa-users',
        'url' => 'users.php',
        'permission' => 'users'
    ],
    [
        'id' => 'settings',
        'title' => 'إعدادات الموقع',
        'icon' => 'fas fa-cog',
        'url' => 'settings.php',
        'permission' => 'settings'
    ],
    [
        'id' => 'reports',
        'title' => 'التقارير والإحصائيات',
        'icon' => 'fas fa-chart-bar',
        'url' => 'reports.php',
        'permission' => 'reports'
    ]
];
?>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg transform translate-x-0 sidebar-transition z-40">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-fire text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">لوحة التحكم</h2>
                    <p class="text-xs text-gray-500">Sphinx Fire</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <?php foreach ($nav_items as $item): ?>
                <?php if (checkPermission($item['permission'])): ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo $current_page === $item['id'] ? 'bg-primary-100 text-primary-700 border-r-4 border-primary-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <i class="<?php echo $item['icon']; ?> text-lg ml-3 <?php echo $current_page === $item['id'] ? 'text-primary-600' : 'text-gray-400'; ?>"></i>
                        <span class="flex-1"><?php echo $item['title']; ?></span>
                        <?php if (isset($item['badge']) && $item['badge'] > 0): ?>
                            <span class="bg-primary-100 text-primary-700 text-xs font-medium px-2 py-1 rounded-full">
                                <?php echo $item['badge']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        
        <!-- Quick Stats -->
        <div class="p-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">إحصائيات سريعة</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-blue-600 text-lg ml-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">الصفحات</p>
                            <p class="text-lg font-bold text-blue-600"><?php echo $quick_stats['pages'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-cogs text-green-600 text-lg ml-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">الخدمات</p>
                            <p class="text-lg font-bold text-green-600"><?php echo $quick_stats['services'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-project-diagram text-purple-600 text-lg ml-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">المشاريع</p>
                            <p class="text-lg font-bold text-purple-600"><?php echo $quick_stats['projects'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-blog text-orange-600 text-lg ml-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">المقالات</p>
                            <p class="text-lg font-bold text-orange-600"><?php echo $quick_stats['blog_posts'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Info -->
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-primary-600 rounded-full flex items-center justify-center ml-3">
                    <span class="text-white font-medium text-sm">
                        <?php echo strtoupper(substr($current_user['first_name'], 0, 1) . substr($current_user['last_name'], 0, 1)); ?>
                    </span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></p>
                    <p class="text-xs text-gray-500"><?php echo htmlspecialchars($current_user['role_name']); ?></p>
                </div>
            </div>
            <div class="mt-3 flex space-x-2 space-x-reverse">
                <a href="profile.php" class="flex-1 bg-white text-primary-600 text-xs font-medium py-2 px-3 rounded border border-primary-200 hover:bg-primary-50 transition-colors duration-200 text-center">
                    <i class="fas fa-user ml-1"></i>
                    الملف
                </a>
                <a href="logout.php" class="flex-1 bg-red-50 text-red-600 text-xs font-medium py-2 px-3 rounded border border-red-200 hover:bg-red-100 transition-colors duration-200 text-center">
                    <i class="fas fa-sign-out-alt ml-1"></i>
                    خروج
                </a>
            </div>
        </div>
    </div>
</aside>

<!-- Main Content Wrapper -->
<div id="main-content" class="mr-64 content-transition">
    <div class="pt-16">
        <!-- Page Content will be inserted here --> 