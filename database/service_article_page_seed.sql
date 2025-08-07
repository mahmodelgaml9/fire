-- =============================
-- SEED: Service Article Page (service-article.php)
-- =============================

-- أضف صفحة service-article لجدول الصفحات (مثلاً id=71)
INSERT INTO pages (id, slug, created_by) VALUES (71, 'service-article', 1)
ON DUPLICATE KEY UPDATE slug=VALUES(slug);

-- أضف السكاشن الثابتة الخاصة بتصميم صفحة الخدمة الواحدة (ابدأ من id=71)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(71, 71, 'hero', 'service-article-hero', 1, TRUE),
(72, 71, 'cta', 'service-article-cta', 2, TRUE),
(73, 71, 'highlight', 'service-article-highlight', 3, TRUE),
(74, 71, 'final-cta', 'service-article-final-cta', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 71, l.id, 'Service Title', 'Service Category', 'This is a dynamic service article page. Content is loaded from services table.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 71, l.id, 'عنوان الخدمة', 'تصنيف الخدمة', 'هذه صفحة خدمة ديناميكية. يتم تحميل المحتوى من جدول الخدمات.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- CTA - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 72, l.id, 'Need This Service?', 'Contact us to discuss your requirements and get a custom solution.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 72, l.id, 'تحتاج هذه الخدمة؟', 'تواصل معنا لمناقشة متطلباتك والحصول على حل مخصص.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Highlight - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 73, l.id, 'Service Highlights', 'Key features, certifications, and client benefits.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Highlight - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 73, l.id, 'أهم مميزات الخدمة', 'المميزات الرئيسية، الشهادات، وفوائد العميل.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Final CTA - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 74, l.id, 'Ready to Book This Service?', 'Get in touch for a free consultation and service assessment.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Final CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 74, l.id, 'جاهز لحجز الخدمة؟', 'تواصل معنا لاستشارة مجانية وتقييم الخدمة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content); 