-- =============================
-- SEED: Main Sections Content (EN/AR)
-- =============================

-- الصفحة الرئيسية (Home)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(1, 1, 'hero', 'home-hero', 1, TRUE),
(2, 1, 'overview', 'home-overview', 2, TRUE),
(3, 1, 'advantage', 'home-advantage-1', 3, TRUE),
(4, 1, 'advantage', 'home-advantage-2', 4, TRUE);

-- ترجمات الصفحة الرئيسية
-- hero
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 1, l.id, 'Fire Protection Is Not a Product.', 'It''s a System. It''s a Promise.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get a Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 1, l.id, 'الحماية من الحريق ليست منتجًا', 'إنها نظام. إنها وعد.', 'حلول متكاملة للسلامة من الحريق مصممة لحماية منشأتك بسرعة واحترافية.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- overview
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 2, l.id, 'Why Sphinx Fire?', 'We deliver certified, rapid, and cost-effective fire safety for Egypt''s industrial sector.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 2, l.id, 'لماذا سفينكس فاير؟', 'نقدم حلول سلامة معتمدة وسريعة وفعالة من حيث التكلفة للقطاع الصناعي في مصر.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- advantage 1
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 3, l.id, 'Strategic Location', 'Based inside Sadat City for fastest response.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 3, l.id, 'موقع استراتيجي', 'مقرنا داخل مدينة السادات لأسرع استجابة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- advantage 2
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 4, l.id, 'Certified Equipment', 'UL/FM certified pumps and suppression.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 4, l.id, 'معدات معتمدة', 'مضخات وأنظمة إطفاء معتمدة UL/FM.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- صفحة من نحن (About)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(5, 2, 'hero', 'about-hero', 1, TRUE),
(6, 2, 'overview', 'about-overview', 2, TRUE),
(7, 2, 'about_value', 'about-vision', 3, TRUE),
(8, 2, 'about_value', 'about-mission', 4, TRUE);
-- hero
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 5, l.id, 'About Sphinx Fire', 'Fire Protection Is Not a Product.', 'At Sphinx Fire, we provide safety systems engineered to defend your business from disaster—with speed, expertise, and precision.', 'Explore Our Services'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 5, l.id, 'عن سفينكس فاير', 'الحماية من الحريق ليست منتجًا', 'في سفينكس فاير نقدم أنظمة سلامة مصممة لحماية منشأتك بسرعة واحترافية.', 'تصفح خدماتنا'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- overview
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 6, l.id, 'Built for Industry. Backed by Experience.', 'Leading provider of comprehensive fire safety solutions for industrial facilities.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 6, l.id, 'مصمم للصناعة. مدعوم بالخبرة.', 'المزود الرائد لحلول السلامة من الحريق للمنشآت الصناعية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- vision
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 7, l.id, 'Vision', 'To lead Egypt''s industrial safety transformation with smart, reliable, and integrated systems.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 7, l.id, 'الرؤية', 'قيادة تحول السلامة الصناعية في مصر بأنظمة ذكية وموثوقة ومتكاملة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
-- mission
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 8, l.id, 'Mission', 'Deliver complete fire safety solutions that combine engineering excellence, fast response, and compliance.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 8, l.id, 'الرسالة', 'تقديم حلول سلامة متكاملة تجمع بين الهندسة والسرعة والامتثال.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- صفحة الخدمات (Services)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(9, 3, 'hero', 'services-hero', 1, TRUE),
(10, 3, 'overview', 'services-overview', 2, TRUE);
-- hero
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 9, l.id, 'Complete Fire Safety Services', 'From design to installation to maintenance', 'We deliver comprehensive protection solutions.', 'Download Our Full Catalog'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 9, l.id, 'خدمات سلامة الحريق المتكاملة', 'من التصميم إلى التركيب إلى الصيانة', 'نقدم حلول حماية شاملة.', 'تحميل الكتالوج الكامل'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- overview
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 10, l.id, 'Every system designed, installed, and maintained by certified experts', 'Comprehensive solutions for every aspect of industrial fire protection.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 10, l.id, 'كل نظام يتم تصميمه وتركيبه وصيانته بواسطة خبراء معتمدين', 'حلول متكاملة لكل جانب من جوانب الحماية من الحريق الصناعي.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- صفحة المشاريع (Projects)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(11, 4, 'hero', 'projects-hero', 1, TRUE);
-- hero
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 11, l.id, 'Our Project Portfolio', 'Real Safety. Real Sites. Real Impact.', 'From design to installation to approval - witness our proven track record across Egypt''s industrial facilities.', 'View All Projects'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 11, l.id, 'أعمالنا', 'سلامة حقيقية. مواقع حقيقية. تأثير حقيقي.', 'من التصميم إلى التركيب إلى الاعتماد - شاهد سجلنا المثبت في منشآت مصر الصناعية.', 'عرض كل المشاريع'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- صفحة المدونة (Blog)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(12, 5, 'hero', 'blog-hero', 1, TRUE);
-- hero
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 12, l.id, 'Fire Safety Insights & Expert Guides', 'Expert fire protection guides, updates, and industrial safety knowledge.', 'Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.', 'Read Latest Articles'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 12, l.id, 'دليلك للسلامة من الحريق', 'أدلة ونصائح خبراء الحماية من الحريق والمعرفة الصناعية.', 'ابقَ على اطلاع دائم على اللوائح مع نصائح مهندسي وخبراء الحماية من الحريق المعتمدين.', 'اقرأ أحدث المقالات'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- صفحة التواصل (Contact)
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(13, 6, 'hero', 'contact-hero', 1, TRUE);
-- hero
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 13, l.id, 'Let''s Talk Safety.', 'We''re Just a Call Away.', 'We respond fast because your facility can''t wait. Contact Sphinx Fire today.', 'Book Free Visit'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text)
SELECT 13, l.id, 'دعنا نتحدث عن السلامة.', 'نحن على بعد مكالمة فقط.', 'نستجيب بسرعة لأن منشأتك لا تحتمل الانتظار. تواصل مع سفينكس فاير اليوم.', 'احجز زيارة مجانية'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- انتهى السكريبت الأساسي للسكاشن الرئيسية 