-- =============================
-- SEED: Blog Article Civil Defense (blog-article-civil-defense.html)
-- =============================

-- مثال: إذا كان آخر id في sections هو 58، نبدأ من 59

-- Hero Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(59, 5, 'hero', 'blog-article-civil-defense-hero', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 59, l.id, 'How to Pass Civil Defense Inspection in Egypt', 'Compliance Guide', 'Step-by-step guide to pass Civil Defense inspection. July 7, 2024. Sphinx Fire Team.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 59, l.id, 'كيف تجتاز فحص الدفاع المدني في مصر', 'دليل الامتثال', 'دليل خطوة بخطوة لاجتياز فحص الدفاع المدني. 7 يوليو 2024. فريق سفينكس فاير.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Mid-Article CTA Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(60, 5, 'cta', 'blog-article-civil-defense-cta', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- CTA - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 60, l.id, 'Want a Free Pre-Inspection Consultation?', NULL, 'Our certified consultants can assess your facility and identify potential issues before the official inspection.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 60, l.id, 'هل تريد استشارة مجانية قبل الفحص؟', NULL, 'يمكن لمستشارينا المعتمدين تقييم منشأتك وتحديد المشكلات المحتملة قبل الفحص الرسمي.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Highlight Box Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(61, 5, 'highlight', 'blog-article-civil-defense-highlight', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Highlight - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 61, l.id, 'Critical Statistic', 'Over 40% of factories fail their first inspection due to missing technical documents and inadequate fire suppression systems.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Highlight - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 61, l.id, 'إحصائية حرجة', 'أكثر من 40% من المصانع تفشل في الفحص الأول بسبب نقص المستندات الفنية وأنظمة الإطفاء غير الكافية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Final CTA Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(62, 5, 'cta', 'blog-article-civil-defense-final-cta', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Final CTA - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 62, l.id, 'Need Help Preparing for Your Civil Defense Inspection?', NULL, 'Our certified consultants ensure you pass on the first try. Free assessment • Expert guidance • 24-hour response guarantee.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Final CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 62, l.id, 'تحتاج مساعدة في التحضير لفحص الدفاع المدني؟', NULL, 'يضمن لك مستشارونا المعتمدون النجاح من أول مرة. تقييم مجاني • إرشاد خبير • استجابة خلال 24 ساعة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content); 