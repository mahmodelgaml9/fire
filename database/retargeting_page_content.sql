START TRANSACTION;

-- =====================================================
-- إضافة محتوى صفحة retargeting في قاعدة البيانات
-- =====================================================

-- إضافة صفحة retargeting إذا لم تكن موجودة
INSERT INTO pages (id, slug, status, published_at, created_at, updated_at) 
VALUES (5, 'retargeting', 'published', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات صفحة retargeting
INSERT INTO page_translations (page_id, language_id, title, meta_title, meta_description, created_at, updated_at) 
VALUES 
(5, 1, 'Special Offer: 20% Off Fire Safety Assessment | Sphinx Fire', 'Special Offer: 20% Off Fire Safety Assessment | Sphinx Fire', 'Limited time offer: Act now and save 20% on professional fire safety assessment. Certified engineers, same-day response, compliance guarantee.', NOW(), NOW()),
(5, 2, 'عرض خاص: خصم 20% على تقييم السلامة من الحريق | سفنكس فاير', 'عرض خاص: خصم 20% على تقييم السلامة من الحريق | سفنكس فاير', 'عرض محدود: تصرف الآن ووفر 20% على تقييم السلامة من الحريق الاحترافي. مهندسون معتمدون، استجابة في نفس اليوم، ضمان الامتثال.', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة أقسام صفحة retargeting
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, created_at, updated_at) 
VALUES 
-- Hero Section
(50, 5, 'hero', 'retargeting-hero', 1, TRUE, NOW(), NOW()),

-- Benefits Section
(51, 5, 'benefits', 'retargeting-benefits', 2, TRUE, NOW(), NOW()),

-- Included Section
(52, 5, 'included', 'retargeting-included', 3, TRUE, NOW(), NOW()),

-- Testimonials Section
(53, 5, 'testimonials', 'retargeting-testimonials', 4, TRUE, NOW(), NOW()),

-- FAQ Section
(54, 5, 'faq', 'retargeting-faq', 5, TRUE, NOW(), NOW()),

-- Final CTA Section
(55, 5, 'cta', 'retargeting-final-cta', 6, TRUE, NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة ترجمات أقسام صفحة retargeting
INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, created_at, updated_at) 
VALUES 
-- Hero Section - English
(50, 1, 'Save 20% on Professional Fire Safety Assessment', 'Protect Your Facility. Ensure Compliance. Save Money.', 'Get a comprehensive fire safety assessment from certified engineers. Identify risks, ensure compliance, and protect your business with our limited-time offer.', 'CLAIM YOUR 20% DISCOUNT NOW', NOW(), NOW()),

-- Hero Section - Arabic
(50, 2, 'وفر 20% على تقييم السلامة من الحريق الاحترافي', 'احمِ منشأتك. تأكد من الامتثال. وفر المال.', 'احصل على تقييم سلامة حريق شامل من مهندسين معتمدين. حدد المخاطر وتأكد من الامتثال واحمِ عملك مع عرضنا المحدود الوقت.', 'احصل على خصم 20% الآن', NOW(), NOW()),

-- Benefits - English
(51, 1, 'Why Choose Our Fire Safety Assessment?', 'Professional evaluation with actionable insights and compliance guidance', 'Our comprehensive fire safety assessment provides certified engineering expertise, detailed reporting, and ensures your facility meets all regulatory requirements.', 'Claim Your 20% Discount Now', NOW(), NOW()),

-- Benefits - Arabic
(51, 2, 'لماذا تختار تقييم السلامة من الحريق لدينا؟', 'تقييم احترافي مع رؤى قابلة للتنفيذ وإرشادات الامتثال', 'تقييم السلامة من الحريق الشامل لدينا يوفر خبرة هندسية معتمدة وتقارير مفصلة ويضمن تلبية منشأتك لجميع المتطلبات التنظيمية.', 'احصل على خصم 20% الآن', NOW(), NOW()),

-- Included - English
(52, 1, 'What\'s Included in Your Assessment', 'Comprehensive evaluation worth EGP 15,000 - now only EGP 12,000', 'Your discounted assessment includes complete system inspection, risk assessment, compliance verification, and detailed recommendations for improvement.', 'Claim Your Special Price', NOW(), NOW()),

-- Included - Arabic
(52, 2, 'ما المدرج في تقييمك', 'تقييم شامل بقيمة 15,000 جنيه - الآن فقط 12,000 جنيه', 'تقييمك المخفض يشمل فحص نظام كامل وتقييم المخاطر والتحقق من الامتثال وتوصيات مفصلة للتحسين.', 'احصل على السعر الخاص', NOW(), NOW()),

-- Testimonials - English
(53, 1, 'What Our Clients Say', 'Real feedback from facilities that chose our assessment service', 'Hear from industrial facilities that have benefited from our professional fire safety assessments and achieved compliance with significant cost savings.', 'Read More Testimonials', NOW(), NOW()),

-- Testimonials - Arabic
(53, 2, 'ماذا يقول عملاؤنا', 'تعليقات حقيقية من المنشآت التي اختارت خدمة التقييم لدينا', 'استمع من المنشآت الصناعية التي استفادت من تقييمات السلامة من الحريق الاحترافية لدينا وحققت الامتثال مع توفير كبير في التكاليف.', 'اقرأ المزيد من التوصيات', NOW(), NOW()),

-- FAQ - English
(54, 1, 'Frequently Asked Questions', 'Quick answers about our special offer', 'Find answers to common questions about our discounted fire safety assessment service, what\'s included, and how to claim your offer.', 'Contact Us for More Info', NOW(), NOW()),

-- FAQ - Arabic
(54, 2, 'الأسئلة الشائعة', 'إجابات سريعة حول عرضنا الخاص', 'اعثر على إجابات للأسئلة الشائعة حول خدمة تقييم السلامة من الحريق المخفضة لدينا وما المدرج وكيفية الحصول على عرضك.', 'اتصل بنا للمزيد من المعلومات', NOW(), NOW()),

-- Final CTA - English
(55, 1, 'Don\'t miss this limited-time opportunity', 'Professional Fire Safety Solutions at Special Pricing', 'Book your discounted assessment now and save 20% while ensuring your facility\'s safety and compliance. Limited spots available.', 'CLAIM YOUR 20% DISCOUNT NOW', NOW(), NOW()),

-- Final CTA - Arabic
(55, 2, 'لا تفوت هذه الفرصة المحدودة الوقت', 'حلول السلامة من الحريق الاحترافية بأسعار خاصة', 'احجز تقييمك المخفض الآن ووفر 20% مع ضمان سلامة وامتثال منشأتك. أماكن محدودة متاحة.', 'احصل على خصم 20% الآن', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

COMMIT; 