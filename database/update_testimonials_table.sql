-- تحديث جدول testimonial_translations لإضافة الحقول المطلوبة

-- إضافة الحقول الجديدة
ALTER TABLE `testimonial_translations` 
ADD COLUMN `client_name` VARCHAR(100) NULL AFTER `content`,
ADD COLUMN `client_position` VARCHAR(100) NULL AFTER `client_name`,
ADD COLUMN `company_name` VARCHAR(100) NULL AFTER `client_position`,
ADD COLUMN `testimonial_text` TEXT NULL AFTER `company_name`,
ADD COLUMN `rating` INT(1) DEFAULT 5 AFTER `testimonial_text`;

-- تحديث البيانات الموجودة (نقل المحتوى من content إلى testimonial_text)
UPDATE `testimonial_translations` 
SET `testimonial_text` = `content`,
    `client_name` = 'Client Name',
    `client_position` = 'Position',
    `company_name` = 'Company Name',
    `rating` = 5
WHERE `testimonial_text` IS NULL;

-- إضافة بيانات تجريبية للشهادات
INSERT INTO `testimonials` (`location_id`, `is_featured`, `is_published`, `sort_order`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, NOW(), NOW()),
(2, 1, 1, 2, 1, NOW(), NOW());

-- إضافة الترجمات الإنجليزية
INSERT INTO `testimonial_translations` (`testimonial_id`, `language_id`, `content`, `client_name`, `client_position`, `company_name`, `testimonial_text`, `rating`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sphinx Fire handled everything with precision and speed. We passed inspection on the first try, and the foam suppression system they designed for our paint storage saved us during a potential incident last month. Professional, fast, and reliable.', 'Ahmed Hassan', 'Safety Manager', 'Delta Paint Industries', 'Sphinx Fire handled everything with precision and speed. We passed inspection on the first try, and the foam suppression system they designed for our paint storage saved us during a potential incident last month. Professional, fast, and reliable.', 5, NOW(), NOW()),
(2, 1, 'The team at Sphinx Fire understood our unique requirements as a pharmaceutical facility. Their clean room fire protection solution met all FDA and GMP standards while ensuring maximum safety. Their local presence means support is always available.', 'Fatma Nour', 'Operations Director', 'Pharma Solutions', 'The team at Sphinx Fire understood our unique requirements as a pharmaceutical facility. Their clean room fire protection solution met all FDA and GMP standards while ensuring maximum safety. Their local presence means support is always available.', 5, NOW(), NOW());

-- إضافة الترجمات العربية
INSERT INTO `testimonial_translations` (`testimonial_id`, `language_id`, `content`, `client_name`, `client_position`, `company_name`, `testimonial_text`, `rating`, `created_at`, `updated_at`) VALUES
(1, 2, 'تعامل سفينكس فاير مع كل شيء بدقة وسرعة. نجحنا في اجتياز الفحص من المرة الأولى، وأنظمة إطفاء الرغوة التي صمموها لمستودع الطلاء أنقذتنا خلال حادثة محتملة الشهر الماضي. مهنيون، سريعون، وموثوقون.', 'أحمد حسن', 'مدير السلامة', 'صناعات دلتا للطلاء', 'تعامل سفينكس فاير مع كل شيء بدقة وسرعة. نجحنا في اجتياز الفحص من المرة الأولى، وأنظمة إطفاء الرغوة التي صمموها لمستودع الطلاء أنقذتنا خلال حادثة محتملة الشهر الماضي. مهنيون، سريعون، وموثوقون.', 5, NOW(), NOW()),
(2, 2, 'فهم فريق سفينكس فاير متطلباتنا الفريدة كمنشأة صيدلانية. حل الحماية من الحريق في الغرف النظيفة يلبي جميع معايير FDA و GMP مع ضمان أقصى درجات الأمان. وجودهم المحلي يعني أن الدعم متاح دائمًا.', 'فاطمة نور', 'مدير العمليات', 'حلول فارما', 'فهم فريق سفينكس فاير متطلباتنا الفريدة كمنشأة صيدلانية. حل الحماية من الحريق في الغرف النظيفة يلبي جميع معايير FDA و GMP مع ضمان أقصى درجات الأمان. وجودهم المحلي يعني أن الدعم متاح دائمًا.', 5, NOW(), NOW()); 