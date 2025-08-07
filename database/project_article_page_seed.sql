-- =============================
-- SEED: Project Article Page (project-article.php)
-- =============================

-- أضف صفحة project-article لجدول الصفحات (مثلاً id=70)
INSERT INTO pages (id, slug, created_by) VALUES (70, 'project-article',1)
ON DUPLICATE KEY UPDATE slug=VALUES(slug);

-- أضف السكاشن الثابتة الخاصة بتصميم صفحة المشروع الواحد (ابدأ من id=67)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(67, 70, 'hero', 'project-article-hero', 1, TRUE),
(68, 70, 'cta', 'project-article-cta', 2, TRUE),
(69, 70, 'highlight', 'project-article-highlight', 3, TRUE),
(70, 70, 'final-cta', 'project-article-final-cta', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 67, l.id, 'Project Title', 'Project Category', 'This is a dynamic project article page. Content is loaded from projects table.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 67, l.id, 'عنوان المشروع', 'تصنيف المشروع', 'هذه صفحة مشروع ديناميكية. يتم تحميل المحتوى من جدول المشاريع.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- CTA - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 68, l.id, 'Want a Similar Project?', 'Contact us to discuss your requirements and get a custom solution.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 68, l.id, 'تريد مشروعًا مماثلًا؟', 'تواصل معنا لمناقشة متطلباتك والحصول على حل مخصص.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Highlight - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 69, l.id, 'Project Highlights', 'Key achievements, certifications, and client feedback.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Highlight - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 69, l.id, 'أهم إنجازات المشروع', 'الإنجازات الرئيسية، الشهادات، وآراء العميل.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Final CTA - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 70, l.id, 'Ready to Start Your Project?', 'Get in touch for a free consultation and site assessment.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Final CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 70, l.id, 'جاهز لبدء مشروعك؟', 'تواصل معنا لاستشارة مجانية وتقييم موقعك.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content); 