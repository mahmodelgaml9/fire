<?php
// جميع الدوال البرمجية الخاصة بجلب/إدارة البيانات من قاعدة البيانات
require_once __DIR__ . '/../config.php';

/**
 * جلب بيانات صفحة (page) مع الترجمة
 */
function fetchPage($slug, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT p.*, pt.title, pt.meta_title, pt.meta_description
        FROM pages p
        JOIN page_translations pt ON p.id = pt.page_id
        JOIN languages l ON pt.language_id = l.id
        WHERE p.slug = ? AND l.code = ? LIMIT 1');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

/**
 * دالة مرنة لجلب السكاشن لأي صفحة وأي نوع
 * @param int $page_id
 * @param string|null $section_type
 * @param string $lang
 * @param int|null $limit
 * @param string $order
 * @return array|false
 */
function fetchSections($page_id, $section_type = null, $lang = 'en', $limit = null, $order = 's.sort_order, s.id') {
    global $pdo;
    $query = 'SELECT s.*, st.title, st.subtitle, st.content, st.button_text, st.button_url, st.background_image
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = ? AND l.code = ? AND s.is_active = 1';
    $params = [$page_id, $lang];
    if ($section_type) {
        $query .= ' AND s.section_type = ?';
        $params[] = $section_type;
    }
    $query .= ' ORDER BY ' . $order;
    if ($limit) {
        $query .= ' LIMIT ?';
        $params[] = $limit;
    }
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    return $rows;
}

/**
 * جلب خدمة واحدة أو جميع الخدمات مع الترجمة
 */
function fetchServices($lang = 'en', $service_id = null) {
    global $pdo;
    $sql = 'SELECT s.*, st.name, st.short_description, st.full_description, st.features, st.benefits, st.specifications
        FROM services s
        JOIN service_translations st ON s.id = st.service_id
        JOIN languages l ON st.language_id = l.id
        WHERE l.code = ? AND s.is_active = 1';
    $params = [$lang];
    if ($service_id) {
        $sql .= ' AND s.id = ?';
        $params[] = $service_id;
    }
    $sql .= ' ORDER BY s.sort_order, s.id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $service_id ? $stmt->fetch() : $stmt->fetchAll();
}

/**
 * جلب المشاريع مع دعم الفلترة حسب التصنيف أو الحالة
 * @param string $lang
 * @param int|null $category_id
 * @param string|null $status
 * @param int|null $limit
 * @return array
 */
function fetchProjects($lang = 'en', $category_id = null, $status = null, $limit = null) {
    global $pdo;
    $query = 'SELECT p.*, pt.title, pt.subtitle, pt.description, pt.challenge, pt.solution, pt.results, pt.testimonial, pt.testimonial_author, pt.testimonial_position, pt.meta_title, pt.meta_description, pt.meta_keywords, pc.slug as category_slug
        FROM projects p
        JOIN project_translations pt ON p.id = pt.project_id
        JOIN languages l ON pt.language_id = l.id
        LEFT JOIN project_categories pc ON p.category_id = pc.id
        WHERE l.code = ? AND p.is_published = 1';
    $params = [$lang];
    if ($category_id) {
        $query .= ' AND p.category_id = ?';
        $params[] = $category_id;
    }
    if ($status) {
        $query .= ' AND p.status = ?';
        $params[] = $status;
    }
    $query .= ' ORDER BY p.sort_order, p.id';
    if ($limit) {
        $query .= ' LIMIT ?';
        $params[] = $limit;
    }
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * جلب التصنيفات (categories) لأي نوع (مشاريع أو خدمات)
 * @param string $type
 * @param string $lang
 * @return array
 */
function fetchCategories($type = 'project', $lang = 'en') {
    global $pdo;
    $table = $type === 'service' ? 'service_categories' : 'project_categories';
    $transTable = $type === 'service' ? 'service_category_translations' : 'project_category_translations';
    $stmt = $pdo->prepare("SELECT c.*, ct.name, ct.description FROM $table c JOIN $transTable ct ON c.id = ct.category_id JOIN languages l ON ct.language_id = l.id WHERE l.code = ? AND c.is_active = 1 ORDER BY c.sort_order, c.id");
    $stmt->execute([$lang]);
    return $stmt->fetchAll();
}

/**
 * جلب مشروع واحد أو جميع المشاريع مع الترجمة
 */


/**
 * جلب سلايدز الهيرو (Hero Slides) لصفحة معينة
 */
function fetchHeroSlides($page_id, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.subtitle, st.content, st.button_text, st.background_image
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = ? AND s.section_type = "hero" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$page_id, $lang]);
    return $stmt->fetchAll();
}

/**
 * جلب مزايا الشركة (Advantages)
 */
function fetchAdvantages($page_id, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.subtitle, st.content
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = ? AND s.section_type = "advantage" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$page_id, $lang]);
    return $stmt->fetchAll();
}

/**
 * جلب الخدمات المميزة (Featured Services)
 */
function fetchFeaturedServices($lang = 'en', $limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.name, st.short_description, st.full_description, st.features, s.duration, s.slug
        FROM services s
        JOIN service_translations st ON s.id = st.service_id
        JOIN languages l ON st.language_id = l.id
        WHERE l.code = ? AND s.is_active = 1 AND s.is_featured = 1
        ORDER BY s.sort_order, s.id LIMIT ?');
    $stmt->execute([$lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب المشاريع المميزة (Featured Projects)
 */
function fetchFeaturedProjects($lang = 'en', $limit = 3) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT p.*, pt.title, pt.subtitle, pt.description, pt.testimonial, pt.testimonial_author, pt.testimonial_position,
        p.duration_days as duration, p.budget_range, p.slug, p.featured_image
        FROM projects p
        JOIN project_translations pt ON p.id = pt.project_id
        JOIN languages l ON pt.language_id = l.id
        WHERE l.code = ? AND p.is_published = 1 AND p.is_featured = 1
        ORDER BY p.sort_order, p.id LIMIT ?');
    $stmt->execute([$lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب الشهادات (Testimonials)
 */
function fetchTestimonials($lang = 'en', $limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT t.*, tt.client_name, tt.client_position, tt.company_name, tt.testimonial_text, tt.rating
        FROM testimonials t
        JOIN testimonial_translations tt ON t.id = tt.testimonial_id
        JOIN languages l ON tt.language_id = l.id
        WHERE l.code = ? AND t.is_published = 1
        ORDER BY t.sort_order, t.id LIMIT ?');
    $stmt->execute([$lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب المناطق الصناعية (Locations)
 */
function fetchLocations($lang = 'en', $limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT loc.*, lt.name, lt.description
        FROM locations loc
        JOIN location_translations lt ON loc.id = lt.location_id
        JOIN languages l ON lt.language_id = l.id
        WHERE l.code = ? AND loc.is_active = 1
        ORDER BY loc.sort_order, loc.id LIMIT ?');
    $stmt->execute([$lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب نظرة عامة عن الشركة (About Overview)
 */
function fetchAboutOverview($page_id, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = ? AND s.section_type = "overview" AND l.code = ? AND s.is_active = 1
        LIMIT 1');
    $stmt->execute([$page_id, $lang]);
    return $stmt->fetch();
}

/**
 * جلب القيم (الرؤية، الرسالة، القيم) لصفحة about
 */
function fetchAboutValues($page_id, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = ? AND s.section_type = "about_value" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$page_id, $lang]);
    return $stmt->fetchAll();
}

/**
 * جلب خطوات العملية (Process Steps)
 */
function fetchProcessSteps($page_id, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = ? AND s.section_type = "process_step" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$page_id, $lang]);
    return $stmt->fetchAll();
}

/*
// تم تعطيل دالة جلب أعضاء الفريق لعدم وجود جدول team في قاعدة البيانات
function fetchTeamMembers(
    $lang = 'en', $limit = 10) {
    // معطلة
    return [];
}
*/

/**
 * جلب الشركاء (Partners)
 * معطل - جدول partners غير موجود في قاعدة البيانات
 */
function fetchPartners($lang = 'en', $limit = 10) {
    // معطل - جدول partners غير موجود في قاعدة البيانات
    return [];
}

/**
 * جلب الشهادات (Certifications)
 * معطل - جدول certifications غير موجود في قاعدة البيانات
 */
function fetchCertifications($lang = 'en', $limit = 10) {
    // معطل - جدول certifications غير موجود في قاعدة البيانات
    return [];
}

/**
 * جلب خدمة واحدة بناءً على slug
 */
function fetchServiceBySlug($slug, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.name, st.short_description, st.full_description, st.features, st.benefits, st.specifications
        FROM services s
        JOIN service_translations st ON s.id = st.service_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.slug = ? AND l.code = ? AND s.is_active = 1 LIMIT 1');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

/**
 * جلب مشروع واحد بناءً على slug
 */
function fetchProjectBySlug($slug, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT p.*, pt.title, pt.subtitle, pt.description, pt.challenge, pt.solution, pt.results, pt.testimonial, pt.testimonial_author, pt.testimonial_position, pt.meta_title, pt.meta_description, pt.meta_keywords
        FROM projects p
        JOIN project_translations pt ON p.id = pt.project_id
        JOIN languages l ON pt.language_id = l.id
        WHERE p.slug = ? AND l.code = ? AND p.is_published = 1 LIMIT 1');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

/**
 * جلب مدينة/منطقة بناءً على slug
 */
function fetchLocationBySlug($slug, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT loc.*, lt.name, lt.description, lt.meta_title, lt.meta_description, lt.local_keywords
        FROM locations loc
        JOIN location_translations lt ON loc.id = lt.location_id
        JOIN languages l ON lt.language_id = l.id
        WHERE loc.slug = ? AND l.code = ? AND loc.is_active = 1 LIMIT 1');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

/**
 * جلب مقالة مدونة واحدة بناءً على slug
 */
function fetchBlogPostBySlug($slug, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT bp.*, bpt.title, bpt.excerpt, bpt.content, bpt.tags, bpt.meta_title, bpt.meta_description, bc.slug as category_slug
        FROM blog_posts bp
        JOIN blog_post_translations bpt ON bp.id = bpt.post_id
        JOIN languages l ON bpt.language_id = l.id
        LEFT JOIN blog_categories bc ON bp.category_id = bc.id
        WHERE bp.slug = ? AND l.code = ? AND bp.status = "published" LIMIT 1');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

/**
 * جلب حملة/صفحة عرض خاصة بناءً على slug
 */
function fetchCampaignBySlug($slug, $lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT c.*, ct.title, ct.subtitle, ct.hero_content, ct.offer_headline, ct.offer_description, ct.cta_primary, ct.form_title, ct.success_message, ct.meta_title, ct.meta_description
        FROM campaign_landing_pages c
        JOIN campaign_landing_page_translations ct ON c.id = ct.page_id
        JOIN languages l ON ct.language_id = l.id
        WHERE c.slug = ? AND l.code = ? AND c.status = "published" LIMIT 1');
    $stmt->execute([$slug, $lang]);
    return $stmt->fetch();
}

// سنضيف المزيد من الدوال المتخصصة مع مراجعة كل صفحة لاحقًا



/**
 * جلب المناطق الصناعية (Industrial Zones/Cities)
 */
function fetchIndustrialZones($lang = 'en', $limit = 6) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT loc.*, lt.name, lt.description, loc.slug, 
        CASE 
            WHEN loc.slug = "sadat-city" THEN 50
            WHEN loc.slug = "october-city" THEN 35
            WHEN loc.slug = "10th-ramadan" THEN 40
            ELSE 25
        END as project_count
        FROM locations loc
        JOIN location_translations lt ON loc.id = lt.location_id
        JOIN languages l ON lt.language_id = l.id
        WHERE l.code = ? AND loc.is_active = 1 AND loc.type = "industrial_zone"
        ORDER BY loc.sort_order, loc.id LIMIT ?');
    $stmt->execute([$lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب إعدادات الموقع
 */
function getSiteSettings($lang = 'en') {
    global $pdo;
    $settings = [];
    
    // جلب الإعدادات الأساسية
    $stmt = $pdo->prepare('SELECT setting_key, setting_value FROM settings WHERE is_public = 1');
    $stmt->execute();
    $basicSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // جلب الترجمات
    $stmt = $pdo->prepare('SELECT st.setting_key, st.setting_value 
        FROM setting_translations st 
        JOIN languages l ON st.language_id = l.id 
        WHERE l.code = ?');
    $stmt->execute([$lang]);
    $translations = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // دمج الإعدادات مع الترجمات
    foreach ($basicSettings as $key => $value) {
        $settings[$key] = isset($translations[$key]) ? $translations[$key] : $value;
    }
    
    return $settings;
}

/**
 * جلب الخدمات للفوتر
 */
function getFooterServices($lang = 'en', $limit = 5) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.slug, st.name 
        FROM services s
        JOIN service_translations st ON s.id = st.service_id
        JOIN languages l ON st.language_id = l.id
        WHERE l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id LIMIT ?');
    $stmt->execute([$lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب مزايا الشركة لصفحة About
 */
function fetchAboutAdvantages($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content, st.subtitle
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = 2 AND s.section_type = "advantage" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$lang]);
    return $stmt->fetchAll();
}

/**
 * جلب خطوات العملية لصفحة About
 */
function fetchAboutProcessSteps($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content, st.subtitle
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = 2 AND s.section_type = "process" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$lang]);
    return $stmt->fetchAll();
}

/**
 * جلب فريق العمل لصفحة About
 */
function fetchTeamMembers($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content, st.subtitle
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = 2 AND s.section_type = "team" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$lang]);
    return $stmt->fetchAll();
}



/**
 * جلب مقالات ذات صلة
 */
function fetchRelatedBlogPosts($current_post_id, $category_id, $lang = 'en', $limit = 3) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT b.*, bt.title, bt.excerpt,
        bc.slug as category_slug
        FROM blog_posts b
        JOIN blog_post_translations bt ON b.id = bt.post_id
        JOIN languages l ON bt.language_id = l.id
        LEFT JOIN blog_categories bc ON b.category_id = bc.id
        WHERE b.id != ? AND b.category_id = ? AND l.code = ? AND b.status = "published"
        ORDER BY b.published_at DESC LIMIT ?');
    $stmt->execute([$current_post_id, $category_id, $lang, $limit]);
    return $stmt->fetchAll();
}

/**
 * جلب محتوى ستاتيكي للمقال
 */
function fetchBlogStaticContent($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.*, st.title, st.content, st.subtitle
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = 5 AND s.section_type = "blog_static" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$lang]);
    return $stmt->fetchAll();
}

/**
 * جلب المحتوى الستاتيكي لصفحة المدونة
 */
function fetchBlogPageContent($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT s.section_key, st.title, st.content, st.subtitle
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        WHERE s.page_id = 5 AND s.section_type = "blog_content" AND l.code = ? AND s.is_active = 1
        ORDER BY s.sort_order, s.id');
    $stmt->execute([$lang]);
    
    $content = [];
    while ($row = $stmt->fetch()) {
        $content[$row['section_key']] = [
            'title' => $row['title'],
            'content' => $row['content'],
            'subtitle' => $row['subtitle']
        ];
    }
    return $content;
}





function fetchBlogCategories($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT bc.*, bct.name FROM blog_categories bc LEFT JOIN blog_category_translations bct ON bc.id = bct.category_id AND bct.language_id = (SELECT id FROM languages WHERE code = ?) WHERE bc.is_active = 1 ORDER BY bc.sort_order, bc.slug');
    $stmt->execute([$lang]);
    return $stmt->fetchAll();
}

function fetchBlogPosts($lang = 'en', $category_id = null, $limit = null, $offset = 0) {
    global $pdo;
    $sql = 'SELECT b.*, bt.title, bt.excerpt, bc.slug as category_slug FROM blog_posts b JOIN blog_post_translations bt ON b.id = bt.post_id JOIN languages l ON bt.language_id = l.id LEFT JOIN blog_categories bc ON b.category_id = bc.id WHERE l.code = ? AND b.status = "published"';
    $params = [$lang];
    
    if ($category_id) {
        $sql .= ' AND b.category_id = ?';
        $params[] = $category_id;
    }
    
    $sql .= ' ORDER BY b.published_at DESC';
    
    if ($limit) {
        $sql .= ' LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetchFeaturedBlogPost($lang = 'en') {
    global $pdo;
    $stmt = $pdo->prepare('SELECT b.*, bt.title, bt.excerpt, bc.slug as category_slug FROM blog_posts b JOIN blog_post_translations bt ON b.id = bt.post_id JOIN languages l ON bt.language_id = l.id LEFT JOIN blog_categories bc ON b.category_id = bc.id WHERE l.code = ? AND b.status = "published" AND b.is_featured = 1 ORDER BY b.published_at DESC LIMIT 1');
    $stmt->execute([$lang]);
    return $stmt->fetch();
}

/**
 * جلب المحتوى الستاتيكي لصفحة Landing
 */
function fetchLandingStaticContent($lang = 'en') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT s.*, st.title, st.subtitle, st.content, st.button_text
            FROM sections s
            JOIN section_translations st ON s.id = st.section_id
            JOIN languages l ON st.language_id = l.id
            WHERE s.page_id = 6 AND l.code = ? AND s.is_active = 1
            ORDER BY s.sort_order
        ");
        $stmt->execute([$lang]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching landing static content: " . $e->getMessage());
        return [];
    }
}

// =====================================================
// دوال إدارة الوسائط والملفات
// =====================================================

/**
 * رفع ملف وتحويله لـ WebP
 */
function uploadMediaFile($file, $type = 'images', $convert_to_webp = true) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // إنشاء مجلد الوسائط
    $upload_dir = createMediaDirectory($type);
    
    // رفع الملف
    $filename = uploadFile($file, $upload_dir, null, $convert_to_webp);
    
    if ($filename) {
        // إنشاء نسخ مختلفة للصور
        if ($convert_to_webp && in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
            $variants = [
                'thumb' => ['width' => 150, 'height' => 150, 'quality' => 80],
                'small' => ['width' => 300, 'height' => 300, 'quality' => 85],
                'medium' => ['width' => 600, 'height' => 600, 'quality' => 85],
                'large' => ['width' => 1200, 'height' => 1200, 'quality' => 90]
            ];
            
            $source_path = $upload_dir . '/' . $filename;
            createImageVariants($source_path, $upload_dir, pathinfo($filename, PATHINFO_FILENAME), $variants);
        }
        
        return [
            'filename' => $filename,
            'path' => getMediaPath($filename, $type),
            'url' => getMediaUrl($filename, $type),
            'size' => filesize($upload_dir . '/' . $filename)
        ];
    }
    
    return false;
}

/**
 * حذف ملف الوسائط وجميع نسخه
 */
function deleteMediaFile($filename, $type = 'images') {
    $file_path = getMediaPath($filename, $type);
    $variants = ['thumb', 'small', 'medium', 'large'];
    
    return deleteImageWithVariants($file_path, $variants);
}

/**
 * الحصول على معلومات ملف الوسائط
 */
function getMediaInfo($filename, $type = 'images') {
    $file_path = getMediaPath($filename, $type);
    return getImageInfo($file_path);
}

/**
 * إنشاء رابط آمن لتحميل الملف
 */
function createSecureMediaUrl($filename, $type = 'images', $expires_in = 3600) {
    $file_path = getMediaPath($filename, $type);
    return createSecureFileUrl($file_path, $expires_in);
}

// =====================================================
// دوال إدارة الإعدادات
// =====================================================

/**
 * حفظ إعداد في قاعدة البيانات
 */
function saveSetting($key, $value) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, updated_at) 
            VALUES (?, ?, NOW()) 
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
        ");
        return $stmt->execute([$key, $value, $value]);
    } catch (PDOException $e) {
        error_log("Error saving setting: " . $e->getMessage());
        return false;
    }
}

/**
 * جلب إعداد من قاعدة البيانات
 */
function getSetting($key, $default = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log("Error getting setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * جلب جميع الإعدادات
 */
function getAllSettings() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log("Error getting all settings: " . $e->getMessage());
        return [];
    }
}

// =====================================================
// دوال إدارة المستخدمين والأدوار
// =====================================================

/**
 * جلب جميع المستخدمين مع أدوارهم
 */
function getAllUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT au.*, ar.name as role_name
            FROM admin_users au
            LEFT JOIN admin_roles ar ON au.role_id = ar.id
            ORDER BY au.created_at DESC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting all users: " . $e->getMessage());
        return [];
    }
}

/**
 * جلب جميع الأدوار
 */
function getAllRoles() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM admin_roles ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting all roles: " . $e->getMessage());
        return [];
    }
}

/**
 * إنشاء مستخدم جديد
 */
function createUser($data) {
    global $pdo;
    
    try {
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO admin_users (username, email, password_hash, first_name, last_name, role_id, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        return $stmt->execute([
            $data['username'],
            $data['email'],
            $password_hash,
            $data['first_name'],
            $data['last_name'],
            $data['role_id'],
            $data['is_active'] ?? 1
        ]);
    } catch (PDOException $e) {
        error_log("Error creating user: " . $e->getMessage());
        return false;
    }
}

/**
 * تحديث مستخدم
 */
function updateUser($user_id, $data) {
    global $pdo;
    
    try {
        $sql = "UPDATE admin_users SET 
                username = ?, email = ?, first_name = ?, last_name = ?, 
                role_id = ?, is_active = ?, updated_at = NOW()";
        $params = [
            $data['username'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['role_id'],
            $data['is_active'] ?? 1
        ];
        
        // إذا تم توفير كلمة مرور جديدة
        if (!empty($data['password'])) {
            $sql .= ", password_hash = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Error updating user: " . $e->getMessage());
        return false;
    }
}

// =====================================================
// دوال إدارة السجلات والإحصائيات
// =====================================================

/**
 * جلب سجلات النشاط
 */
function getActivityLogs($limit = 50, $offset = 0) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT al.*, au.first_name, au.last_name, au.username
            FROM admin_logs al
            LEFT JOIN admin_users au ON al.user_id = au.id
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error getting activity logs: " . $e->getMessage());
        return [];
    }
}

/**
 * جلب إحصائيات سريعة
 */
function getDashboardStats() {
    global $pdo;
    
    try {
        $stats = [];
        
        // عدد الصفحات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pages");
        $stats['pages'] = $stmt->fetch()['count'];
        
        // عدد الخدمات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM services");
        $stats['services'] = $stmt->fetch()['count'];
        
        // عدد المشاريع
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects");
        $stats['projects'] = $stmt->fetch()['count'];
        
        // عدد المقالات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts");
        $stats['blog_posts'] = $stmt->fetch()['count'];
        
        // عدد الشهادات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM testimonials");
        $stats['testimonials'] = $stmt->fetch()['count'];
        
        // عدد المستخدمين
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
        $stats['users'] = $stmt->fetch()['count'];
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        return [];
    }
}

/**
 * تنظيف السجلات القديمة
 */
function cleanupOldLogs($days = 30) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            DELETE FROM admin_logs 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        return $stmt->execute([$days]);
    } catch (PDOException $e) {
        error_log("Error cleaning up old logs: " . $e->getMessage());
        return false;
    }
}