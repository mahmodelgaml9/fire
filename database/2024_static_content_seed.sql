-- Sphinx Fire - Static Content Seed
-- نقل محتوى الصفحات الستاتيك إلى قاعدة البيانات

-- الصفحة الرئيسية (Home)
INSERT INTO pages (id, slug, template, status, is_homepage, created_by) VALUES
(1, 'home', 'default', 'published', TRUE, 1);

INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description) VALUES
(1, 1, 'Sphinx Fire - Leading Fire Safety Solutions Provider in Egypt', 'Sphinx Fire - Leading Fire Safety Solutions Provider in Egypt', 'Sphinx Fire is Egypt\'s premier fire safety company. Expert fire protection systems, consultation, and compliance services for industrial facilities based in Sadat City.'),
(1, 2, 'سفينكس فاير - حلول السلامة من الحريق الرائدة في مصر', 'سفينكس فاير - حلول السلامة من الحريق الرائدة في مصر', 'سفينكس فاير هي الشركة الأولى في مصر لحلول السلامة من الحريق. أنظمة حماية متكاملة واستشارات وخدمات للمصانع والمنشآت الصناعية.');

-- سكشن الهيرو للصفحة الرئيسية
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(1, 1, 'hero', 'home-hero', 1, TRUE);

INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text) VALUES
(1, 1, 'Fire Protection Is Not a Product.', 'It\'s a System. It\'s a Promise.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get a Quote'),
(1, 2, 'الحماية من الحريق ليست منتجًا', 'إنها نظام. إنها وعد.', 'حلول متكاملة للسلامة من الحريق مصممة لحماية منشأتك بسرعة واحترافية.', 'احصل على عرض سعر');

-- صفحة من نحن (About)
INSERT INTO pages (id, slug, template, status, is_homepage, created_by) VALUES
(2, 'about', 'default', 'published', FALSE, 1);

INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description) VALUES
(2, 1, 'About Sphinx Fire', 'About Sphinx Fire', 'Learn about Sphinx Fire\'s mission, vision, and team.'),
(2, 2, 'عن سفينكس فاير', 'عن سفينكس فاير', 'تعرف على رسالة ورؤية وفريق سفينكس فاير.');

-- سكشن الرؤية
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(2, 2, 'values', 'about-vision', 1, TRUE);

INSERT INTO section_translations (section_id, language_id, title, content) VALUES
(2, 1, 'Vision', 'To lead Egypt\'s industrial safety transformation with smart, reliable, and integrated systems.'),
(2, 2, 'الرؤية', 'قيادة تحول السلامة الصناعية في مصر بأنظمة ذكية وموثوقة ومتكاملة.');

-- صفحة الخدمات (Services)
INSERT INTO pages (id, slug, template, status, is_homepage, created_by) VALUES
(3, 'services', 'default', 'published', FALSE, 1);

INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description) VALUES
(3, 1, 'Our Fire Safety Services', 'Our Fire Safety Services', 'Comprehensive solutions for every aspect of industrial fire protection.'),
(3, 2, 'خدماتنا', 'خدماتنا', 'حلول متكاملة لكل جوانب الحماية من الحريق الصناعي.');

-- سكشن الخدمات
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(3, 3, 'services', 'services-list', 1, TRUE);

INSERT INTO section_translations (section_id, language_id, title, content) VALUES
(3, 1, 'Firefighting Systems', 'Complete fire network with UL/FM certified pumps, risers, and suppression for all risk levels.'),
(3, 2, 'أنظمة مكافحة الحريق', 'شبكة مكافحة حريق متكاملة بمضخات وشهادات UL/FM لجميع مستويات الخطورة.');

-- صفحة الحملة (Retargeting Landing)
INSERT INTO campaign_landing_pages (id, slug, campaign_name, campaign_type, template, status, start_date, end_date, conversion_goal, created_by) VALUES
(UUID(), 'fire-safety-assessment-offer', '20% Off Fire Safety Assessment', 'retargeting', 'discount-offer', 'published', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'lead', '00000000-0000-0000-0000-000000000000');

-- ترجمة الحملة
INSERT INTO campaign_landing_page_translations (id, page_id, language_id, title, subtitle, hero_content, offer_headline, offer_description, cta_primary, form_title, success_message, meta_title, meta_description) VALUES
(UUID(), (SELECT id FROM campaign_landing_pages WHERE slug='fire-safety-assessment-offer'), 1, 'Special Offer: 20% Off Fire Safety Assessment', 'Protect Your Facility. Ensure Compliance. Save Money.', 'Get a comprehensive fire safety assessment from certified engineers.', 'Save 20% on Professional Fire Safety Assessment', 'Limited time offer: Act now and save 20% on our comprehensive fire safety assessment.', 'CLAIM YOUR 20% DISCOUNT NOW', 'Book Your Discounted Assessment', 'Thank you! Your discount has been claimed. Our team will contact you within 4 hours to schedule your assessment.', 'Special Offer: 20% Off Fire Safety Assessment | Sphinx Fire', 'Limited time offer: 20% off professional fire safety assessment for industrial facilities. Certified engineers, same-day response, and compliance guarantee.'),
(UUID(), (SELECT id FROM campaign_landing_pages WHERE slug='fire-safety-assessment-offer'), 2, 'عرض خاص: خصم 20% على تقييم السلامة من الحريق', 'احمِ منشأتك. ضمان الامتثال. وفر المال.', 'احصل على تقييم شامل للسلامة من الحريق من مهندسين معتمدين.', 'وفر 20% على تقييم السلامة من الحريق الاحترافي', 'عرض لفترة محدودة: اغتنم الفرصة الآن ووفر 20% على تقييم السلامة الشامل.', 'احصل على خصم 20% الآن', 'احجز تقييمك المخفض', 'شكرًا لك! تم المطالبة بالخصم الخاص بك. سيتصل بك فريقنا خلال 4 ساعات لتحديد موعد التقييم.', 'عرض خاص: خصم 20% على تقييم السلامة من الحريق | سفينكس فاير', 'عرض لفترة محدودة: خصم 20% على تقييم السلامة من الحريق الاحترافي للمنشآت الصناعية. مهندسون معتمدون واستجابة في نفس اليوم وضمان الامتثال.');

-- ====== SERVICES SAMPLE DATA ======
INSERT INTO service_categories (id, slug, icon, color, sort_order, is_active) VALUES
(1, 'firefighting-systems', 'fas fa-fire-extinguisher', '#DC2626', 1, TRUE),
(2, 'fire-alarms', 'fas fa-bell', '#DC2626', 2, TRUE);

INSERT INTO service_category_translations (category_id, language_id, name, description) VALUES
(1, 1, 'Firefighting Systems', 'Complete firefighting systems with UL/FM certified equipment'),
(1, 2, 'أنظمة مكافحة الحريق', 'أنظمة مكافحة حريق متكاملة بمعدات معتمدة UL/FM'),
(2, 1, 'Fire Alarm Systems', 'Smart fire detection and alarm systems'),
(2, 2, 'أنظمة إنذار الحريق', 'أنظمة ذكية لكشف وإنذار الحريق');

INSERT INTO services (id, category_id, slug, icon, featured_image, price_range, duration, is_featured, is_active, created_by) VALUES
(1, 1, 'fire-network', 'fas fa-fire-extinguisher', 'fire-network.jpg', 'EGP 50,000-200,000', '2-4 weeks', TRUE, TRUE, 1),
(2, 2, 'alarm-system', 'fas fa-bell', 'alarm-system.jpg', 'EGP 20,000-80,000', '1-2 weeks', FALSE, TRUE, 1);

INSERT INTO service_translations (service_id, language_id, name, short_description, full_description, features) VALUES
(1, 1, 'Firefighting Network', 'Complete network with certified pumps and risers', 'Full design and installation of firefighting networks for industrial facilities.', '["UL/FM certified pumps","Steel risers","Sprinkler systems"]'),
(1, 2, 'شبكة مكافحة الحريق', 'شبكة متكاملة بمضخات وشهادات معتمدة', 'تصميم وتنفيذ كامل لشبكات مكافحة الحريق للمنشآت الصناعية.', '["مضخات معتمدة UL/FM","رايزرات حديد","أنظمة رش آلي"]'),
(2, 1, 'Fire Alarm System', 'Smart detection and notification', 'Advanced fire alarm systems with SCADA integration.', '["Smoke detectors","Control panels","SCADA ready"]'),
(2, 2, 'نظام إنذار الحريق', 'كشف ذكي وتنبيه فوري', 'أنظمة إنذار متقدمة مع تكامل SCADA.', '["كواشف دخان","لوحات تحكم","جاهز للتكامل مع SCADA"]');

-- ====== SERVICES - مزيد من البيانات ======
INSERT INTO service_categories (id, slug, icon, color, sort_order, is_active) VALUES
(3, 'ppe-equipment', 'fas fa-hard-hat', '#DC2626', 3, TRUE),
(4, 'consulting', 'fas fa-clipboard-check', '#DC2626', 4, TRUE),
(5, 'maintenance', 'fas fa-tools', '#DC2626', 5, TRUE);

INSERT INTO service_category_translations (category_id, language_id, name, description) VALUES
(3, 1, 'PPE Equipment', 'Personal protective equipment for fire safety teams'),
(3, 2, 'معدات الوقاية الشخصية', 'معدات وقاية شخصية لفرق مكافحة الحريق'),
(4, 1, 'Consulting', 'Fire safety consulting and compliance guidance'),
(4, 2, 'استشارات', 'استشارات سلامة الحريق ودعم الامتثال'),
(5, 1, 'Maintenance', 'Preventive and emergency maintenance services'),
(5, 2, 'صيانة', 'خدمات صيانة وقائية وطوارئ');

INSERT INTO services (id, category_id, slug, icon, featured_image, price_range, duration, is_featured, is_active, created_by) VALUES
(3, 3, 'ppe-suits', 'fas fa-hard-hat', 'ppe-suits.jpg', 'EGP 5,000-30,000', '1 week', FALSE, TRUE, 1),
(4, 4, 'risk-consulting', 'fas fa-clipboard-check', 'risk-consulting.jpg', 'EGP 10,000-50,000', '1-2 weeks', FALSE, TRUE, 1),
(5, 5, 'annual-maintenance', 'fas fa-tools', 'annual-maintenance.jpg', 'EGP 8,000-40,000', 'Annual', TRUE, TRUE, 1);

INSERT INTO service_translations (service_id, language_id, name, short_description, full_description, features, benefits, specifications) VALUES
(3, 1, 'PPE Suits', 'Certified fire-resistant suits and helmets', 'Supply of certified PPE suits, helmets, gloves, and boots for fire teams.', '["Fire-resistant suits","Helmets","Gloves","Boots"]', '["Certified to EN469","Comfort fit","High durability"]', '{"material":"Nomex","certification":"EN469"}'),
(3, 2, 'بدل وقاية شخصية', 'بدل وخوذات مقاومة للحريق معتمدة', 'توريد بدل وقاية شخصية معتمدة، خوذات، قفازات، وأحذية لفرق الحريق.', '["بدل مقاومة للحريق","خوذات","قفازات","أحذية"]', '["معتمدة EN469","راحة عالية","متانة"]', '{"المادة":"نوميكس","الشهادة":"EN469"}'),
(4, 1, 'Risk Consulting', 'Site risk assessment and compliance audit', 'Comprehensive risk assessment and compliance audit for industrial facilities.', '["On-site assessment","Compliance report","Action plan"]', '["Reduce risk","Ensure compliance"]', NULL),
(4, 2, 'استشارات المخاطر', 'تقييم مخاطر وفحص امتثال', 'تقييم شامل للمخاطر وفحص امتثال للمنشآت الصناعية.', '["تقييم ميداني","تقرير امتثال","خطة عمل"]', '["تقليل المخاطر","ضمان الامتثال"]', NULL),
(5, 1, 'Annual Maintenance', 'Preventive and emergency maintenance', 'Annual contract for preventive and emergency maintenance of fire systems.', '["Scheduled visits","24/7 emergency support","Spare parts included"]', '["Reduce downtime","Priority support"]', NULL),
(5, 2, 'صيانة سنوية', 'صيانة وقائية وطوارئ', 'عقد سنوي لصيانة وقائية وطوارئ لأنظمة الحريق.', '["زيارات مجدولة","دعم طوارئ 24/7","قطع غيار مشمولة"]', '["تقليل الأعطال","دعم أولوية"]', NULL);

-- ====== PROJECTS SAMPLE DATA ======
INSERT INTO project_categories (id, slug, color, sort_order, is_active) VALUES
(1, 'industrial', '#DC2626', 1, TRUE);

INSERT INTO project_category_translations (category_id, language_id, name, description) VALUES
(1, 1, 'Industrial', 'Industrial fire safety projects'),
(1, 2, 'صناعي', 'مشاريع حماية صناعية من الحريق');

INSERT INTO projects (id, category_id, slug, client_name, location, project_date, status, featured_image, is_featured, is_published, created_by) VALUES
(1, 1, 'delta-paint', 'Delta Paint Manufacturing', 'Sadat City', '2024-01-15', 'completed', 'delta-paint.jpg', TRUE, TRUE, 1);

INSERT INTO project_translations (project_id, language_id, title, subtitle, description, challenge, solution, results, testimonial, testimonial_author, testimonial_position) VALUES
(1, 1, 'Delta Paint Manufacturing', 'Complete Fire Network + Foam Suppression', 'UL/FM certified firefighting system with specialized foam suppression for paint storage areas.', 'Paint storage required special foam system.', 'Installed UL/FM foam system.', 'Passed civil defense inspection on first try.', 'Sphinx Fire handled everything with precision and speed.', 'Ahmed Hassan', 'Safety Manager'),
(1, 2, 'مصنع دلتا للدهانات', 'شبكة مكافحة كاملة + نظام رغوي', 'نظام مكافحة حريق معتمد UL/FM مع نظام رغوي خاص لمخازن الدهانات.', 'مخزن الدهانات تطلب نظام رغوي خاص.', 'تم تركيب نظام رغوي معتمد UL/FM.', 'اجتياز فحص الدفاع المدني من أول مرة.', 'سفينكس فاير أنجزت كل شيء بدقة وسرعة.', 'أحمد حسن', 'مدير السلامة');

-- ====== PROJECTS - مزيد من البيانات ======
INSERT INTO project_categories (id, slug, color, sort_order, is_active) VALUES
(2, 'commercial', '#EF4444', 2, TRUE),
(3, 'chemical', '#F59E42', 3, TRUE);

INSERT INTO project_category_translations (category_id, language_id, name, description) VALUES
(2, 1, 'Commercial', 'Commercial and retail fire safety projects'),
(2, 2, 'تجاري', 'مشاريع حماية تجارية ومولات'),
(3, 1, 'Chemical', 'Chemical plant fire safety projects'),
(3, 2, 'كيميائي', 'مشاريع حماية مصانع كيميائية');

INSERT INTO projects (id, category_id, slug, client_name, location, project_date, status, featured_image, is_featured, is_published, created_by) VALUES
(2, 2, 'city-center-mall', 'City Center Mall', 'Cairo', '2023-09-10', 'completed', 'city-center-mall.jpg', FALSE, TRUE, 1),
(3, 3, 'petrochem-suez', 'Petrochemical Complex', 'Suez', '2024-03-05', 'completed', 'petrochem-suez.jpg', TRUE, TRUE, 1);

INSERT INTO project_translations (project_id, language_id, title, subtitle, description, challenge, solution, results, testimonial, testimonial_author, testimonial_position) VALUES
(2, 1, 'City Center Mall - Cairo', 'Comprehensive Fire Safety', 'Complete fire protection system for 5-story shopping complex with evacuation systems.', 'Complex evacuation planning for large crowds.', 'Installed advanced alarm and evacuation system.', 'Zero incidents since installation.', 'Professional, fast, and reliable.', 'Fatma Nour', 'Operations Director'),
(2, 2, 'سيتي سنتر مول - القاهرة', 'حماية شاملة للمول', 'نظام حماية متكامل لمجمع تسوق من 5 طوابق مع أنظمة إخلاء.', 'تخطيط إخلاء معقد لأعداد كبيرة.', 'تركيب نظام إنذار وإخلاء متقدم.', 'لا حوادث منذ التركيب.', 'احترافية وسرعة وموثوقية.', 'فاطمة نور', 'مدير العمليات'),
(3, 1, 'Petrochemical Complex - Suez', 'High-Risk Deluge System', 'Advanced deluge system with SCADA monitoring for chemical processing facility.', 'High-risk chemical storage required special deluge system.', 'Installed deluge system with SCADA integration.', 'Passed all safety audits.', 'Support always available.', 'Mohamed El-Shamy', 'Operations Manager'),
(3, 2, 'مجمع بتروكيمياويات - السويس', 'نظام ديلوج عالي الخطورة', 'نظام ديلوج متقدم مع مراقبة SCADA لمصنع كيميائي.', 'تخزين كيميائي عالي الخطورة تطلب نظام ديلوج خاص.', 'تركيب نظام ديلوج مع تكامل SCADA.', 'اجتياز جميع اختبارات السلامة.', 'الدعم متوفر دائمًا.', 'محمد الشامي', 'مدير العمليات');

-- ====== BLOG SAMPLE DATA ======
INSERT INTO blog_categories (id, slug, color, sort_order, is_active) VALUES
(1, 'compliance', '#DC2626', 1, TRUE);

INSERT INTO blog_category_translations (category_id, language_id, name, description) VALUES
(1, 1, 'Compliance', 'Fire safety compliance guides and regulations'),
(1, 2, 'الامتثال', 'أدلة الامتثال للسلامة من الحريق واللوائح');

INSERT INTO blog_posts (id, category_id, slug, author_id, featured_image, reading_time, status, is_featured, published_at, created_at) VALUES
(1, 1, 'civil-defense-inspection', 1, 'civil-defense.jpg', 6, 'published', TRUE, NOW(), NOW());

INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description) VALUES
(1, 1, 'How to Pass Civil Defense Inspection in Egypt', 'Step-by-step guide to passing fire safety inspection.', 'Full article content in English...', '["civil defense","inspection","compliance"]', 'How to Pass Civil Defense Inspection in Egypt', 'Expert guide to passing fire safety inspection in Egypt.'),
(1, 2, 'كيف تجتاز فحص الدفاع المدني في مصر', 'دليل خطوة بخطوة لاجتياز فحص السلامة من الحريق.', 'المقال الكامل باللغة العربية...', '["الدفاع المدني","فحص","امتثال"]', 'كيف تجتاز فحص الدفاع المدني في مصر', 'دليل الخبراء لاجتياز فحص السلامة من الحريق في مصر.');

-- ====== BLOG - مزيد من البيانات ======
INSERT INTO blog_categories (id, slug, color, sort_order, is_active) VALUES
(2, 'safety-tips', '#10B981', 2, TRUE),
(3, 'case-studies', '#6366F1', 3, TRUE);

INSERT INTO blog_category_translations (category_id, language_id, name, description) VALUES
(2, 1, 'Safety Tips', 'Practical fire safety tips for businesses'),
(2, 2, 'نصائح السلامة', 'نصائح عملية للسلامة من الحريق للأعمال'),
(3, 1, 'Case Studies', 'Real-world fire safety case studies'),
(3, 2, 'دراسات حالة', 'دراسات حالة واقعية في السلامة من الحريق');

INSERT INTO blog_posts (id, category_id, slug, author_id, featured_image, reading_time, status, is_featured, published_at, created_at) VALUES
(2, 2, 'top-10-fire-safety-tips', 1, 'fire-tips.jpg', 4, 'published', FALSE, NOW(), NOW()),
(3, 3, 'case-study-warehouse', 1, 'warehouse-case.jpg', 7, 'published', FALSE, NOW(), NOW());

INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description) VALUES
(2, 1, 'Top 10 Fire Safety Tips for Factories', 'Essential tips to prevent fire incidents in factories.', 'Full article with 10 tips in English...', '["safety","tips","factory"]', 'Top 10 Fire Safety Tips for Factories', 'Essential fire safety tips for industrial facilities.'),
(2, 2, 'أفضل 10 نصائح للسلامة من الحريق للمصانع', 'نصائح أساسية لمنع الحريق في المصانع.', 'المقال الكامل مع 10 نصائح باللغة العربية...', '["سلامة","نصائح","مصنع"]', 'أفضل 10 نصائح للسلامة من الحريق للمصانع', 'نصائح أساسية للسلامة من الحريق للمنشآت الصناعية.'),
(3, 1, 'Case Study: Warehouse Fire Prevention', 'How a warehouse avoided disaster with proper safety measures.', 'Full case study in English...', '["case study","warehouse","prevention"]', 'Case Study: Warehouse Fire Prevention', 'How a warehouse avoided fire disaster.'),
(3, 2, 'دراسة حالة: منع حريق في مخزن', 'كيف تجنب مخزن كارثة حريق بإجراءات السلامة.', 'دراسة حالة كاملة باللغة العربية...', '["دراسة حالة","مخزن","منع"]', 'دراسة حالة: منع حريق في مخزن', 'كيف تجنب مخزن كارثة حريق.');

-- ملاحظة: IDs متسلسلة ويمكن التوسعة لاحقًا 