-- =====================================================
-- إضافة حقول جديدة للجداول الموجودة - نسخة مبسطة
-- =====================================================

-- إضافة حقول تتبع للمحتوى في جدول pages
-- التحقق من وجود العمود قبل إضافته
SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'created_by') = 0,
    'ALTER TABLE `pages` ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`',
    'SELECT "Column created_by already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'is_featured') = 0,
    'ALTER TABLE `pages` ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`',
    'SELECT "Column is_featured already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'meta_title') = 0,
    'ALTER TABLE `pages` ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`',
    'SELECT "Column meta_title already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'meta_description') = 0,
    'ALTER TABLE `pages` ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`',
    'SELECT "Column meta_description already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'meta_keywords') = 0,
    'ALTER TABLE `pages` ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`',
    'SELECT "Column meta_keywords already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'og_title') = 0,
    'ALTER TABLE `pages` ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`',
    'SELECT "Column og_title already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'og_description') = 0,
    'ALTER TABLE `pages` ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`',
    'SELECT "Column og_description already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'og_image') = 0,
    'ALTER TABLE `pages` ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`',
    'SELECT "Column og_image already exists in pages table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'pages' 
     AND COLUMN_NAME = 'canonical_url') = 0,
    'ALTER TABLE `pages` ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`',
    'SELECT "Column canonical_url already exists in pages table"'
) AS sql_command;

-- إضافة حقول مماثلة لجدول services
SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'services' 
     AND COLUMN_NAME = 'created_by') = 0,
    'ALTER TABLE `services` ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`',
    'SELECT "Column created_by already exists in services table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'services' 
     AND COLUMN_NAME = 'is_featured') = 0,
    'ALTER TABLE `services` ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`',
    'SELECT "Column is_featured already exists in services table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'services' 
     AND COLUMN_NAME = 'meta_title') = 0,
    'ALTER TABLE `services` ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`',
    'SELECT "Column meta_title already exists in services table"'
) AS sql_command;

-- إضافة حقول مماثلة لجدول projects
SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'projects' 
     AND COLUMN_NAME = 'created_by') = 0,
    'ALTER TABLE `projects` ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`',
    'SELECT "Column created_by already exists in projects table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'projects' 
     AND COLUMN_NAME = 'is_featured') = 0,
    'ALTER TABLE `projects` ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`',
    'SELECT "Column is_featured already exists in projects table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'projects' 
     AND COLUMN_NAME = 'meta_title') = 0,
    'ALTER TABLE `projects` ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`',
    'SELECT "Column meta_title already exists in projects table"'
) AS sql_command;

-- إضافة حقول مماثلة لجدول blog_posts
SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'blog_posts' 
     AND COLUMN_NAME = 'created_by') = 0,
    'ALTER TABLE `blog_posts` ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`',
    'SELECT "Column created_by already exists in blog_posts table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'blog_posts' 
     AND COLUMN_NAME = 'is_featured') = 0,
    'ALTER TABLE `blog_posts` ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`',
    'SELECT "Column is_featured already exists in blog_posts table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'blog_posts' 
     AND COLUMN_NAME = 'meta_title') = 0,
    'ALTER TABLE `blog_posts` ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`',
    'SELECT "Column meta_title already exists in blog_posts table"'
) AS sql_command;

-- إضافة حقول مماثلة لجدول testimonials
SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'testimonials' 
     AND COLUMN_NAME = 'created_by') = 0,
    'ALTER TABLE `testimonials` ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`',
    'SELECT "Column created_by already exists in testimonials table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'testimonials' 
     AND COLUMN_NAME = 'is_featured') = 0,
    'ALTER TABLE `testimonials` ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`',
    'SELECT "Column is_featured already exists in testimonials table"'
) AS sql_command;

-- إضافة حقول مماثلة لجدول locations
SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'locations' 
     AND COLUMN_NAME = 'created_by') = 0,
    'ALTER TABLE `locations` ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`',
    'SELECT "Column created_by already exists in locations table"'
) AS sql_command;

SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'locations' 
     AND COLUMN_NAME = 'is_featured') = 0,
    'ALTER TABLE `locations` ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`',
    'SELECT "Column is_featured already exists in locations table"'
) AS sql_command;

-- رسالة نجاح
SELECT 'تم إضافة الحقول الجديدة بنجاح!' AS message; 