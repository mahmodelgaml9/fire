-- =============================
-- SEED: Home Page Sections (index.html)
-- =============================

-- صفحة Home (id=1)
INSERT INTO pages (id, slug, name) VALUES (1, 'home', 'Home') 
ON DUPLICATE KEY UPDATE slug=VALUES(slug), name=VALUES(name);

-- =============================
-- HERO SECTION (3 slides)
-- =============================

-- Hero Slide 1: Firefighting Systems
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(1, 1, 'hero', 'home-hero-slide-1', 1, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero Slide 1 - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 1, l.id, 'Fire Protection Is Not a Product.', 'It''s a System. It''s a Promise.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get a Quote', NULL
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 1 - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 1, l.id, 'الحماية من الحريق ليست منتجًا', 'إنها نظام. إنها وعد.', 'حلول سلامة من الحريق متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر', NULL
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 2: Fire Alarm Systems
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(2, 1, 'hero', 'home-hero-slide-2', 2, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero Slide 2 - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 2, l.id, 'Early Detection Saves Lives.', 'Smart Alarm Systems That Never Sleep.', 'Advanced detection technology with SCADA integration for industrial facilities. 24/7 monitoring and instant alerts.', 'Request Demo', NULL
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 2 - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 2, l.id, 'الكشف المبكر ينقذ الأرواح', 'أنظمة إنذار ذكية لا تنام أبدًا', 'تقنية كشف متقدمة مع تكامل SCADA للمنشآت الصناعية. مراقبة 24/7 وتنبيهات فورية.', 'طلب عرض توضيحي', NULL
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 3: PPE Equipment
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(3, 1, 'hero', 'home-hero-slide-3', 3, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Hero Slide 3 - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 3, l.id, 'Protect Your Most Valuable Asset.', 'Your People.', 'Complete PPE solutions for industrial fire safety teams. Professional-grade equipment with certified training.', 'Training Programs', NULL
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Hero Slide 3 - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 3, l.id, 'احمِ أثمن أصولك', 'أشخاصك', 'حلول PPE متكاملة لفرق السلامة من الحريق الصناعية. معدات احترافية مع تدريب معتمد.', 'برامج التدريب', NULL
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- =============================
-- ADVANTAGES SECTION
-- =============================

-- Advantages Header
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(4, 1, 'advantage', 'home-advantages-header', 4, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantages Header - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 4, l.id, 'Industrial Fire Safety Experts', 'Five key advantages that make us your ideal safety partner', '🔥 WHY CHOOSE SPHINX FIRE'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Advantages Header - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 4, l.id, 'خبراء السلامة من الحريق الصناعية', 'خمس مزايا رئيسية تجعلنا شريكك المثالي للسلامة', '🔥 لماذا تختار سفينكس فاير'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Advantage 1: Strategic Location
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(5, 1, 'advantage', 'home-advantage-location', 5, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage 1 - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 5, l.id, 'Strategically Located', 'Based inside Sadat City, we''re minutes away from major industrial facilities, ensuring rapid response times and cost-effective service delivery.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 1 - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 5, l.id, 'موقع استراتيجي', 'مقيمون داخل مدينة السادات، على بعد دقائق من المنشآت الصناعية الرئيسية، مما يضمن أوقات استجابة سريعة وتقديم خدمات فعالة من حيث التكلفة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 2: Complete Integration
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(6, 1, 'advantage', 'home-advantage-integration', 6, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage 2 - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 6, l.id, 'Complete System Integration', 'SCADA-ready systems that integrate seamlessly with your existing building management and industrial control systems.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 2 - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 6, l.id, 'تكامل النظام الكامل', 'أنظمة جاهزة لـ SCADA تتكامل بسلاسة مع أنظمة إدارة المباني وأنظمة التحكم الصناعية الموجودة.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 3: Expert Leadership
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(7, 1, 'advantage', 'home-advantage-leadership', 7, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage 3 - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 7, l.id, 'Expert Leadership', 'Led by certified consultants with 10+ years in industrial fire safety, bringing deep expertise to every project.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 3 - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 7, l.id, 'قيادة خبيرة', 'يقودنا مستشارون معتمدون بخبرة 10+ سنوات في السلامة من الحريق الصناعية، يجلبون خبرة عميقة لكل مشروع.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 4: Responsive Team
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(8, 1, 'advantage', 'home-advantage-responsive', 8, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage 4 - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 8, l.id, 'Responsive Team', 'Fast site visits, direct contact with decision makers, and emergency support when you need it most.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 4 - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 8, l.id, 'فريق متجاوب', 'زيارات موقع سريعة، اتصال مباشر مع صانعي القرار، ودعم طوارئ عندما تحتاجه أكثر.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 5: Compliance Guaranteed
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(9, 1, 'advantage', 'home-advantage-compliance', 9, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage 5 - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 9, l.id, 'Compliance Guaranteed', 'Full compliance with NFPA, OSHA, and Egyptian Civil Defense regulations, with documentation and certification support.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 5 - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 9, l.id, 'الامتثال مضمون', 'امتثال كامل لمعايير NFPA و OSHA ولوائح الدفاع المدني المصري، مع دعم التوثيق والاعتماد.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 6: UL/FM Certified
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(10, 1, 'advantage', 'home-advantage-certified', 10, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Advantage 6 - English
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 10, l.id, 'UL/FM Certified Equipment', 'Only the highest quality, internationally certified equipment that meets global safety standards and local requirements.'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- Advantage 6 - Arabic
INSERT INTO section_translations (section_id, language_id, title, content)
SELECT 10, l.id, 'معدات معتمدة UL/FM', 'فقط أعلى جودة من المعدات المعتمدة دوليًا التي تلبي معايير السلامة العالمية والمتطلبات المحلية.'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content);

-- =============================
-- SERVICES SECTION
-- =============================

-- Services Header
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(11, 1, 'services', 'home-services-header', 11, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Services Header - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 11, l.id, 'Our Fire Safety Services', 'Comprehensive solutions for every aspect of industrial fire protection', '🛡️ COMPLETE PROTECTION'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Services Header - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 11, l.id, 'خدمات السلامة من الحريق', 'حلول شاملة لكل جانب من جوانب الحماية من الحريق الصناعي', '🛡️ حماية متكاملة'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- PROJECTS SECTION
-- =============================

-- Projects Header
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(12, 1, 'projects', 'home-projects-header', 12, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Projects Header - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 12, l.id, 'Featured Projects', 'Real implementations, real impact, real safety', '✅ PROVEN RESULTS'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Projects Header - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 12, l.id, 'مشاريع مميزة', 'تنفيذات حقيقية، تأثير حقيقي، سلامة حقيقية', '✅ نتائج مثبتة'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- TESTIMONIALS SECTION
-- =============================

-- Testimonials Header
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(13, 1, 'testimonials', 'home-testimonials-header', 13, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Testimonials Header - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 13, l.id, 'What Our Clients Say', 'Real feedback from industrial facilities we''ve protected', '⭐ CLIENT TESTIMONIALS'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Testimonials Header - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 13, l.id, 'ماذا يقول عملاؤنا', 'تعليقات حقيقية من المنشآت الصناعية التي حماها', '⭐ آراء العملاء'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- LOCATIONS SECTION
-- =============================

-- Locations Header
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(14, 1, 'locations', 'home-locations-header', 14, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Locations Header - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 14, l.id, 'Industrial Zones We Serve', 'Local expertise in Egypt''s major industrial cities', '🏭 INDUSTRIAL COVERAGE'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- Locations Header - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content)
SELECT 14, l.id, 'المناطق الصناعية التي نخدمها', 'خبرة محلية في المدن الصناعية الرئيسية في مصر', '🏭 تغطية صناعية'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content);

-- =============================
-- CTA SECTION
-- =============================

-- Final CTA
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active) VALUES
(15, 1, 'cta', 'home-final-cta', 15, TRUE)
ON DUPLICATE KEY UPDATE page_id=VALUES(page_id), section_type=VALUES(section_type), section_key=VALUES(section_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);

-- Final CTA - English
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 15, l.id, 'Let''s build a safer tomorrow—starting with your facility today.', 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety excellence.', 'Free consultation • Expert assessment • Response within 24 hours', 'Request Free Consultation', NULL
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Final CTA - Arabic
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url)
SELECT 15, l.id, 'دعنا نبني غدًا أكثر أمانًا — بدءًا بمنشأتك اليوم.', 'انضم إلى مئات المنشآت التي تثق في سفينكس فاير للتميز في السلامة من الحريق.', 'استشارة مجانية • تقييم خبير • استجابة خلال 24 ساعة', 'طلب استشارة مجانية', NULL
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text); 