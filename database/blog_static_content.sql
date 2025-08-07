START TRANSACTION;

-- =====================================================
-- إضافة المحتوى الستاتيكي للمدونة (Blog Static Content)
-- =====================================================

-- إضافة صفحة المدونة إذا لم تكن موجودة
INSERT INTO pages (id, slug, title, meta_title, meta_description, is_active, created_at, updated_at) 
VALUES (5, 'blog', 'Blog', 'Blog - Sphinx Fire', 'Fire safety articles and guides', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة المحتوى الستاتيكي للمدونة
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (5, 'blog_static', 'mid_article_cta', 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @mid_article_cta_id = LAST_INSERT_ID();

-- ترجمات المحتوى الستاتيكي باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@mid_article_cta_id, 1, 'Want a Free Pre-Inspection Consultation?', 
        'Our certified consultants can assess your facility and identify potential issues before the official inspection.', 
        NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات المحتوى الستاتيكي باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@mid_article_cta_id, 2, 'تريد استشارة مجانية قبل الفحص؟', 
        'يمكن لمستشارينا المعتمدين تقييم منشأتك وتحديد المشاكل المحتملة قبل الفحص الرسمي.', 
        NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- إضافة Final CTA للمدونة
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (5, 'blog_static', 'final_cta', 2, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @final_cta_id = LAST_INSERT_ID();

-- ترجمات Final CTA باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@final_cta_id, 1, 'Need Help Preparing for Your Civil Defense Inspection?', 
        'Don''t leave compliance to chance. Our certified consultants ensure you pass on the first try.', 
        NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Final CTA باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@final_cta_id, 2, 'تحتاج مساعدة في الاستعداد لفحص الدفاع المدني؟', 
        'لا تترك الامتثال للصدفة. مستشارونا المعتمدون يضمنون نجاحك من المحاولة الأولى.', 
        NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

COMMIT;

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • صفحة المدونة (page_id = 5)
-- • 2 سكاشن ستاتيكية للمدونة
-- • 4 ترجمات (إنجليزي + عربي)
-- ===================================================== 