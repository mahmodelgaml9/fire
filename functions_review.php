<?php
require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>مراجعة الفانكشنز والملفات - Sphinx Fire CMS</title>";
echo "<style>";
echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "h1 { color: #1e3a8a; text-align: center; margin-bottom: 30px; }";
echo "h2 { color: #3b82f6; margin-top: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }";
echo "h3 { color: #059669; margin-top: 20px; }";
echo ".file-section { background: #f8fafc; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #3b82f6; }";
echo ".function-info { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e5e7eb; }";
echo ".success { color: #059669; }";
echo ".error { color: #dc2626; }";
echo ".warning { color: #d97706; }";
echo ".info { color: #2563eb; }";
echo "code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; font-family: monospace; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔧 مراجعة الفانكشنز والملفات - Sphinx Fire CMS</h1>";

// 1. مراجعة الملفات الأساسية
echo "<div class='file-section'>";
echo "<h2>📁 مراجعة الملفات الأساسية</h2>";

$core_files = [
    'config.php' => 'ملف الإعدادات الأساسية',
    'admin/includes/auth.php' => 'فانكشنز الأمان والصلاحيات',
    'admin/includes/header.php' => 'هيدر لوحة التحكم',
    'admin/includes/sidebar.php' => 'سايدبار لوحة التحكم',
    'admin/includes/footer.php' => 'فوتر لوحة التحكم',
    'admin/login.php' => 'صفحة تسجيل الدخول',
    'admin/dashboard.php' => 'لوحة التحكم الرئيسية',
    'admin/pages.php' => 'إدارة الصفحات',
    'admin/sections.php' => 'إدارة السكاشنات'
];

foreach ($core_files as $file => $description) {
    if (file_exists($file)) {
        echo "<p class='success'>✅ $file - $description</p>";
    } else {
        echo "<p class='error'>❌ $file - $description (مفقود)</p>";
    }
}
echo "</div>";

// 2. مراجعة فانكشنز الأمان
echo "<div class='file-section'>";
echo "<h2>🔐 مراجعة فانكشنز الأمان</h2>";

if (file_exists('admin/includes/auth.php')) {
    $auth_content = file_get_contents('admin/includes/auth.php');
    
    $security_functions = [
        'checkAdminLogin' => 'التحقق من تسجيل دخول الأدمن',
        'checkPermission' => 'التحقق من الصلاحيات',
        'getCurrentAdminUser' => 'الحصول على بيانات المستخدم الحالي',
        'logAdminAction' => 'تسجيل عمليات الأدمن',
        'generateCSRFToken' => 'إنشاء رمز CSRF',
        'verifyCSRFToken' => 'التحقق من رمز CSRF',
        'sanitizeInput' => 'تنظيف المدخلات',
        'validateUploadedFile' => 'التحقق من الملفات المرفوعة'
    ];
    
    foreach ($security_functions as $function => $description) {
        if (strpos($auth_content, "function $function") !== false) {
            echo "<p class='success'>✅ $function - $description</p>";
        } else {
            echo "<p class='error'>❌ $function - $description (مفقود)</p>";
        }
    }
} else {
    echo "<p class='error'>❌ ملف auth.php غير موجود</p>";
}
echo "</div>";

// 3. مراجعة فانكشنز قاعدة البيانات
echo "<div class='file-section'>";
echo "<h2>🗄️ مراجعة فانكشنز قاعدة البيانات</h2>";

$db_functions = [
    'getQuickStats' => 'الحصول على إحصائيات سريعة',
    'getRecentActivities' => 'الحصول على النشاطات الحديثة',
    'createNotification' => 'إنشاء إشعار جديد',
    'getUserNotifications' => 'الحصول على إشعارات المستخدم',
    'markNotificationAsRead' => 'تحديد الإشعار كمقروء'
];

foreach ($db_functions as $function => $description) {
    if (function_exists($function)) {
        echo "<p class='success'>✅ $function - $description</p>";
    } else {
        echo "<p class='error'>❌ $function - $description (مفقود)</p>";
    }
}
echo "</div>";

// 4. مراجعة فانكشنز المساعدة
echo "<div class='file-section'>";
echo "<h2>🛠️ مراجعة فانكشنز المساعدة</h2>";

$helper_functions = [
    'formatDate' => 'تنسيق التاريخ',
    'formatFileSize' => 'تنسيق حجم الملف',
    'generateSecureLink' => 'إنشاء رابط آمن',
    'redirectWithMessage' => 'إعادة التوجيه مع رسالة',
    'displayMessages' => 'عرض الرسائل'
];

foreach ($helper_functions as $function => $description) {
    if (function_exists($function)) {
        echo "<p class='success'>✅ $function - $description</p>";
    } else {
        echo "<p class='error'>❌ $function - $description (مفقود)</p>";
    }
}
echo "</div>";

// 5. مراجعة فانكشنز الملفات
echo "<div class='file-section'>";
echo "<h2>📁 مراجعة فانكشنز الملفات</h2>";

$file_functions = [
    'uploadFile' => 'رفع ملف',
    'deleteFile' => 'حذف ملف',
    'validateUploadedFile' => 'التحقق من الملف المرفوع'
];

foreach ($file_functions as $function => $description) {
    if (function_exists($function)) {
        echo "<p class='success'>✅ $function - $description</p>";
    } else {
        echo "<p class='error'>❌ $function - $description (مفقود)</p>";
    }
}
echo "</div>";

// 6. مراجعة فانكشنز JavaScript
echo "<div class='file-section'>";
echo "<h2>⚡ مراجعة فانكشنز JavaScript</h2>";

$js_functions = [
    'showLoading' => 'إظهار مؤشر التحميل',
    'hideLoading' => 'إخفاء مؤشر التحميل',
    'showSuccessToast' => 'إظهار رسالة نجاح',
    'showErrorToast' => 'إظهار رسالة خطأ',
    'showConfirmation' => 'إظهار تأكيد',
    'validateForm' => 'التحقق من صحة النموذج',
    'makeAjaxRequest' => 'إرسال طلب AJAX',
    'sortTable' => 'ترتيب الجدول',
    'filterTable' => 'فلترة الجدول',
    'exportTableToCSV' => 'تصدير الجدول إلى CSV'
];

foreach ($js_functions as $function => $description) {
    echo "<p class='info'>🔍 $function - $description (يجب التحقق من وجوده في footer.php)</p>";
}
echo "</div>";

// 7. مراجعة الإعدادات
echo "<div class='file-section'>";
echo "<h2>⚙️ مراجعة الإعدادات</h2>";

if (file_exists('config.php')) {
    $config_content = file_get_contents('config.php');
    
    $config_vars = [
        'DB_HOST' => 'خادم قاعدة البيانات',
        'DB_NAME' => 'اسم قاعدة البيانات',
        'DB_USER' => 'اسم مستخدم قاعدة البيانات',
        'DB_PASS' => 'كلمة مرور قاعدة البيانات',
        'APP_ENV' => 'بيئة التطبيق',
        'APP_DEBUG' => 'وضع التصحيح',
        'APP_URL' => 'رابط التطبيق'
    ];
    
    foreach ($config_vars as $var => $description) {
        if (strpos($config_content, $var) !== false) {
            echo "<p class='success'>✅ $var - $description</p>";
        } else {
            echo "<p class='error'>❌ $var - $description (مفقود)</p>";
        }
    }
} else {
    echo "<p class='error'>❌ ملف config.php غير موجود</p>";
}
echo "</div>";

// 8. مراجعة ملفات CSS و JS
echo "<div class='file-section'>";
echo "<h2>🎨 مراجعة ملفات التصميم</h2>";

$assets_files = [
    'admin/assets/css/admin.css' => 'ملف CSS الرئيسي للوحة التحكم',
    'admin/assets/js/admin.js' => 'ملف JavaScript الرئيسي للوحة التحكم',
    'style.css' => 'ملف CSS الرئيسي للموقع'
];

foreach ($assets_files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p class='success'>✅ $file - $description (الحجم: " . number_format($size) . " بايت)</p>";
    } else {
        echo "<p class='error'>❌ $file - $description (مفقود)</p>";
    }
}
echo "</div>";

// 9. مراجعة ملفات قاعدة البيانات
echo "<div class='file-section'>";
echo "<h2>🗄️ مراجعة ملفات قاعدة البيانات</h2>";

$db_files = [
    'database/admin_basic_tables.sql' => 'جداول الأدمن الأساسية',
    'database/add_admin_fields.sql' => 'الحقول الجديدة للجداول الموجودة',
    'database/add_fields_direct.sql' => 'الحقول الجديدة (طريقة مباشرة)',
    'setup_admin_final.php' => 'ملف إعداد لوحة التحكم النهائي',
    'setup_complete_admin.php' => 'ملف الإعداد الشامل'
];

foreach ($db_files as $file => $description) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "<p class='success'>✅ $file - $description (الحجم: " . number_format($size) . " بايت)</p>";
    } else {
        echo "<p class='error'>❌ $file - $description (مفقود)</p>";
    }
}
echo "</div>";

// 10. توصيات
echo "<div class='file-section'>";
echo "<h2>💡 التوصيات</h2>";

echo "<div class='info'>";
echo "<h4>لضمان عمل لوحة التحكم بشكل صحيح:</h4>";
echo "<ol>";
echo "<li>تأكد من وجود جميع الملفات الأساسية</li>";
echo "<li>تحقق من وجود جميع فانكشنز الأمان</li>";
echo "<li>تأكد من صحة إعدادات قاعدة البيانات</li>";
echo "<li>تحقق من وجود ملفات CSS و JavaScript</li>";
echo "<li>تأكد من وجود ملفات إعداد قاعدة البيانات</li>";
echo "</ol>";
echo "</div>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?> 