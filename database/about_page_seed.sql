-- =============================
-- SEED: About Page Sections (about.html)
-- =============================

-- =============================
-- HERO SECTION (مكرر من home، لن يتم تكراره)
-- =============================

-- =============================
-- OVERVIEW SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(16, 2, 'overview', 'about-overview', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- VISION, MISSION, VALUES SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(17, 2, 'about_value', 'about-vision', 2, TRUE),
(18, 2, 'about_value', 'about-mission', 3, TRUE),
(19, 2, 'about_value', 'about-values', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- =============================
-- PROCESS SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(20, 2, 'process_step', 'about-process-step-1', 5, TRUE),
(21, 2, 'process_step', 'about-process-step-2', 6, TRUE),
(22, 2, 'process_step', 'about-process-step-3', 7, TRUE),
(23, 2, 'process_step', 'about-process-step-4', 8, TRUE),
(24, 2, 'process_step', 'about-process-step-5', 9, TRUE),
(25, 2, 'process_step', 'about-process-step-6', 10, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- =============================
-- PARTNERSHIPS & CERTIFICATIONS SECTION
-- =============================
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(26, 2, 'partners', 'about-partners', 11, TRUE),
(27, 2, 'certifications', 'about-certifications', 12, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Overview - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 16, l.id, 'Built for Industry. Backed by Experience.', 'Based in the heart of Egypt''s industrial zone in Sadat City, Sphinx Fire has emerged as the leading provider of comprehensive fire safety solutions for industrial facilities across the region. Our specialty lies in the complete spectrum of fire safety—from initial consultation and risk assessment to system design, certified equipment supply, professional installation, and ongoing maintenance. Led by certified engineers and safety consultants with decades of combined experience, we serve B2B clients across diverse industries including manufacturing plants, chemical facilities, warehouses, shopping centers, and high-risk industrial operations. Every system we design and install is backed by international certifications and compliance with the highest safety standards, ensuring your facility is protected and your business is secure.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Overview - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 16, l.id, 'مصمم للصناعة. مدعوم بالخبرة.', 'يقع مقر سفينكس فاير في قلب المنطقة الصناعية بمدينة السادات، وقد أصبحت الشركة المزود الرائد لحلول السلامة من الحريق للمنشآت الصناعية في المنطقة. تتخصص الشركة في جميع جوانب السلامة من الحريق بدءًا من الاستشارات والتقييم وحتى التصميم والتوريد والتركيب والصيانة الدورية. يقود الفريق مهندسون ومستشارون معتمدون بخبرة عقود، ونخدم عملاء B2B في قطاعات متنوعة مثل المصانع والمنشآت الكيميائية والمخازن والمراكز التجارية والعمليات الصناعية عالية الخطورة. كل نظام نصممه ونركبه مدعوم بشهادات دولية وامتثال لأعلى معايير السلامة لضمان حماية منشأتك وأمان عملك.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================

-- Vision - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 17, l.id, 'Vision', 'To lead Egypt''s industrial safety transformation with smart, reliable, and integrated systems that set new standards for fire protection excellence.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Vision - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 17, l.id, 'الرؤية', 'قيادة تحول السلامة الصناعية في مصر بأنظمة ذكية وموثوقة ومتكاملة تضع معايير جديدة للتميز في الحماية من الحريق.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Mission - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 18, l.id, 'Mission', 'Deliver complete fire safety solutions that combine engineering excellence, fast response, and compliance with top global standards to protect lives and assets.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Mission - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 18, l.id, 'الرسالة', 'تقديم حلول سلامة متكاملة تجمع بين التميز الهندسي وسرعة الاستجابة والامتثال لأعلى المعايير العالمية لحماية الأرواح والممتلكات.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Values - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 19, l.id, 'Values', 'Integrity • Precision • Readiness • Responsibility • Innovation'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Values - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 19, l.id, 'القيم', 'النزاهة • الدقة • الجاهزية • المسؤولية • الابتكار'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);


-- Process Steps - English
INSERT INTO section_translations (section_id, language_id, title, content) VALUES
(20, (SELECT id FROM languages WHERE code='en'), 'Free Site Visit', 'Our certified consultants conduct a comprehensive site assessment, analyzing risks, existing systems, and regulatory requirements.'),
(21, (SELECT id FROM languages WHERE code='en'), 'Custom Design', 'Engineering team creates detailed system designs tailored to your facility, including calculations, specifications, and compliance documentation.'),
(22, (SELECT id FROM languages WHERE code='en'), 'Certified Supply', 'Procurement of UL/FM certified equipment from trusted manufacturers, ensuring quality and compliance with international standards.'),
(23, (SELECT id FROM languages WHERE code='en'), 'Installation & Testing', 'Professional installation by certified technicians, followed by comprehensive testing and commissioning of all system components.'),
(24, (SELECT id FROM languages WHERE code='en'), 'Training & Certification', 'Comprehensive training for your staff on system operation, maintenance procedures, and emergency response protocols.'),
(25, (SELECT id FROM languages WHERE code='en'), 'Maintenance Contract', 'Ongoing maintenance and support services to ensure optimal system performance and continued compliance with safety regulations.')
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Process Steps - Arabic
INSERT INTO section_translations (section_id, language_id, title, content) VALUES
(20, (SELECT id FROM languages WHERE code='ar'), 'زيارة موقع مجانية', 'يقوم مستشارونا المعتمدون بإجراء تقييم شامل للموقع، وتحليل المخاطر والأنظمة الحالية والمتطلبات التنظيمية.'),
(21, (SELECT id FROM languages WHERE code='ar'), 'تصميم مخصص', 'يقوم فريق الهندسة بإعداد تصاميم نظام مفصلة حسب منشأتك، تشمل الحسابات والمواصفات ووثائق الامتثال.'),
(22, (SELECT id FROM languages WHERE code='ar'), 'توريد معتمد', 'توريد معدات معتمدة UL/FM من موردين موثوقين، لضمان الجودة والامتثال للمعايير الدولية.'),
(23, (SELECT id FROM languages WHERE code='ar'), 'التركيب والاختبار', 'تركيب احترافي بواسطة فنيين معتمدين، متبوع باختبارات شاملة وتشغيل جميع مكونات النظام.'),
(24, (SELECT id FROM languages WHERE code='ar'), 'التدريب والشهادات', 'تدريب شامل لفريقك على تشغيل النظام وإجراءات الصيانة والاستجابة للطوارئ.'),
(25, (SELECT id FROM languages WHERE code='ar'), 'عقد الصيانة', 'خدمات صيانة ودعم مستمرة لضمان الأداء الأمثل للنظام والامتثال الدائم للوائح السلامة.')
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- TEAM SECTION (سيبقى ستاتيك ولن يُدرج)
-- =============================


-- Partners - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 26, l.id, 'Technology Integration', 'SCADA Systems, Siemens, BMS Integration, Control Systems'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Partners - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 26, l.id, 'تكامل التكنولوجيا', 'أنظمة SCADA، سيمنز، تكامل BMS، أنظمة التحكم'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Certifications - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 27, l.id, 'Certifications & Standards', 'UL/FM Listed, NFPA Standards, OSHA Compliant, Civil Defense'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- Certifications - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 27, l.id, 'الشهادات والمعايير', 'UL/FM، معايير NFPA، متوافق مع OSHA، الدفاع المدني'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- CTA SECTION (مكرر من home، لن يتم تكراره)
-- ============================= 