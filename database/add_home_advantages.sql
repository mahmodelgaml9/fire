-- إضافة بيانات المزايا للصفحة الرئيسية
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(1, 'advantage', 'home-advantage-1', 1, 1, NOW(), NOW()),
(1, 'advantage', 'home-advantage-2', 2, 1, NOW(), NOW()),
(1, 'advantage', 'home-advantage-3', 3, 1, NOW(), NOW()),
(1, 'advantage', 'home-advantage-4', 4, 1, NOW(), NOW());

-- إضافة ترجمات المزايا باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, created_at, updated_at) VALUES
(75, 1, 'UL/FM Certified Equipment', 'International Standards', 'All our firefighting systems use UL/FM certified equipment ensuring the highest safety standards and compliance with international regulations.', NOW(), NOW()),
(76, 1, '24/7 Emergency Response', 'Always Available', 'Our emergency response team is available 24/7 to handle any fire safety emergency with rapid response times.', NOW(), NOW()),
(77, 1, 'Expert Engineering Team', 'Certified Professionals', 'Our team consists of certified fire protection engineers with decades of combined experience in industrial safety.', NOW(), NOW()),
(78, 1, 'Complete Turnkey Solutions', 'From Design to Maintenance', 'We provide complete turnkey solutions from initial design and installation to ongoing maintenance and support.', NOW(), NOW());

-- إضافة ترجمات المزايا باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, created_at, updated_at) VALUES
(75, 2, 'معدات معتمدة UL/FM', 'معايير دولية', 'جميع أنظمة مكافحة الحريق لدينا تستخدم معدات معتمدة UL/FM مما يضمن أعلى معايير السلامة والامتثال للوائح الدولية.', NOW(), NOW()),
(76, 2, 'استجابة طوارئ 24/7', 'متوفرون دائمًا', 'فريق الاستجابة للطوارئ متوفر 24/7 للتعامل مع أي طوارئ سلامة من الحريق بأوقات استجابة سريعة.', NOW(), NOW()),
(77, 2, 'فريق هندسي خبير', 'محترفون معتمدون', 'فريقنا يتكون من مهندسي حماية من الحريق المعتمدين بعقود من الخبرة المشتركة في السلامة الصناعية.', NOW(), NOW()),
(78, 2, 'حلول متكاملة', 'من التصميم إلى الصيانة', 'نقدم حلول متكاملة من التصميم الأولي والتركيب إلى الصيانة المستمرة والدعم.', NOW(), NOW()); 