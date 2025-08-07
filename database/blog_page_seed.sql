-- =============================
-- SEED: Blog Page Sections (blog.html)
-- =============================


-- =============================
-- HERO SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(12, 5, 'hero', 'blog-hero', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 12, l.id, 'Insights That Keep Your Facility Safe', 'Expert fire protection guides, updates, and industrial safety knowledge.', 'Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 12, l.id, 'رؤى تحافظ على سلامة منشأتك', 'أدلة ونصائح خبراء الحماية من الحريق والمعرفة الصناعية.', 'ابقَ على اطلاع دائم على اللوائح مع نصائح مهندسي وخبراء الحماية من الحريق المعتمدين.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- SEARCH & FILTER SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(51, 5, 'filter', 'blog-search-filter', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Search & Filter - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 51, l.id, 'Search & Filter', 'All Articles, Compliance, Fire Systems, Extinguishers, OSHA, Civil Defense, Pumps, PPE.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Search & Filter - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 51, l.id, 'بحث وتصفية', 'كل المقالات، الامتثال، أنظمة الحريق، الطفايات، OSHA، الدفاع المدني، المضخات، معدات الحماية الشخصية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- FEATURED ARTICLE SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(52, 5, 'featured', 'blog-featured', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Featured Article - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 52, l.id, 'Most Popular This Month', 'Essential reading for industrial safety professionals', 'Complete Guide: How to Pass Civil Defense Inspection in Egypt (2024). Step-by-step checklist and documentation guide for industrial facilities. Covers all requirements, common mistakes to avoid, and expert tips from certified consultants who have guided 100+ successful inspections.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Featured Article - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 52, l.id, 'الأكثر شعبية هذا الشهر', 'قراءة أساسية لمتخصصي السلامة الصناعية', 'الدليل الكامل: كيف تجتاز فحص الدفاع المدني في مصر (2024). قائمة مراجعة خطوة بخطوة ودليل مستندات للمنشآت الصناعية. يشمل جميع المتطلبات، والأخطاء الشائعة التي يجب تجنبها، ونصائح الخبراء من مستشارين معتمدين قادوا أكثر من 100 فحص ناجح.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- ARTICLES GRID SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(53, 5, 'articles', 'blog-articles-grid', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Articles Grid - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 53, l.id, 'Latest Safety Insights', 'Expert knowledge to keep your facility compliant and safe', NULL
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Articles Grid - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 53, l.id, 'أحدث الرؤى في السلامة', 'معرفة الخبراء للحفاظ على امتثال منشأتك وسلامتها', NULL
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- NEWSLETTER CTA SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(54, 5, 'cta', 'blog-newsletter-cta', 5, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Newsletter CTA - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 54, l.id, 'Want to get the latest safety tips and system updates?', 'Join 2,500+ safety professionals who receive our weekly insights on fire protection, compliance updates, and industry best practices.', 'Free weekly insights • No spam • Unsubscribe anytime.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Newsletter CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 54, l.id, 'هل تريد الحصول على أحدث نصائح السلامة وتحديثات الأنظمة؟', 'انضم إلى أكثر من 2500 متخصص في السلامة يتلقون رؤانا الأسبوعية حول الحماية من الحريق وتحديثات الامتثال وأفضل الممارسات الصناعية.', 'رؤى أسبوعية مجانية • بدون رسائل مزعجة • يمكنك إلغاء الاشتراك في أي وقت.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content); 