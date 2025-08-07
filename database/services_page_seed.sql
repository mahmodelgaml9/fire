-- =============================
-- SEED: Services Page Sections (services.html)
-- =============================

-- =============================
-- HERO SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(30, 3, 'hero', 'services-hero', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 30, l.id, 'Complete Fire Safety Services', 'From design to installation to maintenance - we deliver comprehensive protection solutions.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 30, l.id, 'خدمات السلامة من الحريق المتكاملة', 'من التصميم إلى التركيب إلى الصيانة - نقدم حلول حماية شاملة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- SERVICES GRID SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(31, 3, 'services', 'services-grid', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Services Grid - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 31, l.id, 'Complete Fire Safety Solutions', 'Every system designed, installed, and maintained by certified experts.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Services Grid - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 31, l.id, 'حلول السلامة من الحريق المتكاملة', 'كل نظام يتم تصميمه وتركيبه وصيانته بواسطة خبراء معتمدين.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- SMART SYSTEM ADVANTAGE STRIP
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(32, 3, 'advantage', 'services-advantage-strip', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage Strip - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 32, l.id, 'Smart Integration. Proven Results.', 'UL/FM Certified, SCADA & BMS Integrated, Custom for High-Risk Facilities, Full Extinguisher Library, Expert Consultation Team.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Advantage Strip - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 32, l.id, 'تكامل ذكي. نتائج مثبتة.', 'معتمد UL/FM، تكامل SCADA وBMS، مخصص للمنشآت عالية الخطورة، مكتبة طفايات كاملة، فريق استشاري خبير.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- TECHNICAL DETAILS ACCORDION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(33, 3, 'technical', 'services-technical-details', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Technical Details - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 33, l.id, 'Explore the Components Behind Each System', 'Technical specifications and detailed breakdowns for engineering teams.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Technical Details - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 33, l.id, 'استكشف مكونات كل نظام', 'المواصفات الفنية والتفاصيل الهندسية لفِرق العمل.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- FAQ SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(34, 3, 'faq', 'services-faq', 5, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- FAQ - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 34, l.id, 'Frequently Asked Questions', 'How long does installation take? What if I already have partial systems? Do you offer training? Is civil defense approval included?'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- FAQ - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 34, l.id, 'الأسئلة الشائعة', 'كم يستغرق التركيب؟ ماذا لو كان لدي أنظمة جزئية بالفعل؟ هل تقدمون تدريبًا؟ هل يشمل الموافقة من الدفاع المدني؟'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- CONVERSION SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(35, 3, 'cta', 'services-conversion', 6, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Conversion - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 35, l.id, 'Every day without protection increases your risk. Let''s fix that.', 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety solutions. Response within 24 hours • Free consultation • No obligation.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Conversion - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 35, l.id, 'كل يوم بدون حماية يزيد من المخاطر. دعنا نصلح ذلك.', 'انضم إلى مئات المنشآت التي تثق في سفينكس فاير لحلول السلامة من الحريق الكاملة. استجابة خلال 24 ساعة • استشارة مجانية • بدون التزام.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content); 