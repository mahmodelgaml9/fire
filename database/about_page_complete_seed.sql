-- إضافة بيانات صفحة About (من نحن) - السكاشنات والترجمات

-- 1. إضافة مزايا الشركة (What Sets Us Apart)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'advantage', 'strategic-location', 1, 1, NOW(), NOW()),
(2, 'advantage', 'complete-integration', 2, 1, NOW(), NOW()),
(2, 'advantage', 'expert-leadership', 3, 1, NOW(), NOW()),
(2, 'advantage', 'responsive-team', 4, 1, NOW(), NOW()),
(2, 'advantage', 'compliance-guaranteed', 5, 1, NOW(), NOW());

-- ترجمات المزايا باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(80, 1, 'Strategically Located', 'Based inside Sadat City, we''re minutes away from major industrial facilities, ensuring rapid response times and cost-effective service delivery.', '50+ Projects Completed', NOW(), NOW()),
(81, 1, 'Complete System Integration', 'SCADA-ready systems that integrate seamlessly with your existing building management and industrial control systems.', '100% SCADA Compatible', NOW(), NOW()),
(82, 1, 'Expert Leadership', 'Led by certified consultants with 10+ years in industrial fire safety, bringing deep expertise to every project.', '10+ Years Experience', NOW(), NOW()),
(83, 1, 'Responsive Team', 'Fast site visits, direct contact with decision makers, and emergency support when you need it most.', '24-Hour Response', NOW(), NOW()),
(84, 1, 'Compliance Guaranteed', 'Full compliance with NFPA, OSHA, and Egyptian Civil Defense regulations, with documentation and certification support.', '100% Compliance Rate', NOW(), NOW());

-- ترجمات المزايا باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(80, 2, 'موقع استراتيجي', 'مقرنا داخل مدينة السادات، على بعد دقائق من المنشآت الصناعية الكبرى، مما يضمن أوقات استجابة سريعة وتقديم خدمات فعالة من حيث التكلفة.', '50+ مشروع مكتمل', NOW(), NOW()),
(81, 2, 'تكامل النظام الكامل', 'أنظمة جاهزة لـ SCADA تتكامل بسلاسة مع أنظمة إدارة المباني وأنظمة التحكم الصناعية الموجودة.', '100% متوافق مع SCADA', NOW(), NOW()),
(82, 2, 'قيادة خبيرة', 'يقودنا مستشارون معتمدون بخبرة 10+ سنوات في السلامة من الحريق الصناعي، يجلبون خبرة عميقة لكل مشروع.', '10+ سنوات خبرة', NOW(), NOW()),
(83, 2, 'فريق متجاوب', 'زيارات موقع سريعة، اتصال مباشر مع صانعي القرار، ودعم طوارئ عندما تحتاجه أكثر.', 'استجابة 24 ساعة', NOW(), NOW()),
(84, 2, 'الامتثال مضمون', 'امتثال كامل لمعايير NFPA و OSHA ولوائح الدفاع المدني المصري، مع دعم التوثيق والشهادات.', 'معدل امتثال 100%', NOW(), NOW());

-- 2. إضافة خطوات العملية (Our Process)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'process', 'free-site-visit', 1, 1, NOW(), NOW()),
(2, 'process', 'custom-design', 2, 1, NOW(), NOW()),
(2, 'process', 'certified-supply', 3, 1, NOW(), NOW()),
(2, 'process', 'installation-testing', 4, 1, NOW(), NOW()),
(2, 'process', 'training-certification', 5, 1, NOW(), NOW()),
(2, 'process', 'maintenance-contract', 6, 1, NOW(), NOW());

-- ترجمات خطوات العملية باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(85, 1, 'Free Site Visit', 'Our certified consultants conduct a comprehensive site assessment, analyzing risks, existing systems, and regulatory requirements.', NOW(), NOW()),
(86, 1, 'Custom Design', 'Engineering team creates detailed system designs tailored to your facility, including calculations, specifications, and compliance documentation.', NOW(), NOW()),
(87, 1, 'Certified Supply', 'Procurement of UL/FM certified equipment from trusted manufacturers, ensuring quality and compliance with international standards.', NOW(), NOW()),
(88, 1, 'Installation & Testing', 'Professional installation by certified technicians, followed by comprehensive testing and commissioning of all system components.', NOW(), NOW()),
(89, 1, 'Training & Certification', 'Comprehensive training for your staff on system operation, maintenance procedures, and emergency response protocols.', NOW(), NOW()),
(90, 1, 'Maintenance Contract', 'Ongoing maintenance and support services to ensure optimal system performance and continued compliance with safety regulations.', NOW(), NOW());

-- ترجمات خطوات العملية باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) VALUES
(85, 2, 'زيارة موقع مجانية', 'يقوم مستشارونا المعتمدون بتقييم شامل للموقع، وتحليل المخاطر والأنظمة الموجودة والمتطلبات التنظيمية.', NOW(), NOW()),
(86, 2, 'تصميم مخصص', 'يقوم فريق الهندسة بإنشاء تصميمات نظام مفصلة مخصصة لمنشأتك، بما في ذلك الحسابات والمواصفات وتوثيق الامتثال.', NOW(), NOW()),
(87, 2, 'إمداد معتمد', 'شراء معدات معتمدة UL/FM من مصنعين موثوقين، مما يضمن الجودة والامتثال للمعايير الدولية.', NOW(), NOW()),
(88, 2, 'التركيب والاختبار', 'تركيب احترافي من قبل فنيين معتمدين، يليه اختبار شامل وتشغيل جميع مكونات النظام.', NOW(), NOW()),
(89, 2, 'التدريب والشهادة', 'تدريب شامل لموظفيك على تشغيل النظام وإجراءات الصيانة وبروتوكولات الاستجابة للطوارئ.', NOW(), NOW()),
(90, 2, 'عقد الصيانة', 'خدمات الصيانة والدعم المستمرة لضمان الأداء الأمثل للنظام والامتثال المستمر للوائح السلامة.', NOW(), NOW());

-- 3. إضافة فريق العمل (Meet the Experts)
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(2, 'team', 'engineering-department', 1, 1, NOW(), NOW()),
(2, 'team', 'consultation-team', 2, 1, NOW(), NOW()),
(2, 'team', 'technical-support', 3, 1, NOW(), NOW());

-- ترجمات فريق العمل باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(91, 1, 'Engineering Department', 'Certified professionals with extensive experience in industrial fire protection system design and implementation.', 'Lead Fire Safety Engineers', NOW(), NOW()),
(92, 1, 'Consultation Team', 'Expert consultants specializing in risk assessment, compliance auditing, and safety management systems.', 'Senior Safety Consultants', NOW(), NOW()),
(93, 1, 'Technical Support', 'Skilled technicians providing professional installation, testing, commissioning, and ongoing maintenance services.', 'Installation & Maintenance', NOW(), NOW());

-- ترجمات فريق العمل باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) VALUES
(91, 2, 'قسم الهندسة', 'محترفون معتمدون بخبرة واسعة في تصميم وتنفيذ أنظمة الحماية من الحريق الصناعية.', 'مهندسو السلامة من الحريق الرئيسيون', NOW(), NOW()),
(92, 2, 'فريق الاستشارة', 'مستشارون خبراء متخصصون في تقييم المخاطر ومراجعة الامتثال وأنظمة إدارة السلامة.', 'مستشارو السلامة الكبار', NOW(), NOW()),
(93, 2, 'الدعم التقني', 'فنيون ماهرون يقدمون خدمات التركيب والاختبار والتشغيل والصيانة المستمرة الاحترافية.', 'التركيب والصيانة', NOW(), NOW()); 