<?php
// Fix Database Complete - إصلاح قاعدة البيانات بالكامل
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html dir='rtl' lang='ar'>
<head>
    <meta charset='UTF-8'>
    <title>إصلاح قاعدة البيانات - Sphinx Fire CMS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .step { margin: 10px 0; padding: 10px; border-left: 4px solid #007bff; }
        .step.success { border-left-color: #28a745; }
        .step.error { border-left-color: #dc3545; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 إصلاح قاعدة البيانات - Sphinx Fire CMS</h1>";

try {
    // الاتصال بقاعدة البيانات
    $pdo = new PDO('mysql:host=localhost;dbname=sphinx_fire_cms;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='success'>✅ تم الاتصال بقاعدة البيانات بنجاح</div>";
    
    $totalSteps = 0;
    $successSteps = 0;
    $errorSteps = 0;
    
    // ========================================
    // الخطوة 1: إنشاء جداول الأدمن الأساسية
    // ========================================
    echo "<div class='section'>
        <h2>📋 الخطوة 1: إنشاء جداول الأدمن الأساسية</h2>";
    
    // 1.1 إنشاء جدول admin_roles
    $totalSteps++;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `admin_roles` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(50) NOT NULL,
            `description` text DEFAULT NULL,
            `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إنشاء جدول admin_roles</div>";
        $successSteps++;
        
        // إضافة الأدوار الأساسية
        $roles = [
            ['name' => 'super_admin', 'description' => 'مدير النظام الكامل', 'permissions' => '["*"]'],
            ['name' => 'admin', 'description' => 'مدير عام', 'permissions' => '["pages","services","projects","blog","media","settings"]'],
            ['name' => 'editor', 'description' => 'محرر المحتوى', 'permissions' => '["pages","blog","media"]'],
            ['name' => 'author', 'description' => 'كاتب', 'permissions' => '["blog"]']
        ];
        
        foreach ($roles as $role) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO admin_roles (name, description, permissions) VALUES (?, ?, ?)");
            $stmt->execute([$role['name'], $role['description'], $role['permissions']]);
        }
        echo "<div class='step success'>✅ تم إضافة الأدوار الأساسية</div>";
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إنشاء admin_roles: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 1.2 إنشاء جدول admin_users
    $totalSteps++;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `first_name` varchar(50) DEFAULT NULL,
            `last_name` varchar(50) DEFAULT NULL,
            `role_id` int(11) NOT NULL,
            `avatar_url` varchar(255) DEFAULT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `last_login` timestamp NULL DEFAULT NULL,
            `email_verified_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`),
            KEY `role_id` (`role_id`),
            CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `admin_roles` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إنشاء جدول admin_users</div>";
        $successSteps++;
        
        // إضافة مستخدم الأدمن الأساسي
        $password_hash = password_hash('Fire@2024', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, email, password_hash, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['fireadmin', 'fireadmin@sphinxfire.com', $password_hash, 'Fire', 'Admin', 1]);
        echo "<div class='step success'>✅ تم إضافة مستخدم الأدمن الأساسي (fireadmin/Fire@2024)</div>";
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إنشاء admin_users: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 1.3 إنشاء جدول admin_logs
    $totalSteps++;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `admin_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) DEFAULT NULL,
            `action` varchar(100) NOT NULL,
            `table_name` varchar(50) DEFAULT NULL,
            `record_id` int(11) DEFAULT NULL,
            `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
            `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `action` (`action`),
            KEY `created_at` (`created_at`),
            CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إنشاء جدول admin_logs</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إنشاء admin_logs: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 1.4 إنشاء جدول admin_notifications
    $totalSteps++;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `admin_notifications` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) DEFAULT NULL,
            `title` varchar(255) NOT NULL,
            `message` text NOT NULL,
            `type` enum('info','success','warning','error') DEFAULT 'info',
            `is_read` tinyint(1) DEFAULT 0,
            `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `read_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `user_id` (`user_id`),
            KEY `is_read` (`is_read`),
            KEY `created_at` (`created_at`),
            CONSTRAINT `admin_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إنشاء جدول admin_notifications</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إنشاء admin_notifications: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // ========================================
    // الخطوة 2: إضافة الحقول المفقودة
    // ========================================
    echo "<div class='section'>
        <h2>🔧 الخطوة 2: إضافة الحقول المفقودة</h2>";
    
    // 2.1 إضافة الحقول المفقودة لجدول locations
    $totalSteps++;
    try {
        $sql = "ALTER TABLE `locations` 
                ADD COLUMN IF NOT EXISTS `created_by` int(11) DEFAULT NULL AFTER `sort_order`,
                ADD COLUMN IF NOT EXISTS `updated_by` int(11) DEFAULT NULL AFTER `created_by`,
                ADD COLUMN IF NOT EXISTS `is_featured` tinyint(1) DEFAULT 0 AFTER `updated_by`,
                ADD COLUMN IF NOT EXISTS `meta_title` varchar(255) DEFAULT NULL AFTER `is_featured`,
                ADD COLUMN IF NOT EXISTS `meta_description` text DEFAULT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` text DEFAULT NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `og_title` varchar(255) DEFAULT NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_description` text DEFAULT NULL AFTER `og_title`,
                ADD COLUMN IF NOT EXISTS `og_image` varchar(255) DEFAULT NULL AFTER `og_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` varchar(255) DEFAULT NULL AFTER `og_image`";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إضافة الحقول المفقودة لجدول locations</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة الحقول لجدول locations: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 2.2 إضافة الحقول المفقودة لجدول pages
    $totalSteps++;
    try {
        $sql = "ALTER TABLE `pages` 
                ADD COLUMN IF NOT EXISTS `is_featured` tinyint(1) DEFAULT 0 AFTER `sort_order`,
                ADD COLUMN IF NOT EXISTS `meta_title` varchar(255) DEFAULT NULL AFTER `is_featured`,
                ADD COLUMN IF NOT EXISTS `meta_description` text DEFAULT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` text DEFAULT NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `og_title` varchar(255) DEFAULT NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_description` text DEFAULT NULL AFTER `og_title`,
                ADD COLUMN IF NOT EXISTS `og_image` varchar(255) DEFAULT NULL AFTER `og_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` varchar(255) DEFAULT NULL AFTER `og_image`";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إضافة الحقول المفقودة لجدول pages</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة الحقول لجدول pages: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 2.3 إضافة الحقول المفقودة لجدول services
    $totalSteps++;
    try {
        $sql = "ALTER TABLE `services` 
                ADD COLUMN IF NOT EXISTS `meta_title` varchar(255) DEFAULT NULL AFTER `is_active`,
                ADD COLUMN IF NOT EXISTS `meta_description` text DEFAULT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` text DEFAULT NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `og_title` varchar(255) DEFAULT NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_description` text DEFAULT NULL AFTER `og_title`,
                ADD COLUMN IF NOT EXISTS `og_image` varchar(255) DEFAULT NULL AFTER `og_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` varchar(255) DEFAULT NULL AFTER `og_image`";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إضافة الحقول المفقودة لجدول services</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة الحقول لجدول services: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 2.4 إضافة الحقول المفقودة لجدول projects
    $totalSteps++;
    try {
        $sql = "ALTER TABLE `projects` 
                ADD COLUMN IF NOT EXISTS `meta_title` varchar(255) DEFAULT NULL AFTER `sort_order`,
                ADD COLUMN IF NOT EXISTS `meta_description` text DEFAULT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` text DEFAULT NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `og_title` varchar(255) DEFAULT NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_description` text DEFAULT NULL AFTER `og_title`,
                ADD COLUMN IF NOT EXISTS `og_image` varchar(255) DEFAULT NULL AFTER `og_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` varchar(255) DEFAULT NULL AFTER `og_image`";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إضافة الحقول المفقودة لجدول projects</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة الحقول لجدول projects: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 2.5 إضافة الحقول المفقودة لجدول blog_posts
    $totalSteps++;
    try {
        $sql = "ALTER TABLE `blog_posts` 
                ADD COLUMN IF NOT EXISTS `created_by` int(11) DEFAULT NULL AFTER `shares_count`,
                ADD COLUMN IF NOT EXISTS `updated_by` int(11) DEFAULT NULL AFTER `created_by`,
                ADD COLUMN IF NOT EXISTS `meta_title` varchar(255) DEFAULT NULL AFTER `updated_by`,
                ADD COLUMN IF NOT EXISTS `meta_description` text DEFAULT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` text DEFAULT NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `og_title` varchar(255) DEFAULT NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_description` text DEFAULT NULL AFTER `og_title`,
                ADD COLUMN IF NOT EXISTS `og_image` varchar(255) DEFAULT NULL AFTER `og_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` varchar(255) DEFAULT NULL AFTER `og_image`";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إضافة الحقول المفقودة لجدول blog_posts</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة الحقول لجدول blog_posts: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 2.6 إضافة الحقول المفقودة لجدول testimonials
    $totalSteps++;
    try {
        $sql = "ALTER TABLE `testimonials` 
                ADD COLUMN IF NOT EXISTS `updated_by` int(11) DEFAULT NULL AFTER `created_by`,
                ADD COLUMN IF NOT EXISTS `meta_title` varchar(255) DEFAULT NULL AFTER `updated_by`,
                ADD COLUMN IF NOT EXISTS `meta_description` text DEFAULT NULL AFTER `meta_title`,
                ADD COLUMN IF NOT EXISTS `meta_keywords` text DEFAULT NULL AFTER `meta_description`,
                ADD COLUMN IF NOT EXISTS `og_title` varchar(255) DEFAULT NULL AFTER `meta_keywords`,
                ADD COLUMN IF NOT EXISTS `og_description` text DEFAULT NULL AFTER `og_title`,
                ADD COLUMN IF NOT EXISTS `og_image` varchar(255) DEFAULT NULL AFTER `og_description`,
                ADD COLUMN IF NOT EXISTS `canonical_url` varchar(255) DEFAULT NULL AFTER `og_image`";
        
        $pdo->exec($sql);
        echo "<div class='step success'>✅ تم إضافة الحقول المفقودة لجدول testimonials</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة الحقول لجدول testimonials: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // ========================================
    // الخطوة 3: إضافة المفاتيح الأجنبية
    // ========================================
    echo "<div class='section'>
        <h2>🔗 الخطوة 3: إضافة المفاتيح الأجنبية</h2>";
    
    // 3.1 إضافة المفاتيح الأجنبية لجدول locations
    $totalSteps++;
    try {
        // التحقق من وجود المفاتيح الأجنبية أولاً
        $foreignKeys = $pdo->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'sphinx_fire_cms' 
            AND TABLE_NAME = 'locations' 
            AND REFERENCED_TABLE_NAME = 'users'
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('locations_ibfk_1', $foreignKeys)) {
            $sql = "ALTER TABLE `locations` ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL";
            $pdo->exec($sql);
        }
        
        if (!in_array('locations_ibfk_2', $foreignKeys)) {
            $sql = "ALTER TABLE `locations` ADD CONSTRAINT `locations_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL";
            $pdo->exec($sql);
        }
        
        echo "<div class='step success'>✅ تم إضافة المفاتيح الأجنبية لجدول locations</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة المفاتيح الأجنبية لجدول locations: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 3.2 إضافة المفاتيح الأجنبية لجدول blog_posts
    $totalSteps++;
    try {
        // التحقق من وجود المفاتيح الأجنبية أولاً
        $foreignKeys = $pdo->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'sphinx_fire_cms' 
            AND TABLE_NAME = 'blog_posts' 
            AND REFERENCED_TABLE_NAME = 'users'
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('blog_posts_ibfk_3', $foreignKeys)) {
            $sql = "ALTER TABLE `blog_posts` ADD CONSTRAINT `blog_posts_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL";
            $pdo->exec($sql);
        }
        
        if (!in_array('blog_posts_ibfk_4', $foreignKeys)) {
            $sql = "ALTER TABLE `blog_posts` ADD CONSTRAINT `blog_posts_ibfk_4` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL";
            $pdo->exec($sql);
        }
        
        echo "<div class='step success'>✅ تم إضافة المفاتيح الأجنبية لجدول blog_posts</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة المفاتيح الأجنبية لجدول blog_posts: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // 3.3 إضافة المفاتيح الأجنبية لجدول testimonials
    $totalSteps++;
    try {
        // التحقق من وجود المفاتيح الأجنبية أولاً
        $foreignKeys = $pdo->query("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = 'sphinx_fire_cms' 
            AND TABLE_NAME = 'testimonials' 
            AND REFERENCED_TABLE_NAME = 'users'
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('testimonials_ibfk_5', $foreignKeys)) {
            $sql = "ALTER TABLE `testimonials` ADD CONSTRAINT `testimonials_ibfk_5` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL";
            $pdo->exec($sql);
        }
        
        echo "<div class='step success'>✅ تم إضافة المفاتيح الأجنبية لجدول testimonials</div>";
        $successSteps++;
        
    } catch (Exception $e) {
        echo "<div class='step error'>❌ خطأ في إضافة المفاتيح الأجنبية لجدول testimonials: " . $e->getMessage() . "</div>";
        $errorSteps++;
    }
    
    // ========================================
    // الخطوة 4: التحقق من النتائج
    // ========================================
    echo "<div class='section'>
        <h2>✅ الخطوة 4: التحقق من النتائج</h2>";
    
    // التحقق من الجداول الجديدة
    $newTables = ['admin_roles', 'admin_users', 'admin_logs', 'admin_notifications'];
    foreach ($newTables as $table) {
        $totalSteps++;
        try {
            $result = $pdo->query("SHOW TABLES LIKE '$table'")->fetch();
            if ($result) {
                echo "<div class='step success'>✅ جدول $table موجود</div>";
                $successSteps++;
            } else {
                echo "<div class='step error'>❌ جدول $table غير موجود</div>";
                $errorSteps++;
            }
        } catch (Exception $e) {
            echo "<div class='step error'>❌ خطأ في التحقق من جدول $table: " . $e->getMessage() . "</div>";
            $errorSteps++;
        }
    }
    
    // التحقق من الحقول الجديدة
    $tablesToCheck = [
        'locations' => ['created_by', 'updated_by', 'is_featured', 'meta_title'],
        'pages' => ['is_featured', 'meta_title', 'meta_description'],
        'services' => ['meta_title', 'meta_description'],
        'projects' => ['meta_title', 'meta_description'],
        'blog_posts' => ['created_by', 'updated_by', 'meta_title'],
        'testimonials' => ['updated_by', 'meta_title', 'meta_description']
    ];
    
    foreach ($tablesToCheck as $table => $fields) {
        foreach ($fields as $field) {
            $totalSteps++;
            try {
                $result = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$field'")->fetch();
                if ($result) {
                    echo "<div class='step success'>✅ حقل $field موجود في جدول $table</div>";
                    $successSteps++;
                } else {
                    echo "<div class='step error'>❌ حقل $field غير موجود في جدول $table</div>";
                    $errorSteps++;
                }
            } catch (Exception $e) {
                echo "<div class='step error'>❌ خطأ في التحقق من حقل $field في جدول $table: " . $e->getMessage() . "</div>";
                $errorSteps++;
            }
        }
    }
    
    // ========================================
    // ملخص النتائج
    // ========================================
    echo "<div class='section'>
        <h2>📊 ملخص النتائج</h2>
        <div class='info'>
            <p><strong>إجمالي الخطوات:</strong> $totalSteps</p>
            <p><strong>الخطوات الناجحة:</strong> $successSteps</p>
            <p><strong>الخطوات الفاشلة:</strong> $errorSteps</p>
            <p><strong>نسبة النجاح:</strong> " . round(($successSteps / $totalSteps) * 100, 2) . "%</p>
        </div>";
    
    if ($errorSteps == 0) {
        echo "<div class='success'>
            <h3>🎉 تم إصلاح قاعدة البيانات بنجاح!</h3>
            <p>جميع الجداول والحقول المطلوبة للأدمن تم إنشاؤها بنجاح.</p>
            <p><strong>بيانات تسجيل الدخول:</strong></p>
            <ul>
                <li>اسم المستخدم: fireadmin</li>
                <li>كلمة المرور: Fire@2024</li>
                <li>البريد الإلكتروني: fireadmin@sphinxfire.com</li>
            </ul>
        </div>";
    } else {
        echo "<div class='warning'>
            <h3>⚠️ تم إصلاح قاعدة البيانات مع بعض الأخطاء</h3>
            <p>يرجى مراجعة الأخطاء أعلاه وإعادة تشغيل السكريبت إذا لزم الأمر.</p>
        </div>";
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