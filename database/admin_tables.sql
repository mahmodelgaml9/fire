-- =====================================================
-- جداول لوحة التحكم - Sphinx Fire CMS
-- =====================================================

-- =====================================================
-- جداول المستخدمين والأدوار
-- =====================================================

-- جدول أدوار المستخدمين
CREATE TABLE IF NOT EXISTS `admin_roles` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    `permissions` JSON,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- جدول مستخدمي لوحة التحكم
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `role_id` INT NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `admin_roles`(`id`) ON DELETE RESTRICT,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`),
    INDEX `idx_role_id` (`role_id`)
);

-- جدول صلاحيات المستخدمين
CREATE TABLE IF NOT EXISTS `admin_permissions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `role_id` INT NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `admin_roles`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_role_module_action` (`role_id`, `module`, `action`)
);

-- =====================================================
-- جداول السجلات والإشعارات
-- =====================================================

-- جدول سجل عمليات الأدمن
CREATE TABLE IF NOT EXISTS `admin_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `data` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_module` (`module`),
    INDEX `idx_created_at` (`created_at`)
);

-- جدول سجل النشاطات
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `activity_type` VARCHAR(100) NOT NULL,
    `activity_data` JSON,
    `ip_address` VARCHAR(45),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_activity_type` (`activity_type`),
    INDEX `idx_created_at` (`created_at`)
);

-- جدول إشعارات الأدمن
CREATE TABLE IF NOT EXISTS `admin_notifications` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`)
);

-- =====================================================
-- جداول لوحة التحكم الإضافية
-- =====================================================

-- جدول ويدجت لوحة التحكم
CREATE TABLE IF NOT EXISTS `admin_dashboard_widgets` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `widget_type` VARCHAR(50) NOT NULL,
    `widget_config` JSON,
    `position` INT DEFAULT 0,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_widget_type` (`widget_type`)
);

-- جدول الإجراءات السريعة
CREATE TABLE IF NOT EXISTS `admin_quick_actions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `action_name` VARCHAR(100) NOT NULL,
    `action_url` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(50),
    `color` VARCHAR(20) DEFAULT 'primary',
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`)
);

-- =====================================================
-- جداول المحتوى المتقدمة
-- =====================================================

-- جدول إصدارات المحتوى
CREATE TABLE IF NOT EXISTS `content_versions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `content_type` VARCHAR(50) NOT NULL,
    `content_id` INT NOT NULL,
    `version_number` INT NOT NULL,
    `content_data` JSON NOT NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_content_type_id` (`content_type`, `content_id`),
    INDEX `idx_version_number` (`version_number`)
);

-- جدول الموافقة على المحتوى
CREATE TABLE IF NOT EXISTS `content_approvals` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `content_type` VARCHAR(50) NOT NULL,
    `content_id` INT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `requested_by` INT NOT NULL,
    `approved_by` INT NULL,
    `comments` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`requested_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`approved_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
    INDEX `idx_content_type_id` (`content_type`, `content_id`),
    INDEX `idx_status` (`status`)
);

-- جدول جدولة المحتوى
CREATE TABLE IF NOT EXISTS `content_scheduling` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `content_type` VARCHAR(50) NOT NULL,
    `content_id` INT NOT NULL,
    `scheduled_at` TIMESTAMP NOT NULL,
    `action` ENUM('publish', 'unpublish', 'archive') NOT NULL,
    `created_by` INT NOT NULL,
    `is_executed` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_content_type_id` (`content_type`, `content_id`),
    INDEX `idx_scheduled_at` (`scheduled_at`),
    INDEX `idx_is_executed` (`is_executed`)
);

-- =====================================================
-- جداول التحليلات والتحسينات
-- =====================================================

-- جدول تحليلات المحتوى
CREATE TABLE IF NOT EXISTS `content_analytics` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `content_type` VARCHAR(50) NOT NULL,
    `content_id` INT NOT NULL,
    `views` INT DEFAULT 0,
    `unique_views` INT DEFAULT 0,
    `time_spent` INT DEFAULT 0,
    `bounce_rate` DECIMAL(5,2) DEFAULT 0,
    `conversion_rate` DECIMAL(5,2) DEFAULT 0,
    `date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_content_type_id` (`content_type`, `content_id`),
    INDEX `idx_date` (`date`)
);

-- جدول تحسينات SEO
CREATE TABLE IF NOT EXISTS `seo_optimizations` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `content_type` VARCHAR(50) NOT NULL,
    `content_id` INT NOT NULL,
    `seo_score` INT DEFAULT 0,
    `title_length` INT DEFAULT 0,
    `description_length` INT DEFAULT 0,
    `keyword_density` JSON,
    `internal_links` INT DEFAULT 0,
    `external_links` INT DEFAULT 0,
    `image_alt_tags` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_content_type_id` (`content_type`, `content_id`),
    INDEX `idx_seo_score` (`seo_score`)
);

-- =====================================================
-- جداول النظام والأمان
-- =====================================================

-- جدول سجل النسخ الاحتياطي
CREATE TABLE IF NOT EXISTS `backup_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `backup_type` ENUM('full', 'partial', 'files', 'database') NOT NULL,
    `file_path` VARCHAR(255),
    `file_size` BIGINT,
    `status` ENUM('success', 'failed', 'in_progress') NOT NULL,
    `error_message` TEXT,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_backup_type` (`backup_type`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
);

-- جدول صحة النظام
CREATE TABLE IF NOT EXISTS `system_health` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `metric_name` VARCHAR(100) NOT NULL,
    `metric_value` DECIMAL(10,2) NOT NULL,
    `metric_unit` VARCHAR(20),
    `status` ENUM('healthy', 'warning', 'critical') DEFAULT 'healthy',
    `details` JSON,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_metric_name` (`metric_name`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
);

-- جدول مفاتيح API
CREATE TABLE IF NOT EXISTS `api_keys` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `key_name` VARCHAR(100) NOT NULL,
    `api_key` VARCHAR(255) NOT NULL UNIQUE,
    `permissions` JSON,
    `is_active` BOOLEAN DEFAULT TRUE,
    `last_used` TIMESTAMP NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_api_key` (`api_key`),
    INDEX `idx_is_active` (`is_active`)
);

-- جدول نقاط نهاية Webhook
CREATE TABLE IF NOT EXISTS `webhook_endpoints` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `events` JSON NOT NULL,
    `secret_key` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `last_triggered` TIMESTAMP NULL,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_is_active` (`is_active`)
);

-- =====================================================
-- جداول التواصل والرسائل
-- =====================================================

-- جدول قوالب البريد الإلكتروني
CREATE TABLE IF NOT EXISTS `email_templates` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `variables` JSON,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`created_by`) REFERENCES `admin_users`(`id`) ON DELETE CASCADE,
    INDEX `idx_name` (`name`),
    INDEX `idx_is_active` (`is_active`)
);

-- جدول سجل البريد الإلكتروني
CREATE TABLE IF NOT EXISTS `email_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `template_id` INT NULL,
    `to_email` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `body` TEXT NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') NOT NULL,
    `error_message` TEXT,
    `sent_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`template_id`) REFERENCES `email_templates`(`id`) ON DELETE SET NULL,
    INDEX `idx_to_email` (`to_email`),
    INDEX `idx_status` (`status`),
    INDEX `idx_sent_at` (`sent_at`)
);

-- جدول سجل الرسائل النصية
CREATE TABLE IF NOT EXISTS `sms_logs` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `phone_number` VARCHAR(20) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('sent', 'failed', 'pending') NOT NULL,
    `error_message` TEXT,
    `sent_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_phone_number` (`phone_number`),
    INDEX `idx_status` (`status`),
    INDEX `idx_sent_at` (`sent_at`)
);

-- =====================================================
-- إضافة حقول جديدة للجداول الموجودة
-- =====================================================

-- إضافة حقول تتبع للمحتوى في جدول pages (بدون الأعمدة الموجودة)
ALTER TABLE `pages` 
ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`,
ADD COLUMN `updated_by` INT DEFAULT NULL AFTER `created_by`,
ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `updated_by`,
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`,
ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`,
ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`,
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_updated_by` (`updated_by`),
ADD INDEX `idx_is_featured` (`is_featured`);

-- إضافة حقول تتبع للمحتوى في جدول services
ALTER TABLE `services` 
ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`,
ADD COLUMN `updated_by` INT DEFAULT NULL AFTER `created_by`,
ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `updated_by`,
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`,
ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`,
ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`,
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_updated_by` (`updated_by`),
ADD INDEX `idx_is_featured` (`is_featured`);

-- إضافة حقول تتبع للمحتوى في جدول projects
ALTER TABLE `projects` 
ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`,
ADD COLUMN `updated_by` INT DEFAULT NULL AFTER `created_by`,
ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `updated_by`,
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`,
ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`,
ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`,
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_updated_by` (`updated_by`),
ADD INDEX `idx_is_featured` (`is_featured`);

-- إضافة حقول تتبع للمحتوى في جدول blog_posts
ALTER TABLE `blog_posts` 
ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`,
ADD COLUMN `updated_by` INT DEFAULT NULL AFTER `created_by`,
ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `updated_by`,
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`,
ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`,
ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`,
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_updated_by` (`updated_by`),
ADD INDEX `idx_is_featured` (`is_featured`);

-- إضافة حقول تتبع للمحتوى في جدول testimonials
ALTER TABLE `testimonials` 
ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`,
ADD COLUMN `updated_by` INT DEFAULT NULL AFTER `created_by`,
ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `updated_by`,
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`,
ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`,
ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`,
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_updated_by` (`updated_by`),
ADD INDEX `idx_is_featured` (`is_featured`);

-- إضافة حقول تتبع للمحتوى في جدول locations
ALTER TABLE `locations` 
ADD COLUMN `created_by` INT DEFAULT NULL AFTER `updated_at`,
ADD COLUMN `updated_by` INT DEFAULT NULL AFTER `created_by`,
ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `updated_by`,
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `is_featured`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `og_title` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `og_description` TEXT DEFAULT NULL AFTER `og_title`,
ADD COLUMN `og_image` VARCHAR(255) DEFAULT NULL AFTER `og_description`,
ADD COLUMN `canonical_url` VARCHAR(255) DEFAULT NULL AFTER `og_image`,
ADD INDEX `idx_created_by` (`created_by`),
ADD INDEX `idx_updated_by` (`updated_by`),
ADD INDEX `idx_is_featured` (`is_featured`);

-- =====================================================
-- إدخال البيانات الأولية
-- =====================================================

-- إدخال الأدوار الأساسية
INSERT INTO `admin_roles` (`name`, `description`, `permissions`) VALUES
('super_admin', 'مدير النظام الكامل', '{"all": true}'),
('admin', 'مدير النظام', '{"dashboard": true, "pages": {"read": true, "write": true, "delete": true}, "services": {"read": true, "write": true, "delete": true}, "projects": {"read": true, "write": true, "delete": true}, "blog": {"read": true, "write": true, "delete": true}, "testimonials": {"read": true, "write": true, "delete": true}, "locations": {"read": true, "write": true, "delete": true}, "settings": {"read": true, "write": true}, "users": {"read": true, "write": true, "delete": true}}'),
('editor', 'محرر المحتوى', '{"dashboard": true, "pages": {"read": true, "write": true}, "services": {"read": true, "write": true}, "projects": {"read": true, "write": true}, "blog": {"read": true, "write": true}, "testimonials": {"read": true, "write": true}, "locations": {"read": true, "write": true}}'),
('author', 'كاتب المحتوى', '{"dashboard": true, "pages": {"read": true, "write": true}, "blog": {"read": true, "write": true}}'),
('contributor', 'مساهم', '{"dashboard": true, "blog": {"read": true, "write": true}}');

-- إدخال مستخدم مدير النظام الافتراضي
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `first_name`, `last_name`, `role_id`) VALUES
('admin', 'admin@sphinxfire.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير', 'النظام', 1);

-- إدخال قوالب البريد الإلكتروني الأساسية
INSERT INTO `email_templates` (`name`, `subject`, `body`, `variables`, `created_by`) VALUES
('welcome_email', 'مرحباً بك في Sphinx Fire', '<h1>مرحباً {{first_name}}</h1><p>شكراً لك على الانضمام إلينا!</p>', '["first_name", "last_name", "username"]', 1),
('password_reset', 'إعادة تعيين كلمة المرور', '<h1>إعادة تعيين كلمة المرور</h1><p>انقر على الرابط التالي لإعادة تعيين كلمة المرور: {{reset_link}}</p>', '["reset_link", "username"]', 1),
('notification', 'إشعار جديد', '<h1>{{title}}</h1><p>{{message}}</p>', '["title", "message", "username"]', 1);

-- =====================================================
-- إنشاء الفهارس الإضافية
-- =====================================================

-- فهارس للأداء
CREATE INDEX `idx_admin_logs_user_action` ON `admin_logs` (`user_id`, `action`);
CREATE INDEX `idx_admin_logs_module_created` ON `admin_logs` (`module`, `created_at`);
CREATE INDEX `idx_content_analytics_date` ON `content_analytics` (`date`);
CREATE INDEX `idx_seo_optimizations_score` ON `seo_optimizations` (`seo_score`);
CREATE INDEX `idx_system_health_metric_status` ON `system_health` (`metric_name`, `status`);

-- =====================================================
-- إنهاء الملف
-- =====================================================

-- رسالة نجاح
SELECT 'تم إنشاء جداول لوحة التحكم بنجاح!' AS message; 