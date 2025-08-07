-- إضافة بيانات الشهادات في جدول testimonials
INSERT INTO testimonials (client_name, client_position, client_company, client_avatar, rating, project_id, service_id, location_id, is_featured, is_published, sort_order, created_by, created_at, updated_at) VALUES
('Ahmed Hassan', 'Safety Manager', 'Delta Paint Manufacturing', NULL, 5, 1, NULL, NULL, 1, 1, 1, 1, NOW(), NOW()),
('Fatma Nour', 'Operations Director', 'City Center Mall', NULL, 5, 2, NULL, NULL, 1, 1, 2, 1, NOW(), NOW()),
('Mohamed El-Shamy', 'Operations Manager', 'Petrochemical Complex', NULL, 5, 3, NULL, NULL, 0, 1, 3, 1, NOW(), NOW());

-- إضافة ترجمات الشهادات
INSERT INTO testimonial_translations (testimonial_id, language_id, content, created_at, updated_at) VALUES
(1, 1, 'Sphinx Fire handled everything with precision and speed. Their UL/FM certified foam system exceeded our expectations and we passed civil defense inspection on the first try.', NOW(), NOW()),
(1, 2, 'تعامل سفينكس فاير مع كل شيء بدقة وسرعة. نظام الرغوة المعتمد UL/FM تجاوز توقعاتنا واجتزنا فحص الدفاع المدني من أول مرة.', NOW(), NOW()),
(2, 1, 'Professional, fast, and reliable. The advanced alarm and evacuation system they installed has ensured zero incidents since installation.', NOW(), NOW()),
(2, 2, 'احترافية وسرعة وموثوقية. نظام الإنذار والإخلاء المتقدم الذي ركبوه ضمن عدم حدوث أي حوادث منذ التركيب.', NOW(), NOW()),
(3, 1, 'Support always available. The deluge system with SCADA integration has passed all safety audits and provides excellent protection for our chemical facility.', NOW(), NOW()),
(3, 2, 'الدعم متوفر دائمًا. نظام الديلوج مع تكامل SCADA اجتاز جميع اختبارات السلامة ويوفر حماية ممتازة لمنشأتنا الكيميائية.', NOW(), NOW()); 