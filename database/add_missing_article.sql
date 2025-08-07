START TRANSACTION;

-- =====================================================
-- إضافة المقال المفقود: top-10-fire-safety-tips
-- =====================================================

-- إضافة المقال في جدول blog_posts
INSERT INTO blog_posts (id, category_id, slug, author_id, featured_image, reading_time, status, is_featured, views_count, likes_count, shares_count, published_at, created_at, updated_at) 
VALUES 
(12, 1, 'top-10-fire-safety-tips', 1, 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop', 8, 'published', 0, 0, 0, 0, '2025-01-25 10:00:00', NOW(), NOW())
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- إضافة الترجمة الإنجليزية
INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
(12, 1, 'Top 10 Fire Safety Tips for Industrial Facilities',
'Essential fire safety tips that every industrial facility manager should implement to ensure workplace safety and compliance.',
'<h2>Introduction</h2>
<p>Fire safety in industrial facilities is not just about compliance—it\'s about protecting lives, assets, and business continuity. Here are the top 10 fire safety tips that every facility manager should implement.</p>

<h2>1. Regular Fire Safety Training</h2>
<p>Conduct comprehensive fire safety training for all employees at least quarterly. Include evacuation procedures, fire extinguisher usage, and emergency response protocols.</p>

<h2>2. Maintain Fire Detection Systems</h2>
<p>Ensure all fire detection and alarm systems are properly maintained and tested monthly. Replace batteries and sensors as needed.</p>

<h2>3. Clear Emergency Exits</h2>
<p>Keep all emergency exits clear and unobstructed. Post clear signage and ensure proper lighting for all evacuation routes.</p>

<h2>4. Proper Storage of Flammable Materials</h2>
<p>Store flammable materials in designated areas with proper ventilation and fire suppression systems. Follow NFPA guidelines for storage.</p>

<h2>5. Regular Equipment Maintenance</h2>
<p>Maintain all fire safety equipment including extinguishers, sprinklers, and fire pumps according to manufacturer specifications.</p>

<h2>6. Electrical Safety</h2>
<p>Regularly inspect electrical systems and equipment. Address any issues immediately and ensure proper grounding and circuit protection.</p>

<h2>7. Hot Work Procedures</h2>
<p>Implement strict hot work procedures including permits, fire watches, and proper safety equipment for welding and cutting operations.</p>

<h2>8. Emergency Response Plan</h2>
<p>Develop and regularly update a comprehensive emergency response plan. Include evacuation procedures, communication protocols, and emergency contacts.</p>

<h2>9. Regular Inspections</h2>
<p>Conduct regular fire safety inspections and document findings. Address any violations or concerns immediately.</p>

<h2>10. Stay Updated with Regulations</h2>
<p>Keep abreast of current fire safety regulations and standards. Update procedures and equipment as needed to maintain compliance.</p>

<h2>Conclusion</h2>
<p>Implementing these fire safety tips will significantly reduce the risk of fire incidents and ensure your facility remains compliant with safety regulations.</p>',
'["fire safety","industrial facilities","compliance","training","maintenance"]',
'Top 10 Fire Safety Tips for Industrial Facilities - Sphinx Fire',
'Essential fire safety tips for industrial facilities to ensure workplace safety and compliance.',
'fire safety, industrial facilities, compliance, training, maintenance, emergency response',
NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), updated_at = NOW();

-- إضافة الترجمة العربية
INSERT INTO blog_post_translations (post_id, language_id, title, excerpt, content, tags, meta_title, meta_description, meta_keywords, created_at, updated_at) 
VALUES 
(12, 2, 'أفضل 10 نصائح للسلامة من الحريق في المنشآت الصناعية',
'نصائح أساسية للسلامة من الحريق يجب على كل مدير منشأة صناعية تطبيقها لضمان سلامة مكان العمل والامتثال.',
'<h2>مقدمة</h2>
<p>السلامة من الحريق في المنشآت الصناعية ليست مجرد امتثال—إنها حماية للأرواح والأصول واستمرارية الأعمال. إليك أفضل 10 نصائح للسلامة من الحريق يجب على كل مدير منشأة تطبيقها.</p>

<h2>1. التدريب المنتظم على السلامة من الحريق</h2>
<p>قم بإجراء تدريب شامل على السلامة من الحريق لجميع الموظفين على الأقل ربع سنوياً. شمل إجراءات الإخلاء واستخدام طفايات الحريق وبروتوكولات الاستجابة للطوارئ.</p>

<h2>2. صيانة أنظمة كشف الحريق</h2>
<p>تأكد من صيانة واختبار جميع أنظمة كشف الحريق والإنذار بشكل صحيح شهرياً. استبدل البطاريات والمستشعرات حسب الحاجة.</p>

<h2>3. مخارج الطوارئ الواضحة</h2>
<p>احتفظ بجميع مخارج الطوارئ واضحة وغير معيقة. ضع لافتات واضحة وتأكد من الإضاءة المناسبة لجميع مسارات الإخلاء.</p>

<h2>4. التخزين الصحيح للمواد القابلة للاشتعال</h2>
<p>خزن المواد القابلة للاشتعال في مناطق محددة مع تهوية مناسبة وأنظمة إطفاء الحريق. اتبع إرشادات NFPA للتخزين.</p>

<h2>5. الصيانة المنتظمة للمعدات</h2>
<p>صان جميع معدات السلامة من الحريق بما في ذلك طفايات الحريق والرشاشات ومضخات الحريق وفقاً لمواصفات الشركة المصنعة.</p>

<h2>6. السلامة الكهربائية</h2>
<p>افحص الأنظمة والمعدات الكهربائية بانتظام. عالج أي مشاكل فوراً وتأكد من التأريض المناسب وحماية الدوائر.</p>

<h2>7. إجراءات العمل الساخن</h2>
<p>طبق إجراءات صارمة للعمل الساخن تشمل التصاريح ومراقبة الحريق والمعدات المناسبة لعمليات اللحام والقطع.</p>

<h2>8. خطة الاستجابة للطوارئ</h2>
<p>طور وحدث خطة شاملة للاستجابة للطوارئ بانتظام. شمل إجراءات الإخلاء وبروتوكولات الاتصال ووسائل الاتصال في الطوارئ.</p>

<h2>9. الفحوصات المنتظمة</h2>
<p>قم بإجراء فحوصات منتظمة للسلامة من الحريق وسجل النتائج. عالج أي مخالفات أو مخاوف فوراً.</p>

<h2>10. البقاء محدثاً باللوائح</h2>
<p>ابق على اطلاع بآخر لوائح ومعايير السلامة من الحريق. حدث الإجراءات والمعدات حسب الحاجة للحفاظ على الامتثال.</p>

<h2>الخاتمة</h2>
<p>تطبيق هذه النصائح للسلامة من الحريق سيقلل بشكل كبير من مخاطر حوادث الحريق ويضمن بقاء منشأتك متوافقة مع لوائح السلامة.</p>',
'["السلامة من الحريق","المنشآت الصناعية","الامتثال","التدريب","الصيانة"]',
'أفضل 10 نصائح للسلامة من الحريق في المنشآت الصناعية - سفنكس فاير',
'نصائح أساسية للسلامة من الحريق للمنشآت الصناعية لضمان سلامة مكان العمل والامتثال.',
'السلامة من الحريق، المنشآت الصناعية، الامتثال، التدريب، الصيانة، الاستجابة للطوارئ',
NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), updated_at = NOW();

COMMIT;

-- =====================================================
-- ملخص البيانات المضافة:
-- =====================================================
-- • 1 مقال جديد: top-10-fire-safety-tips
-- • 1 ترجمة إنجليزية
-- • 1 ترجمة عربية
-- • إجمالي: 3 سجلات
-- ===================================================== 