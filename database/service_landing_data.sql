START TRANSACTION;

-- =====================================================
-- إضافة بيانات صفحة Landing للخدمة المفردة
-- =====================================================

-- إضافة صفحة Landing (page_id = 6)
INSERT INTO pages (id, slug, page_type, is_active, created_at, updated_at) 
VALUES (6, 'service-landing', 'landing', 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- 1. إضافة Hero Section للصفحة
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'hero', 'landing-hero', 1, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @hero_section_id = LAST_INSERT_ID();

-- ترجمات Hero باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) 
VALUES (@hero_section_id, 1, 'Professional Fire Safety Solutions', 'Protect your facility with certified fire protection systems', 'Get comprehensive fire safety solutions tailored to your industrial facility. Our certified experts ensure complete protection and compliance.', '🔴 Get Free Quote', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), subtitle = VALUES(subtitle), content = VALUES(content), button_text = VALUES(button_text), updated_at = NOW();

-- ترجمات Hero باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) 
VALUES (@hero_section_id, 2, 'حلول السلامة من الحريق الاحترافية', 'احم منشأتك بأنظمة الحماية من الحريق المعتمدة', 'احصل على حلول السلامة من الحريق الشاملة المخصصة لمنشأتك الصناعية. خبراؤنا المعتمدون يضمنون الحماية الكاملة والامتثال.', '🔴 احصل على عرض سعر مجاني', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), subtitle = VALUES(subtitle), content = VALUES(content), button_text = VALUES(button_text), updated_at = NOW();

-- 2. إضافة Why Choose Us Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'advantage', 'why-choose-us', 2, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @why_choose_us_id = LAST_INSERT_ID();

-- ترجمات Why Choose Us باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@why_choose_us_id, 1, 'Why Choose Sphinx Fire?', 'Professional fire safety solutions with certified expertise and guaranteed compliance', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Why Choose Us باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@why_choose_us_id, 2, 'لماذا تختار سفينكس فاير؟', 'حلول السلامة من الحريق الاحترافية مع خبرة معتمدة وامتثال مضمون', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 3. إضافة Service Features Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'feature', 'service-features', 3, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @service_features_id = LAST_INSERT_ID();

-- ترجمات Service Features باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@service_features_id, 1, 'Service Features', 'Comprehensive fire protection features designed for industrial facilities', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Service Features باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@service_features_id, 2, 'ميزات الخدمة', 'ميزات الحماية من الحريق الشاملة المصممة للمنشآت الصناعية', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 4. إضافة Service Benefits Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'benefit', 'service-benefits', 4, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @service_benefits_id = LAST_INSERT_ID();

-- ترجمات Service Benefits باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@service_benefits_id, 1, 'Service Benefits', 'Key benefits of our fire safety solutions for your facility', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Service Benefits باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@service_benefits_id, 2, 'فوائد الخدمة', 'الفوائد الرئيسية لحلول السلامة من الحريق لمنشأتك', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 5. إضافة Technical Specifications Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'specification', 'technical-specs', 5, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @technical_specs_id = LAST_INSERT_ID();

-- ترجمات Technical Specifications باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@technical_specs_id, 1, 'Technical Specifications', 'Detailed technical specifications for optimal performance', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Technical Specifications باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@technical_specs_id, 2, 'المواصفات التقنية', 'مواصفات تقنية مفصلة للأداء الأمثل', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 6. إضافة Installation Process Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'process', 'installation-process', 6, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @installation_process_id = LAST_INSERT_ID();

-- ترجمات Installation Process باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@installation_process_id, 1, 'Installation Process', 'Our systematic approach ensures quality and efficiency', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Installation Process باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@installation_process_id, 2, 'عملية التركيب', 'نهجنا المنهجي يضمن الجودة والكفاءة', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 7. إضافة Testimonials Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'testimonial', 'landing-testimonials', 7, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @landing_testimonials_id = LAST_INSERT_ID();

-- ترجمات Testimonials باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@landing_testimonials_id, 1, 'What Our Clients Say', 'What our satisfied clients say about our services', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Testimonials باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@landing_testimonials_id, 2, 'ماذا يقول عملاؤنا', 'ماذا يقول عملاؤنا الراضون عن خدماتنا', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 8. إضافة Contact Information Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'contact', 'contact-info', 8, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @contact_info_id = LAST_INSERT_ID();

-- ترجمات Contact Information باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@contact_info_id, 1, 'Contact Information', 'Get in touch with our fire safety experts', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Contact Information باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@contact_info_id, 2, 'معلومات التواصل', 'تواصل مع خبراء السلامة من الحريق لدينا', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- 9. إضافة Final CTA Section
INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES (6, 'cta', 'final-cta', 9, 1, NOW(), NOW()) 
ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id), updated_at = NOW();
SET @final_cta_id = LAST_INSERT_ID();

-- ترجمات Final CTA باللغة الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@final_cta_id, 1, 'Ready to Protect Your Facility?', 'Get a free consultation and quote for your fire safety needs', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

-- ترجمات Final CTA باللغة العربية
INSERT INTO section_translations (section_id, language_id, title, content, created_at, updated_at) 
VALUES (@final_cta_id, 2, 'مستعد لحماية منشأتك؟', 'احصل على استشارة مجانية وعرض سعر لاحتياجات السلامة من الحريق', NOW(), NOW()) 
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), updated_at = NOW();

COMMIT;

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • صفحة Landing جديدة (page_id = 6)
-- • 9 سكاشنات مختلفة للصفحة
-- • 18 ترجمة (إنجليزي + عربي)
-- • 27 سجل إجمالي
-- ===================================================== 