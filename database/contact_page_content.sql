START TRANSACTION;

-- =====================================================
-- إضافة محتوى صفحة الاتصال في قاعدة البيانات
-- =====================================================

-- إضافة صفحة الاتصال إذا لم تكن موجودة
INSERT INTO pages (id, slug, status, published_at, created_at, updated_at) 
VALUES (4, 'contact', 'published', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات صفحة الاتصال
INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description, created_at, updated_at) 
VALUES 
(4, 1, 'Contact Us - Sphinx Fire', 'Contact Sphinx Fire - Professional Fire Safety Services in Egypt', 'Contact Sphinx Fire for professional fire safety services. Get free consultation, site assessment, and emergency response. Located in Sadat City, Egypt.', NOW(), NOW()),
(4, 2, 'اتصل بنا - سفنكس فاير', 'اتصل بسفنكس فاير - خدمات السلامة من الحريق الاحترافية في مصر', 'اتصل بسفنكس فاير لخدمات السلامة من الحريق الاحترافية. احصل على استشارة مجانية وتقييم موقع واستجابة طوارئ. موجودون في مدينة السادات، مصر.', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة أقسام صفحة الاتصال
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES 
-- Hero Section
(40, 4, 'hero', 'contact-hero', 1, TRUE, NOW(), NOW()),

-- Contact Options Section
(41, 4, 'contact_options', 'contact-options', 2, TRUE, NOW(), NOW()),

-- Contact Form Section
(42, 4, 'contact_form', 'contact-form', 3, TRUE, NOW(), NOW()),

-- Location Section
(43, 4, 'location', 'contact-location', 4, TRUE, NOW(), NOW()),

-- Final CTA Section
(44, 4, 'cta', 'contact-final-cta', 5, TRUE, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات أقسام صفحة الاتصال
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) 
VALUES 
-- Hero Section - English
(40, 1, 'Let\'s Talk Safety.', 'We\'re Just a Call Away.', 'We respond fast because your facility can\'t wait. Contact Sphinx Fire today for professional fire safety solutions and emergency response.', 'Request Site Visit', NOW(), NOW()),

-- Hero Section - Arabic
(40, 2, 'دعنا نتحدث عن السلامة.', 'نحن على بعد مكالمة واحدة.', 'نستجيب بسرعة لأن منشأتك لا تستطيع الانتظار. اتصل بسفنكس فاير اليوم لحلول السلامة من الحريق الاحترافية واستجابة الطوارئ.', 'طلب زيارة موقع', NOW(), NOW()),

-- Contact Options - English
(41, 1, 'Multiple Ways to Reach Us', 'Choose the method that works best for your urgent needs', 'Professional fire safety services available through phone, email, WhatsApp, or office visit. Emergency response available 24/7.', 'Get Directions', NOW(), NOW()),

-- Contact Options - Arabic
(41, 2, 'طرق متعددة للوصول إلينا', 'اختر الطريقة التي تناسب احتياجاتك العاجلة', 'خدمات السلامة من الحريق الاحترافية متاحة عبر الهاتف والبريد الإلكتروني والواتساب أو زيارة المكتب. استجابة طوارئ متاحة 24/7.', 'احصل على الاتجاهات', NOW(), NOW()),

-- Contact Form - English
(42, 1, 'Request a Callback or Consultation', 'Professional Fire Safety Assessment', 'Fill out this form and our expert team will contact you within 24 hours to discuss your fire safety requirements and schedule a free site assessment. We provide comprehensive solutions for all industrial facilities.', 'Send Request', NOW(), NOW()),

-- Contact Form - Arabic
(42, 2, 'طلب اتصال أو استشارة', 'تقييم السلامة من الحريق الاحترافي', 'املأ هذا النموذج وسيتصل بك فريقنا الخبير خلال 24 ساعة لمناقشة متطلبات السلامة من الحريق وجدولة تقييم موقع مجاني. نقدم حلول شاملة لجميع المنشآت الصناعية.', 'إرسال الطلب', NOW(), NOW()),

-- Location - English
(43, 1, 'Find Us in Sadat City', 'Strategically located in Egypt\'s industrial hub for fast response', 'Our strategic location in Sadat Industrial City ensures rapid response times and local expertise. We\'re minutes away from major manufacturing facilities and provide 24/7 emergency support.', 'Get Directions to Our Office', NOW(), NOW()),

-- Location - Arabic
(43, 2, 'اعثر علينا في مدينة السادات', 'موقع استراتيجي في مركز مصر الصناعي لاستجابة سريعة', 'موقعنا الاستراتيجي في مدينة السادات الصناعية يضمن أوقات استجابة سريعة وخبرة محلية. نحن على بعد دقائق من المنشآت الصناعية الكبرى ونوفر دعم طوارئ 24/7.', 'احصل على الاتجاهات لمكتبنا', NOW(), NOW()),

-- Final CTA - English
(44, 1, 'We\'re based inside the zone. We\'re closer than you think.', 'Professional Fire Safety Solutions', 'Don\'t wait for an emergency. Get professional fire safety assessment today. Our local presence ensures faster response times, lower costs, and better understanding of your facility\'s specific needs.', 'Book Free Site Assessment', NOW(), NOW()),

-- Final CTA - Arabic
(44, 2, 'نحن داخل المنطقة. نحن أقرب مما تعتقد.', 'حلول السلامة من الحريق الاحترافية', 'لا تنتظر حتى الطوارئ. احصل على تقييم سلامة حريق احترافي اليوم. وجودنا المحلي يضمن أوقات استجابة أسرع وتكاليف أقل وفهم أفضل لاحتياجات منشأتك المحددة.', 'حجز تقييم موقع مجاني', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة إعدادات الموقع إذا لم تكن موجودة
INSERT INTO settings (id, setting_key, setting_value, created_at, updated_at) 
VALUES 
(1, 'company_phone', '+20 123 456 7890', NOW(), NOW()),
(2, 'company_email', 'info@sphinxfire.com', NOW(), NOW()),
(3, 'company_address_line1', 'Industrial Zone, Block 15', NOW(), NOW()),
(4, 'company_address_line2', 'Sadat City, Monufia', NOW(), NOW()),
(5, 'company_address_line3', 'Egypt', NOW(), NOW()),
(6, 'business_hours', 'Saturday - Thursday: 9:00 AM - 6:00 PM', NOW(), NOW()),
(7, 'emergency_phone', '+20 123 456 7890', NOW(), NOW()),
(8, 'whatsapp_number', '+20 123 456 7890', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات إعدادات الموقع
INSERT INTO setting_translations (setting_key, language_id, setting_value, created_at, updated_at) 
VALUES 
('company_phone', 1, '+20 123 456 7890', NOW(), NOW()),
('company_phone', 2, '+20 123 456 7890', NOW(), NOW()),
('company_email', 1, 'info@sphinxfire.com', NOW(), NOW()),
('company_email', 2, 'info@sphinxfire.com', NOW(), NOW()),
('company_address_line1', 1, 'Industrial Zone, Block 15', NOW(), NOW()),
('company_address_line1', 2, 'المنطقة الصناعية، بلوك 15', NOW(), NOW()),
('company_address_line2', 1, 'Sadat City, Monufia', NOW(), NOW()),
('company_address_line2', 2, 'مدينة السادات، المنوفية', NOW(), NOW()),
('company_address_line3', 1, 'Egypt', NOW(), NOW()),
('company_address_line3', 2, 'مصر', NOW(), NOW()),
('business_hours', 1, 'Saturday - Thursday: 9:00 AM - 6:00 PM', NOW(), NOW()),
('business_hours', 2, 'السبت - الخميس: 9:00 ص - 6:00 م', NOW(), NOW()),
('emergency_phone', 1, '+20 123 456 7890', NOW(), NOW()),
('emergency_phone', 2, '+20 123 456 7890', NOW(), NOW()),
('whatsapp_number', 1, '+20 123 456 7890', NOW(), NOW()),
('whatsapp_number', 2, '+20 123 456 7890', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

COMMIT; 