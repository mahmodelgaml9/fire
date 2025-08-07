-- =============================
-- SEED: Home Hero Sections (index.php)
-- =============================

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