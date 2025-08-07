-- =====================================================
-- إضافة حقول جديدة للجداول الموجودة - طريقة مباشرة
-- =====================================================

-- إضافة حقول لجدول pages
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `created_by` INT DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `og_description` TEXT DEFAULT NULL AFTER `og_title`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`;
ALTER TABLE `pages` ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`;

-- إضافة حقول لجدول services
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `created_by` INT DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `og_description` TEXT DEFAULT NULL AFTER `og_title`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`;

-- إضافة حقول لجدول projects
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `created_by` INT DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `og_description` TEXT DEFAULT NULL AFTER `og_title`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`;
ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`;

-- إضافة حقول لجدول blog_posts
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `created_by` INT DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `og_description` TEXT DEFAULT NULL AFTER `og_title`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`;
ALTER TABLE `blog_posts` ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`;

-- إضافة حقول لجدول testimonials
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `created_by` INT DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `og_description` TEXT DEFAULT NULL AFTER `og_title`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`;
ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`;

-- إضافة حقول لجدول locations
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `created_by` INT DEFAULT NULL AFTER `updated_at`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `is_featured` BOOLEAN DEFAULT FALSE AFTER `created_by`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `og_description` TEXT DEFAULT NULL AFTER `og_title`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`;
ALTER TABLE `locations` ADD COLUMN IF NOT EXISTS `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`;

-- رسالة نجاح
SELECT 'تم إضافة الحقول الجديدة بنجاح!' AS message; 