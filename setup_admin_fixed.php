<?php
// ملف إعداد لوحة التحكم - نسخة محدثة
require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>إعداد لوحة التحكم - Sphinx Fire CMS</title>";
echo "<style>";
echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "h1 { color: #1e3a8a; text-align: center; margin-bottom: 30px; }";
echo "h2 { color: #3b82f6; margin-top: 30px; }";
echo ".step { background: #f8fafc; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #3b82f6; }";
echo ".success { color: #059669; }";
echo ".error { color: #dc2626; }";
echo ".warning { color: #d97706; }";
echo ".info { color: #2563eb; }";
echo ".progress { background: #e5e7eb; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0; }";
echo ".progress-bar { background: #3b82f6; height: 100%; transition: width 0.3s; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🚀 إعداد لوحة التحكم - Sphinx Fire CMS</h1>";

$total_steps = 3;
$current_step = 0;

// الخطوة 1: إنشاء جداول الأدمن الأساسية
echo "<div class='step'>";
echo "<h2>الخطوة 1: إنشاء جداول الأدمن الأساسية</h2>";
$current_step++;

try {
    $sql_file = 'database/admin_basic_tables.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("ملف SQL غير موجود: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    $queries = array_filter(array_map('trim', explode(';', $sql_content)));
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($queries as $query) {
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }
        
        try {
            $pdo->exec($query);
            $success_count++;
        } catch (PDOException $e) {
            $error_count++;
            echo "<p class='error'>✗ خطأ: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<p class='success'>✓ تم إنشاء جداول الأدمن الأساسية</p>";
    echo "<p>الأوامر الناجحة: <strong>$success_count</strong></p>";
    echo "<p>الأوامر الفاشلة: <strong>$error_count</strong></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ خطأ في إنشاء جداول الأدمن: " . $e->getMessage() . "</p>";
}
echo "</div>";

// الخطوة 2: إضافة الحقول الجديدة للجداول الموجودة
echo "<div class='step'>";
echo "<h2>الخطوة 2: إضافة الحقول الجديدة للجداول الموجودة</h2>";
$current_step++;

try {
    // إضافة الحقول بطريقة مباشرة
    $tables = ['pages', 'services', 'projects', 'blog_posts', 'testimonials', 'locations'];
    $fields = [
        'created_by' => 'INT DEFAULT NULL',
        'is_featured' => 'BOOLEAN DEFAULT FALSE',
        'meta_title' => 'VARCHAR(255) DEFAULT NULL',
        'meta_description' => 'TEXT DEFAULT NULL',
        'meta_keywords' => 'TEXT DEFAULT NULL',
        'og_title' => 'VARCHAR(255) DEFAULT NULL',
        'og_description' => 'TEXT DEFAULT NULL',
        'og_image' => 'VARCHAR(255) DEFAULT NULL',
        'canonical_url' => 'VARCHAR(255) DEFAULT NULL'
    ];
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($tables as $table) {
        // التحقق من وجود الجدول
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            echo "<p class='warning'>⚠️ جدول $table غير موجود، تخطي...</p>";
            continue;
        }
        
        foreach ($fields as $field => $definition) {
            try {
                // التحقق من وجود العمود
                $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$field'");
                if ($stmt->rowCount() == 0) {
                    // إضافة العمود
                    $sql = "ALTER TABLE `$table` ADD COLUMN `$field` $definition";
                    $pdo->exec($sql);
                    echo "<p class='success'>✓ تم إضافة العمود $field لجدول $table</p>";
                    $success_count++;
                } else {
                    echo "<p class='info'>ℹ️ العمود $field موجود بالفعل في جدول $table</p>";
                }
            } catch (PDOException $e) {
                $error_count++;
                echo "<p class='error'>✗ خطأ في إضافة العمود $field لجدول $table: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<p class='success'>✓ تم إضافة الحقول الجديدة</p>";
    echo "<p>الأوامر الناجحة: <strong>$success_count</strong></p>";
    echo "<p>الأوامر الفاشلة: <strong>$error_count</strong></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ خطأ في إضافة الحقول: " . $e->getMessage() . "</p>";
}
echo "</div>";

// الخطوة 3: التحقق من إعداد قاعدة البيانات
echo "<div class='step'>";
echo "<h2>الخطوة 3: التحقق من إعداد قاعدة البيانات</h2>";
$current_step++;

try {
    // التحقق من وجود الجداول الأساسية
    $required_tables = ['admin_roles', 'admin_users', 'admin_logs', 'admin_notifications'];
    $existing_tables = [];
    
    foreach ($required_tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existing_tables[] = $table;
            echo "<p class='success'>✓ جدول $table موجود</p>";
        } else {
            echo "<p class='error'>✗ جدول $table غير موجود</p>";
        }
    }
    
    // التحقق من وجود المستخدم الافتراضي
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $admin_count = $stmt->fetch()['count'];
    
    if ($admin_count > 0) {
        echo "<p class='success'>✓ المستخدم الافتراضي موجود</p>";
    } else {
        echo "<p class='error'>✗ المستخدم الافتراضي غير موجود</p>";
    }
    
    // التحقق من وجود الأدوار
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admin_roles");
    $stmt->execute();
    $roles_count = $stmt->fetch()['count'];
    
    if ($roles_count > 0) {
        echo "<p class='success'>✓ الأدوار موجودة ($roles_count دور)</p>";
    } else {
        echo "<p class='error'>✗ الأدوار غير موجودة</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ خطأ في التحقق: " . $e->getMessage() . "</p>";
}
echo "</div>";

// عرض شريط التقدم
$progress = ($current_step / $total_steps) * 100;
echo "<div class='progress'>";
echo "<div class='progress-bar' style='width: $progress%'></div>";
echo "</div>";
echo "<p class='info'>التقدم: $current_step من $total_steps خطوات</p>";

// رسالة النجاح
echo "<div class='step' style='background: #ecfdf5; border-left-color: #059669;'>";
echo "<h2 class='success'>✅ تم إعداد لوحة التحكم بنجاح!</h2>";
echo "<p>يمكنك الآن:</p>";
echo "<ul>";
echo "<li>الذهاب إلى <a href='admin/' target='_blank'>لوحة التحكم</a></li>";
echo "<li>تسجيل الدخول بـ: <strong>admin</strong> / <strong>admin123</strong></li>";
echo "<li>بدء إدارة المحتوى والخدمات</li>";
echo "</ul>";
echo "</div>";

// معلومات إضافية
echo "<div class='step'>";
echo "<h2>📋 معلومات مهمة</h2>";
echo "<ul>";
echo "<li><strong>اسم المستخدم:</strong> admin</li>";
echo "<li><strong>كلمة المرور:</strong> admin123</li>";
echo "<li><strong>الدور:</strong> مدير النظام الكامل</li>";
echo "<li><strong>الصلاحيات:</strong> جميع الصلاحيات</li>";
echo "</ul>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?> 