<?php
// ملف إعداد جداول لوحة التحكم
require_once 'config.php';

echo "<h1>إعداد جداول لوحة التحكم - Sphinx Fire CMS</h1>";

try {
    // قراءة ملف SQL
    $sql_file = 'database/admin_basic_tables.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("ملف SQL غير موجود: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // تقسيم SQL إلى أوامر منفصلة
    $queries = array_filter(array_map('trim', explode(';', $sql_content)));
    
    echo "<h2>بدء إنشاء الجداول...</h2>";
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($queries as $query) {
        if (empty($query) || strpos($query, '--') === 0) {
            continue; // تخطي التعليقات والأسطر الفارغة
        }
        
        try {
            $pdo->exec($query);
            echo "<p style='color: green;'>✓ تم تنفيذ: " . substr($query, 0, 50) . "...</p>";
            $success_count++;
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ خطأ في: " . substr($query, 0, 50) . "...</p>";
            echo "<p style='color: red;'>الخطأ: " . $e->getMessage() . "</p>";
            $error_count++;
        }
    }
    
    echo "<h2>نتيجة العملية:</h2>";
    echo "<p>الأوامر الناجحة: <strong style='color: green;'>$success_count</strong></p>";
    echo "<p>الأوامر الفاشلة: <strong style='color: red;'>$error_count</strong></p>";
    
    if ($error_count == 0) {
        echo "<h2 style='color: green;'>✅ تم إنشاء جداول لوحة التحكم بنجاح!</h2>";
        echo "<p>يمكنك الآن:</p>";
        echo "<ul>";
        echo "<li>الذهاب إلى <a href='admin/'>لوحة التحكم</a></li>";
        echo "<li>تسجيل الدخول بـ: admin / admin123</li>";
        echo "</ul>";
    } else {
        echo "<h2 style='color: orange;'>⚠️ تم إنشاء بعض الجداول مع أخطاء</h2>";
        echo "<p>يرجى مراجعة الأخطاء أعلاه</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ خطأ في العملية</h2>";
    echo "<p>الخطأ: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/'>العودة للوحة التحكم</a></p>";
?> 