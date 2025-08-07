-- =============================
-- COMPLETE SEED: Home Page (index.php)
-- =============================

-- ===== HERO SECTIONS =====
-- Hero Slide 1
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(1, 1, 'hero', 'home-hero-slide-1', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 1, l.id, 'Fire Protection Is Not a Product.', 'It''s a System. It''s a Promise.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get a Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 1, l.id, 'الحماية من الحريق ليست منتجًا', 'إنها نظام. إنها وعد.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 2
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(2, 1, 'hero', 'home-hero-slide-2', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 2, l.id, 'Early Detection Saves Lives.', 'Smart Alarm Systems That Never Sleep.', 'Advanced detection technology with SCADA integration for industrial facilities. 24/7 monitoring and instant alerts.', 'Request Demo'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 2, l.id, 'الكشف المبكر ينقذ الأرواح', 'أنظمة إنذار ذكية لا تنام أبدًا', 'تقنية كشف متقدمة مع تكامل SCADA للمنشآت الصناعية. مراقبة 24/7 وتنبيهات فورية.', 'طلب عرض توضيحي'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 3
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(3, 1, 'hero', 'home-hero-slide-3', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 3, l.id, 'Protect Your Most Valuable Asset.', 'Your People.', 'Complete PPE solutions for industrial fire safety teams. Professional-grade equipment with certified training.', 'Training Programs'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 3, l.id, 'احمِ أثمن أصولك', 'أشخاصك', 'حلول معدات حماية شخصية متكاملة لفرق السلامة الصناعية. معدات احترافية مع تدريب معتمد.', 'برامج التدريب'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- ===== MAIN SECTIONS =====
-- Key Advantages Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(4, 1, 'advantages', 'home-advantages', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 4, l.id, 'Industrial Fire Safety Experts', 'Five key advantages that make us your ideal safety partner', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 4, l.id, 'خبراء السلامة من الحريق الصناعية', 'خمس مزايا رئيسية تجعلنا شريكك المثالي في السلامة', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Services Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(5, 1, 'services', 'home-services', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 5, l.id, 'Our Fire Safety Services', 'Comprehensive solutions for every aspect of industrial fire protection', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'View All Services'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 5, l.id, 'خدماتنا في السلامة من الحريق', 'حلول شاملة لكل جانب من جوانب الحماية من الحريق الصناعية', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'عرض جميع الخدمات'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Featured Projects Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(6, 1, 'projects', 'home-projects', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 6, l.id, 'Featured Projects', 'Real implementations, real impact, real safety', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'View All Projects'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 6, l.id, 'المشاريع المميزة', 'تنفيذات حقيقية، تأثير حقيقي، سلامة حقيقية', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'عرض جميع المشاريع'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Testimonials Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(7, 1, 'testimonials', 'home-testimonials', 5, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 7, l.id, 'What Our Clients Say', 'Real feedback from industrial facilities we''ve protected', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Request References'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 7, l.id, 'ماذا يقول عملاؤنا', 'تعليقات حقيقية من المنشآت الصناعية التي حمايتها', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'طلب مراجع'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Industrial Zones Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(8, 1, 'locations', 'home-locations', 6, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 8, l.id, 'Industrial Zones We Serve', 'Local expertise in Egypt''s major industrial cities', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'View All Locations'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 8, l.id, 'المناطق الصناعية التي نخدمها', 'خبرة محلية في المدن الصناعية الرئيسية في مصر', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'عرض جميع المواقع'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Final CTA Section
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(9, 1, 'cta', 'home-final-cta', 7, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 9, l.id, 'Let''s build a safer tomorrow—starting with your facility today.', 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety excellence.', 'Free consultation • Expert assessment • Response within 24 hours', 'Request Free Consultation'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 9, l.id, 'دعنا نبني غدًا أكثر أمانًا — بدءًا بمنشأتك اليوم.', 'انضم إلى مئات المنشآت التي تثق في Sphinx Fire للتميز الكامل في السلامة من الحريق.', 'استشارة مجانية • تقييم خبير • رد خلال 24 ساعة', 'طلب استشارة مجانية'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- ===== SECTION ITEMS =====
-- Advantages Items (6 items)
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(1, 4, 'advantage', 'strategic-location', 1, TRUE),
(2, 4, 'advantage', 'complete-integration', 2, TRUE),
(3, 4, 'advantage', 'expert-leadership', 3, TRUE),
(4, 4, 'advantage', 'responsive-team', 4, TRUE),
(5, 4, 'advantage', 'compliance-guaranteed', 5, TRUE),
(6, 4, 'advantage', 'ul-fm-certified', 6, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Services Items (6 items)
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(7, 5, 'service', 'firefighting-systems', 1, TRUE),
(8, 5, 'service', 'fire-alarm-systems', 2, TRUE),
(9, 5, 'service', 'fire-extinguishers', 3, TRUE),
(10, 5, 'service', 'ppe-equipment', 4, TRUE),
(11, 5, 'service', 'safety-consultation', 5, TRUE),
(12, 5, 'service', 'maintenance-services', 6, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Projects Items (3 items)
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(13, 6, 'project', 'delta-paint', 1, TRUE),
(14, 6, 'project', 'petrochemical-complex', 2, TRUE),
(15, 6, 'project', 'shopping-mall', 3, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Testimonials Items (2 items)
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(16, 7, 'testimonial', 'ahmed-hassan', 1, TRUE),
(17, 7, 'testimonial', 'fatma-nour', 2, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Locations Items (3 items)
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(18, 8, 'location', 'sadat-city', 1, TRUE),
(19, 8, 'location', 'october-city', 2, TRUE),
(20, 8, 'location', 'ramadan-city', 3, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active); 