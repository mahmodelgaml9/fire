-- اختبار اللغة - إضافة مقال واحد باللغتين
START TRANSACTION;

-- إضافة مقال اختبار
INSERT INTO blog_posts (id, category_id, slug, author_id, featured_image, reading_time, status, is_featured, views_count, likes_count, shares_count, published_at, created_at, updated_at) 
VALUES 
(999, 1, 'test-language-article', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=300&fit=crop', 5, 'published', 1, 0, 0, 0, '2025-01-20 10:00:00', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة الترجمة الإنجليزية
INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
(999, 1, 'TEST ARTICLE - ENGLISH VERSION',
'This is a test article in English to verify language switching is working correctly.',
'<h2>English Test Content</h2><p>This is the English version of the test article. If you see this content, the English language is working correctly.</p>',
'["test","english","language"]',
'Test Article - English Version - Sphinx Fire',
'Test article to verify English language functionality.',
'test, english, language, verification',
NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), updated_at = NOW();

-- إضافة الترجمة العربية
INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
(999, 2, 'مقال اختبار - النسخة العربية',
'هذا مقال اختبار باللغة العربية للتحقق من أن تبديل اللغة يعمل بشكل صحيح.',
'<h2>محتوى الاختبار العربي</h2><p>هذه النسخة العربية من مقال الاختبار. إذا رأيت هذا المحتوى، فإن اللغة العربية تعمل بشكل صحيح.</p>',
'["اختبار","عربي","لغة"]',
'مقال اختبار - النسخة العربية - سفنكس فاير',
'مقال اختبار للتحقق من وظائف اللغة العربية.',
'اختبار، عربي، لغة، تحقق',
NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), updated_at = NOW();

COMMIT;

-- التحقق من البيانات
SELECT 'English Article:' as info;
SELECT bt.title, bt.excerpt FROM blog_post_translations bt 
JOIN languages l ON bt.language_id = l.id 
WHERE bt.post_id = 999 AND l.code = 'en';

SELECT 'Arabic Article:' as info;
SELECT bt.title, bt.excerpt FROM blog_post_translations bt 
JOIN languages l ON bt.language_id = l.id 
WHERE bt.post_id = 999 AND l.code = 'ar'; 