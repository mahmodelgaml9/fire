-- =====================================================
-- جداول لوحة التحكم الأساسية - Sphinx Fire CMS
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
-- إدخال البيانات الأولية
-- =====================================================

-- إدخال الأدوار الأساسية
INSERT INTO `admin_roles` (`name`, `description`, `permissions`) VALUES
('super_admin', 'مدير النظام الكامل', '{"all": true}'),
('admin', 'مدير النظام', '{"dashboard": true, "pages": {"read": true, "write": true, "delete": true}, "services": {"read": true, "write": true, "delete": true}, "projects": {"read": true, "write": true, "delete": true}, "blog": {"read": true, "write": true, "delete": true}, "testimonials": {"read": true, "write": true, "delete": true}, "locations": {"read": true, "write": true, "delete": true}, "settings": {"read": true, "write": true}, "users": {"read": true, "write": true, "delete": true}}'),
('editor', 'محرر المحتوى', '{"dashboard": true, "pages": {"read": true, "write": true}, "services": {"read": true, "write": true}, "projects": {"read": true, "write": true}, "blog": {"read": true, "write": true}, "testimonials": {"read": true, "write": true}, "locations": {"read": true, "write": true}}'),
('author', 'كاتب المحتوى', '{"dashboard": true, "pages": {"read": true, "write": true}, "blog": {"read": true, "write": true}}'),
('contributor', 'مساهم', '{"dashboard": true, "blog": {"read": true, "write": true}}');

-- إدخال مستخدم مدير النظام الافتراضي (كلمة المرور: admin123)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `first_name`, `last_name`, `role_id`) VALUES
('admin', 'admin@sphinxfire.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير', 'النظام', 1);

-- =====================================================
-- إنهاء الملف
-- =====================================================

SELECT 'تم إنشاء جداول لوحة التحكم الأساسية بنجاح!' AS message; 