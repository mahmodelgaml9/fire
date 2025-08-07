# Sphinx Fire - Complete Project Documentation

## 📋 Project Overview
**Sphinx Fire** is a comprehensive fire safety solutions provider website with multi-language support (English/Arabic) and a dynamic CMS system. The project serves industrial facilities in Egypt with complete fire protection services.

## 🏗️ Architecture Overview

### Frontend Structure
```
FIRE2/
├── index.html (Home Page)
├── about.html
├── services.html
├── projects.html
├── blog.html
├── contact.html
├── firefighting-systems.html
├── retargeting-landing.html
├── sadat-city-fire-protection.html
├── project-delta-paint.html
├── blog-article-civil-defense.html
├── style.css (Main Styles)
├── main.js (Main JavaScript)
├── performance-config.js
├── counter.js
├── build-optimize.js
├── manifest.json
├── sw.js (Service Worker)
├── package.json
├── scripts/
│   ├── main.js
│   ├── global-performance.js
│   ├── about.js
│   ├── blog.js
│   ├── blog-article.js
│   ├── city-landing.js
│   ├── cms-integration.js
│   ├── contact.js
│   ├── firefighting-systems.js
│   ├── project-single.js
│   ├── projects.js
│   ├── retargeting-landing.js
│   ├── rtl-ltr-manager.js
│   └── services.js
└── api/
    ├── campaign-api.php
    └── cms-api.php
```

### Database Structure
```
Database: sphinx_fire_cms

Tables:
├── pages (id, page_key, title, meta_description, is_active, created_at, updated_at)
├── sections (id, page_id, section_type, section_key, sort_order, is_active, created_at, updated_at)
├── section_items (id, section_id, item_type, item_key, sort_order, is_active, created_at, updated_at)
├── languages (id, code, name, is_active, is_default, created_at, updated_at)
├── page_translations (id, page_id, language_id, title, meta_description, content, created_at, updated_at)
├── section_translations (id, section_id, language_id, title, subtitle, content, button_text, created_at, updated_at)
└── section_item_translations (id, section_item_id, language_id, title, subtitle, content, button_text, created_at, updated_at)
```

## 🗄️ Database Schema Details

### 1. pages Table
```sql
CREATE TABLE pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_key VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    meta_description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. sections Table
```sql
CREATE TABLE sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_id INT NOT NULL,
    section_type ENUM('hero', 'advantages', 'services', 'projects', 'testimonials', 'locations', 'cta', 'content') NOT NULL,
    section_key VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
);
```

### 3. section_items Table
```sql
CREATE TABLE section_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_id INT NOT NULL,
    item_type ENUM('advantage', 'service', 'project', 'testimonial', 'location', 'content') NOT NULL,
    item_key VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
);
```

### 4. languages Table
```sql
CREATE TABLE languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(5) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 5. Translation Tables
```sql
-- Page translations
CREATE TABLE page_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    meta_description TEXT,
    content LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_lang (page_id, language_id)
);

-- Section translations
CREATE TABLE section_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    content TEXT,
    button_text VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_section_lang (section_id, language_id)
);

-- Section item translations
CREATE TABLE section_item_translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_item_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    content TEXT,
    button_text VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_item_id) REFERENCES section_items(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_item_lang (section_item_id, language_id)
);
```

## 🔧 API Structure

### 1. CMS API (api/cms-api.php)
```php
<?php
// Main CMS API for dynamic content
class CMSAPI {
    // Get page content with translations
    public function getPageContent($pageKey, $languageCode = 'en');
    
    // Get sections for a page
    public function getPageSections($pageId, $languageCode = 'en');
    
    // Get section items
    public function getSectionItems($sectionId, $languageCode = 'en');
    
    // Get all translations for a page
    public function getPageTranslations($pageId);
    
    // Update content
    public function updateContent($table, $id, $data);
}
```

### 2. Campaign API (api/campaign-api.php)
```php
<?php
// Campaign tracking and analytics
class CampaignAPI {
    // Track campaign clicks
    public function trackCampaignClick($campaignId, $userId);
    
    // Get campaign analytics
    public function getCampaignStats($campaignId);
    
    // Store lead information
    public function storeLead($data);
}
```

## 📄 Page Structure & Content

### 1. Home Page (index.php)
**Sections:**
- Hero Carousel (3 slides)
- Key Advantages (6 items)
- Services (6 services)
- Featured Projects (3 projects)
- Testimonials (2 testimonials)
- Industrial Zones (3 locations)
- Final CTA

**Content Types:**
- Hero slides with dynamic titles, subtitles, content, and button text
- Advantage cards with icons, titles, descriptions, and highlights
- Service cards with images, titles, descriptions, and CTAs
- Project cards with images, titles, descriptions, and status
- Testimonial cards with client info and quotes
- Location cards with coverage areas and project counts

### 2. Services Page (services.html)
**Sections:**
- Hero section
- Service categories
- Detailed service descriptions
- Case studies
- CTA sections

### 3. Projects Page (projects.html)
**Sections:**
- Project showcase
- Filter by category
- Project details
- Client testimonials

### 4. About Page (about.html)
**Sections:**
- Company story
- Team information
- Certifications
- Mission & Vision

### 5. Contact Page (contact.html)
**Sections:**
- Contact form
- Office locations
- Contact information
- Service areas

## 🌐 Multi-Language Support

### Language Configuration
- **Default Language:** English (en)
- **Secondary Language:** Arabic (ar)
- **RTL Support:** Full RTL support for Arabic
- **Language Switching:** Dynamic language switching without page reload

### Translation Structure
```javascript
// Language switching mechanism
const LanguageManager = {
    currentLanguage: 'en',
    
    switchLanguage(lang) {
        this.currentLanguage = lang;
        this.updateContent();
        this.updateDirection();
    },
    
    updateContent() {
        // Fetch and update all content
    },
    
    updateDirection() {
        // Update RTL/LTR direction
    }
};
```

## 🎨 Frontend Features

### 1. Performance Optimization
- **Lazy Loading:** Images and content
- **Service Worker:** Offline support and caching
- **Minification:** CSS and JS optimization
- **CDN Integration:** Fast content delivery

### 2. Responsive Design
- **Mobile First:** Responsive design approach
- **Breakpoints:** Mobile, tablet, desktop
- **Touch Friendly:** Mobile-optimized interactions

### 3. Interactive Elements
- **Carousel:** Hero section carousel
- **Animations:** Scroll-triggered animations
- **Forms:** Contact and quote forms
- **Maps:** Location integration

## 🔄 CMS Integration

### 1. Content Management
- **Dynamic Content:** All content managed through database
- **Real-time Updates:** Content updates without code changes
- **Version Control:** Content versioning and rollback
- **SEO Management:** Meta tags and descriptions

### 2. Admin Features
- **Content Editor:** Rich text editor for content
- **Media Management:** Image and file uploads
- **User Management:** Admin user roles
- **Analytics:** Content performance tracking

## 📊 Database Seed Data

### 1. Initial Data
```sql
-- Languages
INSERT INTO languages (code, name, is_active, is_default) VALUES
('en', 'English', TRUE, TRUE),
('ar', 'العربية', TRUE, FALSE);

-- Pages
INSERT INTO pages (page_key, title, meta_description, is_active) VALUES
('index', 'Home', 'Sphinx Fire - Leading Fire Safety Solutions', TRUE),
('services', 'Services', 'Complete fire safety services', TRUE),
('projects', 'Projects', 'Our completed projects', TRUE),
('about', 'About', 'About Sphinx Fire', TRUE),
('contact', 'Contact', 'Contact Sphinx Fire', TRUE);
```

### 2. Section Structure
```sql
-- Home page sections
INSERT INTO sections (page_id, section_type, section_key, sort_order) VALUES
(1, 'hero', 'home-hero', 1),
(1, 'advantages', 'home-advantages', 2),
(1, 'services', 'home-services', 3),
(1, 'projects', 'home-projects', 4),
(1, 'testimonials', 'home-testimonials', 5),
(1, 'locations', 'home-locations', 6),
(1, 'cta', 'home-final-cta', 7);
```

## 🚀 Deployment & Hosting

### 1. Server Requirements
- **PHP:** 8.0 or higher
- **MySQL:** 8.0 or higher
- **Web Server:** Apache/Nginx
- **SSL:** HTTPS required

### 2. File Structure
```
public_html/
├── index.php (Entry point)
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── api/
├── database/
└── uploads/
```

### 3. Environment Configuration
```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sphinx_fire_cms');
define('DB_USER', 'username');
define('DB_PASS', 'password');

// config/app.php
define('SITE_URL', 'https://sphinxfire.com');
define('DEFAULT_LANG', 'en');
define('UPLOAD_PATH', '/uploads/');
```

## 📈 Analytics & Tracking

### 1. Google Analytics
- Page views tracking
- User behavior analysis
- Conversion tracking
- Campaign performance

### 2. Custom Tracking
- Form submissions
- Phone calls
- Email clicks
- Service inquiries

## 🔒 Security Features

### 1. Data Protection
- **SQL Injection Prevention:** Prepared statements
- **XSS Protection:** Input sanitization
- **CSRF Protection:** Token validation
- **File Upload Security:** Type and size validation

### 2. Access Control
- **Admin Authentication:** Secure login system
- **Role-based Access:** Different permission levels
- **Session Management:** Secure session handling

## 📱 Mobile Optimization

### 1. Mobile Features
- **Touch Gestures:** Swipe navigation
- **Responsive Images:** Optimized for mobile
- **Fast Loading:** Mobile-optimized performance
- **Offline Support:** Service worker caching

### 2. Progressive Web App
- **Installable:** Add to home screen
- **Offline Functionality:** Cached content
- **Push Notifications:** Engagement features

## 🔧 Development Workflow

### 1. Local Development
```bash
# Setup local environment
git clone [repository]
cd FIRE2
npm install
php -S localhost:8000

# Database setup
mysql -u root -p
CREATE DATABASE sphinx_fire_cms;
source database/migrations/initial_setup.sql;
source database/home_complete_seed.sql;
```

### 2. Testing
- **Unit Tests:** PHPUnit for backend
- **Integration Tests:** API testing
- **Frontend Tests:** JavaScript testing
- **Cross-browser Testing:** Multiple browsers

### 3. Deployment
```bash
# Production deployment
git push origin main
ssh server "cd /var/www && git pull"
php artisan migrate
php artisan cache:clear
```

## 📋 Content Management Workflow

### 1. Content Creation
1. **Plan Content:** Define structure and requirements
2. **Create Database Records:** Add sections and items
3. **Add Translations:** English and Arabic content
4. **Review & Publish:** Content approval process

### 2. Content Updates
1. **Identify Changes:** Determine what needs updating
2. **Update Database:** Modify content records
3. **Test Changes:** Verify on staging environment
4. **Deploy to Production:** Live content updates

### 3. SEO Management
1. **Keyword Research:** Identify target keywords
2. **Content Optimization:** SEO-friendly content
3. **Meta Tag Management:** Title and description updates
4. **Performance Monitoring:** Track SEO performance

## 🎯 Business Features

### 1. Lead Generation
- **Contact Forms:** Multiple contact points
- **Quote Requests:** Service inquiry forms
- **Newsletter Signup:** Email marketing
- **Callback Requests:** Phone lead generation

### 2. Service Areas
- **Sadat City:** Primary service area
- **6th of October:** Industrial zone coverage
- **10th of Ramadan:** Manufacturing facilities
- **Other Industrial Zones:** Expandable coverage

### 3. Service Categories
- **Firefighting Systems:** Complete fire networks
- **Fire Alarm Systems:** Detection and notification
- **Fire Extinguishers:** Various types and sizes
- **PPE Equipment:** Safety gear and training
- **Safety Consultation:** Expert guidance
- **Maintenance Services:** Ongoing support

## 🔄 Future Enhancements

### 1. Planned Features
- **E-commerce Integration:** Online ordering system
- **Client Portal:** Customer account management
- **Advanced Analytics:** Detailed performance tracking
- **Multi-site Support:** Multiple location management

### 2. Technical Improvements
- **API Versioning:** Backward compatibility
- **Microservices:** Service-oriented architecture
- **Cloud Integration:** AWS/Azure deployment
- **AI Integration:** Chatbot and automation

## 📞 Support & Maintenance

### 1. Technical Support
- **Bug Fixes:** Issue resolution
- **Feature Updates:** New functionality
- **Performance Optimization:** Speed improvements
- **Security Updates:** Vulnerability patches

### 2. Content Management
- **Regular Updates:** Fresh content
- **SEO Optimization:** Search engine improvements
- **Analytics Review:** Performance analysis
- **User Feedback:** Continuous improvement

---

## 🚀 Getting Started

1. **Clone Repository:** `git clone [repository-url]`
2. **Setup Database:** Import schema and seed data
3. **Configure Environment:** Update configuration files
4. **Install Dependencies:** Run `npm install`
5. **Start Development Server:** `php -S localhost:8000`
6. **Access Application:** Open browser to `http://localhost:8000`

## 📞 Contact Information

- **Technical Support:** tech@sphinxfire.com
- **Content Management:** content@sphinxfire.com
- **Business Inquiries:** info@sphinxfire.com

---

*This documentation provides a complete overview of the Sphinx Fire project. For specific implementation details, refer to the individual files and code comments.* 