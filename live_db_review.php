<?php
// Live Database Review - عرض قاعدة البيانات الحية
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html dir='rtl' lang='ar'>
<head>
    <meta charset='UTF-8'>
    <title>مراجعة قاعدة البيانات الحية</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .table-info { background: #f8f9fa; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        th { background: #e9ecef; }
        .column-info { font-size: 12px; color: #666; }
        .foreign-key { color: #007bff; font-weight: bold; }
        .primary-key { color: #28a745; font-weight: bold; }
        .index { color: #ffc107; font-weight: bold; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 مراجعة قاعدة البيانات الحية - Sphinx Fire CMS</h1>";

try {
    // الاتصال بقاعدة البيانات
    $pdo = new PDO('mysql:host=localhost;dbname=sphinx_fire_cms;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ تم الاتصال بقاعدة البيانات بنجاح</div>";
    
    // 1. عرض جميع الجداول
    echo "<div class='section'>
        <h2>📋 قائمة جميع الجداول</h2>";
    
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>عدد الجداول:</strong> " . count($tables) . "</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul></div>";
    
    // 2. تفاصيل كل جدول
    foreach ($tables as $table) {
        echo "<div class='section'>
            <h3>📊 جدول: $table</h3>";
        
        // معلومات الأعمدة
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h4>🔧 الأعمدة:</h4>";
        echo "<table>
            <tr>
                <th>اسم العمود</th>
                <th>النوع</th>
                <th>NULL</th>
                <th>المفتاح</th>
                <th>الافتراضي</th>
                <th>إضافي</th>
            </tr>";
        
        foreach ($columns as $column) {
            $keyClass = '';
            if ($column['Key'] == 'PRI') $keyClass = 'primary-key';
            elseif ($column['Key'] == 'MUL') $keyClass = 'foreign-key';
            elseif ($column['Key'] == 'UNI') $keyClass = 'index';
            
            echo "<tr>
                <td class='$keyClass'>{$column['Field']}</td>
                <td>{$column['Type']}</td>
                <td>{$column['Null']}</td>
                <td>{$column['Key']}</td>
                <td>{$column['Default']}</td>
                <td>{$column['Extra']}</td>
            </tr>";
        }
        echo "</table>";
        
        // معلومات المفاتيح الأجنبية
        $foreignKeys = $pdo->query("
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'sphinx_fire_cms' 
            AND TABLE_NAME = '$table' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($foreignKeys)) {
            echo "<h4>🔗 المفاتيح الأجنبية:</h4>";
            echo "<table>
                <tr>
                    <th>العمود</th>
                    <th>الجدول المرجعي</th>
                    <th>العمود المرجعي</th>
                </tr>";
            foreach ($foreignKeys as $fk) {
                echo "<tr>
                    <td>{$fk['COLUMN_NAME']}</td>
                    <td>{$fk['REFERENCED_TABLE_NAME']}</td>
                    <td>{$fk['REFERENCED_COLUMN_NAME']}</td>
                </tr>";
            }
            echo "</table>";
        }
        
        // عدد الصفوف
        $rowCount = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "<p><strong>عدد الصفوف:</strong> $rowCount</p>";
        
        // عرض أول 5 صفوف كعينة
        if ($rowCount > 0) {
            echo "<h4>📝 عينة من البيانات (أول 5 صفوف):</h4>";
            $sampleData = $pdo->query("SELECT * FROM `$table` LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($sampleData)) {
                echo "<table>";
                // رؤوس الجدول
                echo "<tr>";
                foreach (array_keys($sampleData[0]) as $header) {
                    echo "<th>$header</th>";
                }
                echo "</tr>";
                // البيانات
                foreach ($sampleData as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        $displayValue = $value;
                        if (strlen($value) > 50) {
                            $displayValue = substr($value, 0, 50) . '...';
                        }
                        echo "<td>" . htmlspecialchars($displayValue) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        
        echo "</div>";
    }
    
    // 3. معلومات إضافية
    echo "<div class='section'>
        <h2>📈 معلومات إضافية</h2>";
    
    // حجم قاعدة البيانات
    $dbSize = $pdo->query("
        SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB'
        FROM information_schema.tables 
        WHERE table_schema = 'sphinx_fire_cms'
    ")->fetchColumn();
    echo "<p><strong>حجم قاعدة البيانات:</strong> $dbSize MB</p>";
    
    // إحصائيات الجداول
    $tableStats = $pdo->query("
        SELECT 
            table_name,
            table_rows,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size in MB'
        FROM information_schema.tables 
        WHERE table_schema = 'sphinx_fire_cms'
        ORDER BY table_rows DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h4>📊 إحصائيات الجداول:</h4>";
    echo "<table>
        <tr>
            <th>اسم الجدول</th>
            <th>عدد الصفوف</th>
            <th>الحجم (MB)</th>
        </tr>";
    foreach ($tableStats as $stat) {
        echo "<tr>
            <td>{$stat['table_name']}</td>
            <td>{$stat['table_rows']}</td>
            <td>{$stat['Size in MB']}</td>
        </tr>";
    }
    echo "</table>";
    
    echo "</div>";
    
    // 4. فحص الجداول المطلوبة للأدمن
    echo "<div class='section'>
        <h2>🔍 فحص الجداول المطلوبة للأدمن</h2>";
    
    $adminTables = ['admin_roles', 'admin_users', 'admin_logs', 'admin_notifications', 'settings'];
    $missingTables = [];
    $existingTables = [];
    
    foreach ($adminTables as $table) {
        if (in_array($table, $tables)) {
            $existingTables[] = $table;
        } else {
            $missingTables[] = $table;
        }
    }
    
    if (!empty($existingTables)) {
        echo "<div class='success'>
            <h4>✅ الجداول الموجودة:</h4>
            <ul>";
        foreach ($existingTables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul></div>";
    }
    
    if (!empty($missingTables)) {
        echo "<div class='warning'>
            <h4>⚠️ الجداول المفقودة:</h4>
            <ul>";
        foreach ($missingTables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul></div>";
    }
    
    echo "</div>";
    
    // 5. فحص الحقول المطلوبة في الجداول الموجودة
    echo "<div class='section'>
        <h2>🔍 فحص الحقول المطلوبة</h2>";
    
    $contentTables = ['pages', 'services', 'projects', 'blog_posts', 'testimonials', 'locations'];
    $requiredFields = ['created_by', 'updated_by', 'is_featured', 'meta_title', 'meta_description'];
    
    foreach ($contentTables as $table) {
        if (in_array($table, $tables)) {
            echo "<h4>جدول: $table</h4>";
            $tableColumns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_COLUMN, 0);
            
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (!in_array($field, $tableColumns)) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                echo "<div class='warning'>
                    <p>الحقول المفقودة: " . implode(', ', $missingFields) . "</p>
                </div>";
            } else {
                echo "<div class='success'>
                    <p>✅ جميع الحقول المطلوبة موجودة</p>
                </div>";
            }
        }
    }
    
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>
        <h3>❌ خطأ في الاتصال بقاعدة البيانات</h3>
        <p><strong>الخطأ:</strong> " . $e->getMessage() . "</p>
        <p><strong>الكود:</strong> " . $e->getCode() . "</p>
    </div>";
} catch (Exception $e) {
    echo "<div class='error'>
        <h3>❌ خطأ عام</h3>
        <p>" . $e->getMessage() . "</p>
    </div>";
}

echo "</div></body></html>";
?> 