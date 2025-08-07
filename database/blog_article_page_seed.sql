-- =============================
-- SEED: Blog Article Page (blog-article.php)
-- =============================

-- أضف صفحة blog-article لجدول الصفحات (مثلاً id=60)
INSERT INTO pages (id, slug) VALUES (60, 'blog-article')
ON DUPLICATE KEY UPDATE slug=VALUES(slug);

-- أضف السكاشن الثابتة الخاصة بتصميم صفحة المقالة (ابدأ من id=63)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(63, 60, 'hero', 'blog-article-hero', 1, TRUE),
(64, 60, 'cta', 'blog-article-cta', 2, TRUE),
(65, 60, 'highlight', 'blog-article-highlight', 3, TRUE),
(66, 60, 'final-cta', 'blog-article-final-cta', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 63, l.id, 'Article Title', 'Compliance Guide', 'This is a dynamic blog article page. Content is loaded from blog_posts.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);
-- Hero - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 63, l.id, 'عنوان المقال', 'دليل الامتثال', 'هذه صفحة مقالة ديناميكية. يتم تحميل المحتوى من جدول blog_posts.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- CTA - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 64, l.id, 'Want a Free Pre-Inspection Consultation?', 'Our certified consultants can assess your facility and identify potential issues before the official inspection.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 64, l.id, 'هل تريد استشارة مجانية قبل الفحص؟', 'يمكن لمستشارينا المعتمدين تقييم منشأتك وتحديد المشكلات المحتملة قبل الفحص الرسمي.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Highlight - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 65, l.id, 'Critical Statistic', 'Over 40% of factories fail their first inspection due to missing technical documents and inadequate fire suppression systems.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Highlight - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 65, l.id, 'إحصائية حرجة', 'أكثر من 40% من المصانع تفشل في الفحص الأول بسبب نقص المستندات الفنية وأنظمة الإطفاء غير الكافية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Final CTA - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 66, l.id, 'Need Help Preparing for Your Civil Defense Inspection?', 'Our certified consultants ensure you pass on the first try. Free assessment • Expert guidance • 24-hour response guarantee.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Final CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 66, l.id, 'تحتاج مساعدة في التحضير لفحص الدفاع المدني؟', 'يضمن لك مستشارونا المعتمدون النجاح من أول مرة. تقييم مجاني • إرشاد خبير • استجابة خلال 24 ساعة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content); 