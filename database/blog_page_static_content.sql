START TRANSACTION;

-- =====================================================
-- إضافة المحتوى الستاتيكي لصفحة المدونة (Blog Page Static Content)
-- page_id = 5 (صفحة المدونة)
-- section_type = "blog_content"
-- =====================================================

-- إضافة السكاشنز الستاتيكية لصفحة المدونة
INSERT INTO sections (id, page_id, section_type, section_key, sort_order, is_active, settings, created_at, updated_at) 
VALUES 
(50, 5, 'blog_content', 'hero_badge', 1, 1, '{"icon": "📚", "color": "blue"}', NOW(), NOW()),
(51, 5, 'blog_content', 'hero_title', 2, 1, '{"size": "large", "color": "white"}', NOW(), NOW()),
(52, 5, 'blog_content', 'hero_subtitle', 3, 1, '{"size": "medium", "color": "gray-200"}', NOW(), NOW()),
(53, 5, 'blog_content', 'hero_description', 4, 1, '{"size": "medium", "color": "gray-300"}', NOW(), NOW()),
(54, 5, 'blog_content', 'hero_primary_button', 5, 1, '{"style": "primary", "color": "brand-red"}', NOW(), NOW()),
(55, 5, 'blog_content', 'hero_secondary_button', 6, 1, '{"style": "secondary", "color": "white"}', NOW(), NOW()),
(56, 5, 'blog_content', 'search_placeholder', 7, 1, '{"type": "placeholder"}', NOW(), NOW()),
(57, 5, 'blog_content', 'all_articles_button', 8, 1, '{"style": "filter"}', NOW(), NOW()),
(58, 5, 'blog_content', 'featured_guide_badge', 9, 1, '{"icon": "⭐", "color": "yellow"}', NOW(), NOW()),
(59, 5, 'blog_content', 'featured_guide_title', 10, 1, '{"size": "large", "color": "black"}', NOW(), NOW()),
(60, 5, 'blog_content', 'featured_guide_subtitle', 11, 1, '{"size": "medium", "color": "gray"}', NOW(), NOW()),
(61, 5, 'blog_content', 'download_guide_button', 12, 1, '{"style": "primary", "color": "brand-red"}', NOW(), NOW()),
(62, 5, 'blog_content', 'updated_for_text', 13, 1, '{"size": "small", "color": "gray"}', NOW(), NOW()),
(63, 5, 'blog_content', 'latest_insights_title', 14, 1, '{"size": "large", "color": "black"}', NOW(), NOW()),
(64, 5, 'blog_content', 'latest_insights_subtitle', 15, 1, '{"size": "medium", "color": "gray"}', NOW(), NOW()),
(65, 5, 'blog_content', 'load_more_button', 16, 1, '{"style": "secondary", "color": "brand-red"}', NOW(), NOW()),
(66, 5, 'blog_content', 'newsletter_title', 17, 1, '{"size": "large", "color": "white"}', NOW(), NOW()),
(67, 5, 'blog_content', 'newsletter_description', 18, 1, '{"size": "medium", "color": "gray-300"}', NOW(), NOW()),
(68, 5, 'blog_content', 'email_placeholder', 19, 1, '{"type": "placeholder"}', NOW(), NOW()),
(69, 5, 'blog_content', 'subscribe_button', 20, 1, '{"style": "primary", "color": "brand-red"}', NOW(), NOW()),
(70, 5, 'blog_content', 'newsletter_footer', 21, 1, '{"size": "small", "color": "gray-300"}', NOW(), NOW()),
(71, 5, 'blog_content', 'subscribers_label', 22, 1, '{"size": "small", "color": "gray-300"}', NOW(), NOW()),
(72, 5, 'blog_content', 'weekly_label', 23, 1, '{"size": "large", "color": "brand-red"}', NOW(), NOW()),
(73, 5, 'blog_content', 'expert_insights_label', 24, 1, '{"size": "small", "color": "gray-300"}', NOW(), NOW()),
(74, 5, 'blog_content', 'free_content_label', 25, 1, '{"size": "small", "color": "gray-300"}', NOW(), NOW()),
(75, 5, 'blog_content', 'read_more_button', 26, 1, '{"style": "link", "color": "brand-red"}', NOW(), NOW()),
(76, 5, 'blog_content', 'min_read_label', 27, 1, '{"size": "small", "color": "gray"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة الترجمات الإنجليزية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) 
VALUES 
(50, 1, '📚 EXPERT KNOWLEDGE BASE', '', '', NOW(), NOW()),
(51, 1, 'Insights That Keep Your Facility Safe', '', '', NOW(), NOW()),
(52, 1, 'Expert fire protection guides, updates, and industrial safety knowledge.', '', '', NOW(), NOW()),
(53, 1, 'Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.', '', '', NOW(), NOW()),
(54, 1, '🔴 Subscribe to Safety Tips', '', '', NOW(), NOW()),
(55, 1, '⚪ Read Latest Articles', '', '', NOW(), NOW()),
(56, 1, 'Search articles, guides, regulations...', '', '', NOW(), NOW()),
(57, 1, 'All Articles', '', '', NOW(), NOW()),
(58, 1, '⭐ FEATURED GUIDE', '', '', NOW(), NOW()),
(59, 1, 'Most Popular This Month', '', '', NOW(), NOW()),
(60, 1, 'Essential reading for industrial safety professionals', '', '', NOW(), NOW()),
(61, 1, '🔴 Download Free PDF Guide', '', '', NOW(), NOW()),
(62, 1, 'Updated for 2024 regulations', '', '', NOW(), NOW()),
(63, 1, 'Latest Safety Insights', '', '', NOW(), NOW()),
(64, 1, 'Expert knowledge to keep your facility compliant and safe', '', '', NOW(), NOW()),
(65, 1, 'Load More Articles', '', '', NOW(), NOW()),
(66, 1, 'Want to get the latest safety tips and system updates?', '', '', NOW(), NOW()),
(67, 1, 'Join 2,500+ safety professionals who receive our weekly insights on fire protection, compliance updates, and industry best practices.', '', '', NOW(), NOW()),
(68, 1, 'Enter your email address', '', '', NOW(), NOW()),
(69, 1, '🔴 Subscribe', '', '', NOW(), NOW()),
(70, 1, 'Free weekly insights • No spam • Unsubscribe anytime', '', '', NOW(), NOW()),
(71, 1, 'Subscribers', '', '', NOW(), NOW()),
(72, 1, 'Weekly', '', '', NOW(), NOW()),
(73, 1, 'Expert Insights', '', '', NOW(), NOW()),
(74, 1, 'Free Content', '', '', NOW(), NOW()),
(75, 1, 'Read More', '', '', NOW(), NOW()),
(76, 1, 'min read', '', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), subtitle = VALUES(subtitle), updated_at = NOW();

-- إضافة الترجمات العربية
INSERT INTO section_translations (section_id, language_id, title, content, subtitle, created_at, updated_at) 
VALUES 
(50, 2, '📚 قاعدة المعرفة الخبيرة', '', '', NOW(), NOW()),
(51, 2, 'رؤى تحافظ على سلامة منشأتك', '', '', NOW(), NOW()),
(52, 2, 'دلائل الحماية من الحريق الخبيرة، التحديثات، ومعرفة السلامة الصناعية.', '', '', NOW(), NOW()),
(53, 2, 'ابق متقدماً على لوائح السلامة مع رؤى منسقة من قبل مهندسي الحماية من الحريق المعتمدين ومستشاري السلامة.', '', '', NOW(), NOW()),
(54, 2, '🔴 اشترك في نصائح السلامة', '', '', NOW(), NOW()),
(55, 2, '⚪ اقرأ أحدث المقالات', '', '', NOW(), NOW()),
(56, 2, 'ابحث في المقالات، الدلائل، اللوائح...', '', '', NOW(), NOW()),
(57, 2, 'جميع المقالات', '', '', NOW(), NOW()),
(58, 2, '⭐ الدليل المميز', '', '', NOW(), NOW()),
(59, 2, 'الأكثر شعبية هذا الشهر', '', '', NOW(), NOW()),
(60, 2, 'قراءة أساسية لمتخصصي السلامة الصناعية', '', '', NOW(), NOW()),
(61, 2, '🔴 حمل الدليل المجاني PDF', '', '', NOW(), NOW()),
(62, 2, 'محدث للوائح 2024', '', '', NOW(), NOW()),
(63, 2, 'أحدث رؤى السلامة', '', '', NOW(), NOW()),
(64, 2, 'معرفة خبيرة للحفاظ على منشأتك متوافقة وآمنة', '', '', NOW(), NOW()),
(65, 2, 'تحميل المزيد من المقالات', '', '', NOW(), NOW()),
(66, 2, 'تريد الحصول على أحدث نصائح السلامة وتحديثات الأنظمة؟', '', '', NOW(), NOW()),
(67, 2, 'انضم إلى أكثر من 2,500 متخصص سلامة يتلقون رؤانا الأسبوعية حول الحماية من الحريق، تحديثات الامتثال، وأفضل الممارسات في الصناعة.', '', '', NOW(), NOW()),
(68, 2, 'أدخل عنوان بريدك الإلكتروني', '', '', NOW(), NOW()),
(69, 2, '🔴 اشترك', '', '', NOW(), NOW()),
(70, 2, 'رؤى أسبوعية مجانية • لا رسائل مزعجة • إلغاء الاشتراك في أي وقت', '', '', NOW(), NOW()),
(71, 2, 'مشتركين', '', '', NOW(), NOW()),
(72, 2, 'أسبوعي', '', '', NOW(), NOW()),
(73, 2, 'رؤى خبيرة', '', '', NOW(), NOW()),
(74, 2, 'محتوى مجاني', '', '', NOW(), NOW()),
(75, 2, 'اقرأ المزيد', '', '', NOW(), NOW()),
(76, 2, 'دقائق قراءة', '', '', NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content), subtitle = VALUES(subtitle), updated_at = NOW();

COMMIT;

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • 27 سكشن ستاتيكي لصفحة المدونة
-- • 27 ترجمة إنجليزية
-- • 27 ترجمة عربية
-- • إجمالي: 81 سجل
-- ===================================================== 