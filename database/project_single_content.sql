START TRANSACTION;

-- =====================================================
-- إضافة محتوى المشاريع الفردية في قاعدة البيانات
-- =====================================================

-- تحديث المشاريع الموجودة بمحتوى أكثر تفصيلاً
UPDATE project_translations 
SET 
    title = 'Firefighting System for Delta Paint Manufacturing',
    subtitle = 'Complete UL/FM certified firefighting network with specialized foam suppression for paint storage areas.',
    description = 'From risk assessment to final testing – full compliance in 12 working days.',
    challenge = 'High-risk paint storage areas required specialized foam suppression systems that could handle flammable solvents and provide rapid response capabilities.',
    solution = 'Implemented UL/FM certified firefighting network with foam suppression, including specialized nozzles, proportioning systems, and automated monitoring.',
    results = 'Successfully completed in 12 days with full civil defense approval. Zero safety incidents since installation with 95% improvement in response time.',
    testimonial = 'Sphinx Fire handled everything with precision and speed. We passed inspection on the first try, and the foam suppression system they designed for our paint storage saved us during a potential incident last month. Professional, fast, and reliable.',
    testimonial_author = 'Ahmed Hassan',
    testimonial_position = 'Safety Manager, Delta Paint Industries',
    meta_title = 'Delta Paint Manufacturing Fire Safety Project - Sphinx Fire',
    meta_description = 'Complete firefighting system installation for Delta Paint Manufacturing with UL/FM certified foam suppression.',
    meta_keywords = 'fire safety, paint manufacturing, foam suppression, UL certified, civil defense approved',
    updated_at = NOW()
WHERE project_id = 1 AND language_id = 1;

UPDATE project_translations 
SET 
    title = 'نظام إطفاء حريق لمصنع دلتا للدهانات',
    subtitle = 'شبكة إطفاء حريق كاملة معتمدة UL/FM مع إطفاء رغوة متخصص لمناطق تخزين الدهانات.',
    description = 'من تقييم المخاطر إلى الاختبار النهائي – امتثال كامل في 12 يوم عمل.',
    challenge = 'مناطق تخزين الدهانات عالية المخاطر تتطلب أنظمة إطفاء رغوة متخصصة يمكنها التعامل مع المذيبات القابلة للاشتعال وتوفير قدرات استجابة سريعة.',
    solution = 'نفذنا شبكة إطفاء حريق معتمدة UL/FM مع إطفاء رغوة، تشمل فوهات متخصصة وأنظمة تناسب ومراقبة آلية.',
    results = 'تم الإكمال بنجاح في 12 يوم مع موافقة كاملة من الدفاع المدني. صفر حوادث سلامة منذ التركيب مع تحسن 95% في وقت الاستجابة.',
    testimonial = 'تعامل سفنكس فاير مع كل شيء بدقة وسرعة. نجحنا في الفحص من المحاولة الأولى، ونظام إطفاء الرغوة الذي صمموه لتخزين الدهانات أنقذنا خلال حادث محتمل الشهر الماضي. احترافي، سريع، وموثوق.',
    testimonial_author = 'أحمد حسن',
    testimonial_position = 'مدير السلامة، مصنع دلتا للدهانات',
    meta_title = 'مشروع سلامة مصنع دلتا للدهانات من الحريق - سفنكس فاير',
    meta_description = 'تركيب نظام إطفاء حريق كامل لمصنع دلتا للدهانات مع إطفاء رغوة معتمد UL/FM.',
    meta_keywords = 'سلامة من الحريق، تصنيع دهانات، إطفاء رغوة، معتمد UL، معتمد من الدفاع المدني',
    updated_at = NOW()
WHERE project_id = 1 AND language_id = 2;

-- تحديث مشروع City Center Mall
UPDATE project_translations 
SET 
    title = 'City Center Mall - Comprehensive Fire Safety System',
    subtitle = 'Complete fire protection system for 5-story shopping complex including sprinklers, alarms, and emergency evacuation systems.',
    description = 'Advanced fire safety implementation for high-traffic commercial facility with integrated monitoring.',
    challenge = 'High-traffic shopping mall required comprehensive fire protection that wouldn\'t disrupt business operations during installation.',
    solution = 'Implemented phased installation approach with integrated sprinkler systems, fire alarms, and emergency evacuation systems.',
    results = 'Successfully completed in 14 days with minimal disruption to mall operations. Enhanced safety for 50,000+ daily visitors.',
    testimonial = 'The installation was seamless and professional. Our visitors feel safer, and the system integrates perfectly with our building management.',
    testimonial_author = 'Sarah Mohamed',
    testimonial_position = 'Facility Manager, City Center Mall',
    updated_at = NOW()
WHERE project_id = 2 AND language_id = 1;

UPDATE project_translations 
SET 
    title = 'مول سيتي سنتر - نظام سلامة من الحريق شامل',
    subtitle = 'نظام حماية من الحريق كامل لمجمع تسوق من 5 طوابق يشمل رشاشات وإنذارات وأنظمة إخلاء طوارئ.',
    description = 'تنفيذ متقدم للسلامة من الحريق لمنشأة تجارية عالية الحركة مع مراقبة متكاملة.',
    challenge = 'مول تسوق عالي الحركة يتطلب حماية شاملة من الحريق لا تعطل عمليات الأعمال أثناء التركيب.',
    solution = 'نفذنا نهج تركيب تدريجي مع أنظمة رشاشات متكاملة وإنذارات حريق وأنظمة إخلاء طوارئ.',
    results = 'تم الإكمال بنجاح في 14 يوم مع تعطيل ضئيل لعمليات المول. تحسين السلامة لأكثر من 50,000 زائر يومياً.',
    testimonial = 'التركيب كان سلساً واحترافياً. زوارنا يشعرون بأمان أكثر، والنظام يتكامل بشكل مثالي مع إدارة المبنى.',
    testimonial_author = 'سارة محمد',
    testimonial_position = 'مدير المنشأة، مول سيتي سنتر',
    updated_at = NOW()
WHERE project_id = 2 AND language_id = 2;

-- تحديث مشروع Petrochemical Complex
UPDATE project_translations 
SET 
    title = 'Petrochemical Complex - Advanced Deluge System',
    subtitle = 'High-risk deluge system with SCADA monitoring for chemical processing facility.',
    description = 'State-of-the-art fire protection for critical chemical processing operations with real-time monitoring.',
    challenge = 'Critical chemical processing facility required advanced deluge systems with SCADA integration for real-time monitoring.',
    solution = 'Implemented high-risk deluge system with SCADA monitoring, automated controls, and redundant safety systems.',
    results = 'Enhanced safety for critical operations with 100% uptime. Real-time monitoring provides immediate response capabilities.',
    testimonial = 'The SCADA integration gives us complete control and peace of mind. The system has exceeded our expectations.',
    testimonial_author = 'Dr. Omar Khalil',
    testimonial_position = 'Chief Engineer, Petrochemical Complex',
    updated_at = NOW()
WHERE project_id = 3 AND language_id = 1;

UPDATE project_translations 
SET 
    title = 'مجمع بتروكيماويات - نظام دوش متقدم',
    subtitle = 'نظام دوش عالي المخاطر مع مراقبة SCADA لمنشأة معالجة كيميائية.',
    description = 'حماية متقدمة من الحريق لعمليات معالجة كيميائية حرجة مع مراقبة فورية.',
    challenge = 'منشأة معالجة كيميائية حرجة تتطلب أنظمة دوش متقدمة مع تكامل SCADA للمراقبة الفورية.',
    solution = 'نفذنا نظام دوش عالي المخاطر مع مراقبة SCADA وضوابط آلية وأنظمة أمان احتياطية.',
    results = 'تحسين السلامة للعمليات الحرجة مع 100% وقت تشغيل. المراقبة الفورية توفر قدرات استجابة فورية.',
    testimonial = 'تكامل SCADA يعطينا تحكم كامل وراحة البال. النظام تجاوز توقعاتنا.',
    testimonial_author = 'د. عمر خليل',
    testimonial_position = 'كبير المهندسين، مجمع بتروكيماويات',
    updated_at = NOW()
WHERE project_id = 3 AND language_id = 2;

-- إضافة محتوى إضافي للمشاريع الجديدة
INSERT INTO project_translations (project_id, language_id, title, subtitle, description, challenge, solution, results, testimonial, testimonial_author, testimonial_position, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
-- Logistics Warehouse (ID 4)
(4, 1, 'Logistics Hub - High-Ceiling Sprinkler System', 'Specialized high-ceiling sprinkler system for 50,000 sqm warehouse facility.', 'Zone-controlled system with automated monitoring for large-scale logistics operations.', 'High ceiling heights required specialized sprinkler design and installation techniques for effective coverage.', 'Implemented high-ceiling sprinkler system with zone control and automated monitoring capabilities.', 'Successfully installed system covering 50,000 sqm with full automation and monitoring.', 'The high-ceiling sprinkler system has been working perfectly. The automated monitoring gives us peace of mind.', 'Mohamed Ali', 'Facility Manager, Logistics Hub', 'Logistics Hub Fire Protection - Sphinx Fire', 'High-ceiling sprinkler system for logistics warehouse', 'warehouse fire protection, high ceiling sprinklers, logistics', NOW(), NOW()),
(4, 2, 'مركز لوجستي - نظام رشاشات سقف عالي', 'نظام رشاشات متخصص للسقف العالي لمنشأة مخزن بمساحة 50,000 متر مربع.', 'نظام تحكم بالمناطق مع مراقبة آلية لعمليات لوجستية واسعة النطاق.', 'ارتفاعات السقف العالية تتطلب تصميم وتركيب رشاشات متخصصة لتغطية فعالة.', 'نفذنا نظام رشاشات للسقف العالي مع تحكم بالمناطق وقدرات مراقبة آلية.', 'تم تركيب النظام بنجاح لتغطية 50,000 متر مربع مع أتمتة ومراقبة كاملة.', 'نظام الرشاشات للسقف العالي يعمل بشكل مثالي. المراقبة الآلية تعطينا راحة البال.', 'محمد علي', 'مدير المنشأة، مركز لوجستي', 'حماية مركز لوجستي من الحريق - سفنكس فاير', 'نظام رشاشات للسقف العالي لمخزن لوجستي', 'حماية مخازن من الحريق، رشاشات سقف عالي، لوجستي', NOW(), NOW()),

-- Food Processing (ID 5)
(5, 1, 'Food Processing Plant - Food-Safe Fire Protection', 'Wet chemical suppression and CO2 systems for food processing facility.', 'Specialized fire protection designed for food safety compliance and minimal contamination risk.', 'Required food-safe fire suppression systems that wouldn\'t contaminate products or affect food safety standards.', 'Installed wet chemical suppression for kitchen areas and CO2 systems for cold storage with food-safe materials.', 'Full compliance with food safety standards while providing effective fire protection with zero contamination risk.', 'The food-safe suppression systems are exactly what we needed. No contamination concerns and full compliance.', 'Ahmed Hassan', 'Safety Director, Food Processing Plant', 'Food Processing Plant Fire Safety - Sphinx Fire', 'Food-safe fire suppression systems for processing plant', 'food processing fire safety, wet chemical suppression, CO2 systems', NOW(), NOW()),
(5, 2, 'مصنع معالجة غذائية - حماية من الحريق آمنة للغذاء', 'إطفاء كيميائي رطب وأنظمة CO2 لمنشأة معالجة غذائية.', 'حماية متخصصة من الحريق مصممة لامتثال سلامة الغذاء ومخاطر تلوث ضئيلة.', 'تطلبت أنظمة إطفاء حريق آمنة للغذاء لا تلوث المنتجات أو تؤثر على معايير سلامة الغذاء.', 'ركبنا إطفاء كيميائي رطب لمناطق المطبخ وأنظمة CO2 للتخزين البارد مع مواد آمنة للغذاء.', 'امتثال كامل لمعايير سلامة الغذاء مع توفير حماية فعالة من الحريق مع صفر مخاطر تلوث.', 'أنظمة الإطفاء الآمنة للغذاء هي بالضبط ما نحتاجه. لا توجد مخاوف تلوث وامتثال كامل.', 'أحمد حسن', 'مدير السلامة، مصنع معالجة غذائية', 'سلامة مصنع معالجة غذائية من الحريق - سفنكس فاير', 'أنظمة إطفاء حريق آمنة للغذاء لمصنع معالجة', 'سلامة مصانع غذائية من الحريق، إطفاء كيميائي رطب، أنظمة CO2', NOW(), NOW()),

-- Textile Factory (ID 6)
(6, 1, 'Textile Manufacturing - Dust Suppression System', 'Comprehensive fire protection with specialized dust suppression for textile facility.', 'Advanced fire and dust suppression system designed for textile manufacturing environment.', 'High dust levels in textile manufacturing required specialized dust suppression systems to prevent fire hazards.', 'Implemented comprehensive fire network with specialized dust suppression and fabric storage protection.', 'Complete protection system installed with ongoing monitoring and maintenance for dust control.', 'The dust suppression system has significantly reduced fire risks in our facility.', 'Fatima Ahmed', 'Production Manager, Textile Manufacturing', 'Textile Factory Fire Protection - Sphinx Fire', 'Comprehensive fire protection for textile manufacturing', 'textile fire protection, dust suppression, fabric storage', NOW(), NOW()),
(6, 2, 'تصنيع نسيج - نظام إطفاء غبار', 'حماية شاملة من الحريق مع إطفاء غبار متخصص لمنشأة نسيج.', 'نظام إطفاء حريق وغبار متقدم مصمم لبيئة تصنيع النسيج.', 'مستويات الغبار العالية في تصنيع النسيج تتطلب أنظمة إطفاء غبار متخصصة لمنع مخاطر الحريق.', 'نفذنا شبكة حريق شاملة مع إطفاء غبار متخصص وحماية تخزين الأقمشة.', 'تم تركيب نظام حماية كامل مع مراقبة وصيانة مستمرة للتحكم في الغبار.', 'نظام إطفاء الغبار قلل بشكل كبير من مخاطر الحريق في منشأتنا.', 'فاطمة أحمد', 'مدير الإنتاج، تصنيع نسيج', 'حماية مصنع نسيج من الحريق - سفنكس فاير', 'حماية شاملة من الحريق لتصنيع النسيج', 'حماية مصانع نسيج من الحريق، إطفاء غبار، تخزين أقمشة', NOW(), NOW()),

-- Pharmaceutical Plant (ID 7)
(7, 1, 'Pharmaceutical Plant - Clean Room Fire Protection', 'Specialized clean room fire protection with inert gas suppression systems.', 'FDA and GMP compliant fire protection for pharmaceutical manufacturing with clean room environments.', 'Clean room environments required specialized fire protection that wouldn\'t compromise sterile conditions.', 'Installed inert gas suppression systems designed for clean room environments with FDA/GMP compliance.', 'Full FDA and GMP compliance achieved with effective fire protection for clean room areas.', 'The clean room fire protection system meets all FDA requirements. Excellent work.', 'Dr. Sarah Mohamed', 'Quality Assurance Manager, Pharmaceutical Plant', 'Pharmaceutical Plant Fire Safety - Sphinx Fire', 'Clean room fire protection for pharmaceutical facility', 'pharmaceutical fire safety, clean room protection, gas suppression', NOW(), NOW()),
(7, 2, 'مصنع أدوية - حماية غرف نظيفة من الحريق', 'حماية متخصصة من الحريق لغرف نظيفة مع أنظمة إطفاء غاز خامل.', 'حماية من الحريق متوافقة مع FDA و GMP لتصنيع الأدوية مع بيئات غرف نظيفة.', 'بيئات الغرف النظيفة تتطلب حماية متخصصة من الحريق لا تتعرض للظروف المعقمة.', 'ركبنا أنظمة إطفاء غاز خامل مصممة لبيئات الغرف النظيفة مع توافق FDA/GMP.', 'تم تحقيق توافق كامل مع FDA و GMP مع حماية فعالة من الحريق لمناطق الغرف النظيفة.', 'نظام حماية الغرف النظيفة من الحريق يلبي جميع متطلبات FDA. عمل ممتاز.', 'د. سارة محمد', 'مدير ضمان الجودة، مصنع أدوية', 'سلامة مصنع أدوية من الحريق - سفنكس فاير', 'حماية غرف نظيفة من الحريق لمنشأة أدوية', 'سلامة مصانع أدوية من الحريق، حماية غرف نظيفة، إطفاء غاز', NOW(), NOW()),

-- Cold Storage (ID 8)
(8, 1, 'Cold Storage Facility - Low-Temperature Fire Protection', 'Specialized fire protection system designed for sub-zero environments.', 'Glycol-based systems with freeze protection for cold storage operations.', 'Sub-zero temperatures required specialized fire protection systems that wouldn\'t freeze or malfunction.', 'Implemented glycol-based fire protection systems designed for low-temperature environments with freeze protection.', 'Successfully installed system rated for -20°C with full freeze protection and reliable operation.', 'The low-temperature fire protection system works perfectly even at -20°C.', 'Omar Khalil', 'Facility Engineer, Cold Storage Facility', 'Cold Storage Fire Protection - Sphinx Fire', 'Low-temperature fire protection for cold storage facility', 'cold storage fire protection, low temperature systems, glycol systems', NOW(), NOW()),
(8, 2, 'منشأة تخزين بارد - حماية من الحريق لدرجات حرارة منخفضة', 'نظام حماية متخصص من الحريق مصمم لبيئات تحت الصفر.', 'أنظمة تعتمد على الجليكول مع حماية من التجمد لعمليات التخزين البارد.', 'درجات الحرارة تحت الصفر تتطلب أنظمة حماية متخصصة من الحريق لا تتجمد أو تعطل.', 'نفذنا أنظمة حماية من الحريق تعتمد على الجليكول مصممة لبيئات درجة حرارة منخفضة مع حماية من التجمد.', 'تم تركيب النظام بنجاح مع تصنيف -20°م مع حماية كاملة من التجمد وتشغيل موثوق.', 'نظام الحماية من الحريق لدرجات الحرارة المنخفضة يعمل بشكل مثالي حتى -20°م.', 'عمر خليل', 'مهندس منشأة، منشأة تخزين بارد', 'حماية منشأة تخزين بارد من الحريق - سفنكس فاير', 'حماية من الحريق لدرجات حرارة منخفضة لمنشأة تخزين بارد', 'حماية مخازن باردة من الحريق، أنظمة درجة حرارة منخفضة، أنظمة جليكول', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

COMMIT; 