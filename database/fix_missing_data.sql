-- إصلاح البيانات المفقودة في قاعدة البيانات

-- 1. إضافة البيانات المفقودة في جدول location_translations
INSERT INTO location_translations (location_id, language_id, name, description, meta_title, meta_description, local_keywords, created_at, updated_at) VALUES
(2, 1, 'October Industrial City', 'Fire protection services for October Industrial City', 'Fire Protection October City - Sphinx Fire', 'Professional fire safety services in October Industrial City, Egypt', 'fire protection October City, firefighting systems October industrial zone', NOW(), NOW()),
(2, 2, 'مدينة أكتوبر الصناعية', 'خدمات الحماية من الحريق لمدينة أكتوبر الصناعية', 'الحماية من الحريق مدينة أكتوبر - سفينكس فاير', 'خدمات السلامة من الحريق المهنية في مدينة أكتوبر الصناعية، مصر', 'الحماية من الحريق مدينة أكتوبر، أنظمة مكافحة الحريق المنطقة الصناعية أكتوبر', NOW(), NOW()),
(3, 1, 'Quesna Industrial Zone', 'Fire protection services for Quesna Industrial Zone', 'Fire Protection Quesna - Sphinx Fire', 'Professional fire safety services in Quesna Industrial Zone, Egypt', 'fire protection Quesna, firefighting systems Quesna industrial zone', NOW(), NOW()),
(3, 2, 'المنطقة الصناعية قويسنا', 'خدمات الحماية من الحريق للمنطقة الصناعية قويسنا', 'الحماية من الحريق قويسنا - سفينكس فاير', 'خدمات السلامة من الحريق المهنية في المنطقة الصناعية قويسنا، مصر', 'الحماية من الحريق قويسنا، أنظمة مكافحة الحريق المنطقة الصناعية قويسنا', NOW(), NOW()),
(4, 1, 'Badr Industrial City', 'Fire protection services for Badr Industrial City', 'Fire Protection Badr City - Sphinx Fire', 'Professional fire safety services in Badr Industrial City, Egypt', 'fire protection Badr City, firefighting systems Badr industrial zone', NOW(), NOW()),
(4, 2, 'مدينة بدر الصناعية', 'خدمات الحماية من الحريق لمدينة بدر الصناعية', 'الحماية من الحريق مدينة بدر - سفينكس فاير', 'خدمات السلامة من الحريق المهنية في مدينة بدر الصناعية، مصر', 'الحماية من الحريق مدينة بدر، أنظمة مكافحة الحريق المنطقة الصناعية بدر', NOW(), NOW()),
(5, 1, '10th of Ramadan Industrial City', 'Fire protection services for 10th of Ramadan Industrial City', 'Fire Protection 10th Ramadan - Sphinx Fire', 'Professional fire safety services in 10th of Ramadan Industrial City, Egypt', 'fire protection 10th Ramadan, firefighting systems 10th Ramadan industrial zone', NOW(), NOW()),
(5, 2, 'مدينة العاشر من رمضان الصناعية', 'خدمات الحماية من الحريق لمدينة العاشر من رمضان الصناعية', 'الحماية من الحريق العاشر من رمضان - سفينكس فاير', 'خدمات السلامة من الحريق المهنية في مدينة العاشر من رمضان الصناعية، مصر', 'الحماية من الحريق العاشر من رمضان، أنظمة مكافحة الحريق المنطقة الصناعية العاشر من رمضان', NOW(), NOW());

-- 2. إضافة بيانات الشهادات في جدول testimonials
INSERT INTO testimonials (client_name, client_position, client_company, client_avatar, rating, project_id, service_id, location_id, is_featured, is_published, sort_order, created_by, created_at, updated_at) VALUES
('Ahmed Hassan', 'Safety Manager', 'Delta Paint Manufacturing', NULL, 5, 1, NULL, NULL, 1, 1, 1, 1, NOW(), NOW()),
('Fatma Nour', 'Operations Director', 'City Center Mall', NULL, 5, 2, NULL, NULL, 1, 1, 2, 1, NOW(), NOW()),
('Mohamed El-Shamy', 'Operations Manager', 'Petrochemical Complex', NULL, 5, 3, NULL, NULL, 0, 1, 3, 1, NOW(), NOW());

-- 3. إضافة ترجمات الشهادات
INSERT INTO testimonial_translations (testimonial_id, language_id, content, created_at, updated_at) VALUES
(1, 1, 'Sphinx Fire handled everything with precision and speed. Their UL/FM certified foam system exceeded our expectations and we passed civil defense inspection on the first try.', NOW(), NOW()),
(1, 2, 'تعامل سفينكس فاير مع كل شيء بدقة وسرعة. نظام الرغوة المعتمد UL/FM تجاوز توقعاتنا واجتزنا فحص الدفاع المدني من أول مرة.', NOW(), NOW()),
(2, 1, 'Professional, fast, and reliable. The advanced alarm and evacuation system they installed has ensured zero incidents since installation.', NOW(), NOW()),
(2, 2, 'احترافية وسرعة وموثوقية. نظام الإنذار والإخلاء المتقدم الذي ركبوه ضمن عدم حدوث أي حوادث منذ التركيب.', NOW(), NOW()),
(3, 1, 'Support always available. The deluge system with SCADA integration has passed all safety audits and provides excellent protection for our chemical facility.', NOW(), NOW()),
(3, 2, 'الدعم متوفر دائمًا. نظام الديلوج مع تكامل SCADA اجتاز جميع اختبارات السلامة ويوفر حماية ممتازة لمنشأتنا الكيميائية.', NOW(), NOW());

-- 4. إضافة بيانات المزايا للصفحة الرئيسية
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) VALUES
(1, 'advantage', 'home-advantage-1', 1, 1, NOW(), NOW()),
(1, 'advantage', 'home-advantage-2', 2, 1, NOW(), NOW()),
(1, 'advantage', 'home-advantage-3', 3, 1, NOW(), NOW()),
(1, 'advantage', 'home-advantage-4', 4, 1, NOW(), NOW());

-- 5. إضافة ترجمات المزايا باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, created_at, updated_at) VALUES
(75, 1, 'UL/FM Certified Equipment', 'International Standards', 'All our firefighting systems use UL/FM certified equipment ensuring the highest safety standards and compliance with international regulations.', NOW(), NOW()),
(76, 1, '24/7 Emergency Response', 'Always Available', 'Our emergency response team is available 24/7 to handle any fire safety emergency with rapid response times.', NOW(), NOW()),
(77, 1, 'Expert Engineering Team', 'Certified Professionals', 'Our team consists of certified fire protection engineers with decades of combined experience in industrial safety.', NOW(), NOW()),
(78, 1, 'Complete Turnkey Solutions', 'From Design to Maintenance', 'We provide complete turnkey solutions from initial design and installation to ongoing maintenance and support.', NOW(), NOW());

-- 6. إضافة ترجمات المزايا باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, created_at, updated_at) VALUES
(75, 2, 'معدات معتمدة UL/FM', 'معايير دولية', 'جميع أنظمة مكافحة الحريق لدينا تستخدم معدات معتمدة UL/FM مما يضمن أعلى معايير السلامة والامتثال للوائح الدولية.', NOW(), NOW()),
(76, 2, 'استجابة طوارئ 24/7', 'متوفرون دائمًا', 'فريق الاستجابة للطوارئ متوفر 24/7 للتعامل مع أي طوارئ سلامة من الحريق بأوقات استجابة سريعة.', NOW(), NOW()),
(77, 2, 'فريق هندسي خبير', 'محترفون معتمدون', 'فريقنا يتكون من مهندسي حماية من الحريق المعتمدين بعقود من الخبرة المشتركة في السلامة الصناعية.', NOW(), NOW()),
(78, 2, 'حلول متكاملة', 'من التصميم إلى الصيانة', 'نقدم حلول متكاملة من التصميم الأولي والتركيب إلى الصيانة المستمرة والدعم.', NOW(), NOW()); 