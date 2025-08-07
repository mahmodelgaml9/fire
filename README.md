# Sphinx Fire Website - توثيق هيكل المشروع

## 1️⃣ هيكل الصفحات الأساسية
- **index.html**: الصفحة الرئيسية (كاروسيل، مزايا، خدمات مختصرة، مشاريع، شهادات، CTA، Footer)
- **about.html**: من نحن (رؤية، رسالة، فريق، شهادات)
- **services.html**: جميع الخدمات (شبكات، إنذار، صيانة، إلخ)
- **projects.html**: المشاريع المنفذة (شبكة، صور، تفاصيل)
- **blog.html**: المدونة (مقالات توعوية)
- **contact.html**: تواصل معنا (فورم، بيانات، خريطة)
- **firefighting-systems.html**: صفحة خدمة متخصصة
- **project-delta-paint.html**: صفحة مشروع منفذ
- **blog-article-civil-defense.html**: مقالة متخصصة
- **retargeting-landing.html**: صفحة عرض خاص
- **sadat-city-fire-protection.html**: صفحة مدينة/تغطية جغرافية

## 2️⃣ المكونات المتكررة (Partials)
- **header**: وسم <head> + بداية <body>
- **navbar**: شريط التنقل الرئيسي
- **footer**: تذييل الموقع وروابطه
- **whatsapp-float**: زر واتساب عائم
- **sticky-cta**: زر CTA ثابت
- **seo_helper**: تهيئة SEO وMeta
- **functions.php**: دوال مساعدة عامة

## 3️⃣ الكلاسات المهمة في CSS
- `.hero-slide` : سلايد الكاروسيل
- `.advantage-card`, `.feature-card`, `.service-card`, `.project-card`, `.testimonial-card`, `.value-card`, `.team-card`, `.approach-step`, `.results-card`, `.contact-card`, `.faq-item`, `.blog-card` : جميع الكروت المتحركة
- `.animate` : كلاس إظهار الكارد بالأنيميشن
- `.sticky-header` : الهيدر الثابت
- `.carousel-dot`, `.carousel-arrow` : نقاط وأسهم الكاروسيل
- `.whatsapp-float`, `.sticky-cta` : أزرار عائمة
- `.badge-reveal` : شارة متحركة

## 4️⃣ منطق الجافاسكريبت الموحد
- جميع وظائف الكاروسيل، الأنيميشن، البوب-أب، المينيو، إلخ في ملف `main.js`
- أي منطق خاص بصفحة معينة في ملف منفصل داخل `scripts/`
- كل كارد يُضاف له `.animate` تلقائيًا عند التحميل

## 5️⃣ خطة التحويل إلى PHP ديناميكي
1. توحيد كل partials في ملفات PHP (`header.php`, `footer.php`, ...)
2. تحويل كل صفحة رئيسية إلى ملف PHP (مثلاً: `index.php`)
3. ربط كل صفحة وجزء ديناميكي بقاعدة البيانات (pages, sections, translations, ...)
4. استخدام include/require للقوالب المتكررة
5. اختبار كل صفحة بعد تحويلها مباشرة
6. توثيق أي منطق خاص أو استثناءات أثناء التحويل

---

> **ملاحظة:**
> هذا التوثيق يسهّل على أي مطور فهم المشروع بالكامل قبل البدء في التحويل إلى نظام ديناميكي احترافي. 