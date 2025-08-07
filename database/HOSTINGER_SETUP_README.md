# 🚀 إعداد قاعدة البيانات على Hostinger

## 📋 المتطلبات
- حساب Hostinger Business
- قاعدة بيانات MySQL
- phpMyAdmin

## 🔧 خطوات الإعداد

### 1. إنشاء قاعدة البيانات
1. ادخل إلى لوحة تحكم Hostinger
2. اذهب إلى "Databases" > "MySQL Databases"
3. أنشئ قاعدة بيانات جديدة باسم `u404645000_sphinx`
4. أنشئ مستخدم قاعدة بيانات باسم `u404645000_sweed`
5. اربط المستخدم بقاعدة البيانات مع صلاحيات كاملة

### 2. رفع ملف قاعدة البيانات
1. اذهب إلى phpMyAdmin
2. اختر قاعدة البيانات `u404645000_sphinx`
3. اذهب إلى تبويب "Import"
4. اختر ملف `hostinger_clean_schema.sql`
5. اضغط "Go" لرفع الملف

### 3. تحديث بيانات الاتصال
1. شغل ملف `update_config.php` من المتصفح:
   ```
   http://yourdomain.com/FIRE2/database/update_config.php
   ```
2. تأكد من ظهور رسالة النجاح

### 4. اختبار الاتصال
1. اذهب إلى لوحة الإدارة:
   ```
   http://yourdomain.com/FIRE2/admin/
   ```
2. جرب تسجيل الدخول باستخدام:
   - Email: `admin@sphinxfire.com`
   - Password: `password`

## 📊 الجداول المطلوبة

### الجداول الأساسية:
- `languages` - اللغات المدعومة
- `users` - المستخدمين
- `pages` - الصفحات
- `page_translations` - ترجمات الصفحات
- `sections` - الأقسام
- `section_translations` - ترجمات الأقسام

### جداول المحتوى:
- `service_categories` - فئات الخدمات
- `services` - الخدمات
- `service_translations` - ترجمات الخدمات
- `project_categories` - فئات المشاريع
- `projects` - المشاريع
- `project_translations` - ترجمات المشاريع
- `blog_categories` - فئات المدونة
- `blog_posts` - مقالات المدونة
- `blog_post_translations` - ترجمات المقالات

### جداول الإدارة:
- `admin_roles` - أدوار الإدارة
- `admin_users` - مستخدمي الإدارة
- `admin_logs` - سجلات الإدارة
- `admin_activity_logs` - سجلات النشاط
- `admin_notifications` - الإشعارات
- `settings` - الإعدادات

### جداول إضافية:
- `testimonials` - الشهادات
- `testimonial_translations` - ترجمات الشهادات
- `locations` - المواقع
- `location_translations` - ترجمات المواقع

## 🔒 الأمان
- تم إزالة جميع `DEFINER` و `PROCEDURE` من الملف
- تم إزالة جميع `FOREIGN KEY` لتجنب مشاكل التوافق
- تم استخدام `utf8mb4_unicode_ci` للدعم الكامل للعربية

## 🐛 حل المشاكل

### مشكلة: "Access denied"
- تأكد من صحة بيانات الاتصال
- تأكد من صلاحيات المستخدم

### مشكلة: "Table doesn't exist"
- تأكد من رفع ملف SQL بنجاح
- تحقق من وجود جميع الجداول في phpMyAdmin

### مشكلة: "Connection failed"
- تأكد من صحة اسم قاعدة البيانات
- تأكد من صحة اسم المستخدم وكلمة المرور

## 📞 الدعم
إذا واجهت أي مشاكل، تأكد من:
1. صحة بيانات الاتصال
2. وجود جميع الجداول
3. صلاحيات المستخدم
4. إعدادات PHP

## ✅ التحقق من النجاح
بعد الإعداد، يجب أن تعمل:
- ✅ لوحة الإدارة
- ✅ إدارة الصفحات
- ✅ إدارة الخدمات
- ✅ إدارة المشاريع
- ✅ إدارة المدونة
- ✅ إدارة الشهادات
- ✅ إدارة المواقع 