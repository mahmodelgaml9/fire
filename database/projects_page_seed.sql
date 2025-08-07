-- =============================
-- SEED: Projects Page Sections (projects.html)
-- =============================

-- صفحة Projects (id=4)
INSERT INTO pages (id, slug) VALUES (4, 'projects') 
ON DUPLICATE KEY UPDATE slug=VALUES(slug);

-- =============================
-- HERO SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(11, 4, 'hero', 'projects-hero', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 11, l.id, 'Real Safety. Real Sites. Real Impact.', 'Explore our recent projects and see how we bring industrial safety to life.', 'From design to installation to approval - witness our proven track record across Egypt''s industrial facilities.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 11, l.id, 'سلامة حقيقية. مواقع حقيقية. تأثير حقيقي.', 'استكشف مشاريعنا الأخيرة وشاهد كيف نجسد السلامة الصناعية.', 'من التصميم إلى التركيب إلى الاعتماد - شاهد سجلنا المثبت في منشآت مصر الصناعية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- STATS SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(41, 4, 'stats', 'projects-stats', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Stats - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 41, l.id, 'Project Stats', '50+ Projects Completed, 100% Civil Defense Approval, 15 Industrial Sectors, 24/7 Support Available.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Stats - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 41, l.id, 'إحصائيات المشاريع', 'أكثر من 50 مشروعًا مكتملًا، 100% موافقة الدفاع المدني، 15 قطاعًا صناعيًا، دعم متوفر 24/7.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- FILTER SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(42, 4, 'filter', 'projects-filter', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Filter - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 42, l.id, 'Filter Projects by Category', 'All Projects, Manufacturing, Chemical, Retail & Malls, Warehouses, Food Industry.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Filter - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 42, l.id, 'تصفية المشاريع حسب الفئة', 'كل المشاريع، التصنيع، الكيماويات، التجزئة والمولات، المخازن، الصناعات الغذائية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- PROJECTS GRID SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(43, 4, 'projects', 'projects-grid', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Projects Grid - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 43, l.id, 'Our Project Portfolio', 'Real implementations, proven results, satisfied clients', NULL
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Projects Grid - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 43, l.id, 'محفظة مشاريعنا', 'تنفيذات حقيقية، نتائج مثبتة، عملاء راضون', NULL
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- HIGHLIGHTED PROJECT SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(44, 4, 'highlight', 'projects-highlighted', 5, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Highlighted Project - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 44, l.id, 'Client Spotlight', 'Outstanding results that exceed expectations', 'Delta Paint Industries - Complete Fire Safety Transformation. "Sphinx Fire didn''t just install a system - they transformed our entire approach to safety. The foam suppression system they designed for our paint storage saved us during a potential incident last month. Professional, fast, and reliable." - Ahmed Hassan, Safety Manager, Delta Paint Industries.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Highlighted Project - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 44, l.id, 'مشروع الشهر', 'نتائج مميزة تتجاوز التوقعات', 'مصنع دلتا للدهانات - تحول كامل في السلامة من الحريق. "سفينكس فاير لم تركب نظامًا فقط - بل غيرت نهجنا بالكامل تجاه السلامة. نظام الرغوة الذي صمموه لمخزن الدهانات أنقذنا أثناء حادث محتمل الشهر الماضي. احترافية وسرعة وموثوقية." - أحمد حسن، مدير السلامة، دلتا للدهانات.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- CTA STRIP SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(45, 4, 'cta', 'projects-cta-strip', 6, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- CTA Strip - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 45, l.id, 'Need a similar safety solution for your facility?', 'Every project starts with understanding your unique requirements. Let''s discuss your needs.', 'Free consultation • Expert assessment • Response within 24 hours.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- CTA Strip - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 45, l.id, 'تحتاج حل سلامة مماثل لمنشأتك؟', 'كل مشروع يبدأ بفهم احتياجاتك الفريدة. دعنا نناقش متطلباتك.', 'استشارة مجانية • تقييم خبير • استجابة خلال 24 ساعة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content); 