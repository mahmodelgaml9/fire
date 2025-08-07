-- =============================
-- SEED: Home Page Section Items (index.php)
-- =============================

-- ===== ADVANTAGES SECTION ITEMS =====
-- Advantage 1: Strategic Location
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(1, 4, 'advantage', 'strategic-location', 1, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 1, l.id, 'Strategically Located', '30-Minute Response Time', 'Based inside Sadat City, we''re minutes away from major industrial facilities, ensuring rapid response times and cost-effective service delivery.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 1, l.id, 'موقع استراتيجي', 'وقت استجابة 30 دقيقة', 'مقيمون داخل مدينة السادات، على بعد دقائق من المنشآت الصناعية الرئيسية، مما يضمن أوقات استجابة سريعة وتقديم خدمة فعالة من حيث التكلفة.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Advantage 2: Complete Integration
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(2, 4, 'advantage', 'complete-integration', 2, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 2, l.id, 'Complete System Integration', '100% SCADA Compatible', 'SCADA-ready systems that integrate seamlessly with your existing building management and industrial control systems.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 2, l.id, 'تكامل نظام كامل', 'متوافق 100% مع SCADA', 'أنظمة جاهزة لـ SCADA تتكامل بسلاسة مع أنظمة إدارة المباني وأنظمة التحكم الصناعية الموجودة.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Advantage 3: Expert Leadership
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(3, 4, 'advantage', 'expert-leadership', 3, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 3, l.id, 'Expert Leadership', 'NFPA Certified Team', 'Led by certified consultants with 10+ years in industrial fire safety, bringing deep expertise to every project.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 3, l.id, 'قيادة خبيرة', 'فريق معتمد من NFPA', 'يقودنا مستشارون معتمدون بخبرة 10+ سنوات في السلامة من الحريق الصناعية، يجلبون خبرة عميقة لكل مشروع.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Advantage 4: Responsive Team
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(4, 4, 'advantage', 'responsive-team', 4, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 4, l.id, 'Responsive Team', '24-Hour Support', 'Fast site visits, direct contact with decision makers, and emergency support when you need it most.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 4, l.id, 'فريق متجاوب', 'دعم 24 ساعة', 'زيارات موقع سريعة، اتصال مباشر مع صانعي القرار، ودعم طوارئ عندما تحتاجه أكثر.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Advantage 5: Compliance Guaranteed
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(5, 4, 'advantage', 'compliance-guaranteed', 5, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 5, l.id, 'Compliance Guaranteed', '100% Approval Rate', 'Full compliance with NFPA, OSHA, and Egyptian Civil Defense regulations, with documentation and certification support.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 5, l.id, 'الامتثال مضمون', 'معدل موافقة 100%', 'امتثال كامل لمعايير NFPA و OSHA وأنظمة الدفاع المدني المصري، مع دعم التوثيق والاعتماد.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Advantage 6: UL/FM Certified
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(6, 4, 'advantage', 'ul-fm-certified', 6, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 6, l.id, 'UL/FM Certified Equipment', 'Certified Components', 'Only the highest quality, internationally certified equipment that meets global safety standards and local requirements.', 'Learn More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 6, l.id, 'معدات معتمدة UL/FM', 'مكونات معتمدة', 'فقط أعلى جودة من المعدات المعتمدة دوليًا التي تلبي معايير السلامة العالمية والمتطلبات المحلية.', 'اعرف المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- ===== SERVICES SECTION ITEMS =====
-- Service 1: Firefighting Systems
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(7, 5, 'service', 'firefighting-systems', 1, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 7, l.id, 'Firefighting Systems', 'Complete fire network with UL/FM certified pumps, risers, and suppression for all risk levels.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 7, l.id, 'أنظمة مكافحة الحريق', 'شبكة حريق كاملة مع مضخات ومواسير وقمع معتمدة UL/FM لجميع مستويات المخاطر.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Service 2: Fire Alarm Systems
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(8, 5, 'service', 'fire-alarm-systems', 2, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 8, l.id, 'Fire Alarm Systems', 'Smart detection and notification systems with SCADA integration for industrial facilities.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 8, l.id, 'أنظمة إنذار الحريق', 'أنظمة كشف وإشعار ذكية مع تكامل SCADA للمنشآت الصناعية.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Service 3: Fire Extinguishers
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(9, 5, 'service', 'fire-extinguishers', 3, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 9, l.id, 'Fire Extinguishers', 'Complete extinguisher library: CO2, foam, powder, wet chemical, and specialized agents.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 9, l.id, 'طفايات الحريق', 'مكتبة طفايات كاملة: CO2، رغوة، مسحوق، كيماويات رطبة، وعوامل متخصصة.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Service 4: PPE Equipment
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(10, 5, 'service', 'ppe-equipment', 4, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 10, l.id, 'PPE Equipment', 'Professional-grade protective equipment: suits, helmets, breathing apparatus, and emergency gear.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 10, l.id, 'معدات الحماية الشخصية', 'معدات حماية احترافية: بدلات، خوذ، أجهزة تنفس، ومعدات طوارئ.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Service 5: Safety Consultation
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(11, 5, 'service', 'safety-consultation', 5, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 11, l.id, 'Safety Consultation', 'Expert risk assessment, compliance guidance, and certification assistance for your facility.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 11, l.id, 'استشارات السلامة', 'تقييم مخاطر خبير، إرشادات الامتثال، ومساعدة الاعتماد لمنشأتك.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Service 6: Maintenance Services
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(12, 5, 'service', 'maintenance-services', 6, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 12, l.id, 'Maintenance Services', 'Regular inspections, preventive maintenance, emergency repairs, and system upgrades.', 'Complete fire safety solutions engineered to defend your business from disaster—with speed, expertise, and precision.', 'Get Quote'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 12, l.id, 'خدمات الصيانة', 'فحوصات دورية، صيانة وقائية، إصلاحات طوارئ، وترقيات النظام.', 'حلول سلامة متكاملة مصممة لحماية منشأتك من الكوارث بسرعة واحترافية ودقة.', 'احصل على عرض سعر'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- ===== PROJECTS SECTION ITEMS =====
-- Project 1: Delta Paint
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(13, 6, 'project', 'delta-paint', 1, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 13, l.id, 'Delta Paint Manufacturing', 'Complete Fire Network + Foam Suppression', 'UL/FM certified firefighting system with specialized foam suppression for paint storage areas.', 'View Details'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 13, l.id, 'مصنع دلتا للدهانات', 'شبكة حريق كاملة + قمع رغوي', 'نظام مكافحة حريق معتمد UL/FM مع قمع رغوي متخصص لمناطق تخزين الدهانات.', 'عرض التفاصيل'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Project 2: Petrochemical Complex
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(14, 6, 'project', 'petrochemical-complex', 2, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 14, l.id, 'Petrochemical Complex - Suez', 'High-Risk Deluge System', 'Advanced deluge system with SCADA monitoring for chemical processing facility.', 'View Details'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 14, l.id, 'مجمع بتروكيماويات - السويس', 'نظام دش عالي المخاطر', 'نظام دش متقدم مع مراقبة SCADA لمنشأة معالجة كيماوية.', 'عرض التفاصيل'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Project 3: Shopping Mall
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(15, 6, 'project', 'shopping-mall', 3, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 15, l.id, 'City Center Mall - Cairo', 'Comprehensive Fire Safety', 'Complete fire protection system for 5-story shopping complex with evacuation systems.', 'View Details'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 15, l.id, 'مول سيتي سنتر - القاهرة', 'سلامة حريق شاملة', 'نظام حماية من الحريق كامل لمجمع تسوق 5 طوابق مع أنظمة إخلاء.', 'عرض التفاصيل'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- ===== TESTIMONIALS SECTION ITEMS =====
-- Testimonial 1: Ahmed Hassan
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(16, 7, 'testimonial', 'ahmed-hassan', 1, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 16, l.id, 'Ahmed Hassan', 'Safety Manager, Delta Paint Industries', 'Sphinx Fire handled everything with precision and speed. We passed inspection on the first try, and the foam suppression system they designed for our paint storage saved us during a potential incident last month. Professional, fast, and reliable.', 'Read More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 16, l.id, 'أحمد حسن', 'مدير السلامة، صناعات دلتا للدهانات', 'تعامل Sphinx Fire مع كل شيء بدقة وسرعة. نجحنا في الفحص من المحاولة الأولى، ونظام القمع الرغوي الذي صمموه لتخزين الدهانات أنقذنا خلال حادث محتمل الشهر الماضي. احترافي، سريع، وموثوق.', 'اقرأ المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Testimonial 2: Fatma Nour
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(17, 7, 'testimonial', 'fatma-nour', 2, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 17, l.id, 'Fatma Nour', 'Operations Director, Pharma Solutions', 'The team at Sphinx Fire understood our unique requirements as a pharmaceutical facility. Their clean room fire protection solution met all FDA and GMP standards while ensuring maximum safety. Their local presence means support is always available.', 'Read More'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 17, l.id, 'فاطمة نور', 'مدير العمليات، حلول الأدوية', 'فهم فريق Sphinx Fire متطلباتنا الفريدة كمنشأة دوائية. حل حماية الحريق في الغرف النظيفة لجميع معايير FDA و GMP مع ضمان أقصى سلامة. وجودهم المحلي يعني أن الدعم متاح دائمًا.', 'اقرأ المزيد'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- ===== LOCATIONS SECTION ITEMS =====
-- Location 1: Sadat City
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(18, 8, 'location', 'sadat-city', 1, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 18, l.id, 'Sadat City', '50+ Projects', 'Complete fire safety solutions for factories and industrial facilities in Sadat City Industrial Zone.', 'View Services'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 18, l.id, 'مدينة السادات', '50+ مشروع', 'حلول سلامة حريق كاملة للمصانع والمنشآت الصناعية في المنطقة الصناعية بمدينة السادات.', 'عرض الخدمات'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Location 2: 6th of October
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(19, 8, 'location', 'october-city', 2, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 19, l.id, '6th of October', '35+ Projects', 'Fire protection systems for manufacturing plants and warehouses in 6th of October Industrial Zone.', 'View Services'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 19, l.id, 'مدينة 6 أكتوبر', '35+ مشروع', 'أنظمة حماية من الحريق لمصانع التصنيع والمستودعات في المنطقة الصناعية بمدينة 6 أكتوبر.', 'عرض الخدمات'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);

-- Location 3: 10th of Ramadan
INSERT INTO section_items (id, section_id, item_type, item_key, sort_order, is_active) VALUES
(20, 8, 'location', 'ramadan-city', 3, TRUE)
ON DUPLICATE KEY UPDATE section_id=VALUES(section_id), item_type=VALUES(item_type), item_key=VALUES(item_key), sort_order=VALUES(sort_order), is_active=VALUES(is_active);
-- English
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 20, l.id, '10th of Ramadan', '40+ Projects', 'Industrial fire safety solutions for factories and facilities in 10th of Ramadan Industrial City.', 'View Services'
FROM languages l WHERE l.code = 'en'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text);
-- Arabic
INSERT INTO section_item_translations (section_item_id, language_id, title, subtitle, content, button_text)
SELECT 20, l.id, 'مدينة العاشر من رمضان', '40+ مشروع', 'حلول السلامة من الحريق الصناعية للمصانع والمنشآت في مدينة العاشر من رمضان الصناعية.', 'عرض الخدمات'
FROM languages l WHERE l.code = 'ar'
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), content=VALUES(content), button_text=VALUES(button_text); 