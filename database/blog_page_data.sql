START TRANSACTION;

-- =====================================================
-- إضافة البيانات المطلوبة لصفحة المدونة (Blog Page Data)
-- متوافق مع بنية الجداول الصحيحة
-- =====================================================

-- إضافة فئات المدونة الجديدة
INSERT INTO blog_categories (id, slug, color, sort_order, is_active, created_at, updated_at) 
VALUES 
(4, 'fire-systems', '#F59E0B', 4, 1, NOW(), NOW()),
(5, 'extinguishers', '#8B5CF6', 5, 1, NOW(), NOW()),
(6, 'osha', '#EF4444', 6, 1, NOW(), NOW()),
(7, 'civil-defense', '#06B6D4', 7, 1, NOW(), NOW()),
(8, 'pumps', '#84CC16', 8, 1, NOW(), NOW()),
(9, 'ppe', '#EC4899', 9, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات الفئات الجديدة باللغة الإنجليزية
INSERT INTO blog_category_translations (category_id, language_id, name, description, meta_title, meta_description, created_at, updated_at) 
VALUES 
(4, 1, 'Fire Systems', 'Fire detection and suppression systems', 'Fire Systems - Sphinx Fire', 'Complete guide to fire detection and suppression systems', NOW(), NOW()),
(5, 1, 'Extinguishers', 'Fire extinguisher guides and maintenance', 'Fire Extinguishers - Sphinx Fire', 'Complete guide to fire extinguishers and maintenance', NOW(), NOW()),
(6, 1, 'OSHA', 'OSHA fire safety requirements', 'OSHA Fire Safety - Sphinx Fire', 'OSHA fire safety requirements and regulations', NOW(), NOW()),
(7, 1, 'Civil Defense', 'Egyptian Civil Defense regulations', 'Civil Defense - Sphinx Fire', 'Egyptian Civil Defense regulations and requirements', NOW(), NOW()),
(8, 1, 'Pumps', 'Fire pump systems and maintenance', 'Fire Pumps - Sphinx Fire', 'Fire pump systems and maintenance guides', NOW(), NOW()),
(9, 1, 'PPE', 'Personal Protective Equipment', 'PPE - Sphinx Fire', 'Personal Protective Equipment for fire safety', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), updated_at = NOW();

-- إضافة ترجمات الفئات الجديدة باللغة العربية
INSERT INTO blog_category_translations (category_id, language_id, name, description, meta_title, meta_description, created_at, updated_at) 
VALUES 
(4, 2, 'أنظمة الحريق', 'أنظمة كشف وإطفاء الحريق', 'أنظمة الحريق - سفنكس فاير', 'دليل شامل لأنظمة كشف وإطفاء الحريق', NOW(), NOW()),
(5, 2, 'طفايات الحريق', 'دلائل وصيانة طفايات الحريق', 'طفايات الحريق - سفنكس فاير', 'دليل شامل لطفايات الحريق والصيانة', NOW(), NOW()),
(6, 2, 'أوشا', 'متطلبات السلامة من الحريق OSHA', 'أوشا السلامة من الحريق - سفنكس فاير', 'متطلبات ولوائح السلامة من الحريق OSHA', NOW(), NOW()),
(7, 2, 'الدفاع المدني', 'لوائح الدفاع المدني المصري', 'الدفاع المدني - سفنكس فاير', 'لوائح ومتطلبات الدفاع المدني المصري', NOW(), NOW()),
(8, 2, 'المضخات', 'أنظمة مضخات الحريق وصيانتها', 'مضخات الحريق - سفنكس فاير', 'أنظمة مضخات الحريق ودلائل الصيانة', NOW(), NOW()),
(9, 2, 'معدات الحماية الشخصية', 'معدات الحماية الشخصية', 'معدات الحماية الشخصية - سفنكس فاير', 'معدات الحماية الشخصية للسلامة من الحريق', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), updated_at = NOW();

-- إضافة مقالات المدونة الجديدة
INSERT INTO blog_posts (id, category_id, slug, author_id, featured_image, reading_time, status, is_featured, views_count, likes_count, shares_count, published_at, created_at, updated_at) 
VALUES 
(4, 4, 'nfpa20-fire-pump-standards', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 6, 'published', 0, 0, 0, 0, '2025-01-15 09:00:00', NOW(), NOW()),
(5, 5, 'fire-extinguisher-selection-guide', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 4, 'published', 0, 0, 0, 0, '2025-01-12 11:00:00', NOW(), NOW()),
(6, 6, 'osha-fire-safety-requirements', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 7, 'published', 0, 0, 0, 0, '2025-01-10 14:00:00', NOW(), NOW()),
(7, 7, 'egyptian-civil-defense-updates', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 5, 'published', 0, 0, 0, 0, '2025-01-08 16:00:00', NOW(), NOW()),
(8, 8, 'fire-pump-maintenance-guide', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 9, 'published', 0, 0, 0, 0, '2025-01-05 13:00:00', NOW(), NOW()),
(9, 9, 'ppe-selection-guide', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 6, 'published', 0, 0, 0, 0, '2025-01-03 15:00:00', NOW(), NOW()),
(10, 1, 'fire-safety-documentation', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 8, 'published', 0, 0, 0, 0, '2024-12-30 12:00:00', NOW(), NOW()),
(11, 4, 'foam-suppression-systems', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 10, 'published', 0, 0, 0, 0, '2024-12-28 10:00:00', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات المقالات الجديدة باللغة الإنجليزية
INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
(4, 1, 'NFPA 20: Fire Pump Installation Standards Explained',
'Complete breakdown of NFPA 20 requirements for fire pump installations. Covers pump room design, electrical connections, and testing procedures.',
'<h2>NFPA 20 Overview</h2><p>NFPA 20 provides requirements for the selection and installation of pumps supplying liquid for private fire protection...</p>',
'["NFPA 20","fire pump","installation","standards"]',
'NFPA 20: Fire Pump Installation Standards Explained - Sphinx Fire',
'Complete guide to NFPA 20 fire pump installation standards and requirements.',
'NFPA 20, fire pump, installation, standards, fire protection',
NOW(), NOW()),

(5, 1, 'Fire Extinguisher Selection: Complete Guide for Industrial Facilities',
'How to choose the right extinguisher type for different fire classes. Includes placement guidelines and maintenance schedules.',
'<h2>Understanding Fire Classes</h2><p>Different types of fires require different extinguishing agents...</p>',
'["fire extinguisher","selection","industrial","fire classes"]',
'Fire Extinguisher Selection Guide for Industrial Facilities - Sphinx Fire',
'Complete guide to selecting and maintaining fire extinguishers in industrial facilities.',
'fire extinguisher, selection, industrial, fire classes, maintenance',
NOW(), NOW()),

(6, 1, 'OSHA Fire Safety Requirements: What Every Facility Manager Must Know',
'Essential OSHA regulations for workplace fire safety. Emergency action plans, exit routes, and employee training requirements.',
'<h2>OSHA Fire Safety Standards</h2><p>OSHA has specific requirements for workplace fire safety...</p>',
'["OSHA","fire safety","workplace","regulations"]',
'OSHA Fire Safety Requirements for Facility Managers - Sphinx Fire',
'Essential OSHA fire safety regulations and requirements for workplace safety.',
'OSHA, fire safety, workplace, regulations, emergency plans',
NOW(), NOW()),

(7, 1, 'Egyptian Civil Defense: 2024 Regulation Updates for Industrial Facilities',
'Latest changes in Egyptian fire safety regulations. New requirements for industrial facilities and updated inspection procedures.',
'<h2>2024 Updates Overview</h2><p>The Egyptian Civil Defense has updated several regulations for industrial facilities...</p>',
'["Egyptian Civil Defense","regulations","2024","industrial facilities"]',
'Egyptian Civil Defense 2024 Regulation Updates - Sphinx Fire',
'Latest updates to Egyptian Civil Defense regulations for industrial facilities.',
'Egyptian Civil Defense, regulations, 2024, industrial facilities, updates',
NOW(), NOW()),

(8, 1, 'Fire Pump Maintenance: Preventive Care for Maximum Reliability',
'Comprehensive maintenance schedule for fire pumps. Weekly, monthly, and annual inspection checklists to ensure optimal performance.',
'<h2>Maintenance Schedule</h2><p>Regular maintenance is crucial for fire pump reliability...</p>',
'["fire pump","maintenance","preventive care","reliability"]',
'Fire Pump Maintenance Guide - Sphinx Fire',
'Comprehensive guide to fire pump maintenance and preventive care.',
'fire pump, maintenance, preventive care, reliability, inspection',
NOW(), NOW()),

(9, 1, 'Personal Protective Equipment: Selection Guide for Fire Safety Teams',
'How to choose appropriate PPE for different fire scenarios. Fire suits, breathing apparatus, and emergency response equipment.',
'<h2>PPE Requirements</h2><p>Fire safety teams require specific protective equipment...</p>',
'["PPE","personal protective equipment","fire safety","emergency response"]',
'PPE Selection Guide for Fire Safety Teams - Sphinx Fire',
'Complete guide to selecting personal protective equipment for fire safety teams.',
'PPE, personal protective equipment, fire safety, emergency response',
NOW(), NOW()),

(10, 1, 'Fire Safety Documentation: Essential Records Every Facility Must Keep',
'Complete guide to fire safety record keeping. Inspection logs, maintenance records, and training documentation requirements.',
'<h2>Documentation Requirements</h2><p>Proper documentation is essential for compliance...</p>',
'["fire safety","documentation","records","compliance"]',
'Fire Safety Documentation Guide - Sphinx Fire',
'Essential fire safety documentation and record keeping requirements.',
'fire safety, documentation, records, compliance, inspections',
NOW(), NOW()),

(11, 1, 'Foam Suppression Systems: Design and Application for High-Risk Facilities',
'When and how to use foam suppression systems. Design considerations for chemical storage, paint facilities, and flammable liquid areas.',
'<h2>Foam System Basics</h2><p>Foam suppression systems are essential for high-risk facilities...</p>',
'["foam suppression","fire protection","high-risk facilities","design"]',
'Foam Suppression Systems Design Guide - Sphinx Fire',
'Complete guide to foam suppression system design and application.',
'foam suppression, fire protection, high-risk facilities, design',
NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), updated_at = NOW();

-- إضافة ترجمات المقالات الجديدة باللغة العربية
INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
(4, 2, 'NFPA 20: شرح معايير تركيب مضخات الحريق',
'تحليل شامل لمتطلبات NFPA 20 لتركيب مضخات الحريق. يغطي تصميم غرفة المضخة والاتصالات الكهربائية وإجراءات الاختبار.',
'<h2>نظرة عامة على NFPA 20</h2><p>يوفر NFPA 20 متطلبات اختيار وتركيب المضخات التي تزود السوائل للحماية من الحريق الخاصة...</p>',
'["NFPA 20","مضخة الحريق","تركيب","معايير"]',
'NFPA 20: شرح معايير تركيب مضخات الحريق - سفنكس فاير',
'دليل شامل لمعايير تركيب مضخات الحريق NFPA 20 والمتطلبات.',
'NFPA 20، مضخة الحريق، تركيب، معايير، الحماية من الحريق',
NOW(), NOW()),

(5, 2, 'اختيار طفايات الحريق: دليل شامل للمنشآت الصناعية',
'كيفية اختيار نوع طفاية الحريق المناسب لفئات الحريق المختلفة. يتضمن إرشادات التنسيب وجداول الصيانة.',
'<h2>فهم فئات الحريق</h2><p>تتطلب أنواع مختلفة من الحرائق عوامل إطفاء مختلفة...</p>',
'["طفاية الحريق","اختيار","صناعي","فئات الحريق"]',
'دليل اختيار طفايات الحريق للمنشآت الصناعية - سفنكس فاير',
'دليل شامل لاختيار وصيانة طفايات الحريق في المنشآت الصناعية.',
'طفاية الحريق، اختيار، صناعي، فئات الحريق، صيانة',
NOW(), NOW()),

(6, 2, 'متطلبات السلامة من الحريق OSHA: ما يجب أن يعرفه كل مدير منشأة',
'اللوائح الأساسية OSHA للسلامة من الحريق في مكان العمل. خطط الطوارئ ومسارات الخروج ومتطلبات تدريب الموظفين.',
'<h2>معايير السلامة من الحريق OSHA</h2><p>لدى OSHA متطلبات محددة للسلامة من الحريق في مكان العمل...</p>',
'["OSHA","السلامة من الحريق","مكان العمل","لوائح"]',
'متطلبات السلامة من الحريق OSHA لمديري المنشآت - سفنكس فاير',
'اللوائح الأساسية OSHA للسلامة من الحريق والمتطلبات لسلامة مكان العمل.',
'OSHA، السلامة من الحريق، مكان العمل، لوائح، خطط الطوارئ',
NOW(), NOW()),

(7, 2, 'الدفاع المدني المصري: تحديثات اللوائح 2024 للمنشآت الصناعية',
'أحدث التغييرات في لوائح السلامة من الحريق المصرية. متطلبات جديدة للمنشآت الصناعية وإجراءات فحص محدثة.',
'<h2>نظرة عامة على تحديثات 2024</h2><p>قام الدفاع المدني المصري بتحديث عدة لوائح للمنشآت الصناعية...</p>',
'["الدفاع المدني المصري","لوائح","2024","المنشآت الصناعية"]',
'تحديثات لوائح الدفاع المدني المصري 2024 - سفنكس فاير',
'أحدث تحديثات لوائح الدفاع المدني المصري للمنشآت الصناعية.',
'الدفاع المدني المصري، لوائح، 2024، المنشآت الصناعية، تحديثات',
NOW(), NOW()),

(8, 2, 'صيانة مضخات الحريق: الرعاية الوقائية لأقصى موثوقية',
'جدول صيانة شامل لمضخات الحريق. قوائم فحص أسبوعية وشهرية وسنوية لضمان الأداء الأمثل.',
'<h2>جدول الصيانة</h2><p>الصيانة المنتظمة ضرورية لموثوقية مضخة الحريق...</p>',
'["مضخة الحريق","صيانة","رعاية وقائية","موثوقية"]',
'دليل صيانة مضخات الحريق - سفنكس فاير',
'دليل شامل لصيانة مضخات الحريق والرعاية الوقائية.',
'مضخة الحريق، صيانة، رعاية وقائية، موثوقية، فحص',
NOW(), NOW()),

(9, 2, 'معدات الحماية الشخصية: دليل الاختيار لفرق السلامة من الحريق',
'كيفية اختيار معدات الحماية الشخصية المناسبة لسيناريوهات الحريق المختلفة. بدلات الحريق وأجهزة التنفس ومعدات الاستجابة للطوارئ.',
'<h2>متطلبات معدات الحماية الشخصية</h2><p>تتطلب فرق السلامة من الحريق معدات حماية محددة...</p>',
'["معدات الحماية الشخصية","السلامة من الحريق","الاستجابة للطوارئ"]',
'دليل اختيار معدات الحماية الشخصية لفرق السلامة من الحريق - سفنكس فاير',
'دليل شامل لاختيار معدات الحماية الشخصية لفرق السلامة من الحريق.',
'معدات الحماية الشخصية، السلامة من الحريق، الاستجابة للطوارئ',
NOW(), NOW()),

(10, 2, 'توثيق السلامة من الحريق: السجلات الأساسية التي يجب أن تحتفظ بها كل منشأة',
'دليل شامل لحفظ سجلات السلامة من الحريق. سجلات الفحص وسجلات الصيانة ومتطلبات توثيق التدريب.',
'<h2>متطلبات التوثيق</h2><p>التوثيق المناسب ضروري للامتثال...</p>',
'["السلامة من الحريق","توثيق","سجلات","امتثال"]',
'دليل توثيق السلامة من الحريق - سفنكس فاير',
'متطلبات توثيق السلامة من الحريق الأساسية وحفظ السجلات.',
'السلامة من الحريق، توثيق، سجلات، امتثال، فحوصات',
NOW(), NOW()),

(11, 2, 'أنظمة إطفاء الرغوة: التصميم والتطبيق للمنشآت عالية المخاطر',
'متى وكيف تستخدم أنظمة إطفاء الرغوة. اعتبارات التصميم لتخزين المواد الكيميائية ومنشآت الطلاء ومناطق السوائل القابلة للاشتعال.',
'<h2>أساسيات نظام الرغوة</h2><p>أنظمة إطفاء الرغوة ضرورية للمنشآت عالية المخاطر...</p>',
'["إطفاء الرغوة","الحماية من الحريق","المنشآت عالية المخاطر","تصميم"]',
'دليل تصميم أنظمة إطفاء الرغوة - سفنكس فاير',
'دليل شامل لتصميم وتطبيق أنظمة إطفاء الرغوة.',
'إطفاء الرغوة، الحماية من الحريق، المنشآت عالية المخاطر، تصميم',
NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), updated_at = NOW();

COMMIT;

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • 6 فئات جديدة للمدونة (إنجليزي + عربي)
-- • 8 مقالات مدونة جديدة (إنجليزي + عربي)
-- • 16 ترجمة للمقالات الجديدة
-- • 12 ترجمة للفئات الجديدة
-- ===================================================== 