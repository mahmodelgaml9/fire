/*
  # Add Campaign Landing Pages Schema

  1. New Tables
    - `campaign_landing_pages` - Stores landing page configurations
    - `campaign_landing_page_translations` - Stores multilingual content
    - `campaign_leads` - Stores leads captured from landing pages
    - `campaign_conversions` - Tracks conversion events
    - `campaign_ab_tests` - Stores A/B test configurations
    - `campaign_ab_test_variants` - Stores variant configurations for A/B tests
*/

-- ===== CAMPAIGN LANDING PAGES =====

-- Campaign Landing Pages
CREATE TABLE IF NOT EXISTS campaign_landing_pages (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    slug VARCHAR(100) NOT NULL UNIQUE,
    campaign_name VARCHAR(100) NOT NULL,
    campaign_type VARCHAR(50) NOT NULL, -- retargeting, acquisition, promotion, etc.
    template VARCHAR(50) DEFAULT 'default',
    status ENUM('draft', 'published', 'archived', 'scheduled') DEFAULT 'draft',
    start_date TIMESTAMP NULL,
    end_date TIMESTAMP NULL,
    target_audience JSON, -- targeting criteria
    offer_details JSON, -- discount, promotion details
    conversion_goal VARCHAR(50), -- lead, sale, signup, etc.
    utm_parameters JSON, -- default UTM parameters
    settings JSON, -- page-specific settings
    is_ab_test BOOLEAN DEFAULT FALSE,
    created_by CHAR(36) NOT NULL,
    updated_by CHAR(36),
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Campaign Landing Page Translations
CREATE TABLE IF NOT EXISTS campaign_landing_page_translations (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    page_id CHAR(36) NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    hero_content TEXT,
    offer_headline VARCHAR(255),
    offer_description TEXT,
    benefits_content TEXT,
    testimonials JSON, -- array of testimonials
    faq_content JSON, -- array of FAQs
    cta_primary VARCHAR(100),
    cta_secondary VARCHAR(100),
    form_title VARCHAR(255),
    success_message TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    og_title VARCHAR(255),
    og_description TEXT,
    og_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (page_id) REFERENCES campaign_landing_pages(id) ON DELETE CASCADE
);

-- Campaign Leads
CREATE TABLE IF NOT EXISTS campaign_leads (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    campaign_id CHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    job_title VARCHAR(100),
    industry VARCHAR(100),
    facility_type VARCHAR(100),
    preferred_date DATE,
    message TEXT,
    is_urgent BOOLEAN DEFAULT FALSE,
    utm_source VARCHAR(100),
    utm_medium VARCHAR(100),
    utm_campaign VARCHAR(100),
    utm_term VARCHAR(100),
    utm_content VARCHAR(100),
    referrer VARCHAR(500),
    landing_page VARCHAR(500),
    user_agent TEXT,
    ip_address VARCHAR(45),
    device_type VARCHAR(20),
    status ENUM('new', 'contacted', 'qualified', 'converted', 'lost') DEFAULT 'new',
    assigned_to CHAR(36),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaign_landing_pages(id)
);

-- Campaign Conversions
CREATE TABLE IF NOT EXISTS campaign_conversions (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    campaign_id CHAR(36) NOT NULL,
    lead_id CHAR(36),
    event_name VARCHAR(100) NOT NULL, -- ViewContent, Lead, Purchase, etc.
    event_category VARCHAR(50),
    event_value DECIMAL(10,2),
    event_currency VARCHAR(3) DEFAULT 'EGP',
    event_data JSON,
    utm_source VARCHAR(100),
    utm_medium VARCHAR(100),
    utm_campaign VARCHAR(100),
    utm_term VARCHAR(100),
    utm_content VARCHAR(100),
    page_url VARCHAR(500),
    referrer VARCHAR(500),
    user_agent TEXT,
    ip_address VARCHAR(45),
    device_type VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaign_landing_pages(id),
    FOREIGN KEY (lead_id) REFERENCES campaign_leads(id) ON DELETE SET NULL
);

-- Campaign A/B Tests
CREATE TABLE IF NOT EXISTS campaign_ab_tests (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    campaign_id CHAR(36) NOT NULL,
    test_name VARCHAR(100) NOT NULL,
    status ENUM('draft', 'running', 'completed', 'paused') DEFAULT 'draft',
    start_date TIMESTAMP NULL,
    end_date TIMESTAMP NULL,
    traffic_split JSON, -- percentage allocation to variants
    winning_variant VARCHAR(50),
    metrics JSON, -- metrics to track for determining winner
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaign_landing_pages(id) ON DELETE CASCADE
);

-- Campaign A/B Test Variants
CREATE TABLE IF NOT EXISTS campaign_ab_test_variants (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    test_id CHAR(36) NOT NULL,
    variant_name VARCHAR(50) NOT NULL,
    variant_key VARCHAR(50) NOT NULL, -- A, B, C, etc.
    is_control BOOLEAN DEFAULT FALSE,
    content_changes JSON, -- what's different in this variant
    impressions INT DEFAULT 0,
    conversions INT DEFAULT 0,
    conversion_rate DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES campaign_ab_tests(id) ON DELETE CASCADE
);

-- ===== INDEXES =====

-- Campaign Landing Pages
CREATE INDEX idx_campaign_landing_pages_slug ON campaign_landing_pages(slug);
CREATE INDEX idx_campaign_landing_pages_status ON campaign_landing_pages(status);
CREATE INDEX idx_campaign_landing_pages_dates ON campaign_landing_pages(start_date, end_date);

-- Campaign Leads
CREATE INDEX idx_campaign_leads_campaign ON campaign_leads(campaign_id);
CREATE INDEX idx_campaign_leads_email ON campaign_leads(email);
CREATE INDEX idx_campaign_leads_status ON campaign_leads(status);
CREATE INDEX idx_campaign_leads_created ON campaign_leads(created_at);

-- Campaign Conversions
CREATE INDEX idx_campaign_conversions_campaign ON campaign_conversions(campaign_id);
CREATE INDEX idx_campaign_conversions_lead ON campaign_conversions(lead_id);
CREATE INDEX idx_campaign_conversions_event ON campaign_conversions(event_name);
CREATE INDEX idx_campaign_conversions_created ON campaign_conversions(created_at);

-- Campaign A/B Tests
CREATE INDEX idx_campaign_ab_tests_campaign ON campaign_ab_tests(campaign_id);
CREATE INDEX idx_campaign_ab_tests_status ON campaign_ab_tests(status);


-- ===== VIEWS =====

-- Active campaigns view
CREATE OR REPLACE VIEW active_campaigns AS
SELECT 
    clp.id,
    clp.slug,
    clp.campaign_name,
    clp.campaign_type,
    clp.template,
    clpt.language_id,
    clpt.title,
    clpt.subtitle,
    clpt.offer_headline,
    clpt.meta_title,
    clpt.meta_description,
    clp.start_date,
    clp.end_date,
    clp.offer_details,
    clp.conversion_goal,
    clp.utm_parameters,
    clp.is_ab_test
FROM campaign_landing_pages clp
JOIN campaign_landing_page_translations clpt ON clp.id = clpt.page_id
WHERE clp.status = 'published'
AND (clp.start_date IS NULL OR clp.start_date <= NOW())
AND (clp.end_date IS NULL OR clp.end_date >= NOW());

-- Campaign performance view
CREATE OR REPLACE VIEW campaign_performance AS
SELECT 
    clp.id,
    clp.campaign_name,
    clp.campaign_type,
    clp.conversion_goal,
    COUNT(DISTINCT cl.id) AS total_leads,
    COUNT(DISTINCT CASE WHEN cl.status = 'converted' THEN cl.id END) AS converted_leads,
    COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) AS page_views,
    COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) AS form_submissions,
    CASE 
        WHEN COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END) > 0 
        THEN ROUND((COUNT(DISTINCT CASE WHEN cc.event_name = 'Lead' THEN cc.id END) * 100.0) / 
             COUNT(DISTINCT CASE WHEN cc.event_name = 'ViewContent' THEN cc.id END), 2)
        ELSE 0
    END AS conversion_rate,
    clp.start_date,
    clp.end_date
FROM campaign_landing_pages clp
LEFT JOIN campaign_leads cl ON clp.id = cl.campaign_id
LEFT JOIN campaign_conversions cc ON clp.id = cc.campaign_id
GROUP BY clp.id, clp.campaign_name, clp.campaign_type, clp.conversion_goal, clp.start_date, clp.end_date;

-- A/B test results view
CREATE OR REPLACE VIEW ab_test_results AS
SELECT 
    cat.id AS test_id,
    clp.campaign_name,
    cat.test_name,
    cat.status,
    catv.variant_key,
    catv.variant_name,
    catv.is_control,
    catv.impressions,
    catv.conversions,
    catv.conversion_rate,
    cat.winning_variant,
    cat.start_date,
    cat.end_date
FROM campaign_ab_tests cat
JOIN campaign_landing_pages clp ON cat.campaign_id = clp.id
JOIN campaign_ab_test_variants catv ON cat.id = catv.test_id
ORDER BY cat.id, catv.variant_key;

-- ===== SAMPLE DATA =====

-- Insert sample campaign landing page
-- Note: We generate a UUID for the page and store it in a variable to use in subsequent inserts.
SET @campaign_page_slug = 'fire-safety-assessment-offer';
SET @created_by_user_id = '00000000-0000-0000-0000-000000000000'; -- Replace with an actual user UUID if needed

INSERT INTO campaign_landing_pages (
    id, slug, campaign_name, campaign_type, template, status, 
    start_date, end_date, target_audience, offer_details, 
    conversion_goal, utm_parameters, settings, is_ab_test, created_by
) VALUES (
    UUID(), -- Generate UUID for the new page
    @campaign_page_slug,
    '20% Off Fire Safety Assessment',
    'retargeting',
    'discount-offer',
    'published',
    NOW(),
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    '{"industries": ["manufacturing", "chemical", "warehouse"], "facility_size": "medium-large", "previous_visitors": true}',
    '{"discount_percentage": 20, "original_price": 15000, "discounted_price": 12000, "currency": "EGP", "expiry_days": 3}',
    'lead',
    '{"utm_source": "facebook", "utm_medium": "cpc", "utm_campaign": "retargeting-q3-2025"}',
    '{"show_countdown": true, "show_testimonials": true, "exit_intent": true, "sticky_cta": true}',
    false,
    @created_by_user_id
);

-- Store the ID of the newly inserted page in a variable
SET @last_page_id = (SELECT id FROM campaign_landing_pages WHERE slug = @campaign_page_slug);

-- Insert English translation
INSERT INTO campaign_landing_page_translations (
    page_id, language_id, title, subtitle, hero_content, 
    offer_headline, offer_description, benefits_content,
    testimonials, faq_content, cta_primary, cta_secondary,
    form_title, success_message, meta_title, meta_description
) VALUES (
    @last_page_id,
    1, -- English
    'Special Offer: 20% Off Fire Safety Assessment',
    'Protect Your Facility. Ensure Compliance. Save Money.',
    'Get a comprehensive fire safety assessment from certified engineers. Identify risks, ensure compliance, and protect your business.',
    'Save 20% on Professional Fire Safety Assessment',
    'Limited time offer: Act now and save 20% on our comprehensive fire safety assessment. Certified engineers, same-day response, and compliance guarantee.',
    'Professional evaluation with actionable insights and compliance guidance',
    '[{"name":"Ahmed Hassan","position":"Safety Manager, Delta Industries","quote":"The assessment identified critical issues we had overlooked for years. Their recommendations helped us pass civil defense inspection on the first try.","rating":5},{"name":"Fatma Mahmoud","position":"Facility Director, Cairo Textiles","quote":"Professional, thorough, and incredibly valuable. The assessment paid for itself by identifying cost-saving opportunities in our fire protection systems.","rating":5}]',
    '[{"question":"How long does the assessment take?","answer":"The on-site assessment typically takes 4-8 hours depending on facility size and complexity. You will receive the preliminary findings on the same day, with the full report delivered within 3 business days."},{"question":"How long is this discount valid?","answer":"This special 20% discount is available for a limited time only. The offer expires in 3 days, and we have only 5 slots available at this rate. Once these slots are filled, the regular price will apply."}]',
    'CLAIM YOUR 20% DISCOUNT NOW',
    'Learn More',
    'Book Your Discounted Assessment',
    'Thank you! Your discount has been claimed. Our team will contact you within 4 hours to schedule your assessment.',
    'Special Offer: 20% Off Fire Safety Assessment | Sphinx Fire',
    'Limited time offer: 20% off professional fire safety assessment for industrial facilities. Certified engineers, same-day response, and compliance guarantee.'
);

-- Insert Arabic translation
INSERT INTO campaign_landing_page_translations (
    page_id, language_id, title, subtitle, hero_content, 
    offer_headline, offer_description, benefits_content,
    testimonials, faq_content, cta_primary, cta_secondary,
    form_title, success_message, meta_title, meta_description
) VALUES (
    @last_page_id,
    2, -- Arabic
    'عرض خاص: خصم 20٪ على تقييم السلامة من الحريق',
    'احمِ منشأتك. ضمان الامتثال. وفر المال.',
    'احصل على تقييم شامل للسلامة من الحريق من مهندسين معتمدين. حدد المخاطر، وضمان الامتثال، وحماية عملك.',
    'وفر 20٪ على تقييم السلامة من الحريق الاحترافي',
    'عرض لفترة محدودة: اغتنم الفرصة الآن ووفر 20٪ على تقييم السلامة من الحريق الشامل. مهندسون معتمدون، واستجابة في نفس اليوم، وضمان الامتثال.',
    'تقييم احترافي مع رؤى قابلة للتنفيذ وإرشادات الامتثال',
    '[{"name":"أحمد حسن","position":"مدير السلامة، دلتا للصناعات","quote":"حدد التقييم المشكلات الحرجة التي أغفلناها لسنوات. ساعدتنا توصياتهم على اجتياز فحص الدفاع المدني من المحاولة الأولى.","rating":5},{"name":"فاطمة محمود","position":"مديرة المنشأة، القاهرة للمنسوجات","quote":"احترافية وشاملة وذات قيمة لا تصدق. دفع التقييم ثمنه من خلال تحديد فرص توفير التكاليف في أنظمة الحماية من الحريق لدينا.","rating":5}]',
    '[{"question":"كم من الوقت يستغرق التقييم؟","answer":"يستغرق التقييم الميداني عادة من 4 إلى 8 ساعات حسب حجم المنشأة وتعقيدها. ستتلقى النتائج الأولية في نفس اليوم، مع تسليم التقرير الكامل في غضون 3 أيام عمل."},{"question":"ما هي مدة صلاحية هذا الخصم؟","answer":"هذا الخصم الخاص بنسبة 20٪ متاح لفترة محدودة فقط. ينتهي العرض في غضون 3 أيام، ولدينا 5 أماكن فقط متاحة بهذا السعر. بمجرد ملء هذه الأماكن، سيتم تطبيق السعر العادي."}]',
    'احصل على خصم 20٪ الآن',
    'اعرف المزيد',
    'احجز تقييمك المخفض',
    'شكرًا لك! تم المطالبة بالخصم الخاص بك. سيتصل بك فريقنا في غضون 4 ساعات لجدولة التقييم الخاص بك.',
    'عرض خاص: خصم 20٪ على تقييم السلامة من الحريق | سفينكس فاير',
    'عرض لفترة محدودة: خصم 20٪ على تقييم السلامة من الحريق الاحترافي للمنشآت الصناعية. مهندسون معتمدون، واستجابة في نفس اليوم، وضمان الامتثال.'
);

-- ===== FUNCTIONS (MySQL Version) =====

-- Make sure to change the delimiter so you can use semicolons inside the procedures
DELIMITER $$

-- Procedure to record A/B test impression
CREATE PROCEDURE record_ab_test_impression(
    IN p_test_id CHAR(36),
    IN p_variant_key VARCHAR(50)
)
BEGIN
    UPDATE campaign_ab_test_variants
    SET impressions = impressions + 1,
        conversion_rate = CASE 
            WHEN (impressions + 1) > 0 THEN (conversions * 100.0) / (impressions + 1)
            ELSE 0
        END
    WHERE test_id = p_test_id AND variant_key = p_variant_key;
END$$


-- Procedure to determine A/B test winner
CREATE PROCEDURE determine_ab_test_winner(
    IN p_test_id CHAR(36)
)
BEGIN
    DECLARE v_winner VARCHAR(50);
    
    -- Find variant with highest conversion rate (with a minimum sample size)
    SELECT variant_key INTO v_winner
    FROM campaign_ab_test_variants
    WHERE test_id = p_test_id
    AND impressions >= 100 -- Minimum sample size
    ORDER BY conversion_rate DESC
    LIMIT 1;
    
    -- Update the test with the winner if found
    IF v_winner IS NOT NULL THEN
        UPDATE campaign_ab_tests
        SET winning_variant = v_winner,
            status = 'completed'
        WHERE id = p_test_id;
    END IF;
    
    -- You can select the winner to see the result after calling the procedure
    SELECT v_winner;
END$$


-- Function to track campaign conversion
-- This is more complex and combines insertion and updates. A procedure is a better fit.
CREATE PROCEDURE track_campaign_conversion(
    IN p_campaign_id CHAR(36),
    IN p_lead_id CHAR(36),
    IN p_event_name VARCHAR(100),
    IN p_event_category VARCHAR(50),
    IN p_event_value DECIMAL(10,2),
    IN p_event_currency VARCHAR(3),
    IN p_event_data JSON,
    IN p_utm_source VARCHAR(100),
    IN p_utm_medium VARCHAR(100),
    IN p_utm_campaign VARCHAR(100),
    IN p_utm_term VARCHAR(100),
    IN p_utm_content VARCHAR(100),
    IN p_page_url VARCHAR(500),
    IN p_referrer VARCHAR(500),
    IN p_user_agent TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_device_type VARCHAR(20),
    OUT v_conversion_id CHAR(36)
)
BEGIN
    SET v_conversion_id = UUID();

    INSERT INTO campaign_conversions (
        id, campaign_id, lead_id, event_name, event_category, event_value, event_currency,
        event_data, utm_source, utm_medium, utm_campaign, utm_term, utm_content,
        page_url, referrer, user_agent, ip_address, device_type
    ) VALUES (
        v_conversion_id, p_campaign_id, p_lead_id, p_event_name, p_event_category, p_event_value, p_event_currency,
        p_event_data, p_utm_source, p_utm_medium, p_campaign, p_utm_term, p_utm_content,
        p_page_url, p_referrer, p_user_agent, p_ip_address, p_device_type
    );
    
    -- Update A/B test metrics if applicable
    IF p_event_name = 'Lead' OR p_event_name = 'Purchase' THEN
        UPDATE campaign_ab_test_variants
        SET conversions = conversions + 1,
            conversion_rate = CASE 
                WHEN impressions > 0 THEN (conversions + 1) * 100.0 / impressions
                ELSE 0
            END
        WHERE test_id IN (
            SELECT id FROM campaign_ab_tests 
            WHERE campaign_id = p_campaign_id AND status = 'running'
        )
        AND variant_key = COALESCE(
            JSON_UNQUOTE(JSON_EXTRACT(p_event_data, '$.variant')),
            'A' -- Default to A if no variant specified
        );
    END IF;
    
END$$

-- Don't forget to change the delimiter back to the default
DELIMITER ;
