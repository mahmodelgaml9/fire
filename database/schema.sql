-- ===== Sphinx Fire CMS Database =====
-- Complete Content Management System with Multi-language Support
-- Created: 2024-07-08
-- Version: 1.0

SET FOREIGN_KEY_CHECKS = 0;
DROP DATABASE IF EXISTS sphinx_fire_cms;
CREATE DATABASE sphinx_fire_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sphinx_fire_cms;

-- ===== CORE SYSTEM TABLES =====

-- Languages Table
CREATE TABLE languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(5) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    native_name VARCHAR(50) NOT NULL,
    direction ENUM('ltr', 'rtl') DEFAULT 'ltr',
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default languages
INSERT INTO languages (code, name, native_name, direction, is_default, is_active) VALUES
('en', 'English', 'English', 'ltr', TRUE, TRUE),
('ar', 'Arabic', 'العربية', 'rtl', FALSE, TRUE);

-- Users & Authentication
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role ENUM('super_admin', 'admin', 'editor', 'author', 'contributor') DEFAULT 'contributor',
    avatar_url VARCHAR(255),
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, email, password_hash, first_name, last_name, role, is_active) VALUES
('admin', 'admin@sphinxfire.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', 'super_admin', TRUE);

-- User Sessions
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===== CONTENT MANAGEMENT TABLES =====

-- Pages Management
CREATE TABLE pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL,
    template VARCHAR(50) DEFAULT 'default',
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_homepage BOOLEAN DEFAULT FALSE,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    created_by INT NOT NULL,
    updated_by INT,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    FOREIGN KEY (parent_id) REFERENCES pages(id) ON DELETE SET NULL,
    UNIQUE KEY unique_slug (slug)
);

-- Page Translations
CREATE TABLE page_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    content LONGTEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    og_title VARCHAR(255),
    og_description TEXT,
    og_image VARCHAR(255),
    canonical_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_page_language (page_id, language_id)
);

-- Sections Management (for dynamic page sections)
CREATE TABLE sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_id INT NOT NULL,
    section_type VARCHAR(50) NOT NULL, -- hero, services, about, testimonials, etc.
    section_key VARCHAR(100) NOT NULL, -- unique identifier for section
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    settings JSON, -- section-specific settings
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_section (page_id, section_key)
);

-- Section Translations
CREATE TABLE section_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    content LONGTEXT,
    button_text VARCHAR(100),
    button_url VARCHAR(255),
    background_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_section_language (section_id, language_id)
);

-- ===== SERVICES MANAGEMENT =====

-- Service Categories
CREATE TABLE service_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(100),
    color VARCHAR(7) DEFAULT '#DC2626',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Service Category Translations
CREATE TABLE service_category_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    language_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_category_language (category_id, language_id)
);

-- Services
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(100),
    featured_image VARCHAR(255),
    gallery JSON, -- array of image URLs
    price_range VARCHAR(50),
    duration VARCHAR(50),
    sort_order INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Service Translations
CREATE TABLE service_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    language_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    short_description TEXT,
    full_description LONGTEXT,
    features JSON, -- array of feature strings
    benefits JSON, -- array of benefit strings
    specifications JSON, -- technical specifications
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_service_language (service_id, language_id)
);

-- ===== PROJECTS MANAGEMENT =====

-- Project Categories
CREATE TABLE project_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(7) DEFAULT '#DC2626',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Project Category Translations
CREATE TABLE project_category_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    language_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES project_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_project_category_language (category_id, language_id)
);

-- Projects
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    client_name VARCHAR(100),
    client_logo VARCHAR(255),
    location VARCHAR(100),
    project_date DATE,
    duration_days INT,
    budget_range VARCHAR(50),
    status ENUM('completed', 'ongoing', 'planned') DEFAULT 'completed',
    featured_image VARCHAR(255),
    gallery JSON, -- array of image URLs
    services_provided JSON, -- array of service IDs
    team_size INT,
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_by INT NOT NULL,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES project_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Project Translations
CREATE TABLE project_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    description LONGTEXT,
    challenge TEXT,
    solution TEXT,
    results TEXT,
    testimonial TEXT,
    testimonial_author VARCHAR(100),
    testimonial_position VARCHAR(100),
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_project_language (project_id, language_id)
);

-- ===== BLOG MANAGEMENT =====

-- Blog Categories
CREATE TABLE blog_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(7) DEFAULT '#DC2626',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog Category Translations
CREATE TABLE blog_category_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    language_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_blog_category_language (category_id, language_id)
);

-- Blog Posts
CREATE TABLE blog_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    author_id INT NOT NULL,
    featured_image VARCHAR(255),
    reading_time INT, -- in minutes
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    likes_count INT DEFAULT 0,
    shares_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Blog Post Translations
CREATE TABLE blog_post_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    excerpt TEXT,
    content LONGTEXT,
    tags JSON, -- array of tag strings
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    og_title VARCHAR(255),
    og_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_post_language (post_id, language_id)
);

-- ===== MEDIA MANAGEMENT =====

-- Media Library
CREATE TABLE media (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT NOT NULL, -- in bytes
    width INT NULL, -- for images
    height INT NULL, -- for images
    file_path VARCHAR(500) NOT NULL,
    url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    description TEXT,
    uploaded_by INT NOT NULL,
    folder VARCHAR(100) DEFAULT 'uploads',
    is_optimized BOOLEAN DEFAULT FALSE,
    optimization_data JSON, -- compression info, formats available, etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Media Translations (for alt text, captions in different languages)
CREATE TABLE media_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    media_id INT NOT NULL,
    language_id INT NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_media_language (media_id, language_id)
);

-- ===== LOCATION-BASED CONTENT =====

-- Cities/Locations
CREATE TABLE locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    type ENUM('city', 'region', 'industrial_zone') DEFAULT 'city',
    coordinates POINT, -- GPS coordinates
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Location Translations
CREATE TABLE location_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location_id INT NOT NULL,
    language_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    local_keywords TEXT, -- SEO keywords specific to this location
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_location_language (location_id, language_id)
);

-- Location-specific Content
CREATE TABLE location_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    location_id INT NOT NULL,
    content_type ENUM('advantage', 'service_highlight', 'testimonial', 'project_example') NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    data JSON, -- flexible data storage for different content types
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);

-- Location Content Translations
CREATE TABLE location_content_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    content_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255),
    description TEXT,
    additional_data JSON, -- language-specific additional data
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES location_content(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_location_content_language (content_id, language_id)
);

-- ===== TESTIMONIALS & REVIEWS =====

-- Testimonials
CREATE TABLE testimonials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(100) NOT NULL,
    client_position VARCHAR(100),
    client_company VARCHAR(100),
    client_avatar VARCHAR(255),
    rating INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    project_id INT NULL, -- link to specific project
    service_id INT NULL, -- link to specific service
    location_id INT NULL, -- link to specific location
    is_featured BOOLEAN DEFAULT FALSE,
    is_published BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Testimonial Translations
CREATE TABLE testimonial_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    testimonial_id INT NOT NULL,
    language_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (testimonial_id) REFERENCES testimonials(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_testimonial_language (testimonial_id, language_id)
);

-- ===== CONTACT & LEADS MANAGEMENT =====

-- Contact Forms
CREATE TABLE contact_forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    form_name VARCHAR(100) NOT NULL,
    form_key VARCHAR(100) NOT NULL UNIQUE,
    fields JSON NOT NULL, -- form field definitions
    email_recipients JSON, -- array of email addresses
    success_message TEXT,
    redirect_url VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact Submissions
CREATE TABLE contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    form_id INT NOT NULL,
    data JSON NOT NULL, -- submitted form data
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    assigned_to INT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (form_id) REFERENCES contact_forms(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Lead Tracking
CREATE TABLE leads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    source VARCHAR(100), -- website, phone, email, referral, etc.
    campaign VARCHAR(100), -- marketing campaign identifier
    contact_name VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    company_name VARCHAR(100),
    service_interest VARCHAR(100),
    location VARCHAR(100),
    budget_range VARCHAR(50),
    urgency ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('new', 'contacted', 'qualified', 'proposal', 'won', 'lost') DEFAULT 'new',
    assigned_to INT NULL,
    estimated_value DECIMAL(10,2),
    notes TEXT,
    follow_up_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- ===== MAINTENANCE & OPTIMIZATION =====

-- System Maintenance Log
CREATE TABLE maintenance_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_type ENUM('backup', 'optimization', 'update', 'cleanup', 'security') NOT NULL,
    task_name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('pending', 'running', 'completed', 'failed') DEFAULT 'pending',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    error_message TEXT,
    performed_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Performance Monitoring
CREATE TABLE performance_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_url VARCHAR(500) NOT NULL,
    metric_type ENUM('lcp', 'fid', 'cls', 'ttfb', 'load_time') NOT NULL,
    value DECIMAL(10,3) NOT NULL,
    device_type ENUM('desktop', 'mobile', 'tablet') DEFAULT 'desktop',
    user_agent TEXT,
    connection_type VARCHAR(50),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page_metric (page_url, metric_type),
    INDEX idx_timestamp (timestamp)
);

-- SEO Monitoring
CREATE TABLE seo_metrics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_url VARCHAR(500) NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    title_length INT,
    meta_description_length INT,
    h1_count INT,
    h2_count INT,
    image_count INT,
    images_without_alt INT,
    internal_links INT,
    external_links INT,
    word_count INT,
    readability_score DECIMAL(5,2),
    lighthouse_seo_score INT,
    last_crawled TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (language_code) REFERENCES languages(code),
    INDEX idx_page_language (page_url, language_code),
    INDEX idx_last_crawled (last_crawled)
);

-- ===== ANALYTICS & TRACKING =====

-- Page Views
CREATE TABLE page_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_url VARCHAR(500) NOT NULL,
    referrer VARCHAR(500),
    user_agent TEXT,
    ip_address VARCHAR(45),
    session_id VARCHAR(128),
    user_id INT NULL,
    device_type ENUM('desktop', 'mobile', 'tablet'),
    browser VARCHAR(50),
    os VARCHAR(50),
    country VARCHAR(2),
    city VARCHAR(100),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_page_url (page_url),
    INDEX idx_viewed_at (viewed_at),
    INDEX idx_session (session_id)
);

-- Event Tracking
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_name VARCHAR(100) NOT NULL,
    event_category VARCHAR(50),
    event_data JSON,
    page_url VARCHAR(500),
    user_id INT NULL,
    session_id VARCHAR(128),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_name (event_name),
    INDEX idx_created_at (created_at),
    INDEX idx_session (session_id)
);

-- ===== SYSTEM SETTINGS =====

-- Global Settings
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'text') DEFAULT 'string',
    category VARCHAR(50) DEFAULT 'general',
    is_public BOOLEAN DEFAULT FALSE, -- can be accessed from frontend
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Setting Translations
CREATE TABLE setting_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL,
    language_id INT NOT NULL,
    setting_value LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE KEY unique_setting_language (setting_key, language_id)
);

-- ===== CACHE MANAGEMENT =====

-- Cache Storage
CREATE TABLE cache (
    cache_key VARCHAR(255) PRIMARY KEY,
    cache_value LONGTEXT NOT NULL,
    expiry_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_expiry (expiry_time)
);

-- ===== SAMPLE DATA INSERTION =====

-- Insert sample service categories
INSERT INTO service_categories (slug, icon, sort_order) VALUES
('firefighting-systems', 'fas fa-fire-extinguisher', 1),
('fire-alarms', 'fas fa-bell', 2),
('fire-extinguishers', 'fas fa-spray-can', 3),
('ppe-equipment', 'fas fa-hard-hat', 4),
('consulting', 'fas fa-clipboard-check', 5),
('maintenance', 'fas fa-tools', 6);

-- Insert service category translations
INSERT INTO service_category_translations (category_id, language_id, name, description, meta_title, meta_description) VALUES
(1, 1, 'Firefighting Systems', 'Complete firefighting systems with UL/FM certified equipment', 'Firefighting Systems - Sphinx Fire', 'Professional firefighting systems installation and maintenance services'),
(1, 2, 'أنظمة مكافحة الحريق', 'أنظمة مكافحة حريق متكاملة بمعدات معتمدة UL/FM', 'أنظمة مكافحة الحريق - سفينكس فاير', 'خدمات تركيب وصيانة أنظمة مكافحة الحريق المهنية'),
(2, 1, 'Fire Alarm Systems', 'Smart fire detection and alarm systems', 'Fire Alarm Systems - Sphinx Fire', 'Advanced fire alarm and detection systems for industrial facilities'),
(2, 2, 'أنظمة إنذار الحريق', 'أنظمة ذكية لكشف وإنذار الحريق', 'أنظمة إنذار الحريق - سفينكس فاير', 'أنظمة إنذار وكشف الحريق المتقدمة للمنشآت الصناعية');

-- Insert sample locations
INSERT INTO locations (slug, type, is_active, sort_order) VALUES
('sadat-city', 'industrial_zone', TRUE, 1),
('october-city', 'industrial_zone', TRUE, 2),
('quesna', 'industrial_zone', TRUE, 3),
('badr-city', 'industrial_zone', TRUE, 4),
('10th-ramadan', 'industrial_zone', TRUE, 5);

-- Insert location translations
INSERT INTO location_translations (location_id, language_id, name, description, meta_title, meta_description, local_keywords) VALUES
(1, 1, 'Sadat Industrial City', 'Fire protection services for Sadat Industrial City', 'Fire Protection Sadat City - Sphinx Fire', 'Professional fire safety services in Sadat Industrial City, Egypt', 'fire protection Sadat City, firefighting systems Sadat industrial zone'),
(1, 2, 'مدينة السادات الصناعية', 'خدمات الحماية من الحريق لمدينة السادات الصناعية', 'الحماية من الحريق مدينة السادات - سفينكس فاير', 'خدمات السلامة من الحريق المهنية في مدينة السادات الصناعية، مصر', 'الحماية من الحريق مدينة السادات، أنظمة مكافحة الحريق المنطقة الصناعية السادات');

-- Insert sample blog categories
INSERT INTO blog_categories (slug, sort_order) VALUES
('compliance', 1),
('fire-systems', 2),
('safety-tips', 3),
('industry-news', 4),
('case-studies', 5);

-- Insert blog category translations
INSERT INTO blog_category_translations (category_id, language_id, name, description, meta_title, meta_description) VALUES
(1, 1, 'Compliance', 'Fire safety compliance guides and regulations', 'Fire Safety Compliance - Sphinx Fire Blog', 'Expert guides on fire safety compliance and regulations'),
(1, 2, 'الامتثال', 'أدلة الامتثال للسلامة من الحريق واللوائح', 'الامتثال للسلامة من الحريق - مدونة سفينكس فاير', 'أدلة الخبراء حول الامتثال للسلامة من الحريق واللوائح');

-- Insert sample settings
INSERT INTO settings (setting_key, setting_value, setting_type, category, is_public, description) VALUES
('site_name', 'Sphinx Fire', 'string', 'general', TRUE, 'Website name'),
('site_tagline', 'Fire Safety Solutions', 'string', 'general', TRUE, 'Website tagline'),
('contact_phone', '+20 123 456 7890', 'string', 'contact', TRUE, 'Main contact phone number'),
('contact_email', 'info@sphinxfire.com', 'string', 'contact', TRUE, 'Main contact email'),
('office_address', 'Sadat City, Egypt', 'string', 'contact', TRUE, 'Office address'),
('google_analytics_id', '', 'string', 'analytics', FALSE, 'Google Analytics tracking ID'),
('facebook_pixel_id', '', 'string', 'analytics', FALSE, 'Facebook Pixel ID'),
('enable_cache', 'true', 'boolean', 'performance', FALSE, 'Enable caching system'),
('cache_duration', '3600', 'number', 'performance', FALSE, 'Cache duration in seconds'),
('enable_compression', 'true', 'boolean', 'performance', FALSE, 'Enable GZIP compression'),
('maintenance_mode', 'false', 'boolean', 'system', FALSE, 'Enable maintenance mode');

-- Insert setting translations
INSERT INTO setting_translations (setting_key, language_id, setting_value) VALUES
('site_name', 2, 'سفينكس فاير'),
('site_tagline', 2, 'حلول السلامة من الحريق'),
('office_address', 2, 'مدينة السادات، مصر');

-- Insert sample contact forms
INSERT INTO contact_forms (form_name, form_key, fields, email_recipients, success_message) VALUES
('General Contact', 'general_contact', 
'[{"name":"name","type":"text","required":true,"label":"Full Name"},{"name":"email","type":"email","required":true,"label":"Email"},{"name":"phone","type":"tel","required":false,"label":"Phone"},{"name":"company","type":"text","required":false,"label":"Company"},{"name":"message","type":"textarea","required":true,"label":"Message"}]',
'["info@sphinxfire.com"]',
'Thank you for your message. We will contact you within 24 hours.'),
('Site Visit Request', 'site_visit', 
'[{"name":"name","type":"text","required":true,"label":"Full Name"},{"name":"company","type":"text","required":true,"label":"Company Name"},{"name":"email","type":"email","required":true,"label":"Email"},{"name":"phone","type":"tel","required":true,"label":"Phone"},{"name":"facility_type","type":"select","required":true,"label":"Facility Type","options":["Manufacturing","Warehouse","Chemical","Retail","Office","Other"]},{"name":"location","type":"text","required":true,"label":"Location"},{"name":"urgent","type":"checkbox","required":false,"label":"Urgent Request"}]',
'["sales@sphinxfire.com","info@sphinxfire.com"]',
'Your site visit request has been submitted. Our team will contact you within 24 hours to schedule the visit.');

-- ===== INDEXES FOR PERFORMANCE =====

-- Page-related indexes
CREATE INDEX idx_pages_slug ON pages(slug);
CREATE INDEX idx_pages_status ON pages(status);
CREATE INDEX idx_pages_published_at ON pages(published_at);

-- Content indexes
CREATE INDEX idx_page_translations_language ON page_translations(language_id);
CREATE INDEX idx_sections_page_type ON sections(page_id, section_type);
CREATE INDEX idx_section_translations_language ON section_translations(language_id);

-- Service indexes
CREATE INDEX idx_services_category ON services(category_id);
CREATE INDEX idx_services_featured ON services(is_featured);
CREATE INDEX idx_services_active ON services(is_active);

-- Project indexes
CREATE INDEX idx_projects_category ON projects(category_id);
CREATE INDEX idx_projects_status ON projects(status);
CREATE INDEX idx_projects_featured ON projects(is_featured);

-- Blog indexes
CREATE INDEX idx_blog_posts_category ON blog_posts(category_id);
CREATE INDEX idx_blog_posts_status ON blog_posts(status);
CREATE INDEX idx_blog_posts_published ON blog_posts(published_at);

-- Media indexes
CREATE INDEX idx_media_mime_type ON media(mime_type);
CREATE INDEX idx_media_folder ON media(folder);

-- Analytics indexes
CREATE INDEX idx_page_views_url_date ON page_views(page_url, viewed_at);
CREATE INDEX idx_events_name_date ON events(event_name, created_at);

-- ===== VIEWS FOR COMMON QUERIES =====

-- Active pages with translations
CREATE VIEW active_pages AS
SELECT 
    p.id,
    p.slug,
    p.template,
    p.status,
    pt.language_id,
    pt.title,
    pt.meta_title,
    pt.meta_description,
    pt.content,
    pt.featured_image,
    p.created_at,
    p.updated_at
FROM pages p
JOIN page_translations pt ON p.id = pt.page_id
WHERE p.status = 'published';

-- Active services with translations
CREATE VIEW active_services AS
SELECT 
    s.id,
    s.slug,
    s.category_id,
    sc.slug as category_slug,
    st.language_id,
    st.name,
    st.short_description,
    st.full_description,
    s.featured_image,
    s.is_featured,
    s.sort_order
FROM services s
JOIN service_categories sc ON s.category_id = sc.id
JOIN service_translations st ON s.id = st.service_id
WHERE s.is_active = TRUE AND sc.is_active = TRUE;

-- Published blog posts with translations
CREATE VIEW published_blog_posts AS
SELECT 
    bp.id,
    bp.slug,
    bp.category_id,
    bc.slug as category_slug,
    bpt.language_id,
    bpt.title,
    bpt.excerpt,
    bpt.content,
    bp.featured_image,
    bp.reading_time,
    bp.views_count,
    bp.published_at,
    u.first_name as author_first_name,
    u.last_name as author_last_name
FROM blog_posts bp
JOIN blog_categories bc ON bp.category_id = bc.id
JOIN blog_post_translations bpt ON bp.id = bpt.post_id
JOIN users u ON bp.author_id = u.id
WHERE bp.status = 'published' AND bp.published_at <= NOW();

-- ===== STORED PROCEDURES =====

DELIMITER //

-- Procedure to get page content with all sections
CREATE PROCEDURE GetPageContent(IN page_slug VARCHAR(100), IN lang_code VARCHAR(5))
BEGIN
    DECLARE lang_id INT;
    
    -- Get language ID
    SELECT id INTO lang_id FROM languages WHERE code = lang_code AND is_active = TRUE;
    
    -- Get page info
    SELECT 
        p.id,
        p.slug,
        p.template,
        pt.title,
        pt.meta_title,
        pt.meta_description,
        pt.meta_keywords,
        pt.content,
        pt.featured_image,
        pt.og_title,
        pt.og_description,
        pt.og_image
    FROM pages p
    JOIN page_translations pt ON p.id = pt.page_id
    WHERE p.slug = page_slug AND pt.language_id = lang_id AND p.status = 'published';
    
    -- Get page sections
    SELECT 
        s.id,
        s.section_type,
        s.section_key,
        s.sort_order,
        s.settings,
        st.title,
        st.subtitle,
        st.content,
        st.button_text,
        st.button_url,
        st.background_image
    FROM sections s
    JOIN section_translations st ON s.id = st.section_id
    WHERE s.page_id = (SELECT id FROM pages WHERE slug = page_slug)
    AND st.language_id = lang_id
    AND s.is_active = TRUE
    ORDER BY s.sort_order;
END //

-- Procedure to update page views
CREATE PROCEDURE UpdatePageViews(
    IN page_url VARCHAR(500),
    IN referrer_url VARCHAR(500),
    IN user_agent_str TEXT,
    IN ip_addr VARCHAR(45),
    IN session_id_str VARCHAR(128),
    IN user_id_val INT,
    IN device_type_val ENUM('desktop', 'mobile', 'tablet'),
    IN browser_val VARCHAR(50),
    IN os_val VARCHAR(50),
    IN country_val VARCHAR(2),
    IN city_val VARCHAR(100)
)
BEGIN
    INSERT INTO page_views (
        page_url, referrer, user_agent, ip_address, session_id, 
        user_id, device_type, browser, os, country, city
    ) VALUES (
        page_url, referrer_url, user_agent_str, ip_addr, session_id_str,
        user_id_val, device_type_val, browser_val, os_val, country_val, city_val
    );
END //

-- Procedure to track events
CREATE PROCEDURE TrackEvent(
    IN event_name_val VARCHAR(100),
    IN event_category_val VARCHAR(50),
    IN event_data_val JSON,
    IN page_url_val VARCHAR(500),
    IN user_id_val INT,
    IN session_id_val VARCHAR(128),
    IN ip_address_val VARCHAR(45),
    IN user_agent_val TEXT
)
BEGIN
    INSERT INTO events (
        event_name, event_category, event_data, page_url,
        user_id, session_id, ip_address, user_agent
    ) VALUES (
        event_name_val, event_category_val, event_data_val, page_url_val,
        user_id_val, session_id_val, ip_address_val, user_agent_val
    );
END //

DELIMITER ;

-- ===== TRIGGERS =====

-- Update blog post views count
DELIMITER //
CREATE TRIGGER update_blog_views 
AFTER INSERT ON page_views
FOR EACH ROW
BEGIN
    IF NEW.page_url LIKE '/blog/%' THEN
        UPDATE blog_posts 
        SET views_count = views_count + 1 
        WHERE CONCAT('/blog/', slug) = NEW.page_url;
    END IF;
END //
DELIMITER ;

-- Auto-update timestamps
DELIMITER //
CREATE TRIGGER update_pages_timestamp 
BEFORE UPDATE ON pages
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;

-- ===== FINAL NOTES =====
/*
This database schema provides:

1. ✅ Complete multilingual content management (Arabic/English)
2. ✅ Full page and section management with dynamic content
3. ✅ Services, projects, and blog management
4. ✅ Media library with optimization tracking
5. ✅ Location-based content for city-specific pages
6. ✅ Testimonials and reviews system
7. ✅ Contact forms and lead management
8. ✅ Performance and SEO monitoring
9. ✅ Analytics and event tracking
10. ✅ System maintenance and optimization logs
11. ✅ Caching system
12. ✅ User management with roles
13. ✅ Settings management with translations

Key Features:
- RTL/LTR support for Arabic and English
- Comprehensive SEO management
- Performance monitoring
- Media optimization tracking
- Location-based content
- Advanced analytics
- Maintenance logging
- Cache management
- Security considerations

Usage:
1. Import this SQL file into MySQL/MariaDB
2. Configure your application to connect to this database
3. Use the provided views and procedures for common operations
4. Implement the frontend CMS interface
5. Set up automated maintenance tasks

The schema is designed to handle high traffic and provides
all necessary features for a professional fire safety
company website with full CMS capabilities.
*/