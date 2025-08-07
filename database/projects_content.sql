START TRANSACTION;

-- =====================================================
-- إضافة محتوى المشاريع في قاعدة البيانات
-- =====================================================

-- تحديث المشاريع الموجودة
UPDATE projects 
SET duration_days = 12, budget_range = '$50,000 - $75,000', team_size = 8
WHERE id = 1;

UPDATE projects 
SET duration_days = 14, budget_range = '$30,000 - $45,000', team_size = 6
WHERE id = 2;

UPDATE projects 
SET duration_days = 21, budget_range = '$100,000 - $150,000', team_size = 12
WHERE id = 3;

-- إضافة مشاريع جديدة
INSERT INTO projects (id, category_id, slug, client_name, client_logo, location, project_date, duration_days, budget_range, status, featured_image, gallery, services_provided, team_size, is_featured, is_published, sort_order, created_by, updated_by, created_at, updated_at) 
VALUES 
(4, 2, 'logistics-warehouse', 'Logistics Hub', NULL, '6th October', '2024-02-20', 10, '$40,000 - $60,000', 'completed', 'logistics-warehouse.jpg', NULL, NULL, 7, 0, 1, 0, 1, NULL, NOW(), NOW()),
(5, 1, 'food-processing', 'Food Processing Plant', NULL, 'Quesna', '2024-04-15', 8, '$35,000 - $50,000', 'completed', 'food-processing.jpg', NULL, NULL, 5, 0, 1, 0, 1, NULL, NOW(), NOW()),
(6, 1, 'textile-factory', 'Textile Manufacturing', NULL, 'Mahalla', '2024-06-01', 15, '$60,000 - $80,000', 'ongoing', 'textile-factory.jpg', NULL, NULL, 10, 0, 1, 0, 1, NULL, NOW(), NOW()),
(7, 3, 'pharmaceutical-plant', 'Pharmaceutical Plant', NULL, 'Alexandria', '2024-03-10', 25, '$120,000 - $180,000', 'completed', 'pharmaceutical-plant.jpg', NULL, NULL, 15, 1, 1, 0, 1, NULL, NOW(), NOW()),
(8, 2, 'cold-storage', 'Cold Storage Facility', NULL, 'Obour', '2024-05-05', 18, '$80,000 - $110,000', 'completed', 'cold-storage.jpg', NULL, NULL, 9, 0, 1, 0, 1, NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات المشاريع الجديدة
INSERT INTO project_translations (project_id, language_id, title, subtitle, description, challenge, solution, results, testimonial, testimonial_author, testimonial_position, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
-- Logistics Warehouse (ID 4)
(4, 1, 'Logistics Hub - 6th October', 'High-Ceiling Sprinkler Network', 'Specialized high-ceiling sprinkler system for 50,000 sqm warehouse facility. Zone-controlled with automated monitoring.', 'High ceiling heights required specialized sprinkler design and installation techniques.', 'Implemented high-ceiling sprinkler system with zone control and automated monitoring capabilities.', 'Successfully installed system covering 50,000 sqm with full automation and monitoring.', 'The high-ceiling sprinkler system has been working perfectly. The automated monitoring gives us peace of mind.', 'Mohamed Ali', 'Facility Manager', 'Logistics Hub Fire Protection - Sphinx Fire', 'High-ceiling sprinkler system for logistics warehouse', 'warehouse fire protection, high ceiling sprinklers, logistics', NOW(), NOW()),
(4, 2, 'مركز لوجستي - 6 أكتوبر', 'شبكة رشاشات للسقف العالي', 'نظام رشاشات متخصص للسقف العالي لمنشأة مخزن بمساحة 50,000 متر مربع. تحكم بالمناطق مع مراقبة آلية.', 'ارتفاعات السقف العالية تتطلب تصميم وتركيب رشاشات متخصصة.', 'نفذنا نظام رشاشات للسقف العالي مع تحكم بالمناطق وقدرات مراقبة آلية.', 'تم تركيب النظام بنجاح لتغطية 50,000 متر مربع مع أتمتة ومراقبة كاملة.', 'نظام الرشاشات للسقف العالي يعمل بشكل مثالي. المراقبة الآلية تعطينا راحة البال.', 'محمد علي', 'مدير المنشأة', 'حماية مركز لوجستي من الحريق - سفنكس فاير', 'نظام رشاشات للسقف العالي لمخزن لوجستي', 'حماية مخازن من الحريق، رشاشات سقف عالي، لوجستي', NOW(), NOW()),

-- Food Processing (ID 5)
(5, 1, 'Food Processing Plant - Quesna', 'Wet Chemical Suppression + CO2 Systems', 'Specialized suppression systems for kitchen equipment and cold storage areas. Food-safe fire protection solutions.', 'Required food-safe fire suppression systems that wouldn\'t contaminate products.', 'Installed wet chemical suppression for kitchen areas and CO2 systems for cold storage.', 'Full compliance with food safety standards while providing effective fire protection.', 'The food-safe suppression systems are exactly what we needed. No contamination concerns.', 'Ahmed Hassan', 'Safety Director', 'Food Processing Plant Fire Safety - Sphinx Fire', 'Food-safe fire suppression systems for processing plant', 'food processing fire safety, wet chemical suppression, CO2 systems', NOW(), NOW()),
(5, 2, 'مصنع معالجة غذائية - قويسنا', 'إطفاء كيميائي رطب + أنظمة CO2', 'أنظمة إطفاء متخصصة لمعدات المطبخ ومناطق التخزين البارد. حلول حماية من الحريق آمنة للغذاء.', 'تطلبت أنظمة إطفاء حريق آمنة للغذاء لا تلوث المنتجات.', 'ركبنا إطفاء كيميائي رطب لمناطق المطبخ وأنظمة CO2 للتخزين البارد.', 'امتثال كامل لمعايير سلامة الغذاء مع توفير حماية فعالة من الحريق.', 'أنظمة الإطفاء الآمنة للغذاء هي بالضبط ما نحتاجه. لا توجد مخاوف تلوث.', 'أحمد حسن', 'مدير السلامة', 'سلامة مصنع معالجة غذائية من الحريق - سفنكس فاير', 'أنظمة إطفاء حريق آمنة للغذاء لمصنع معالجة', 'سلامة مصانع غذائية من الحريق، إطفاء كيميائي رطب، أنظمة CO2', NOW(), NOW()),

-- Textile Factory (ID 6)
(6, 1, 'Textile Manufacturing - Mahalla', 'Fire Network + Dust Suppression', 'Comprehensive fire protection for textile facility including specialized dust suppression systems and fabric storage protection.', 'High dust levels in textile manufacturing required specialized dust suppression systems.', 'Implemented comprehensive fire network with specialized dust suppression and fabric storage protection.', 'Complete protection system installed with ongoing monitoring and maintenance.', 'The dust suppression system has significantly reduced fire risks in our facility.', 'Fatima Ahmed', 'Production Manager', 'Textile Factory Fire Protection - Sphinx Fire', 'Comprehensive fire protection for textile manufacturing', 'textile fire protection, dust suppression, fabric storage', NOW(), NOW()),
(6, 2, 'تصنيع نسيج - المحلة', 'شبكة حريق + إطفاء غبار', 'حماية شاملة من الحريق لمنشأة نسيج تشمل أنظمة إطفاء غبار متخصصة وحماية تخزين الأقمشة.', 'مستويات الغبار العالية في تصنيع النسيج تتطلب أنظمة إطفاء غبار متخصصة.', 'نفذنا شبكة حريق شاملة مع إطفاء غبار متخصص وحماية تخزين الأقمشة.', 'تم تركيب نظام حماية كامل مع مراقبة وصيانة مستمرة.', 'نظام إطفاء الغبار قلل بشكل كبير من مخاطر الحريق في منشأتنا.', 'فاطمة أحمد', 'مدير الإنتاج', 'حماية مصنع نسيج من الحريق - سفنكس فاير', 'حماية شاملة من الحريق لتصنيع النسيج', 'حماية مصانع نسيج من الحريق، إطفاء غبار، تخزين أقمشة', NOW(), NOW()),

-- Pharmaceutical Plant (ID 7)
(7, 1, 'Pharmaceutical Plant - Alexandria', 'Clean Room Fire Protection + Gas Suppression', 'Specialized clean room fire protection with inert gas suppression systems. FDA and GMP compliant installation.', 'Clean room environments required specialized fire protection that wouldn\'t compromise sterile conditions.', 'Installed inert gas suppression systems designed for clean room environments with FDA/GMP compliance.', 'Full FDA and GMP compliance achieved with effective fire protection for clean room areas.', 'The clean room fire protection system meets all FDA requirements. Excellent work.', 'Dr. Sarah Mohamed', 'Quality Assurance Manager', 'Pharmaceutical Plant Fire Safety - Sphinx Fire', 'Clean room fire protection for pharmaceutical facility', 'pharmaceutical fire safety, clean room protection, gas suppression', NOW(), NOW()),
(7, 2, 'مصنع أدوية - الإسكندرية', 'حماية غرف نظيفة من الحريق + إطفاء غاز', 'حماية متخصصة من الحريق لغرف نظيفة مع أنظمة إطفاء غاز خامل. تركيب متوافق مع FDA و GMP.', 'بيئات الغرف النظيفة تتطلب حماية متخصصة من الحريق لا تتعرض للظروف المعقمة.', 'ركبنا أنظمة إطفاء غاز خامل مصممة لبيئات الغرف النظيفة مع توافق FDA/GMP.', 'تم تحقيق توافق كامل مع FDA و GMP مع حماية فعالة من الحريق لمناطق الغرف النظيفة.', 'نظام حماية الغرف النظيفة من الحريق يلبي جميع متطلبات FDA. عمل ممتاز.', 'د. سارة محمد', 'مدير ضمان الجودة', 'سلامة مصنع أدوية من الحريق - سفنكس فاير', 'حماية غرف نظيفة من الحريق لمنشأة أدوية', 'سلامة مصانع أدوية من الحريق، حماية غرف نظيفة، إطفاء غاز', NOW(), NOW()),

-- Cold Storage (ID 8)
(8, 1, 'Cold Storage Facility - Obour', 'Low-Temperature Fire Protection', 'Specialized fire protection system designed for sub-zero environments. Glycol-based systems with freeze protection.', 'Sub-zero temperatures required specialized fire protection systems that wouldn\'t freeze.', 'Implemented glycol-based fire protection systems designed for low-temperature environments.', 'Successfully installed system rated for -20°C with full freeze protection.', 'The low-temperature fire protection system works perfectly even at -20°C.', 'Omar Khalil', 'Facility Engineer', 'Cold Storage Fire Protection - Sphinx Fire', 'Low-temperature fire protection for cold storage facility', 'cold storage fire protection, low temperature systems, glycol systems', NOW(), NOW()),
(8, 2, 'منشأة تخزين بارد - العبور', 'حماية من الحريق لدرجات حرارة منخفضة', 'نظام حماية متخصص من الحريق مصمم لبيئات تحت الصفر. أنظمة تعتمد على الجليكول مع حماية من التجمد.', 'درجات الحرارة تحت الصفر تتطلب أنظمة حماية متخصصة من الحريق لا تتجمد.', 'نفذنا أنظمة حماية من الحريق تعتمد على الجليكول مصممة لبيئات درجة حرارة منخفضة.', 'تم تركيب النظام بنجاح مع تصنيف -20°م مع حماية كاملة من التجمد.', 'نظام الحماية من الحريق لدرجات الحرارة المنخفضة يعمل بشكل مثالي حتى -20°م.', 'عمر خليل', 'مهندس منشأة', 'حماية منشأة تخزين بارد من الحريق - سفنكس فاير', 'حماية من الحريق لدرجات حرارة منخفضة لمنشأة تخزين بارد', 'حماية مخازن باردة من الحريق، أنظمة درجة حرارة منخفضة، أنظمة جليكول', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة محتوى Hero Section لصفحة المشاريع
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, settings, created_at, updated_at) 
VALUES 
(200, 4, 'hero', 'projects_hero', 1, 1, '{"background": "hero-bg", "overlay": "gradient"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات Hero Section
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) 
VALUES 
(200, 1, 'Real Safety. Real Sites. Real Impact.', 'From design to installation to approval - witness our proven track record across Egypt\'s industrial facilities.', 'Explore our recent projects and see how we bring industrial safety to life.', NOW(), NOW()),
(200, 2, 'سلامة حقيقية. مواقع حقيقية. تأثير حقيقي.', 'من التصميم إلى التركيب إلى الموافقة - شاهد سجلنا المثبت عبر المنشآت الصناعية في مصر.', 'استكشف مشاريعنا الحديثة وشاهد كيف نجعل السلامة الصناعية حية.', NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), subtitle = VALUES(subtitle), updated_at = NOW();

COMMIT; 