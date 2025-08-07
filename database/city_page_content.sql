START TRANSACTION;

-- =====================================================
-- إضافة محتوى صفحة city في قاعدة البيانات
-- =====================================================

-- إضافة صفحة city إذا لم تكن موجودة
INSERT INTO pages (id, slug, status, published_at, created_at, updated_at) 
VALUES (6, 'city', 'published', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات صفحة city
INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description, created_at, updated_at) 
VALUES 
(6, 1, 'Fire Protection Systems in Sadat City - Sphinx Fire', 'Fire Protection Systems in Sadat City - Sphinx Fire', 'Professional fire protection systems in Sadat Industrial City. Local expertise, certified equipment, and fast response from inside the industrial zone.', NOW(), NOW()),
(6, 2, 'أنظمة الحماية من الحريق في مدينة السادات - سفنكس فاير', 'أنظمة الحماية من الحريق في مدينة السادات - سفنكس فاير', 'أنظمة حماية حريق احترافية في مدينة السادات الصناعية. خبرة محلية ومعدات معتمدة واستجابة سريعة من داخل المنطقة الصناعية.', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة أقسام صفحة city
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES 
-- Hero Section
(60, 6, 'hero', 'city-hero', 1, TRUE, NOW(), NOW()),

-- Local Advantages Section
(61, 6, 'local_advantages', 'city-local-advantages', 2, TRUE, NOW(), NOW()),

-- Services Section
(62, 6, 'services', 'city-services', 3, TRUE, NOW(), NOW()),

-- Local Project Section
(63, 6, 'local_project', 'city-local-project', 4, TRUE, NOW(), NOW()),

-- Location Map Section
(64, 6, 'location_map', 'city-location-map', 5, TRUE, NOW(), NOW()),

-- SEO Content Section
(65, 6, 'seo_content', 'city-seo-content', 6, TRUE, NOW(), NOW()),

-- Local CTA Section
(66, 6, 'local_cta', 'city-local-cta', 7, TRUE, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات أقسام صفحة city
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) 
VALUES 
-- Hero Section - English
(60, 1, 'Fire Protection Systems in Sadat City', 'Certified protection, fast local response, zero logistics delays.', 'Looking for certified fire protection inside Sadat City? We\'re minutes away—not hours from Cairo. Our local presence ensures faster response times and better understanding of your facility\'s specific needs.', 'Book Free Site Assessment', NOW(), NOW()),

-- Hero Section - Arabic
(60, 2, 'أنظمة الحماية من الحريق في مدينة السادات', 'حماية معتمدة، استجابة محلية سريعة، بدون تأخير في اللوجستيات.', 'تبحث عن حماية حريق معتمدة داخل مدينة السادات؟ نحن على بعد دقائق—وليس ساعات من القاهرة. وجودنا المحلي يضمن أوقات استجابة أسرع وفهم أفضل لاحتياجات منشأتك المحددة.', 'حجز تقييم موقع مجاني', NOW(), NOW()),

-- Local Advantages - English
(61, 1, 'Why Choose a Local Provider Inside Sadat City?', 'Strategic location means better service, faster response, and lower costs', 'Our strategic location inside Sadat Industrial City provides significant advantages including faster emergency response, lower logistics costs, and better understanding of local compliance requirements.', 'Learn More', NOW(), NOW()),

-- Local Advantages - Arabic
(61, 2, 'لماذا تختار مزود محلي داخل مدينة السادات؟', 'الموقع الاستراتيجي يعني خدمة أفضل واستجابة أسرع وتكاليف أقل', 'موقعنا الاستراتيجي داخل مدينة السادات الصناعية يوفر مزايا كبيرة تشمل استجابة طوارئ أسرع وتكاليف لوجستية أقل وفهم أفضل لمتطلبات الامتثال المحلية.', 'اعرف المزيد', NOW(), NOW()),

-- Services - English
(62, 1, 'Complete Fire Safety Services in Sadat City', 'Every system designed, installed, and maintained by local certified experts', 'We provide comprehensive fire safety services specifically tailored for Sadat Industrial City facilities, including firefighting systems, alarms, extinguishers, and civil defense preparation.', 'Request Service', NOW(), NOW()),

-- Services - Arabic
(62, 2, 'خدمات السلامة من الحريق الكاملة في مدينة السادات', 'كل نظام مصمم ومركب ومحافظ عليه بواسطة خبراء محليين معتمدين', 'نوفر خدمات سلامة حريق شاملة مصممة خصيصاً لمنشآت مدينة السادات الصناعية، تشمل أنظمة مكافحة الحريق والإنذارات والطفايات وتحضير الدفاع المدني.', 'اطلب الخدمة', NOW(), NOW()),

-- Local Project - English
(63, 1, 'We Helped 3 Plastic Factories in Sadat City Pass Inspection', 'Real results from real facilities in your industrial zone', 'Our local expertise and understanding of Sadat City requirements has helped multiple facilities achieve compliance and pass civil defense inspections on the first try.', 'Get Similar Results', NOW(), NOW()),

-- Local Project - Arabic
(63, 2, 'ساعدنا 3 مصانع بلاستيك في مدينة السادات في اجتياز الفحص', 'نتائج حقيقية من منشآت حقيقية في منطقتك الصناعية', 'خبرتنا المحلية وفهم متطلبات مدينة السادات ساعدت منشآت متعددة في تحقيق الامتثال واجتياز فحوصات الدفاع المدني من المحاولة الأولى.', 'احصل على نتائج مماثلة', NOW(), NOW()),

-- Location Map - English
(64, 1, 'We\'re Right Here in Sadat City', 'Strategic location for fast response to all industrial facilities', 'Our strategic location inside Sadat Industrial City ensures we can respond to emergencies within minutes, not hours. We\'re at the heart of the industrial district for maximum efficiency.', 'Get Directions', NOW(), NOW()),

-- Location Map - Arabic
(64, 2, 'نحن هنا في مدينة السادات', 'موقع استراتيجي لاستجابة سريعة لجميع المنشآت الصناعية', 'موقعنا الاستراتيجي داخل مدينة السادات الصناعية يضمن أننا نستطيع الاستجابة للطوارئ خلال دقائق، وليس ساعات. نحن في قلب المنطقة الصناعية لأقصى كفاءة.', 'احصل على الاتجاهات', NOW(), NOW()),

-- SEO Content - English
(65, 1, 'Fire Safety Solutions for Sadat Industrial City', 'Professional fire protection services in Sadat City', 'Comprehensive fire safety solutions specifically designed for industrial facilities in Sadat Industrial City. Local expertise, certified equipment, and fast response times.', 'Contact Us', NOW(), NOW()),

-- SEO Content - Arabic
(65, 2, 'حلول السلامة من الحريق لمدينة السادات الصناعية', 'خدمات حماية حريق احترافية في مدينة السادات', 'حلول سلامة حريق شاملة مصممة خصيصاً للمنشآت الصناعية في مدينة السادات الصناعية. خبرة محلية ومعدات معتمدة وأوقات استجابة سريعة.', 'اتصل بنا', NOW(), NOW()),

-- Local CTA - English
(66, 1, 'Don\'t wait until inspection day. Get a free system design today.', 'Local expertise, certified equipment, and fast response', 'Don\'t wait for an emergency. Get professional fire safety assessment today. Our local presence ensures faster response times, lower costs, and better understanding of your facility\'s specific needs.', 'Book a Local Visit', NOW(), NOW()),

-- Local CTA - Arabic
(66, 2, 'لا تنتظر حتى يوم الفحص. احصل على تصميم نظام مجاني اليوم.', 'خبرة محلية ومعدات معتمدة واستجابة سريعة', 'لا تنتظر حتى الطوارئ. احصل على تقييم سلامة حريق احترافي اليوم. وجودنا المحلي يضمن أوقات استجابة أسرع وتكاليف أقل وفهم أفضل لاحتياجات منشأتك المحددة.', 'احجز زيارة محلية', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

COMMIT; 