<?php
// =====================================================
// Update Database Configuration for Hostinger
// =====================================================

// Read the current config file
$config_file = '../config.php';
$config_content = file_get_contents($config_file);

// New Hostinger database credentials
$new_db_host = 'localhost';
$new_db_name = 'u404645000_sphinx';
$new_db_user = 'u404645000_sweed';
$new_db_pass = '8:Qx=s6w9j^X';

// Update the database configuration
$config_content = preg_replace(
    "/define\('DB_HOST',\s*'[^']*'\);/",
    "define('DB_HOST', '$new_db_host');",
    $config_content
);

$config_content = preg_replace(
    "/define\('DB_NAME',\s*'[^']*'\);/",
    "define('DB_NAME', '$new_db_name');",
    $config_content
);

$config_content = preg_replace(
    "/define\('DB_USER',\s*'[^']*'\);/",
    "define('DB_USER', '$new_db_user');",
    $config_content
);

$config_content = preg_replace(
    "/define\('DB_PASS',\s*'[^']*'\);/",
    "define('DB_PASS', '$new_db_pass');",
    $config_content
);

// Write the updated config file
if (file_put_contents($config_file, $config_content)) {
    echo "✅ تم تحديث ملف config.php بنجاح!\n";
    echo "📊 بيانات الاتصال الجديدة:\n";
    echo "   - Host: $new_db_host\n";
    echo "   - Database: $new_db_name\n";
    echo "   - User: $new_db_user\n";
    echo "   - Password: $new_db_pass\n";
} else {
    echo "❌ فشل في تحديث ملف config.php\n";
}

echo "\n📝 ملاحظات مهمة:\n";
echo "1. تأكد من رفع ملف hostinger_clean_schema.sql على قاعدة البيانات\n";
echo "2. تأكد من أن جميع الجداول تم إنشاؤها بنجاح\n";
echo "3. تأكد من أن بيانات الاتصال صحيحة\n";
echo "4. جرب تسجيل الدخول للوحة الإدارة\n";
?> 