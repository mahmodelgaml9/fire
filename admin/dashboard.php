<?php
require_once '../config.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
checkAdminLogin();

// الحصول على معلومات المستخدم
$current_user = getCurrentAdminUser();

// الحصول على الإحصائيات
$quick_stats = getQuickStats();
$recent_activities = getRecentActivities(5);

// إحصائيات افتراضية
$active_users = 3;
$today_actions = 25;
$unread_notifications = 2;

// تسجيل زيارة لوحة التحكم
logAdminActivity('dashboard_visit');

$page_title = 'لوحة التحكم';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">مرحباً، <?php echo htmlspecialchars($current_user['first_name']); ?>!</h1>
        <p class="text-gray-600">مرحباً بك في لوحة تحكم Sphinx Fire</p>
    </div>
    
    <!-- Display Messages -->
    <?php displayMessages(); ?>
    
    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pages Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">الصفحات</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $quick_stats['pages'] ?? 0; ?></p>
                </div>
                <div class="text-left">
                    <span class="text-green-600 text-sm font-medium">+12%</span>
                </div>
            </div>
        </div>
        
        <!-- Services Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cogs text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">الخدمات</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $quick_stats['services'] ?? 0; ?></p>
                </div>
                <div class="text-left">
                    <span class="text-green-600 text-sm font-medium">+8%</span>
                </div>
            </div>
        </div>
        
        <!-- Projects Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">المشاريع</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $quick_stats['projects'] ?? 0; ?></p>
                </div>
                <div class="text-left">
                    <span class="text-green-600 text-sm font-medium">+15%</span>
                </div>
            </div>
        </div>
        
        <!-- Blog Posts Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-blog text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mr-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">المقالات</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $quick_stats['blog_posts'] ?? 0; ?></p>
                </div>
                <div class="text-left">
                    <span class="text-green-600 text-sm font-medium">+5%</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Activity Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">نشاط النظام</h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option>آخر 7 أيام</option>
                    <option>آخر 30 يوم</option>
                    <option>آخر 3 أشهر</option>
                </select>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
        
        <!-- System Health -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">صحة النظام</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">استخدام قاعدة البيانات</span>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 ml-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">65%</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">استخدام الذاكرة</span>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 ml-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">45%</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">مساحة التخزين</span>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 ml-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">78%</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">سرعة الاستجابة</span>
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2 ml-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900">92%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activities -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">آخر النشاطات</h3>
                <a href="logs.php" class="text-sm text-primary-600 hover:text-primary-700">عرض الكل</a>
            </div>
            
            <div class="space-y-4">
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">لا توجد نشاطات حديثة</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">إجراءات سريعة</h3>
            
            <div class="space-y-3">
                <a href="pages.php?action=create" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-plus text-blue-600 ml-3"></i>
                    <span class="text-sm font-medium text-blue-700">إضافة صفحة جديدة</span>
                </a>
                
                <a href="services.php?action=create" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <i class="fas fa-cogs text-green-600 ml-3"></i>
                    <span class="text-sm font-medium text-green-700">إضافة خدمة جديدة</span>
                </a>
                
                <a href="blog.php?action=create" class="flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-200">
                    <i class="fas fa-edit text-orange-600 ml-3"></i>
                    <span class="text-sm font-medium text-orange-700">كتابة مقال جديد</span>
                </a>
                
                <a href="media.php" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                    <i class="fas fa-upload text-purple-600 ml-3"></i>
                    <span class="text-sm font-medium text-purple-700">رفع ملفات</span>
                </a>
                
                <a href="settings.php" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <i class="fas fa-cog text-gray-600 ml-3"></i>
                    <span class="text-sm font-medium text-gray-700">إعدادات الموقع</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <!-- System Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">إحصائيات النظام</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">المستخدمين النشطين</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo $active_users; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">العمليات اليوم</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo $today_actions; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">الإشعارات الجديدة</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo $unread_notifications; ?></span>
                </div>
            </div>
        </div>
        
        <!-- Content Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">إحصائيات المحتوى</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">الشهادات</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo $quick_stats['testimonials'] ?? 0; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">المناطق</span>
                    <span class="text-sm font-medium text-gray-900"><?php echo $quick_stats['locations'] ?? 0; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">الوسائط</span>
                    <span class="text-sm font-medium text-gray-900">0</span>
                </div>
            </div>
        </div>
        
        <!-- Performance Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">مؤشرات الأداء</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">سرعة التحميل</span>
                    <span class="text-sm font-medium text-green-600">2.3 ثانية</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">معدل النجاح</span>
                    <span class="text-sm font-medium text-green-600">99.8%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">وقت الاستجابة</span>
                    <span class="text-sm font-medium text-green-600">150ms</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Activity Chart
const ctx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
        datasets: [{
            label: 'النشاطات',
            data: [12, 19, 15, 25, 22, 30, 28],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Auto-refresh dashboard every 30 seconds
setInterval(function() {
    // You can add AJAX call here to refresh data
    console.log('Dashboard auto-refresh');
}, 30000);
</script>

<?php include 'includes/footer.php'; ?> 