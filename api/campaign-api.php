<?php
/**
 * Sphinx Fire Campaign API
 * RESTful API for campaign management and tracking
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
class CampaignRouter {
    private $pdo;
    private $routes = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->setupRoutes();
    }
    
    private function setupRoutes() {
        // Campaign Landing Pages
        $this->routes['GET']['/api/campaigns'] = 'getCampaigns';
        $this->routes['GET']['/api/campaigns/{id}'] = 'getCampaign';
        $this->routes['POST']['/api/campaigns'] = 'createCampaign';
        $this->routes['PUT']['/api/campaigns/{id}'] = 'updateCampaign';
        $this->routes['DELETE']['/api/campaigns/{id}'] = 'deleteCampaign';
        
        // Campaign Leads
        $this->routes['GET']['/api/campaign-leads'] = 'getCampaignLeads';
        $this->routes['GET']['/api/campaign-leads/{id}'] = 'getCampaignLead';
        $this->routes['POST']['/api/campaign-leads'] = 'createCampaignLead';
        $this->routes['PUT']['/api/campaign-leads/{id}'] = 'updateCampaignLead';
        
        // Campaign Conversions
        $this->routes['POST']['/api/track-conversion'] = 'trackConversion';
        $this->routes['GET']['/api/campaign-performance/{id}'] = 'getCampaignPerformance';
        $this->routes['GET']['/api/campaign-conversions/{id}'] = 'getCampaignConversions';
        
        // A/B Testing
        $this->routes['GET']['/api/ab-tests'] = 'getABTests';
        $this->routes['GET']['/api/ab-tests/{id}'] = 'getABTest';
        $this->routes['POST']['/api/ab-tests'] = 'createABTest';
        $this->routes['PUT']['/api/ab-tests/{id}'] = 'updateABTest';
        $this->routes['GET']['/api/ab-test-variants/{id}'] = 'getABTestVariants';
        $this->routes['POST']['/api/ab-test-impression'] = 'recordABTestImpression';
        
        // Campaign Content
        $this->routes['GET']['/api/campaign-content/{slug}'] = 'getCampaignContent';
        $this->routes['GET']['/api/campaign-content/{slug}/{lang}'] = 'getCampaignContentByLanguage';
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
    
    // ===== CAMPAIGN LANDING PAGES METHODS =====
    
    private function getCampaigns() {
        $status = $_GET['status'] ?? 'published';
        $type = $_GET['type'] ?? null;
        
        $sql = "
            SELECT c.id, c.slug, c.campaign_name, c.campaign_type, c.template, c.status,
                   c.start_date, c.end_date, c.conversion_goal, c.is_ab_test,
                   ct.title, ct.offer_headline, c.created_at
            FROM campaign_landing_pages c
            JOIN campaign_landing_page_translations ct ON c.id = ct.page_id
            JOIN languages l ON ct.language_id = l.id
            WHERE l.code = 'en'
        ";
        
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " AND c.status = ?";
            $params[] = $status;
        }
        
        if ($type) {
            $sql .= " AND c.campaign_type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return ['data' => $stmt->fetchAll()];
    }
    
    private function getCampaign($id) {
        $lang = $_GET['lang'] ?? 'en';
        
        // Get campaign with translations
        $sql = "
            SELECT c.*, 
                   ct.title, ct.subtitle, ct.hero_content, ct.offer_headline, 
                   ct.offer_description, ct.benefits_content, ct.testimonials,
                   ct.faq_content, ct.cta_primary, ct.cta_secondary, ct.form_title,
                   ct.success_message, ct.meta_title, ct.meta_description
            FROM campaign_landing_pages c
            JOIN campaign_landing_page_translations ct ON c.id = ct.page_id
            JOIN languages l ON ct.language_id = l.id
            WHERE c.id = ? AND l.code = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $lang]);
        $campaign = $stmt->fetch();
        
        if (!$campaign) {
            http_response_code(404);
            return ['error' => 'Campaign not found'];
        }
        
        // Parse JSON fields
        $campaign['target_audience'] = json_decode($campaign['target_audience'] ?? '{}');
        $campaign['offer_details'] = json_decode($campaign['offer_details'] ?? '{}');
        $campaign['utm_parameters'] = json_decode($campaign['utm_parameters'] ?? '{}');
        $campaign['settings'] = json_decode($campaign['settings'] ?? '{}');
        $campaign['testimonials'] = json_decode($campaign['testimonials'] ?? '[]');
        $campaign['faq_content'] = json_decode($campaign['faq_content'] ?? '[]');
        
        // Get campaign performance
        $sql = "
            SELECT 
                COUNT(DISTINCT cl.id) AS total_leads,
                COUNT(DISTINCT CASE WHEN cl.status = 'converted' THEN cl.id END) AS converted_leads,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) AS page_views,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) AS form_submissions,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) > 0 
                    THEN ROUND((COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) * 100.0) / 
                         COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END), 2)
                    ELSE 0
                END AS conversion_rate
            FROM campaign_landing_pages c
            LEFT JOIN campaign_leads cl ON c.id = cl.campaign_id
            LEFT JOIN campaign_conversions cc ON c.id = cc.campaign_id
            WHERE c.id = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $campaign['performance'] = $stmt->fetch();
        
        // Get A/B tests if applicable
        if ($campaign['is_ab_test']) {
            $sql = "
                SELECT t.id, t.test_name, t.status, t.start_date, t.end_date, 
                       t.winning_variant, t.metrics
                FROM campaign_ab_tests t
                WHERE t.campaign_id = ?
                ORDER BY t.created_at DESC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $campaign['ab_tests'] = $stmt->fetchAll();
            
            // Parse JSON fields
            foreach ($campaign['ab_tests'] as &$test) {
                $test['metrics'] = json_decode($test['metrics'] ?? '{}');
                
                // Get variants
                $sql = "
                    SELECT v.id, v.variant_key, v.variant_name, v.is_control, 
                           v.impressions, v.conversions, v.conversion_rate, v.content_changes
                    FROM campaign_ab_test_variants v
                    WHERE v.test_id = ?
                    ORDER BY v.variant_key
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$test['id']]);
                $test['variants'] = $stmt->fetchAll();
                
                // Parse JSON fields
                foreach ($test['variants'] as &$variant) {
                    $variant['content_changes'] = json_decode($variant['content_changes'] ?? '{}');
                }
            }
        }
        
        return ['data' => $campaign];
    }
    
    private function createCampaign() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $this->pdo->beginTransaction();
            
            // Create campaign
            $sql = "INSERT INTO campaign_landing_pages (
                        slug, campaign_name, campaign_type, template, status, 
                        start_date, end_date, target_audience, offer_details, 
                        conversion_goal, utm_parameters, settings, is_ab_test, created_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['slug'],
                $data['campaign_name'],
                $data['campaign_type'],
                $data['template'] ?? 'default',
                $data['status'] ?? 'draft',
                $data['start_date'] ?? null,
                $data['end_date'] ?? null,
                json_encode($data['target_audience'] ?? []),
                json_encode($data['offer_details'] ?? []),
                $data['conversion_goal'] ?? 'lead',
                json_encode($data['utm_parameters'] ?? []),
                json_encode($data['settings'] ?? []),
                $data['is_ab_test'] ?? false,
                $data['created_by']
            ]);
            
            $campaignId = $this->pdo->lastInsertId();
            
            // Create translations
            foreach ($data['translations'] as $langCode => $translation) {
                $langId = $this->getLanguageId($langCode);
                
                $sql = "INSERT INTO campaign_landing_page_translations (
                            page_id, language_id, title, subtitle, hero_content, 
                            offer_headline, offer_description, benefits_content,
                            testimonials, faq_content, cta_primary, cta_secondary,
                            form_title, success_message, meta_title, meta_description
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $campaignId,
                    $langId,
                    $translation['title'],
                    $translation['subtitle'] ?? null,
                    $translation['hero_content'] ?? null,
                    $translation['offer_headline'] ?? null,
                    $translation['offer_description'] ?? null,
                    $translation['benefits_content'] ?? null,
                    json_encode($translation['testimonials'] ?? []),
                    json_encode($translation['faq_content'] ?? []),
                    $translation['cta_primary'] ?? null,
                    $translation['cta_secondary'] ?? null,
                    $translation['form_title'] ?? null,
                    $translation['success_message'] ?? null,
                    $translation['meta_title'] ?? null,
                    $translation['meta_description'] ?? null
                ]);
            }
            
            // Create A/B test if applicable
            if ($data['is_ab_test'] && isset($data['ab_test'])) {
                $sql = "INSERT INTO campaign_ab_tests (
                            campaign_id, test_name, status, start_date, end_date,
                            traffic_split, metrics, created_by
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $campaignId,
                    $data['ab_test']['test_name'],
                    $data['ab_test']['status'] ?? 'draft',
                    $data['ab_test']['start_date'] ?? null,
                    $data['ab_test']['end_date'] ?? null,
                    json_encode($data['ab_test']['traffic_split'] ?? []),
                    json_encode($data['ab_test']['metrics'] ?? []),
                    $data['created_by']
                ]);
                
                $testId = $this->pdo->lastInsertId();
                
                // Create variants
                foreach ($data['ab_test']['variants'] as $variant) {
                    $sql = "INSERT INTO campaign_ab_test_variants (
                                test_id, variant_name, variant_key, is_control, content_changes
                            ) VALUES (?, ?, ?, ?, ?)";
                    
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        $testId,
                        $variant['variant_name'],
                        $variant['variant_key'],
                        $variant['is_control'] ?? false,
                        json_encode($variant['content_changes'] ?? [])
                    ]);
                }
            }
            
            $this->pdo->commit();
            
            return [
                'data' => [
                    'id' => $campaignId,
                    'message' => 'Campaign created successfully'
                ]
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            return ['error' => 'Failed to create campaign: ' . $e->getMessage()];
        }
    }
    
    // ===== CAMPAIGN LEADS METHODS =====
    
    private function getCampaignLeads() {
        $campaignId = $_GET['campaign_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        $sql = "
            SELECT cl.*, c.campaign_name
            FROM campaign_leads cl
            JOIN campaign_landing_pages c ON cl.campaign_id = c.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($campaignId) {
            $sql .= " AND cl.campaign_id = ?";
            $params[] = $campaignId;
        }
        
        if ($status) {
            $sql .= " AND cl.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY cl.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $leads = $stmt->fetchAll();
        
        // Get total count for pagination
        $countSql = "
            SELECT COUNT(*) as total
            FROM campaign_leads cl
            WHERE 1=1
        ";
        
        $countParams = [];
        
        if ($campaignId) {
            $countSql .= " AND cl.campaign_id = ?";
            $countParams[] = $campaignId;
        }
        
        if ($status) {
            $countSql .= " AND cl.status = ?";
            $countParams[] = $status;
        }
        
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($countParams);
        $total = $countStmt->fetch()['total'];
        
        return [
            'data' => $leads,
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'pages' => ceil($total / $limit)
            ]
        ];
    }
    
    private function createCampaignLead() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Validate required fields
            if (empty($data['campaign_id'])) {
                throw new Exception('Campaign ID is required');
            }
            
            if (empty($data['name']) || empty($data['email'])) {
                throw new Exception('Name and email are required');
            }
            
            // Insert lead
            $sql = "INSERT INTO campaign_leads (
                        campaign_id, name, email, phone, company, job_title,
                        industry, facility_type, preferred_date, message, is_urgent,
                        utm_source, utm_medium, utm_campaign, utm_term, utm_content,
                        referrer, landing_page, user_agent, ip_address, device_type
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['campaign_id'],
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['company'] ?? null,
                $data['job_title'] ?? null,
                $data['industry'] ?? null,
                $data['facility_type'] ?? null,
                $data['preferred_date'] ?? null,
                $data['message'] ?? null,
                $data['is_urgent'] ?? false,
                $data['utm_source'] ?? null,
                $data['utm_medium'] ?? null,
                $data['utm_campaign'] ?? null,
                $data['utm_term'] ?? null,
                $data['utm_content'] ?? null,
                $data['referrer'] ?? null,
                $data['landing_page'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $this->getDeviceType()
            ]);
            
            $leadId = $this->pdo->lastInsertId();
            
            // Track conversion
            $this->trackLeadConversion($leadId, $data);
            
            return [
                'data' => [
                    'id' => $leadId,
                    'message' => 'Lead created successfully'
                ]
            ];
            
        } catch (Exception $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }
    
    // ===== CONVERSION TRACKING METHODS =====
    
    private function trackConversion() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Validate required fields
            if (empty($data['campaign_id']) || empty($data['event_name'])) {
                throw new Exception('Campaign ID and event name are required');
            }
            
            // Insert conversion
            $sql = "INSERT INTO campaign_conversions (
                        campaign_id, lead_id, event_name, event_category,
                        event_value, event_currency, event_data,
                        utm_source, utm_medium, utm_campaign, utm_term, utm_content,
                        page_url, referrer, user_agent, ip_address, device_type
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['campaign_id'],
                $data['lead_id'] ?? null,
                $data['event_name'],
                $data['event_category'] ?? null,
                $data['event_value'] ?? null,
                $data['event_currency'] ?? 'EGP',
                json_encode($data['event_data'] ?? []),
                $data['utm_source'] ?? null,
                $data['utm_medium'] ?? null,
                $data['utm_campaign'] ?? null,
                $data['utm_term'] ?? null,
                $data['utm_content'] ?? null,
                $data['page_url'] ?? null,
                $data['referrer'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $this->getDeviceType()
            ]);
            
            $conversionId = $this->pdo->lastInsertId();
            
            // Update A/B test metrics if applicable
            if (($data['event_name'] === 'Lead' || $data['event_name'] === 'Purchase') && 
                isset($data['event_data']['variant'])) {
                
                $this->updateABTestConversion(
                    $data['campaign_id'],
                    $data['event_data']['variant']
                );
            }
            
            return [
                'data' => [
                    'id' => $conversionId,
                    'message' => 'Conversion tracked successfully'
                ]
            ];
            
        } catch (Exception $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getCampaignPerformance($id) {
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        // Get campaign details
        $sql = "
            SELECT c.id, c.campaign_name, c.campaign_type, c.conversion_goal,
                   c.start_date, c.end_date, ct.title
            FROM campaign_landing_pages c
            JOIN campaign_landing_page_translations ct ON c.id = ct.page_id
            JOIN languages l ON ct.language_id = l.id
            WHERE c.id = ? AND l.code = 'en'
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $campaign = $stmt->fetch();
        
        if (!$campaign) {
            http_response_code(404);
            return ['error' => 'Campaign not found'];
        }
        
        // Get performance metrics
        $sql = "
            SELECT 
                COUNT(DISTINCT cl.id) AS total_leads,
                COUNT(DISTINCT CASE WHEN cl.status = 'converted' THEN cl.id END) AS converted_leads,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) AS page_views,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) AS form_submissions,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) > 0 
                    THEN ROUND((COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) * 100.0) / 
                         COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END), 2)
                    ELSE 0
                END AS conversion_rate
            FROM campaign_landing_pages c
            LEFT JOIN campaign_leads cl ON c.id = cl.campaign_id
            LEFT JOIN campaign_conversions cc ON c.id = cc.campaign_id
            WHERE c.id = ?
        ";
        
        $params = [$id];
        
        if ($startDate) {
            $sql .= " AND (cl.created_at >= ? OR cc.created_at >= ?)";
            $params[] = $startDate;
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND (cl.created_at <= ? OR cc.created_at <= ?)";
            $params[] = $endDate;
            $params[] = $endDate;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $performance = $stmt->fetch();
        
        // Get daily metrics
        $sql = "
            SELECT 
                DATE(cc.created_at) AS date,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) AS views,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) AS leads,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) > 0 
                    THEN ROUND((COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) * 100.0) / 
                         COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END), 2)
                    ELSE 0
                END AS daily_rate
            FROM campaign_conversions cc
            WHERE cc.campaign_id = ?
        ";
        
        $params = [$id];
        
        if ($startDate) {
            $sql .= " AND cc.created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND cc.created_at <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY DATE(cc.created_at) ORDER BY DATE(cc.created_at)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $dailyMetrics = $stmt->fetchAll();
        
        // Get UTM source breakdown
        $sql = "
            SELECT 
                COALESCE(cc.utm_source, 'direct') AS source,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) AS views,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) AS leads,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) > 0 
                    THEN ROUND((COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) * 100.0) / 
                         COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END), 2)
                    ELSE 0
                END AS source_rate
            FROM campaign_conversions cc
            WHERE cc.campaign_id = ?
        ";
        
        $params = [$id];
        
        if ($startDate) {
            $sql .= " AND cc.created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND cc.created_at <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY COALESCE(cc.utm_source, 'direct') ORDER BY leads DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $sourceBreakdown = $stmt->fetchAll();
        
        // Get device breakdown
        $sql = "
            SELECT 
                cc.device_type,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) AS views,
                COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) AS leads,
                CASE 
                    WHEN COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) > 0 
                    THEN ROUND((COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) * 100.0) / 
                         COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END), 2)
                    ELSE 0
                END AS device_rate
            FROM campaign_conversions cc
            WHERE cc.campaign_id = ? AND cc.device_type IS NOT NULL
        ";
        
        $params = [$id];
        
        if ($startDate) {
            $sql .= " AND cc.created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND cc.created_at <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY cc.device_type ORDER BY leads DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $deviceBreakdown = $stmt->fetchAll();
        
        return [
            'data' => [
                'campaign' => $campaign,
                'performance' => $performance,
                'daily_metrics' => $dailyMetrics,
                'source_breakdown' => $sourceBreakdown,
                'device_breakdown' => $deviceBreakdown
            ]
        ];
    }
    
    // ===== A/B TESTING METHODS =====
    
    private function getABTests() {
        $campaignId = $_GET['campaign_id'] ?? null;
        $status = $_GET['status'] ?? null;
        
        $sql = "
            SELECT t.id, t.campaign_id, c.campaign_name, t.test_name, t.status,
                   t.start_date, t.end_date, t.winning_variant, t.metrics,
                   t.created_at
            FROM campaign_ab_tests t
            JOIN campaign_landing_pages c ON t.campaign_id = c.id
        ";
        
        $params = [];
        
        if ($campaignId) {
            $sql .= " WHERE t.campaign_id = ?";
            $params[] = $campaignId;
            
            if ($status) {
                $sql .= " AND t.status = ?";
                $params[] = $status;
            }
        } else if ($status) {
            $sql .= " WHERE t.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $tests = $stmt->fetchAll();
        
        // Parse JSON fields
        foreach ($tests as &$test) {
            $test['metrics'] = json_decode($test['metrics'] ?? '{}');
        }
        
        return ['data' => $tests];
    }
    
    private function getABTestVariants($testId) {
        $sql = "
            SELECT v.id, v.test_id, v.variant_key, v.variant_name, v.is_control,
                   v.impressions, v.conversions, v.conversion_rate, v.content_changes,
                   v.created_at, v.updated_at
            FROM campaign_ab_test_variants v
            WHERE v.test_id = ?
            ORDER BY v.variant_key
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$testId]);
        $variants = $stmt->fetchAll();
        
        // Parse JSON fields
        foreach ($variants as &$variant) {
            $variant['content_changes'] = json_decode($variant['content_changes'] ?? '{}');
        }
        
        return ['data' => $variants];
    }
    
    private function recordABTestImpression() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            // Validate required fields
            if (empty($data['test_id']) || empty($data['variant_key'])) {
                throw new Exception('Test ID and variant key are required');
            }
            
            // Update impression count
            $sql = "
                UPDATE campaign_ab_test_variants
                SET impressions = impressions + 1,
                    conversion_rate = CASE 
                        WHEN impressions > 0 THEN conversions * 100.0 / (impressions + 1)
                        ELSE 0
                    END
                WHERE test_id = ? AND variant_key = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['test_id'],
                $data['variant_key']
            ]);
            
            return [
                'data' => [
                    'message' => 'Impression recorded successfully'
                ]
            ];
            
        } catch (Exception $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }
    
    // ===== CAMPAIGN CONTENT METHODS =====
    
    private function getCampaignContent($slug) {
        $lang = $_GET['lang'] ?? 'en';
        
        // Get campaign with translations
        $sql = "
            SELECT c.id, c.slug, c.campaign_name, c.campaign_type, c.template,
                   c.offer_details, c.settings, c.is_ab_test, c.utm_parameters,
                   ct.title, ct.subtitle, ct.hero_content, ct.offer_headline, 
                   ct.offer_description, ct.benefits_content, ct.testimonials,
                   ct.faq_content, ct.cta_primary, ct.cta_secondary, ct.form_title,
                   ct.success_message, ct.meta_title, ct.meta_description,
                   ct.og_title, ct.og_description, ct.og_image
            FROM campaign_landing_pages c
            JOIN campaign_landing_page_translations ct ON c.id = ct.page_id
            JOIN languages l ON ct.language_id = l.id
            WHERE c.slug = ? AND l.code = ? AND c.status = 'published'
            AND (c.start_date IS NULL OR c.start_date <= NOW())
            AND (c.end_date IS NULL OR c.end_date >= NOW())
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$slug, $lang]);
        $campaign = $stmt->fetch();
        
        if (!$campaign) {
            http_response_code(404);
            return ['error' => 'Campaign not found or not active'];
        }
        
        // Parse JSON fields
        $campaign['offer_details'] = json_decode($campaign['offer_details'] ?? '{}');
        $campaign['settings'] = json_decode($campaign['settings'] ?? '{}');
        $campaign['utm_parameters'] = json_decode($campaign['utm_parameters'] ?? '{}');
        $campaign['testimonials'] = json_decode($campaign['testimonials'] ?? '[]');
        $campaign['faq_content'] = json_decode($campaign['faq_content'] ?? '[]');
        
        // Get A/B test variant if applicable
        if ($campaign['is_ab_test']) {
            $sql = "
                SELECT t.id AS test_id
                FROM campaign_ab_tests t
                WHERE t.campaign_id = ? AND t.status = 'running'
                LIMIT 1
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$campaign['id']]);
            $test = $stmt->fetch();
            
            if ($test) {
                // Get variants
                $sql = "
                    SELECT v.variant_key, v.variant_name, v.is_control, v.content_changes
                    FROM campaign_ab_test_variants v
                    WHERE v.test_id = ?
                    ORDER BY v.variant_key
                ";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$test['test_id']]);
                $variants = $stmt->fetchAll();
                
                // Randomly select a variant based on traffic split
                // In a real implementation, this would use a more sophisticated algorithm
                // and would maintain consistency for the same user
                $selectedVariant = $variants[array_rand($variants)];
                
                // Parse content changes
                $contentChanges = json_decode($selectedVariant['content_changes'] ?? '{}', true);
                
                // Apply content changes to campaign
                foreach ($contentChanges as $field => $value) {
                    if (isset($campaign[$field])) {
                        $campaign[$field] = $value;
                    }
                }
                
                // Add variant info
                $campaign['ab_test'] = [
                    'test_id' => $test['test_id'],
                    'variant_key' => $selectedVariant['variant_key'],
                    'variant_name' => $selectedVariant['variant_name']
                ];
            }
        }
        
        return ['data' => $campaign];
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
    
    private function getDeviceType() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4))) {
            return 'mobile';
        }
        
        if (preg_match('/tablet|ipad|playbook|silk|android(?!.*mobile)/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }
    
    private function trackLeadConversion($leadId, $data) {
        // Insert conversion event
        $sql = "INSERT INTO campaign_conversions (
                    campaign_id, lead_id, event_name, event_category,
                    event_value, event_currency, event_data,
                    utm_source, utm_medium, utm_campaign, utm_term, utm_content,
                    page_url, referrer, user_agent, ip_address, device_type
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['campaign_id'],
            $leadId,
            'Lead',
            'form_submission',
            $data['event_value'] ?? null,
            $data['event_currency'] ?? 'EGP',
            json_encode($data['event_data'] ?? []),
            $data['utm_source'] ?? null,
            $data['utm_medium'] ?? null,
            $data['utm_campaign'] ?? null,
            $data['utm_term'] ?? null,
            $data['utm_content'] ?? null,
            $data['landing_page'] ?? null,
            $data['referrer'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $this->getDeviceType()
        ]);
        
        // Update A/B test metrics if applicable
        if (isset($data['event_data']['variant'])) {
            $this->updateABTestConversion(
                $data['campaign_id'],
                $data['event_data']['variant']
            );
        }
    }
    
    private function updateABTestConversion($campaignId, $variantKey) {
        // Find active A/B test for campaign
        $sql = "
            SELECT t.id
            FROM campaign_ab_tests t
            WHERE t.campaign_id = ? AND t.status = 'running'
            LIMIT 1
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$campaignId]);
        $test = $stmt->fetch();
        
        if (!$test) {
            return;
        }
        
        // Update variant conversion
        $sql = "
            UPDATE campaign_ab_test_variants
            SET conversions = conversions + 1,
                conversion_rate = CASE 
                    WHEN impressions > 0 THEN (conversions + 1) * 100.0 / impressions
                    ELSE 0
                END
            WHERE test_id = ? AND variant_key = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $test['id'],
            $variantKey
        ]);
        
        // Check if we should determine a winner
        $sql = "
            SELECT COUNT(*) AS variant_count,
                   MIN(impressions) AS min_impressions
            FROM campaign_ab_test_variants
            WHERE test_id = ?
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$test['id']]);
        $stats = $stmt->fetch();
        
        // If all variants have at least 100 impressions, determine winner
        if ($stats['min_impressions'] >= 100) {
            $sql = "
                SELECT determine_ab_test_winner(?)
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$test['id']]);
        }
    }
}

// Initialize router and process request
$router = new CampaignRouter($pdo);
$response = $router->route();

// Output JSON response
echo json_encode($response);