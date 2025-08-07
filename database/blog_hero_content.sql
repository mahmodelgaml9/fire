START TRANSACTION;

-- =====================================================
-- إضافة محتوى Hero Section لصفحة المدونة
-- page_id = 5 (صفحة المدونة)
-- section_type = "hero"
-- =====================================================

-- إضافة سكشن Hero لصفحة المدونة
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, settings, created_at, updated_at) 
VALUES 
(100, 5, 'hero', 'blog_hero', 1, 1, '{"background": "hero-bg", "overlay": "gradient"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة الترجمة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) 
VALUES 
(100, 1, '📚 EXPERT KNOWLEDGE BASE', 'Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.', 'Insights That Keep Your Facility Safe', NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), subtitle = VALUES(subtitle), updated_at = NOW();

-- إضافة الترجمة العربية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) 
VALUES 
(100, 2, '📚 قاعدة المعرفة الخبيرة', 'ابق متقدماً على لوائح السلامة مع رؤى منسقة من قبل مهندسي الحماية من الحريق المعتمدين ومستشاري السلامة.', 'رؤى تحافظ على سلامة منشأتك', NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), subtitle = VALUES(subtitle), updated_at = NOW();

COMMIT;

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • 1 سكشن hero لصفحة المدونة
-- • 1 ترجمة إنجليزية
-- • 1 ترجمة عربية
-- • إجمالي: 3 سجلات
-- ===================================================== 