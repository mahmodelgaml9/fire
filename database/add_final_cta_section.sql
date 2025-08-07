-- إضافة بيانات السكشن الأخير (Final CTA) للصفحة الرئيسية
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(1, 'cta', 'home-final-cta', 10, 1, NOW(), NOW());

-- إضافة ترجمات السكشن الأخير باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(79, 1, 'Let\'s build a safer tomorrow—starting with your facility today.', 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety excellence.', NOW(), NOW());

-- إضافة ترجمات السكشن الأخير باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(79, 2, 'دعنا نبني غداً أكثر أماناً—بدءاً بمنشأتك اليوم.', 'انضم إلى مئات المنشآت التي تثق في سفينكس فاير للتميز الكامل في السلامة من الحريق.', NOW(), NOW()); 