START TRANSACTION;

-- =====================================================
-- إضافة محتوى الخدمات في قاعدة البيانات
-- =====================================================

-- إضافة تصنيفات الخدمات (الهيكل الصحيح)
INSERT INTO service_categories (id, slug, icon, color, sort_order, is_active, created_at, updated_at) 
VALUES 
(1, 'firefighting-systems', 'fas fa-fire-extinguisher', '#DC2626', 1, 1, NOW(), NOW()),
(2, 'alarm-detection', 'fas fa-bell', '#DC2626', 2, 1, NOW(), NOW()),
(3, 'fire-extinguishers', 'fas fa-spray-can', '#DC2626', 3, 1, NOW(), NOW()),
(4, 'ppe-equipment', 'fas fa-hard-hat', '#DC2626', 4, 1, NOW(), NOW()),
(5, 'safety-consulting', 'fas fa-clipboard-check', '#DC2626', 5, 1, NOW(), NOW()),
(6, 'maintenance-services', 'fas fa-tools', '#DC2626', 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات تصنيفات الخدمات
INSERT INTO service_category_translations (category_id, language_id, name, description, created_at, updated_at) 
VALUES 
-- Firefighting Systems
(1, 1, 'Firefighting Systems', 'Complete fire suppression and protection systems', NOW(), NOW()),
(1, 2, 'أنظمة إطفاء الحريق', 'أنظمة إطفاء وحماية حريق كاملة', NOW(), NOW()),

-- Alarm & Detection
(2, 1, 'Alarm & Detection', 'Fire alarm and detection systems', NOW(), NOW()),
(2, 2, 'الإنذار والكشف', 'أنظمة إنذار وكشف الحريق', NOW(), NOW()),

-- Fire Extinguishers
(3, 1, 'Fire Extinguishers', 'Portable and fixed fire extinguishers', NOW(), NOW()),
(3, 2, 'طفايات الحريق', 'طفايات حريق محمولة وثابتة', NOW(), NOW()),

-- PPE Equipment
(4, 1, 'PPE Equipment', 'Personal protective equipment', NOW(), NOW()),
(4, 2, 'معدات الحماية الشخصية', 'معدات الحماية الشخصية', NOW(), NOW()),

-- Safety Consulting
(5, 1, 'Safety Consulting', 'Fire safety consulting and training', NOW(), NOW()),
(5, 2, 'الاستشارات الأمنية', 'استشارات وتدريب السلامة من الحريق', NOW(), NOW()),

-- Maintenance Services
(6, 1, 'Maintenance Services', 'System maintenance and support', NOW(), NOW()),
(6, 2, 'خدمات الصيانة', 'صيانة ودعم الأنظمة', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة الخدمات
INSERT INTO services (id, category_id, slug, icon, featured_image, price_range, duration, is_featured, is_active, sort_order, created_by, created_at, updated_at) 
VALUES 
(1, 1, 'firefighting-systems', 'fas fa-fire-extinguisher', 'firefighting-systems.jpg', '$10,000 - $50,000', '2-4 weeks', 1, 1, 1, 1, NOW(), NOW()),
(2, 2, 'alarm-detection', 'fas fa-bell', 'alarm-detection.jpg', '$5,000 - $25,000', '1-2 weeks', 1, 1, 2, 1, NOW(), NOW()),
(3, 3, 'fire-extinguishers', 'fas fa-spray-can', 'fire-extinguishers.jpg', '$500 - $5,000', '1-3 days', 1, 1, 3, 1, NOW(), NOW()),
(4, 4, 'ppe-equipment', 'fas fa-hard-hat', 'ppe-equipment.jpg', '$1,000 - $10,000', '1-2 weeks', 1, 1, 4, 1, NOW(), NOW()),
(5, 5, 'safety-consulting', 'fas fa-clipboard-check', 'safety-consulting.jpg', '$2,000 - $15,000', '1-4 weeks', 1, 1, 5, 1, NOW(), NOW()),
(6, 6, 'maintenance-services', 'fas fa-tools', 'maintenance-services.jpg', '$1,000 - $8,000', 'Ongoing', 1, 1, 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات الخدمات
INSERT INTO service_translations (service_id, language_id, name, short_description, full_description, features, benefits, specifications, created_at, updated_at) 
VALUES 
-- Firefighting Systems (ID 1)
(1, 1, 'Firefighting Systems', 'High-pressure pump systems, foam suppression, sprinkler networks, and deluge systems for comprehensive fire protection.', 'Complete fire suppression systems designed for industrial facilities. Our firefighting systems include high-pressure pumps, foam suppression units, sprinkler networks, and deluge systems. All systems are UL/FM certified and designed to meet specific facility requirements.', '["High-pressure pumps", "Foam suppression", "Sprinkler networks", "Deluge systems", "UL/FM certified", "SCADA integration"]', '["Complete protection coverage", "Rapid response time", "Minimal water damage", "Automated operation", "Regulatory compliance"]', '{"Pump capacity": "500-2000 GPM", "Pressure": "150-300 PSI", "Coverage area": "Up to 50,000 sqm", "Response time": "<30 seconds"}', NOW(), NOW()),
(1, 2, 'أنظمة إطفاء الحريق', 'أنظمة مضخات عالية الضغط، إطفاء رغوة، شبكات رشاشات، وأنظمة دوش للحماية الشاملة من الحريق.', 'أنظمة إطفاء حريق كاملة مصممة للمنشآت الصناعية. أنظمة إطفاء الحريق لدينا تشمل مضخات عالية الضغط ووحدات إطفاء رغوة وشبكات رشاشات وأنظمة دوش. جميع الأنظمة معتمدة UL/FM ومصممة لتلبية متطلبات المنشأة المحددة.', '["مضخات عالية الضغط", "إطفاء رغوة", "شبكات رشاشات", "أنظمة دوش", "معتمد UL/FM", "تكامل SCADA"]', '["تغطية حماية كاملة", "وقت استجابة سريع", "أضرار مياه ضئيلة", "تشغيل آلي", "امتثال تنظيمي"]', '{"سعة المضخة": "500-2000 جالون/دقيقة", "الضغط": "150-300 رطل/بوصة مربعة", "منطقة التغطية": "حتى 50,000 متر مربع", "وقت الاستجابة": "<30 ثانية"}', NOW(), NOW()),

-- Alarm & Detection (ID 2)
(2, 1, 'Alarm & Detection', 'Smart smoke detectors, heat sensors, manual call points, and integrated alarm panels with SCADA connectivity.', 'Advanced fire detection and alarm systems with intelligent sensors and integrated control panels. Our systems provide early warning capabilities and seamless integration with building management systems.', '["Smart smoke detectors", "Heat sensors", "Manual call points", "Addressable panels", "SCADA integration", "Remote monitoring"]', '["Early detection", "Reduced false alarms", "Easy maintenance", "System integration", "Real-time monitoring"]', '{"Detection range": "0-100m", "Response time": "<10 seconds", "Operating temperature": "-10°C to +70°C", "Power": "24V DC"}', NOW(), NOW()),
(2, 2, 'الإنذار والكشف', 'كاشفات دخان ذكية، مستشعرات حرارة، نقاط استدعاء يدوية، وألواح إنذار متكاملة مع اتصال SCADA.', 'أنظمة كشف وإنذار حريق متقدمة مع مستشعرات ذكية وألواح تحكم متكاملة. أنظمتنا توفر قدرات إنذار مبكر وتكامل سلس مع أنظمة إدارة المباني.', '["كاشفات دخان ذكية", "مستشعرات حرارة", "نقاط استدعاء يدوية", "ألواح قابلة للعنونة", "تكامل SCADA", "مراقبة عن بعد"]', '["كشف مبكر", "تقليل الإنذارات الكاذبة", "صيانة سهلة", "تكامل النظام", "مراقبة فورية"]', '{"نطاق الكشف": "0-100 متر", "وقت الاستجابة": "<10 ثانية", "درجة حرارة التشغيل": "-10°م إلى +70°م", "الطاقة": "24 فولت تيار مستمر"}', NOW(), NOW()),

-- Fire Extinguishers (ID 3)
(3, 1, 'Fire Extinguishers', 'Complete extinguisher library: CO2, foam, powder, wet chemical, and specialized suppression agents.', 'Comprehensive range of fire extinguishers for all types of fires. From portable units to fixed systems, we provide the right extinguishing agent for your specific fire hazards.', '["CO2 extinguishers", "Foam extinguishers", "Powder extinguishers", "Wet chemical", "Specialized agents", "Fixed systems"]', '["Versatile protection", "Easy to use", "Cost-effective", "Reliable performance", "Wide coverage"]', '{"Capacity": "1-50kg", "Discharge time": "8-60 seconds", "Range": "2-8 meters", "Operating temperature": "-20°C to +60°C"}', NOW(), NOW()),
(3, 2, 'طفايات الحريق', 'مكتبة طفايات كاملة: CO2، رغوة، مسحوق، كيميائي رطب، ومواد إطفاء متخصصة.', 'مجموعة شاملة من طفايات الحريق لجميع أنواع الحرائق. من الوحدات المحمولة إلى الأنظمة الثابتة، نقدم عامل الإطفاء المناسب لمخاطر الحريق المحددة لديك.', '["طفايات CO2", "طفايات رغوة", "طفايات مسحوق", "كيميائي رطب", "عوامل متخصصة", "أنظمة ثابتة"]', '["حماية متعددة الاستخدامات", "سهلة الاستخدام", "فعالة من حيث التكلفة", "أداء موثوق", "تغطية واسعة"]', '{"السعة": "1-50 كجم", "وقت التفريغ": "8-60 ثانية", "المدى": "2-8 أمتار", "درجة حرارة التشغيل": "-20°م إلى +60°م"}', NOW(), NOW()),

-- PPE Equipment (ID 4)
(4, 1, 'PPE Equipment', 'Professional-grade protective equipment: suits, helmets, breathing apparatus, and emergency response gear.', 'Complete personal protective equipment for fire safety personnel. Our PPE includes fire-resistant suits, helmets, breathing apparatus, and emergency response equipment designed for maximum protection.', '["Fire-resistant suits", "Safety helmets", "Breathing apparatus", "Emergency gear", "High-visibility clothing", "Heat protection"]', '["Maximum protection", "Comfortable fit", "Durability", "Compliance standards", "Emergency ready"]', '{"Heat resistance": "Up to 1000°C", "Weight": "2-8kg", "Material": "Nomex/Kevlar", "Standards": "NFPA 1971/1975"}', NOW(), NOW()),
(4, 2, 'معدات الحماية الشخصية', 'معدات حماية احترافية: بدلات، خوذ، أجهزة تنفس، ومعدات استجابة طوارئ.', 'معدات حماية شخصية كاملة لموظفي السلامة من الحريق. معدات الحماية الشخصية لدينا تشمل بدلات مقاومة للحريق وخوذ وأجهزة تنفس ومعدات استجابة طوارئ مصممة للحماية القصوى.', '["بدلات مقاومة للحريق", "خوذ أمان", "أجهزة تنفس", "معدات طوارئ", "ملابس عالية الرؤية", "حماية من الحرارة"]', '["حماية قصوى", "ملاءمة مريحة", "متانة", "معايير الامتثال", "جاهز للطوارئ"]', '{"مقاومة الحرارة": "حتى 1000°م", "الوزن": "2-8 كجم", "المادة": "نومكس/كيفلار", "المعايير": "NFPA 1971/1975"}', NOW(), NOW()),

-- Safety Consulting (ID 5)
(5, 1, 'Safety Consulting', 'Expert consultation, compliance audits, training programs, and certification assistance for your facility.', 'Comprehensive fire safety consulting services including risk assessments, compliance audits, training programs, and certification assistance. Our experts help ensure your facility meets all safety standards.', '["Risk assessment", "Compliance audits", "Training programs", "Certification assistance", "Emergency planning", "Safety reviews"]', '["Regulatory compliance", "Risk reduction", "Staff training", "Emergency preparedness", "Cost optimization"]', '{"Audit duration": "1-5 days", "Training sessions": "2-8 hours", "Certification time": "2-4 weeks", "Follow-up": "6-12 months"}', NOW(), NOW()),
(5, 2, 'الاستشارات الأمنية', 'استشارات خبراء، تدقيقات امتثال، برامج تدريب، ومساعدة شهادات لمنشأتك.', 'خدمات استشارات سلامة حريق شاملة تشمل تقييمات المخاطر وتدقيقات الامتثال وبرامج التدريب ومساعدة الشهادات. خبراؤنا يساعدون في ضمان تلبية منشأتك لجميع معايير السلامة.', '["تقييم المخاطر", "تدقيقات الامتثال", "برامج التدريب", "مساعدة الشهادات", "تخطيط الطوارئ", "مراجعات السلامة"]', '["امتثال تنظيمي", "تقليل المخاطر", "تدريب الموظفين", "الاستعداد للطوارئ", "تحسين التكلفة"]', '{"مدة التدقيق": "1-5 أيام", "جلسات التدريب": "2-8 ساعات", "وقت الشهادة": "2-4 أسابيع", "المتابعة": "6-12 شهر"}', NOW(), NOW()),

-- Maintenance Services (ID 6)
(6, 1, 'Maintenance Services', 'Regular inspections, preventive maintenance, emergency repairs, and system upgrades to ensure optimal performance.', 'Comprehensive maintenance services to keep your fire safety systems operating at peak performance. Our maintenance programs include regular inspections, preventive maintenance, emergency repairs, and system upgrades.', '["Regular inspections", "Preventive maintenance", "Emergency repairs", "System upgrades", "Performance monitoring", "Parts replacement"]', '["Optimal performance", "Extended lifespan", "Reduced downtime", "Emergency readiness", "Cost savings"]', '{"Inspection frequency": "Monthly/Quarterly", "Response time": "2-24 hours", "Coverage": "24/7 support", "Warranty": "1-5 years"}', NOW(), NOW()),
(6, 2, 'خدمات الصيانة', 'فحوصات منتظمة، صيانة وقائية، إصلاحات طوارئ، وترقيات أنظمة لضمان الأداء الأمثل.', 'خدمات صيانة شاملة للحفاظ على أنظمة السلامة من الحريق تعمل بأداء ذروة. برامج الصيانة لدينا تشمل فحوصات منتظمة وصيانة وقائية وإصلاحات طوارئ وترقيات أنظمة.', '["فحوصات منتظمة", "صيانة وقائية", "إصلاحات طوارئ", "ترقيات أنظمة", "مراقبة الأداء", "استبدال قطع غيار"]', '["أداء أمثل", "عمر ممتد", "تقليل وقت التوقف", "جاهزية الطوارئ", "توفير التكلفة"]', '{"تكرار الفحص": "شهري/ربع سنوي", "وقت الاستجابة": "2-24 ساعة", "التغطية": "دعم 24/7", "الضمان": "1-5 سنوات"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة محتوى صفحة الخدمات (sections)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES 
-- Hero Section
(30, 3, 'hero', 'services-hero', 1, TRUE, NOW(), NOW()),

-- Services Grid Section
(31, 3, 'services', 'services-grid', 2, TRUE, NOW(), NOW()),

-- Advantage Strip Section
(32, 3, 'advantage', 'services-advantage-strip', 3, TRUE, NOW(), NOW()),

-- Technical Details Section
(33, 3, 'technical', 'services-technical-details', 4, TRUE, NOW(), NOW()),

-- FAQ Section
(34, 3, 'faq', 'services-faq', 5, TRUE, NOW(), NOW()),

-- CTA Section
(35, 3, 'cta', 'services-conversion', 6, TRUE, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات محتوى صفحة الخدمات
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, background_image, created_at, updated_at) 
VALUES 
-- Hero Section - English
(30, 1, 'Complete Fire Safety Services', 'From design to installation to maintenance - we deliver comprehensive protection solutions.', 'Professional fire safety services for industrial facilities. Our team of certified experts provides complete solutions from initial design through installation and ongoing maintenance.', 'Download Our Full Catalog', NULL, NOW(), NOW()),

-- Hero Section - Arabic
(30, 2, 'خدمات السلامة من الحريق الشاملة', 'من التصميم إلى التركيب إلى الصيانة - نقدم حلول حماية شاملة.', 'خدمات سلامة حريق احترافية للمنشآت الصناعية. فريقنا من الخبراء المعتمدين يوفر حلول كاملة من التصميم الأولي عبر التركيب والصيانة المستمرة.', 'تحميل الكتالوج الكامل', NULL, NOW(), NOW()),

-- Services Grid - English
(31, 1, 'Complete Fire Safety Solutions', 'Every system designed, installed, and maintained by certified experts', 'Our comprehensive range of fire safety services includes firefighting systems, alarm and detection, extinguishers, PPE equipment, safety consulting, and maintenance services.', 'View All Services', NULL, NOW(), NOW()),

-- Services Grid - Arabic
(31, 2, 'حلول السلامة من الحريق الشاملة', 'كل نظام مصمم ومركب وصيانته بواسطة خبراء معتمدين', 'مجموعتنا الشاملة من خدمات السلامة من الحريق تشمل أنظمة إطفاء الحريق والإنذار والكشف والطفايات ومعدات الحماية الشخصية والاستشارات الأمنية وخدمات الصيانة.', 'عرض جميع الخدمات', NULL, NOW(), NOW()),

-- Advantage Strip - English
(32, 1, 'Smart Integration. Proven Results.', 'Advanced technology that works seamlessly with your existing systems', 'Our fire safety systems integrate seamlessly with your existing building management systems, providing comprehensive protection without disrupting your operations.', 'How We Integrate With Your Facility', NULL, NOW(), NOW()),

-- Advantage Strip - Arabic
(32, 2, 'تكامل ذكي. نتائج مثبتة.', 'تقنية متقدمة تعمل بسلاسة مع أنظمتك الموجودة', 'أنظمة السلامة من الحريق لدينا تتكامل بسلاسة مع أنظمة إدارة المباني الموجودة لديك، مما يوفر حماية شاملة دون تعطيل عملياتك.', 'كيف نتكامل مع منشأتك', NULL, NOW(), NOW()),

-- Technical Details - English
(33, 1, 'Explore the Components Behind Each System', 'Technical specifications and detailed breakdowns for engineering teams', 'Detailed technical information about our fire safety systems including specifications, installation requirements, and maintenance procedures.', 'Download Technical Specs', NULL, NOW(), NOW()),

-- Technical Details - Arabic
(33, 2, 'استكشف المكونات خلف كل نظام', 'المواصفات التقنية والتفاصيل لفريق الهندسة', 'معلومات تقنية مفصلة حول أنظمة السلامة من الحريق لدينا تشمل المواصفات ومتطلبات التركيب وإجراءات الصيانة.', 'تحميل المواصفات التقنية', NULL, NOW(), NOW()),

-- FAQ - English
(34, 1, 'Frequently Asked Questions', 'Quick answers to common questions about our services', 'Find answers to the most common questions about our fire safety services, installation processes, and maintenance requirements.', 'Contact Us for More Info', NULL, NOW(), NOW()),

-- FAQ - Arabic
(34, 2, 'الأسئلة الشائعة', 'إجابات سريعة للأسئلة الشائعة حول خدماتنا', 'اعثر على إجابات للأسئلة الأكثر شيوعاً حول خدمات السلامة من الحريق لدينا وعمليات التركيب ومتطلبات الصيانة.', 'اتصل بنا للمزيد من المعلومات', NULL, NOW(), NOW()),

-- CTA - English
(35, 1, 'Every day without protection increases your risk. Let\'s fix that.', 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety solutions.', 'Don\'t wait until it\'s too late. Contact us today for a comprehensive fire safety assessment and customized protection plan for your facility.', 'Get a Custom Offer Now', NULL, NOW(), NOW()),

-- CTA - Arabic
(35, 2, 'كل يوم بدون حماية يزيد من مخاطرك. دعنا نصلح ذلك.', 'انضم لمئات المنشآت التي تثق في سفنكس فاير لحلول السلامة من الحريق الشاملة.', 'لا تنتظر حتى يكون الوقت متأخراً. اتصل بنا اليوم لتقييم سلامة حريق شامل وخطة حماية مخصصة لمنشأتك.', 'احصل على عرض مخصص الآن', NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

COMMIT; 