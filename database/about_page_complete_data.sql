-- =====================================================
-- إضافة بيانات صفحة About (من نحن) - جميع السكاشنات
-- =====================================================

-- 1. إضافة Hero Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'hero', 'about-hero', 1, 1, NOW(), NOW());

-- ترجمات Hero باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) VALUES
(94, 1, 'Fire Protection Is Not a Product.', 'It''s a System. It''s a Promise.', 'At Sphinx Fire, we provide safety systems engineered to defend your business from disaster—with speed, expertise, and precision.', '🔴 Explore Our Services', NOW(), NOW());

-- ترجمات Hero باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) VALUES
(94, 2, 'الحماية من الحريق ليست منتجاً.', 'إنها نظام. إنها وعد.', 'في سفينكس فاير، نقدم أنظمة سلامة مصممة هندسياً للدفاع عن عملك من الكوارث—بالسرعة والخبرة والدقة.', '🔴 استكشف خدماتنا', NOW(), NOW());

-- 2. إضافة Overview Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'overview', 'about-overview', 2, 1, NOW(), NOW());

-- ترجمات Overview باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(95, 1, 'Built for Industry. Backed by Experience.', 'Based in the heart of Egypt''s industrial zone in Sadat City, Sphinx Fire has emerged as the leading provider of comprehensive fire safety solutions for industrial facilities across the region. Our specialty lies in the complete spectrum of fire safety—from initial consultation and risk assessment to system design, certified equipment supply, professional installation, and ongoing maintenance. Led by certified engineers and safety consultants with decades of combined experience, we serve B2B clients across diverse industries including manufacturing plants, chemical facilities, warehouses, shopping centers, and high-risk industrial operations. Every system we design and install is backed by international certifications and compliance with the highest safety standards, ensuring your facility is protected and your business is secure.', NOW(), NOW());

-- ترجمات Overview باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(95, 2, 'مبني للصناعة. مدعوم بالخبرة.', 'مقرنا في قلب المنطقة الصناعية المصرية في مدينة السادات، ظهرت سفينكس فاير كالمزود الرائد لحلول السلامة من الحريق الشاملة للمنشآت الصناعية في جميع أنحاء المنطقة. تخصصنا يكمن في الطيف الكامل للسلامة من الحريق—من الاستشارة الأولية وتقييم المخاطر إلى تصميم النظام وإمداد المعدات المعتمدة والتركيب الاحترافي والصيانة المستمرة. يقودنا مهندسون معتمدون ومستشارو سلامة بعقود من الخبرة المشتركة، نخدم عملاء B2B عبر صناعات متنوعة تشمل مصانع التصنيع والمنشآت الكيميائية والمستودعات ومراكز التسوق والعمليات الصناعية عالية المخاطر. كل نظام نصممه ونركبه مدعوم بشهادات دولية وامتثال لأعلى معايير السلامة، مما يضمن حماية منشأتك وأمان عملك.', NOW(), NOW());

-- 3. إضافة Values Section (الرؤية، الرسالة، القيم)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'value', 'vision', 3, 1, NOW(), NOW()),
(2, 'value', 'mission', 4, 1, NOW(), NOW()),
(2, 'value', 'values', 5, 1, NOW(), NOW());

-- ترجمات Values باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(96, 1, 'Vision', 'To lead Egypt''s industrial safety transformation with smart, reliable, and integrated systems that set new standards for fire protection excellence.', NOW(), NOW()),
(97, 1, 'Mission', 'Deliver complete fire safety solutions that combine engineering excellence, fast response, and compliance with top global standards to protect lives and assets.', NOW(), NOW()),
(98, 1, 'Values', 'Integrity • Precision • Readiness • Responsibility • Innovation', NOW(), NOW());

-- ترجمات Values باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(96, 2, 'الرؤية', 'قيادة التحول الصناعي للسلامة في مصر بأنظمة ذكية وموثوقة ومتكاملة تحدد معايير جديدة للتميز في الحماية من الحريق.', NOW(), NOW()),
(97, 2, 'الرسالة', 'تقديم حلول السلامة من الحريق الكاملة التي تجمع بين التميز الهندسي والاستجابة السريعة والامتثال لأعلى المعايير العالمية لحماية الأرواح والأصول.', NOW(), NOW()),
(98, 2, 'القيم', 'النزاهة • الدقة • الاستعداد • المسؤولية • الابتكار', NOW(), NOW());

-- 4. إضافة Advantages Section (ما يميزنا)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'advantage', 'strategic-location', 6, 1, NOW(), NOW()),
(2, 'advantage', 'complete-integration', 7, 1, NOW(), NOW()),
(2, 'advantage', 'expert-leadership', 8, 1, NOW(), NOW()),
(2, 'advantage', 'responsive-team', 9, 1, NOW(), NOW()),
(2, 'advantage', 'compliance-guaranteed', 10, 1, NOW(), NOW());

-- ترجمات Advantages باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(99, 1, 'Strategically Located', 'Based inside Sadat City, we''re minutes away from major industrial facilities, ensuring rapid response times and cost-effective service delivery.', '50+ Projects Completed', NOW(), NOW()),
(100, 1, 'Complete System Integration', 'SCADA-ready systems that integrate seamlessly with your existing building management and industrial control systems.', '100% SCADA Compatible', NOW(), NOW()),
(101, 1, 'Expert Leadership', 'Led by certified consultants with 10+ years in industrial fire safety, bringing deep expertise to every project.', '10+ Years Experience', NOW(), NOW()),
(102, 1, 'Responsive Team', 'Fast site visits, direct contact with decision makers, and emergency support when you need it most.', '24-Hour Response', NOW(), NOW()),
(103, 1, 'Compliance Guaranteed', 'Full compliance with NFPA, OSHA, and Egyptian Civil Defense regulations, with documentation and certification support.', '100% Compliance Rate', NOW(), NOW());

-- ترجمات Advantages باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(99, 2, 'موقع استراتيجي', 'مقرنا داخل مدينة السادات، على بعد دقائق من المنشآت الصناعية الكبرى، مما يضمن أوقات استجابة سريعة وتقديم خدمات فعالة من حيث التكلفة.', '50+ مشروع مكتمل', NOW(), NOW()),
(100, 2, 'تكامل النظام الكامل', 'أنظمة جاهزة لـ SCADA تتكامل بسلاسة مع أنظمة إدارة المباني وأنظمة التحكم الصناعية الموجودة.', '100% متوافق مع SCADA', NOW(), NOW()),
(101, 2, 'قيادة خبيرة', 'يقودنا مستشارون معتمدون بخبرة 10+ سنوات في السلامة من الحريق الصناعي، يجلبون خبرة عميقة لكل مشروع.', '10+ سنوات خبرة', NOW(), NOW()),
(102, 2, 'فريق متجاوب', 'زيارات موقع سريعة، اتصال مباشر مع صانعي القرار، ودعم طوارئ عندما تحتاجه أكثر.', 'استجابة 24 ساعة', NOW(), NOW()),
(103, 2, 'الامتثال مضمون', 'امتثال كامل لمعايير NFPA و OSHA ولوائح الدفاع المدني المصري، مع دعم التوثيق والشهادات.', 'معدل امتثال 100%', NOW(), NOW());

-- 5. إضافة Process Steps (خطوات العملية)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'process', 'free-site-visit', 11, 1, NOW(), NOW()),
(2, 'process', 'custom-design', 12, 1, NOW(), NOW()),
(2, 'process', 'certified-supply', 13, 1, NOW(), NOW()),
(2, 'process', 'installation-testing', 14, 1, NOW(), NOW()),
(2, 'process', 'training-certification', 15, 1, NOW(), NOW()),
(2, 'process', 'maintenance-contract', 16, 1, NOW(), NOW());

-- ترجمات Process Steps باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(104, 1, 'Free Site Visit', 'Our certified consultants conduct a comprehensive site assessment, analyzing risks, existing systems, and regulatory requirements.', NOW(), NOW()),
(105, 1, 'Custom Design', 'Engineering team creates detailed system designs tailored to your facility, including calculations, specifications, and compliance documentation.', NOW(), NOW()),
(106, 1, 'Certified Supply', 'Procurement of UL/FM certified equipment from trusted manufacturers, ensuring quality and compliance with international standards.', NOW(), NOW()),
(107, 1, 'Installation & Testing', 'Professional installation by certified technicians, followed by comprehensive testing and commissioning of all system components.', NOW(), NOW()),
(108, 1, 'Training & Certification', 'Comprehensive training for your staff on system operation, maintenance procedures, and emergency response protocols.', NOW(), NOW()),
(109, 1, 'Maintenance Contract', 'Ongoing maintenance and support services to ensure optimal system performance and continued compliance with safety regulations.', NOW(), NOW());

-- ترجمات Process Steps باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(104, 2, 'زيارة موقع مجانية', 'يقوم مستشارونا المعتمدون بتقييم شامل للموقع، وتحليل المخاطر والأنظمة الموجودة والمتطلبات التنظيمية.', NOW(), NOW()),
(105, 2, 'تصميم مخصص', 'يقوم فريق الهندسة بإنشاء تصميمات نظام مفصلة مخصصة لمنشأتك، بما في ذلك الحسابات والمواصفات وتوثيق الامتثال.', NOW(), NOW()),
(106, 2, 'إمداد معتمد', 'شراء معدات معتمدة UL/FM من مصنعين موثوقين، مما يضمن الجودة والامتثال للمعايير الدولية.', NOW(), NOW()),
(107, 2, 'التركيب والاختبار', 'تركيب احترافي من قبل فنيين معتمدين، يليه اختبار شامل وتشغيل جميع مكونات النظام.', NOW(), NOW()),
(108, 2, 'التدريب والشهادة', 'تدريب شامل لموظفيك على تشغيل النظام وإجراءات الصيانة وبروتوكولات الاستجابة للطوارئ.', NOW(), NOW()),
(109, 2, 'عقد الصيانة', 'خدمات الصيانة والدعم المستمرة لضمان الأداء الأمثل للنظام والامتثال المستمر للوائح السلامة.', NOW(), NOW());

-- 6. إضافة Team Members (فريق العمل)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'team', 'engineering-department', 17, 1, NOW(), NOW()),
(2, 'team', 'consultation-team', 18, 1, NOW(), NOW()),
(2, 'team', 'technical-support', 19, 1, NOW(), NOW());

-- ترجمات Team Members باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(110, 1, 'Engineering Department', 'Certified professionals with extensive experience in industrial fire protection system design and implementation.', 'Lead Fire Safety Engineers', NOW(), NOW()),
(111, 1, 'Consultation Team', 'Expert consultants specializing in risk assessment, compliance auditing, and safety management systems.', 'Senior Safety Consultants', NOW(), NOW()),
(112, 1, 'Technical Support', 'Skilled technicians providing professional installation, testing, commissioning, and ongoing maintenance services.', 'Installation & Maintenance', NOW(), NOW());

-- ترجمات Team Members باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(110, 2, 'قسم الهندسة', 'محترفون معتمدون بخبرة واسعة في تصميم وتنفيذ أنظمة الحماية من الحريق الصناعية.', 'مهندسو السلامة من الحريق الرئيسيون', NOW(), NOW()),
(111, 2, 'فريق الاستشارة', 'مستشارون خبراء متخصصون في تقييم المخاطر ومراجعة الامتثال وأنظمة إدارة السلامة.', 'مستشارو السلامة الكبار', NOW(), NOW()),
(112, 2, 'الدعم التقني', 'فنيون ماهرون يقدمون خدمات التركيب والاختبار والتشغيل والصيانة المستمرة الاحترافية.', 'التركيب والصيانة', NOW(), NOW());

-- 7. إضافة Final CTA Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'cta', 'about-final-cta', 20, 1, NOW(), NOW());

-- ترجمات Final CTA باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(113, 1, 'Let''s build a safer tomorrow—starting with your facility today.', 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety excellence.', NOW(), NOW());

-- ترجمات Final CTA باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(113, 2, 'دعنا نبني غداً أكثر أماناً—بدءاً بمنشأتك اليوم.', 'انضم إلى مئات المنشآت التي تثق في سفينكس فاير للتميز الكامل في السلامة من الحريق.', NOW(), NOW());

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • 1 Hero Section (2 ترجمات)
-- • 1 Overview Section (2 ترجمات)  
-- • 3 Values Sections (6 ترجمات)
-- • 5 Advantages Sections (10 ترجمات)
-- • 6 Process Steps (12 ترجمة)
-- • 3 Team Members (6 ترجمات)
-- • 1 Final CTA (2 ترجمات)
-- =====================================================
-- المجموع: 20 سكشن + 40 ترجمة = 60 سجل
-- ===================================================== 