-- =============================
-- SEED: Home Page Sections (index.php)
-- =============================

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