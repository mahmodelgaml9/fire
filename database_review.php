<?php
require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html lang='ar' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>مراجعة بنية قاعدة البيانات - Sphinx Fire CMS</title>";
echo "<style>";
echo "body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo "h1 { color: #1e3a8a; text-align: center; margin-bottom: 30px; }";
echo "h2 { color: #3b82f6; margin-top: 30px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }";
echo "h3 { color: #059669; margin-top: 20px; }";
echo ".table-section { background: #f8fafc; padding: 20px; margin: 15px 0; border-radius: 8px; border-left: 4px solid #3b82f6; }";
echo ".column-info { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e5e7eb; }";
echo ".success { color: #059669; }";
echo ".error { color: #dc2626; }";
echo ".warning { color: #d97706; }";
echo ".info { color: #2563eb; }";
echo "table { width: 100%; border-collapse: collapse; margin: 10px 0; }";
echo "th, td { padding: 8px 12px; text-align: right; border: 1px solid #e5e7eb; }";
echo "th { background: #f3f4f6; font-weight: bold; }";
echo ".foreign-key { background: #fef3c7; }";
echo ".primary-key { background: #dbeafe; }";
echo ".index { background: #f0fdf4; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 مراجعة بنية قاعدة البيانات - Sphinx Fire CMS</h1>";

try {
    // 1. معلومات قاعدة البيانات
    echo "<div class='table-section'>";
    echo "<h2>📊 معلومات قاعدة البيانات</h2>";
    
    $stmt = $pdo->query("SELECT DATABASE() as db_name, VERSION() as mysql_version");
    $db_info = $stmt->fetch();
    
    echo "<div class='column-info'>";
    echo "<strong>اسم قاعدة البيانات:</strong> " . $db_info['db_name'] . "<br>";
    echo "<strong>إصدار MySQL:</strong> " . $db_info['mysql_version'] . "<br>";
    echo "</div>";
    echo "</div>";

    // 2. قائمة الجداول
    echo "<div class='table-section'>";
    echo "<h2>📋 قائمة الجداول</h2>";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<table>";
    echo "<tr><th>اسم الجدول</th><th>عدد الأعمدة</th><th>عدد الصفوف</th><th>الحجم التقريبي</th></tr>";
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as row_count FROM `$table`");
        $row_count = $stmt->fetch()['row_count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as col_count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table'");
        $col_count = $stmt->fetch()['col_count'];
        
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td>$col_count</td>";
        echo "<td>$row_count</td>";
        echo "<td>-</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

    // 3. تفاصيل كل جدول
    foreach ($tables as $table) {
        echo "<div class='table-section'>";
        echo "<h3>📄 جدول: $table</h3>";
        
        // أعمدة الجدول
        $stmt = $pdo->query("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                COLUMN_KEY,
                EXTRA,
                CHARACTER_MAXIMUM_LENGTH
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '$table'
            ORDER BY ORDINAL_POSITION
        ");
        $columns = $stmt->fetchAll();
        
        echo "<h4>الأعمدة:</h4>";
        echo "<table>";
        echo "<tr><th>اسم العمود</th><th>النوع</th><th>يمكن أن يكون فارغ</th><th>القيمة الافتراضية</th><th>المفتاح</th><th>إضافي</th><th>الطول</th></tr>";
        
        foreach ($columns as $col) {
            $row_class = '';
            if ($col['COLUMN_KEY'] == 'PRI') $row_class = 'primary-key';
            elseif ($col['COLUMN_KEY'] == 'MUL') $row_class = 'foreign-key';
            elseif ($col['COLUMN_KEY'] == 'UNI') $row_class = 'index';
            
            echo "<tr class='$row_class'>";
            echo "<td><strong>" . $col['COLUMN_NAME'] . "</strong></td>";
            echo "<td>" . $col['DATA_TYPE'] . "</td>";
            echo "<td>" . $col['IS_NULLABLE'] . "</td>";
            echo "<td>" . ($col['COLUMN_DEFAULT'] ?? 'NULL') . "</td>";
            echo "<td>" . ($col['COLUMN_KEY'] ?: '-') . "</td>";
            echo "<td>" . ($col['EXTRA'] ?: '-') . "</td>";
            echo "<td>" . ($col['CHARACTER_MAXIMUM_LENGTH'] ?: '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // الفهارس
        $stmt = $pdo->query("SHOW INDEX FROM `$table`");
        $indexes = $stmt->fetchAll();
        
        if (!empty($indexes)) {
            echo "<h4>الفهارس:</h4>";
            echo "<table>";
            echo "<tr><th>اسم الفهرس</th><th>العمود</th><th>النوع</th><th>الترتيب</th></tr>";
            
            foreach ($indexes as $index) {
                echo "<tr>";
                echo "<td>" . $index['Key_name'] . "</td>";
                echo "<td>" . $index['Column_name'] . "</td>";
                echo "<td>" . $index['Index_type'] . "</td>";
                echo "<td>" . $index['Seq_in_index'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // العلاقات الخارجية
        $stmt = $pdo->query("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '$table'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $foreign_keys = $stmt->fetchAll();
        
        if (!empty($foreign_keys)) {
            echo "<h4>العلاقات الخارجية:</h4>";
            echo "<table>";
            echo "<tr><th>اسم القيد</th><th>العمود</th><th>الجدول المرجعي</th><th>العمود المرجعي</th></tr>";
            
            foreach ($foreign_keys as $fk) {
                echo "<tr>";
                echo "<td>" . $fk['CONSTRAINT_NAME'] . "</td>";
                echo "<td>" . $fk['COLUMN_NAME'] . "</td>";
                echo "<td>" . $fk['REFERENCED_TABLE_NAME'] . "</td>";
                echo "<td>" . $fk['REFERENCED_COLUMN_NAME'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "</div>";
    }

    // 4. مراجعة الجداول المطلوبة للأدمن
    echo "<div class='table-section'>";
    echo "<h2>🔐 مراجعة جداول الأدمن</h2>";
    
    $admin_tables = ['admin_roles', 'admin_users', 'admin_logs', 'admin_notifications'];
    $missing_tables = [];
    
    foreach ($admin_tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✅ جدول $table موجود</p>";
        } else {
            echo "<p class='error'>❌ جدول $table مفقود</p>";
            $missing_tables[] = $table;
        }
    }
    
    if (!empty($missing_tables)) {
        echo "<div class='warning'>";
        echo "<h4>⚠️ الجداول المفقودة:</h4>";
        echo "<ul>";
        foreach ($missing_tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        echo "<p>يجب تشغيل ملف setup_admin_final.php لإعداد هذه الجداول</p>";
        echo "</div>";
    }
    echo "</div>";

    // 5. مراجعة الحقول المطلوبة
    echo "<div class='table-section'>";
    echo "<h2>🔧 مراجعة الحقول المطلوبة</h2>";
    
    $required_fields = [
        'pages' => ['created_by', 'is_featured', 'meta_title'],
        'services' => ['created_by', 'is_featured', 'meta_title'],
        'projects' => ['created_by', 'is_featured', 'meta_title'],
        'blog_posts' => ['created_by', 'is_featured', 'meta_title'],
        'testimonials' => ['created_by', 'is_featured'],
        'locations' => ['created_by', 'is_featured']
    ];
    
    foreach ($required_fields as $table => $fields) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<h4>جدول $table:</h4>";
            foreach ($fields as $field) {
                $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$field'");
                if ($stmt->rowCount() > 0) {
                    echo "<p class='success'>✅ العمود $field موجود في $table</p>";
                } else {
                    echo "<p class='error'>❌ العمود $field مفقود في $table</p>";
                }
            }
        } else {
            echo "<p class='warning'>⚠️ جدول $table غير موجود</p>";
        }
    }
    echo "</div>";

    // 6. مراجعة البيانات الموجودة
    echo "<div class='table-section'>";
    echo "<h2>📈 مراجعة البيانات الموجودة</h2>";
    
    $data_tables = ['pages', 'services', 'projects', 'blog_posts', 'testimonials', 'locations'];
    
    foreach ($data_tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $stmt->fetch()['count'];
            echo "<p><strong>$table:</strong> $count صف</p>";
        }
    }
    echo "</div>";

    // 7. توصيات
    echo "<div class='table-section'>";
    echo "<h2>💡 التوصيات</h2>";
    
    echo "<div class='info'>";
    echo "<h4>لإعداد لوحة التحكم بشكل صحيح:</h4>";
    echo "<ol>";
    echo "<li>تأكد من وجود جميع جداول الأدمن</li>";
    echo "<li>أضف الحقول المطلوبة للجداول الموجودة</li>";
    echo "<li>أنشئ مستخدم أدمن واحد على الأقل</li>";
    echo "<li>تأكد من صحة العلاقات الخارجية</li>";
    echo "</ol>";
    echo "</div>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<h2>❌ خطأ في الاتصال بقاعدة البيانات</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?> 