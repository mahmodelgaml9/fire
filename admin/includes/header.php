<?php
require_once '../config.php';
require_once 'auth.php';

// التحقق من تسجيل الدخول
checkAdminLogin();

// الحصول على معلومات المستخدم
$current_user = getCurrentAdminUser();

// الحصول على الإشعارات
$notifications = getUserNotifications($current_user['id'], 5);
$unread_notifications = array_filter($notifications, function($n) { return !$n['is_read']; });
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'لوحة التحكم'; ?> - Sphinx Fire</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css">
    
    <style>
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
        .content-transition {
            transition: margin-right 0.3s ease-in-out;
        }
        .notification-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 right-0 left-0 z-50">
        <div class="flex items-center justify-between px-6 py-4">
            <!-- Logo and Menu Toggle -->
            <div class="flex items-center">
                <button id="sidebar-toggle" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="mr-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-fire text-white text-sm"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900">Sphinx Fire</h1>
                            <p class="text-xs text-gray-500">لوحة التحكم</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side -->
            <div class="flex items-center space-x-4 space-x-reverse">
                <!-- Search -->
                <div class="relative">
                    <input type="text" placeholder="البحث..." class="w-64 px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Notifications -->
                <div class="relative">
                    <button id="notifications-toggle" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if (count($unread_notifications) > 0): ?>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center notification-badge">
                                <?php echo count($unread_notifications); ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    
                    <!-- Notifications Dropdown -->
                    <div id="notifications-dropdown" class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">الإشعارات</h3>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <?php if (empty($notifications)): ?>
                                <div class="p-4 text-center text-gray-500">
                                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                    <p>لا توجد إشعارات جديدة</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 <?php echo !$notification['is_read'] ? 'bg-blue-50' : ''; ?>">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <?php
                                                $icon_class = 'fas fa-info-circle text-blue-500';
                                                switch ($notification['type']) {
                                                    case 'success':
                                                        $icon_class = 'fas fa-check-circle text-green-500';
                                                        break;
                                                    case 'warning':
                                                        $icon_class = 'fas fa-exclamation-triangle text-yellow-500';
                                                        break;
                                                    case 'error':
                                                        $icon_class = 'fas fa-times-circle text-red-500';
                                                        break;
                                                }
                                                ?>
                                                <i class="<?php echo $icon_class; ?> text-lg"></i>
                                            </div>
                                            <div class="mr-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($notification['title']); ?></p>
                                                <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <p class="text-xs text-gray-400 mt-2"><?php echo formatDate($notification['created_at'], 'Y-m-d H:i'); ?></p>
                                            </div>
                                            <?php if (!$notification['is_read']): ?>
                                                <div class="flex-shrink-0">
                                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 border-t border-gray-200">
                            <a href="notifications.php" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                عرض جميع الإشعارات
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="relative">
                    <button id="user-menu-toggle" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center mr-2">
                            <span class="text-white font-medium text-sm">
                                <?php echo strtoupper(substr($current_user['first_name'], 0, 1) . substr($current_user['last_name'], 0, 1)); ?>
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($current_user['first_name'] . ' ' . $current_user['last_name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($current_user['role_name']); ?></p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 mr-2"></i>
                    </button>
                    
                    <!-- User Dropdown -->
                    <div id="user-dropdown" class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                        <div class="py-1">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                <i class="fas fa-user ml-2"></i>
                                الملف الشخصي
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                <i class="fas fa-cog ml-2"></i>
                                الإعدادات
                            </a>
                            <hr class="my-1">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                <i class="fas fa-sign-out-alt ml-2"></i>
                                تسجيل الخروج
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script>
        // Toggle Sidebar
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('main-content');
            
            sidebar.classList.toggle('-translate-x-full');
            content.classList.toggle('mr-64');
        });
        
        // Toggle Notifications
        document.getElementById('notifications-toggle').addEventListener('click', function() {
            const dropdown = document.getElementById('notifications-dropdown');
            dropdown.classList.toggle('hidden');
        });
        
        // Toggle User Menu
        document.getElementById('user-menu-toggle').addEventListener('click', function() {
            const dropdown = document.getElementById('user-dropdown');
            dropdown.classList.toggle('hidden');
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const notificationsToggle = document.getElementById('notifications-toggle');
            const notificationsDropdown = document.getElementById('notifications-dropdown');
            const userMenuToggle = document.getElementById('user-menu-toggle');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (!notificationsToggle.contains(event.target) && !notificationsDropdown.contains(event.target)) {
                notificationsDropdown.classList.add('hidden');
            }
            
            if (!userMenuToggle.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script> 