<?php
/**
 * Sphinx Fire CMS API
 * RESTful API for content management system
 * 
 * @version 1.0
 * @author Sphinx Fire Development Team
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
$config = [
    'host' => 'localhost',
    'dbname' => 'sphinx_fire_cms',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Router class
class CMSRouter {
    private $pdo;
    private $routes = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->setupRoutes();
    }
    
    private function setupRoutes() {
        // Pages
        $this->routes['GET']['/api/pages'] = 'getPages';
        $this->routes['GET']['/api/pages/{id}'] = 'getPage';
        $this->routes['POST']['/api/pages'] = 'createPage';
        $this->routes['PUT']['/api/pages/{id}'] = 'updatePage';
        $this->routes['DELETE']['/api/pages/{id}'] = 'deletePage';
        
        // Services
        $this->routes['GET']['/api/services'] = 'getServices';
        $this->routes['GET']['/api/services/{id}'] = 'getService';
        $this->routes['POST']['/api/services'] = 'createService';
        $this->routes['PUT']['/api/services/{id}'] = 'updateService';
        $this->routes['DELETE']['/api/services/{id}'] = 'deleteService';
        
        // Projects
        $this->routes['GET']['/api/projects'] = 'getProjects';
        $this->routes['GET']['/api/projects/{id}'] = 'getProject';
        $this->routes['POST']['/api/projects'] = 'createProject';
        $this->routes['PUT']['/api/projects/{id}'] = 'updateProject';
        $this->routes['DELETE']['/api/projects/{id}'] = 'deleteProject';
        
        // Blog
        $this->routes['GET']['/api/blog'] = 'getBlogPosts';
        $this->routes['GET']['/api/blog/{id}'] = 'getBlogPost';
        $this->routes['POST']['/api/blog'] = 'createBlogPost';
        $this->routes['PUT']['/api/blog/{id}'] = 'updateBlogPost';
        $this->routes['DELETE']['/api/blog/{id}'] = 'deleteBlogPost';
        
        // Media
        $this->routes['GET']['/api/media'] = 'getMedia';
        $this->routes['POST']['/api/media'] = 'uploadMedia';
        $this->routes['DELETE']['/api/media/{id}'] = 'deleteMedia';
        
        // Locations
        $this->routes['GET']['/api/locations'] = 'getLocations';
        $this->routes['GET']['/api/locations/{id}'] = 'getLocation';
        
        // Testimonials
        $this->routes['GET']['/api/testimonials'] = 'getTestimonials';
        $this->routes['POST']['/api/testimonials'] = 'createTestimonial';
        
        // Contact
        $this->routes['POST']['/api/contact'] = 'submitContact';
        $this->routes['GET']['/api/contact/submissions'] = 'getContactSubmissions';
        
        // Settings
        $this->routes['GET']['/api/settings'] = 'getSettings';
        $this->routes['PUT']['/api/settings'] = 'updateSettings';
        
        // Analytics
        $this->routes['POST']['/api/analytics/event'] = 'trackEvent';
        $this->routes['POST']['/api/analytics/pageview'] = 'trackPageView';
        $this->routes['GET']['/api/analytics/stats'] = 'getAnalyticsStats';
        
        // Performance
        $this->routes['POST']['/api/performance/metrics'] = 'recordPerformanceMetrics';
        $this->routes['GET']['/api/performance/report'] = 'getPerformanceReport';
        
        // SEO
        $this->routes['GET']['/api/seo/metrics'] = 'getSEOMetrics';
        $this->routes['POST']['/api/seo/crawl'] = 'crawlPageSEO';
        
        // Cache
        $this->routes['DELETE']['/api/cache'] = 'clearCache';
        $this->routes['GET']['/api/cache/stats'] = 'getCacheStats';
    }
    
    public function route() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Find matching route
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove full match
                return $this->$handler(...$matches);
            }
        }
        
        http_response_code(404);
        return ['error' => 'Route not found'];
    }
    
    // ===== PAGE METHODS =====
    
    private function getPages() {
        $lang = $_GET['lang'] ?? 'en';
        $status = $_GET['status'] ?? 'published';
        
        $sql = "
            SELECT p.id, p.slug, p.template, p.status, p.is_homepage,
                   pt.title, pt.meta_title, pt.meta_description, pt.excerpt,
                   pt.featured_image, p.published_at, p.created_at
            FROM pages p
            JOIN page_translations pt ON p.id = pt.page_id
            JOIN languages l ON pt.language_id = l.id
            WHERE l.code = ? AND p.status = ?
            ORDER BY p.sort_order, p.created_at DESC
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$lang, $status]);
        
        return ['data' => $stmt->fetchAll()];
    }
    
    private function getPage($id) {
        $lang = $_GET['lang'] ?? 'en';
        
        // Get page with translations
        $sql = "
            SELECT p.*, pt.title, pt.meta_title, pt.meta_description, 
                   pt.meta_keywords, pt.content, pt.excerpt, pt.featured_image,
                   pt.og_title, pt.og_description, pt.og_image, pt.canonical_url
            FROM pages p
            JOIN page_translations pt ON p.id = pt.page_id
            JOIN languages l ON pt.language_id = l.id
            WHERE p.id = ? AND l.code = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $page = $stmt->fetch();
        
        if (!$page) {
            http_response_code(404);
            return ['error' => 'Page not found'];
        }
        
        // Get page sections
        $sql = "
            SELECT s.id, s.section_type, s.section_key, s.sort_order, s.settings,
                   st.title, st.subtitle, st.content, st.button_text, 
                   st.button_url, st.background_image
            FROM sections s
            JOIN section_translations st ON s.id = st.section_id
            JOIN languages l ON st.language_id = l.id
            WHERE s.page_id = ? AND l.code = ? AND s.is_active = 1
            ORDER BY s.sort_order
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $page['sections'] = $stmt->fetchAll();
        
        return ['data' => $page];
    }
    
    private function createPage() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $this->pdo->beginTransaction();
            
            // Create page
            $sql = "INSERT INTO pages (slug, template, status, is_homepage, parent_id, sort_order, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['slug'],
                $data['template'] ?? 'default',
                $data['status'] ?? 'draft',
                $data['is_homepage'] ?? false,
                $data['parent_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['created_by'] ?? 1
            ]);
            
            $pageId = $this->pdo->lastInsertId();
            
            // Create translations
            foreach ($data['translations'] as $langCode => $translation) {
                $langId = $this->getLanguageId($langCode);
                
                $sql = "INSERT INTO page_translations 
                        (page_id, language_id, title, meta_title, meta_description, 
                         meta_keywords, content, excerpt, featured_image, og_title, 
                         og_description, og_image, canonical_url) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $pageId, $langId, $translation['title'], $translation['meta_title'] ?? null,
                    $translation['meta_description'] ?? null, $translation['meta_keywords'] ?? null,
                    $translation['content'] ?? null, $translation['excerpt'] ?? null,
                    $translation['featured_image'] ?? null, $translation['og_title'] ?? null,
                    $translation['og_description'] ?? null, $translation['og_image'] ?? null,
                    $translation['canonical_url'] ?? null
                ]);
            }
            
            $this->pdo->commit();
            
            return ['data' => ['id' => $pageId, 'message' => 'Page created successfully']];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            return ['error' => 'Failed to create page: ' . $e->getMessage()];
        }
    }
    
    // ===== SERVICE METHODS =====
    
    private function getServices() {
        $lang = $_GET['lang'] ?? 'en';
        $category = $_GET['category'] ?? null;
        
        $sql = "
            SELECT s.id, s.slug, s.category_id, sc.slug as category_slug,
                   st.name, st.short_description, s.featured_image, s.icon,
                   s.is_featured, s.sort_order
            FROM services s
            JOIN service_categories sc ON s.category_id = sc.id
            JOIN service_translations st ON s.id = st.service_id
            JOIN languages l ON st.language_id = l.id
            WHERE l.code = ? AND s.is_active = 1
        ";
        
        $params = [$lang];
        
        if ($category) {
            $sql .= " AND sc.slug = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY s.sort_order, s.id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return ['data' => $stmt->fetchAll()];
    }
    
    private function getService($id) {
        $lang = $_GET['lang'] ?? 'en';
        
        // Get service with translations
        $sql = "
            SELECT s.*, sc.slug as category_slug, 
                   st.name, st.short_description, st.full_description,
                   st.features, st.benefits, st.specifications,
                   st.meta_title, st.meta_description, st.meta_keywords
            FROM services s
            JOIN service_categories sc ON s.category_id = sc.id
            JOIN service_translations st ON s.id = st.service_id
            JOIN languages l ON st.language_id = l.id
            WHERE s.id = ? AND l.code = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $service = $stmt->fetch();
        
        if (!$service) {
            http_response_code(404);
            return ['error' => 'Service not found'];
        }
        
        // Parse JSON fields
        $service['gallery'] = json_decode($service['gallery'] ?? '[]');
        $service['features'] = json_decode($service['features'] ?? '[]');
        $service['benefits'] = json_decode($service['benefits'] ?? '[]');
        $service['specifications'] = json_decode($service['specifications'] ?? '[]');
        
        return ['data' => $service];
    }
    
    // ===== BLOG METHODS =====
    
    private function getBlogPosts() {
        $lang = $_GET['lang'] ?? 'en';
        $category = $_GET['category'] ?? null;
        $limit = intval($_GET['limit'] ?? 10);
        $offset = intval($_GET['offset'] ?? 0);
        
        $sql = "
            SELECT bp.id, bp.slug, bp.category_id, bc.slug as category_slug,
                   bpt.title, bpt.excerpt, bp.featured_image, bp.reading_time,
                   bp.views_count, bp.published_at, u.first_name, u.last_name
            FROM blog_posts bp
            JOIN blog_categories bc ON bp.category_id = bc.id
            JOIN blog_post_translations bpt ON bp.id = bpt.post_id
            JOIN languages l ON bpt.language_id = l.id
            JOIN users u ON bp.author_id = u.id
            WHERE l.code = ? AND bp.status = 'published' AND bp.published_at <= NOW()
        ";
        
        $params = [$lang];
        
        if ($category) {
            $sql .= " AND bc.slug = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY bp.published_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $posts = $stmt->fetchAll();
        
        // Get total count for pagination
        $countSql = "
            SELECT COUNT(*) as total
            FROM blog_posts bp
            JOIN blog_categories bc ON bp.category_id = bc.id
            WHERE bp.status = 'published' AND bp.published_at <= NOW()
        ";
        
        $countParams = [];
        
        if ($category) {
            $countSql .= " AND bc.slug = ?";
            $countParams[] = $category;
        }
        
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
        
        return [
            'data' => $posts,
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'pages' => ceil($total / $limit)
            ]
        ];
    }
    
    private function getBlogPost($id) {
        $lang = $_GET['lang'] ?? 'en';
        
        // Get blog post with translations
        $sql = "
            SELECT bp.*, bc.slug as category_slug, 
                   bpt.title, bpt.excerpt, bpt.content, bpt.tags,
                   bpt.meta_title, bpt.meta_description, bpt.meta_keywords,
                   bpt.og_title, bpt.og_description,
                   u.first_name, u.last_name, u.avatar_url
            FROM blog_posts bp
            JOIN blog_categories bc ON bp.category_id = bc.id
            JOIN blog_post_translations bpt ON bp.id = bpt.post_id
            JOIN languages l ON bpt.language_id = l.id
            JOIN users u ON bp.author_id = u.id
            WHERE bp.id = ? AND l.code = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $post = $stmt->fetch();
        
        if (!$post) {
            http_response_code(404);
            return ['error' => 'Blog post not found'];
        }
        
        // Parse JSON fields
        $post['tags'] = json_decode($post['tags'] ?? '[]');
        
        // Increment view count
        $this->incrementBlogViews($id);
        
        // Get related posts
        $sql = "
            SELECT bp.id, bp.slug, bpt.title, bp.featured_image
            FROM blog_posts bp
            JOIN blog_post_translations bpt ON bp.id = bpt.post_id
            JOIN languages l ON bpt.language_id = l.id
            WHERE bp.category_id = ? AND bp.id != ? AND l.code = ?
                  AND bp.status = 'published' AND bp.published_at <= NOW()
            ORDER BY bp.published_at DESC
            LIMIT 3
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$post['category_id'], $id, $lang]);
        $post['related_posts'] = $stmt->fetchAll();
        
        return ['data' => $post];
    }
    
    private function incrementBlogViews($postId) {
        $sql = "UPDATE blog_posts SET views_count = views_count + 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId]);
    }
    
    // ===== LOCATION METHODS =====
    
    private function getLocations() {
        $lang = $_GET['lang'] ?? 'en';
        
        $sql = "
            SELECT l.id, l.slug, l.type, 
                   lt.name, lt.description, lt.meta_title, lt.meta_description
            FROM locations l
            JOIN location_translations lt ON l.id = lt.location_id
            JOIN languages lang ON lt.language_id = lang.id
            WHERE lang.code = ? AND l.is_active = 1
            ORDER BY l.sort_order
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$lang]);
        
        return ['data' => $stmt->fetchAll()];
    }
    
    private function getLocation($id) {
        $lang = $_GET['lang'] ?? 'en';
        
        // Get location with translations
        $sql = "
            SELECT l.*, 
                   lt.name, lt.description, lt.meta_title, lt.meta_description, lt.local_keywords
            FROM locations l
            JOIN location_translations lt ON l.id = lt.location_id
            JOIN languages lang ON lt.language_id = lang.id
            WHERE l.id = ? AND lang.code = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $location = $stmt->fetch();
        
        if (!$location) {
            http_response_code(404);
            return ['error' => 'Location not found'];
        }
        
        // Get location content
        $sql = "
            SELECT lc.id, lc.content_type, lc.sort_order, lc.data,
                   lct.title, lct.description, lct.additional_data
            FROM location_content lc
            JOIN location_content_translations lct ON lc.id = lct.content_id
            JOIN languages lang ON lct.language_id = lang.id
            WHERE lc.location_id = ? AND lang.code = ? AND lc.is_active = 1
            ORDER BY lc.content_type, lc.sort_order
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $content = $stmt->fetchAll();
        
        // Group content by type
        $groupedContent = [];
        foreach ($content as $item) {
            $type = $item['content_type'];
            if (!isset($groupedContent[$type])) {
                $groupedContent[$type] = [];
            }
            
            // Parse JSON data
            $item['data'] = json_decode($item['data'] ?? '{}', true);
            $item['additional_data'] = json_decode($item['additional_data'] ?? '{}', true);
            
            $groupedContent[$type][] = $item;
        }
        
        $location['content'] = $groupedContent;
        
        // Get location testimonials
        $sql = "
            SELECT t.id, t.client_name, t.client_position, t.client_company, 
                   t.client_avatar, t.rating, tt.content
            FROM testimonials t
            JOIN testimonial_translations tt ON t.id = tt.testimonial_id
            JOIN languages lang ON tt.language_id = lang.id
            WHERE t.location_id = ? AND lang.code = ? AND t.is_published = 1
            ORDER BY t.sort_order
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $location['testimonials'] = $stmt->fetchAll();
        
        // Get location projects
        $sql = "
            SELECT p.id, p.slug, p.status, p.featured_image, 
                   pt.title, pt.subtitle, pt.description
            FROM projects p
            JOIN project_translations pt ON p.id = pt.project_id
            JOIN languages lang ON pt.language_id = lang.id
            WHERE p.location LIKE ? AND lang.code = ? AND p.is_published = 1
            ORDER BY p.sort_order
            LIMIT 3
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['%' . $location['name'] . '%', $lang]);
        $location['projects'] = $stmt->fetchAll();
        
        return ['data' => $location];
    }
    
    // ===== CONTACT METHODS =====
    
    private function submitContact() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Validate form data
            if (empty($data['form_id'])) {
                throw new Exception('Form ID is required');
            }
            
            // Get form configuration
            $sql = "SELECT * FROM contact_forms WHERE form_key = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$data['form_id']]);
            $form = $stmt->fetch();
            
            if (!$form) {
                throw new Exception('Form not found');
            }
            
            // Validate required fields
            $fields = json_decode($form['fields'], true);
            foreach ($fields as $field) {
                if ($field['required'] && empty($data[$field['name']])) {
                    throw new Exception("Field '{$field['name']}' is required");
                }
            }
            
            // Store submission
            $sql = "INSERT INTO contact_submissions (form_id, data, ip_address, user_agent, referrer) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $form['id'],
                json_encode($data),
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $data['referrer'] ?? null
            ]);
            
            $submissionId = $this->pdo->lastInsertId();
            
            // Send email notification (in real implementation)
            // $this->sendEmailNotification($form, $data);
            
            // Create lead if applicable
            if (isset($data['create_lead']) && $data['create_lead']) {
                $this->createLeadFromSubmission($data, $submissionId);
            }
            
            return [
                'data' => [
                    'id' => $submissionId,
                    'message' => $form['success_message'] ?? 'Thank you for your submission!'
                ]
            ];
            
        } catch (Exception $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }
    
    private function createLeadFromSubmission($data, $submissionId) {
        $sql = "INSERT INTO leads (
                    source, contact_name, contact_email, contact_phone, 
                    company_name, service_interest, location, urgency, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'website',
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['company'] ?? null,
            $data['service'] ?? null,
            $data['location'] ?? null,
            $data['urgent'] ? 'high' : 'medium',
            'new'
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    // ===== ANALYTICS METHODS =====
    
    private function trackPageView() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $sql = "CALL UpdatePageViews(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['page_url'] ?? null,
                $data['referrer'] ?? null,
                $data['user_agent'] ?? null,
                $data['ip_address'] ?? null,
                $data['session_id'] ?? null,
                $data['user_id'] ?? null,
                $data['device_type'] ?? null,
                $data['browser'] ?? null,
                $data['os'] ?? null,
                $data['country'] ?? null,
                $data['city'] ?? null
            ]);
            
            return ['data' => ['message' => 'Page view recorded']];
            
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Failed to record page view: ' . $e->getMessage()];
        }
    }
    
    private function trackEvent() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $sql = "CALL TrackEvent(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['event_name'] ?? null,
                $data['event_category'] ?? null,
                json_encode($data['event_data'] ?? []),
                $data['page_url'] ?? null,
                $data['user_id'] ?? null,
                $data['session_id'] ?? null,
                $data['ip_address'] ?? null,
                $data['user_agent'] ?? null
            ]);
            
            return ['data' => ['message' => 'Event recorded']];
            
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Failed to record event: ' . $e->getMessage()];
        }
    }
    
    // ===== PERFORMANCE METHODS =====
    
    private function recordPerformanceMetrics() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $sql = "INSERT INTO performance_metrics 
                    (page_url, metric_type, value, device_type, user_agent, connection_type) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($data['metrics'] as $metric) {
                $stmt->execute([
                    $data['page_url'] ?? null,
                    $metric['type'] ?? null,
                    $metric['value'] ?? 0,
                    $data['device_type'] ?? 'desktop',
                    $data['user_agent'] ?? null,
                    $data['connection_type'] ?? null
                ]);
            }
            
            return ['data' => ['message' => 'Performance metrics recorded']];
            
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Failed to record performance metrics: ' . $e->getMessage()];
        }
    }
    
    // ===== SETTINGS METHODS =====
    
    private function getSettings() {
        $lang = $_GET['lang'] ?? 'en';
        $category = $_GET['category'] ?? null;
        $publicOnly = isset($_GET['public']) && $_GET['public'] === 'true';
        
        $sql = "
            SELECT s.setting_key, 
                   COALESCE(st.setting_value, s.setting_value) as setting_value,
                   s.setting_type, s.category, s.is_public
            FROM settings s
            LEFT JOIN setting_translations st ON s.setting_key = st.setting_key
            LEFT JOIN languages l ON st.language_id = l.id AND l.code = ?
        ";
        
        $params = [$lang];
        
        if ($category) {
            $sql .= " WHERE s.category = ?";
            $params[] = $category;
        }
        
        if ($publicOnly) {
            $sql .= $category ? " AND s.is_public = 1" : " WHERE s.is_public = 1";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $settings = $stmt->fetchAll();
        
        // Format settings as key-value pairs
        $formattedSettings = [];
        foreach ($settings as $setting) {
            $value = $setting['setting_value'];
            
            // Convert value based on type
            switch ($setting['setting_type']) {
                case 'number':
                    $value = (float) $value;
                    break;
                case 'boolean':
                    $value = $value === 'true' || $value === '1';
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }
            
            $formattedSettings[$setting['setting_key']] = $value;
        }
        
        return ['data' => $formattedSettings];
    }
    
    // ===== CACHE METHODS =====
    
    private function clearCache() {
        try {
            $sql = "DELETE FROM cache";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return ['data' => ['message' => 'Cache cleared successfully']];
            
        } catch (Exception $e) {
            http_response_code(500);
            return ['error' => 'Failed to clear cache: ' . $e->getMessage()];
        }
    }
    
    // ===== UTILITY METHODS =====
    
    private function getLanguageId($code) {
        $sql = "SELECT id FROM languages WHERE code = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$code]);
        $result = $stmt->fetch();
        
        if (!$result) {
            throw new Exception("Language with code '$code' not found");
        }
        
        return $result['id'];
    }
}

// Initialize router and process request
$router = new CMSRouter($pdo);
$response = $router->route();

// Output JSON response
echo json_encode($response);