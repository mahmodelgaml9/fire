<?php
include 'config.php';
include 'includes/functions.php';

// تحديد اللغة
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en');
$lang_id = ($lang == 'ar') ? 2 : 1;

// حفظ اللغة في الكوكي
if (isset($_GET['lang'])) {
    setcookie('site_lang', $_GET['lang'], time() + (365 * 24 * 60 * 60), '/');
}

// ترجمة النصوص الثابتة
$translations = [
    'en' => [
        'limited_offer' => '🔥 LIMITED TIME OFFER - ONLY 5 SPOTS LEFT',
        'save_20_percent' => 'Save 20% on Professional Fire Safety Assessment',
        'protect_facility' => 'Protect Your Facility. Ensure Compliance. Save Money.',
        'comprehensive_assessment' => 'Get a comprehensive fire safety assessment from certified engineers. Identify risks, ensure compliance, and protect your business.',
        'offer_expires' => 'Offer Expires In:',
        'claim_discount' => '🔴 CLAIM YOUR 20% DISCOUNT NOW',
        'book_discounted' => 'Book Your Discounted Assessment',
        'full_name' => 'Full Name *',
        'company_name' => 'Company Name *',
        'email_address' => 'Email Address *',
        'phone_number' => 'Phone Number *',
        'facility_type' => 'Facility Type *',
        'select_facility' => 'Select your facility type',
        'manufacturing' => 'Manufacturing Plant',
        'warehouse' => 'Warehouse/Distribution',
        'chemical' => 'Chemical Processing',
        'retail' => 'Retail/Shopping Center',
        'office' => 'Office Building',
        'other' => 'Other Industrial Facility',
        'preferred_date' => 'Preferred Assessment Date',
        'urgent_assessment' => 'I need an urgent assessment (within 48 hours)',
        'claim_20_discount' => '🔴 CLAIM 20% DISCOUNT NOW',
        'limited_time_offer' => 'Limited time offer. By submitting this form, you agree to be contacted by Sphinx Fire regarding your fire safety requirements.',
        'discount_claimed' => 'Discount Claimed Successfully!',
        'contact_4_hours' => 'Our team will contact you within 4 hours to schedule your discounted assessment.',
        'why_choose' => 'Why Choose Our Fire Safety Assessment?',
        'professional_evaluation' => 'Professional evaluation with actionable insights and compliance guidance',
        'certified_engineers' => 'Certified Engineers',
        'certified_desc' => 'Assessment conducted by NFPA-certified fire safety engineers with 10+ years of experience.',
        'comprehensive_report' => 'Comprehensive Report',
        'report_desc' => 'Detailed 20+ page report with risk assessment, compliance status, and improvement recommendations.',
        'civil_defense_ready' => 'Civil Defense Ready',
        'civil_defense_desc' => 'Ensures your facility meets all Egyptian Civil Defense requirements and international standards.',
        'claim_discount_now' => 'Claim Your 20% Discount Now',
        'whats_included' => 'What\'s Included in Your Assessment',
        'comprehensive_evaluation' => 'Comprehensive evaluation worth EGP 15,000 - now only EGP 12,000',
        'complete_inspection' => 'Complete System Inspection',
        'inspection_desc' => 'Thorough evaluation of all fire protection systems including pumps, sprinklers, alarms, extinguishers, and emergency equipment.',
        'total_value' => 'Total Value: EGP 15,000 → Special Price: EGP 12,000',
        'what_clients_say' => 'What Our Clients Say',
        'real_feedback' => 'Real feedback from facilities that chose our assessment service',
        'testimonial_text' => 'The assessment identified critical issues we had overlooked for years. Their recommendations helped us pass civil defense inspection on the first try.',
        'testimonial_name' => 'Ahmed Hassan',
        'testimonial_position' => 'Safety Manager, Delta Industries',
        'faq_title' => 'Frequently Asked Questions',
        'faq_subtitle' => 'Quick answers about our special offer',
        'dont_miss' => 'Don\'t miss this limited-time opportunity',
        'book_discounted_assessment' => 'Book your discounted assessment now and save 20% while ensuring your facility\'s safety and compliance.',
        'claim_20_discount_final' => '🔴 CLAIM YOUR 20% DISCOUNT NOW',
        'discount_label' => 'Discount',
        'spots_left' => 'Spots Left',
        'days_remaining' => 'Days Remaining',
        'emergency_contact' => 'For urgent fire safety emergencies or immediate assistance:',
        'available_24_7' => 'Available 24/7 for emergency response'
    ],
    'ar' => [
        'limited_offer' => '🔥 عرض محدود - 5 أماكن متبقية فقط',
        'save_20_percent' => 'وفر 20% على تقييم السلامة من الحريق الاحترافي',
        'protect_facility' => 'احمِ منشأتك. تأكد من الامتثال. وفر المال.',
        'comprehensive_assessment' => 'احصل على تقييم سلامة حريق شامل من مهندسين معتمدين. حدد المخاطر وتأكد من الامتثال واحمِ عملك.',
        'offer_expires' => 'ينتهي العرض في:',
        'claim_discount' => '🔴 احصل على خصم 20% الآن',
        'book_discounted' => 'احجز تقييمك المخفض',
        'full_name' => 'الاسم الكامل *',
        'company_name' => 'اسم الشركة *',
        'email_address' => 'عنوان البريد الإلكتروني *',
        'phone_number' => 'رقم الهاتف *',
        'facility_type' => 'نوع المنشأة *',
        'select_facility' => 'اختر نوع منشأتك',
        'manufacturing' => 'مصنع تصنيع',
        'warehouse' => 'مستودع/توزيع',
        'chemical' => 'معالجة كيميائية',
        'retail' => 'بيع بالتجزئة/مركز تسوق',
        'office' => 'مبنى مكتبي',
        'other' => 'منشأة صناعية أخرى',
        'preferred_date' => 'تاريخ التقييم المفضل',
        'urgent_assessment' => 'أحتاج تقييم عاجل (خلال 48 ساعة)',
        'claim_20_discount' => '🔴 احصل على خصم 20% الآن',
        'limited_time_offer' => 'عرض محدود الوقت. بإرسال هذا النموذج، توافق على أن يتصل بك سفنكس فاير بخصوص متطلبات السلامة من الحريق.',
        'discount_claimed' => 'تم الحصول على الخصم بنجاح!',
        'contact_4_hours' => 'سيتصل بك فريقنا خلال 4 ساعات لجدولة تقييمك المخفض.',
        'why_choose' => 'لماذا تختار تقييم السلامة من الحريق لدينا؟',
        'professional_evaluation' => 'تقييم احترافي مع رؤى قابلة للتنفيذ وإرشادات الامتثال',
        'certified_engineers' => 'مهندسون معتمدون',
        'certified_desc' => 'تقييم يتم إجراؤه بواسطة مهندسي سلامة حريق معتمدين من NFPA مع خبرة 10+ سنوات.',
        'comprehensive_report' => 'تقرير شامل',
        'report_desc' => 'تقرير مفصل 20+ صفحة مع تقييم المخاطر وحالة الامتثال وتوصيات التحسين.',
        'civil_defense_ready' => 'جاهز للدفاع المدني',
        'civil_defense_desc' => 'يضمن تلبية منشأتك لجميع متطلبات الدفاع المدني المصري والمعايير الدولية.',
        'claim_discount_now' => 'احصل على خصم 20% الآن',
        'whats_included' => 'ما المدرج في تقييمك',
        'comprehensive_evaluation' => 'تقييم شامل بقيمة 15,000 جنيه - الآن فقط 12,000 جنيه',
        'complete_inspection' => 'فحص نظام كامل',
        'inspection_desc' => 'تقييم شامل لجميع أنظمة الحماية من الحريق بما في ذلك المضخات والرشاشات والإنذارات والطفايات ومعدات الطوارئ.',
        'total_value' => 'القيمة الإجمالية: 15,000 جنيه → السعر الخاص: 12,000 جنيه',
        'what_clients_say' => 'ماذا يقول عملاؤنا',
        'real_feedback' => 'تعليقات حقيقية من المنشآت التي اختارت خدمة التقييم لدينا',
        'testimonial_text' => 'حدد التقييم مشاكل حرجة كنا قد تجاهلناها لسنوات. ساعدت توصياتهم في اجتياز فحص الدفاع المدني من المحاولة الأولى.',
        'testimonial_name' => 'أحمد حسن',
        'testimonial_position' => 'مدير السلامة، دلتا إندستريز',
        'faq_title' => 'الأسئلة الشائعة',
        'faq_subtitle' => 'إجابات سريعة حول عرضنا الخاص',
        'dont_miss' => 'لا تفوت هذه الفرصة المحدودة الوقت',
        'book_discounted_assessment' => 'احجز تقييمك المخفض الآن ووفر 20% مع ضمان سلامة وامتثال منشأتك.',
        'claim_20_discount_final' => '🔴 احصل على خصم 20% الآن',
        'discount_label' => 'الخصم',
        'spots_left' => 'أماكن متبقية',
        'days_remaining' => 'أيام متبقية',
        'emergency_contact' => 'لطوارئ السلامة من الحريق العاجلة أو المساعدة الفورية:',
        'available_24_7' => 'متاح 24/7 لاستجابة الطوارئ'
    ]
];

$t = $translations[$lang];

// جلب محتوى الأقسام من قاعدة البيانات
$hero_section = fetchSections(5, 'hero', $lang, 1)[0] ?? null;
$benefits_section = fetchSections(5, 'benefits', $lang, 1)[0] ?? null;
$included_section = fetchSections(5, 'included', $lang, 1)[0] ?? null;
$testimonials_section = fetchSections(5, 'testimonials', $lang, 1)[0] ?? null;
$faq_section = fetchSections(5, 'faq', $lang, 1)[0] ?? null;
$final_cta_section = fetchSections(5, 'cta', $lang, 1)[0] ?? null;

// جلب إعدادات الموقع
$site_settings = getSiteSettings($lang);

// تخصيص SEO لهذه الصفحة
$page_title = $lang == 'ar' ? 'عرض محدود - سفنكس فاير' : 'Retargeting Landing - Sphinx Fire';
$page_description = $lang == 'ar' ? 'عرض محدود: خصم 20% على تقييم السلامة من الحريق الاحترافي للمنشآت الصناعية. مهندسون معتمدون، استجابة في نفس اليوم، وضمان الامتثال.' : 'Limited time offer: 20% off professional fire safety assessment for industrial facilities. Certified engineers, same-day response, and compliance guarantee.';
$page_keywords = $lang == 'ar' ? 'عرض السلامة من الحريق، خصم السلامة الصناعية، تقييم الحماية من الحريق، عرض خاص، وقت محدود، مصر' : 'fire safety offer, industrial safety discount, fire protection assessment, special offer, limited time, Egypt';
$og_title = $lang == 'ar' ? 'عرض خاص: خصم 20% على تقييم السلامة من الحريق | سفنكس فاير' : 'Special Offer: 20% Off Fire Safety Assessment | Sphinx Fire';
$og_description = $lang == 'ar' ? 'عرض محدود: تصرف الآن ووفر 20% على تقييم السلامة من الحريق الاحترافي. مهندسون معتمدون، استجابة في نفس اليوم، ضمان الامتثال.' : 'Limited time offer: Act now and save 20% on professional fire safety assessment. Certified engineers, same-day response, compliance guarantee.';
$og_image = 'https://sphinxfire.com/logo.png';

include 'includes/header.php';
?>
    <!-- Hero Section -->
    <section id="hero" class="hero-bg min-h-screen flex items-center justify-center text-white">
        <div class="max-w-6xl mx-auto px-6 py-20 md:py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column - Offer Details -->
                <div>
                    <div class="mb-6 animate-fade-in">
                        <div class="limited-spots inline-block mb-4">
                            <?php echo htmlspecialchars($t['limited_offer']); ?>
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold mb-6 animate-fade-in leading-tight">
                        <?php echo htmlspecialchars($hero_section['title'] ?? $t['save_20_percent']); ?>
                    </h1>
                    <h2 class="text-2xl md:text-3xl font-semibold mb-6 text-gray-200 animate-slide-up">
                        <?php echo htmlspecialchars($hero_section['subtitle'] ?? $t['protect_facility']); ?>
                    </h2>
                    <p class="text-xl mb-8 text-gray-300 animate-slide-up">
                        <?php echo htmlspecialchars($hero_section['content'] ?? $t['comprehensive_assessment']); ?>
                    </p>
                    <!-- Countdown Timer -->
                    <div class="mb-8 animate-slide-up">
                        <p class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($t['offer_expires']); ?></p>
                        <div class="countdown-timer flex space-x-4 text-3xl font-bold">
                            <div class="bg-brand-red rounded-lg p-3 w-20 text-center">
                                <span id="days">02</span>
                                <div class="text-xs font-normal">Days</div>
                            </div>
                            <div class="bg-brand-red rounded-lg p-3 w-20 text-center">
                                <span id="hours">12</span>
                                <div class="text-xs font-normal">Hours</div>
                            </div>
                            <div class="bg-brand-red rounded-lg p-3 w-20 text-center">
                                <span id="minutes">45</span>
                                <div class="text-xs font-normal">Minutes</div>
                            </div>
                            <div class="bg-brand-red rounded-lg p-3 w-20 text-center">
                                <span id="seconds">30</span>
                                <div class="text-xs font-normal">Seconds</div>
                            </div>
                        </div>
                    </div>
                    <div class="animate-bounce-in">
                        <a href="#offer-form" class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse inline-block">
                            <?php echo htmlspecialchars($t['claim_discount']); ?>
                        </a>
                    </div>
                </div>
                <!-- Right Column - Form -->
                <div id="offer-form" class="bg-white rounded-xl p-8 shadow-2xl relative">
                    <div class="discount-badge">-20%</div>
                    <h3 class="text-brand-black text-2xl font-bold mb-6 text-center">
                        <?php echo htmlspecialchars($t['book_discounted']); ?>
                    </h3>
                    <form id="conversion-form" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['full_name']); ?></label>
                            <input type="text" id="name" name="name" required 
                                   class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="<?php echo htmlspecialchars($t['full_name']); ?>">
                        </div>
                        <div>
                            <label for="company" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['company_name']); ?></label>
                            <input type="text" id="company" name="company" required 
                                   class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="<?php echo htmlspecialchars($t['company_name']); ?>">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['email_address']); ?></label>
                            <input type="email" id="email" name="email" required 
                                   class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="your.email@company.com">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['phone_number']); ?></label>
                            <input type="tel" id="phone" name="phone" required 
                                   class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   placeholder="+20 XXX XXX XXXX">
                        </div>
                        <div>
                            <label for="facility-type" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['facility_type']); ?></label>
                            <select id="facility-type" name="facility-type" required
                                    class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none">
                                <option value=""><?php echo htmlspecialchars($t['select_facility']); ?></option>
                                <option value="manufacturing"><?php echo htmlspecialchars($t['manufacturing']); ?></option>
                                <option value="warehouse"><?php echo htmlspecialchars($t['warehouse']); ?></option>
                                <option value="chemical"><?php echo htmlspecialchars($t['chemical']); ?></option>
                                <option value="retail"><?php echo htmlspecialchars($t['retail']); ?></option>
                                <option value="office"><?php echo htmlspecialchars($t['office']); ?></option>
                                <option value="other"><?php echo htmlspecialchars($t['other']); ?></option>
                            </select>
                        </div>
                        <div>
                            <label for="preferred-date" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['preferred_date']); ?></label>
                            <input type="date" id="preferred-date" name="preferred-date"
                                   class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                                   min="2025-07-09">
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="urgent" name="urgent" class="w-4 h-4 text-brand-red border-gray-300 rounded focus:ring-brand-red">
                            <label for="urgent" class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['urgent_assessment']); ?></label>
                        </div>
                        <button type="submit" 
                                class="w-full bg-brand-red text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition-colors cta-pulse">
                            <?php echo htmlspecialchars($t['claim_20_discount']); ?>
                        </button>
                        <p class="text-xs text-brand-gray text-center">
                            <?php echo htmlspecialchars($t['limited_time_offer']); ?>
                        </p>
                    </form>
                    <!-- Success Message -->
                    <div id="success-message" class="success-message bg-green-50 border border-green-200 rounded-lg p-6 mt-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-green-800"><?php echo htmlspecialchars($t['discount_claimed']); ?></h4>
                                <p class="text-green-700 text-sm"><?php echo htmlspecialchars($t['contact_4_hours']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Benefits Section -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($benefits_section['title'] ?? $t['why_choose']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($benefits_section['subtitle'] ?? $t['professional_evaluation']); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Benefit 1 -->
                <div class="feature-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['certified_engineers']); ?></h3>
                    <p class="text-brand-gray">
                        <?php echo htmlspecialchars($t['certified_desc']); ?>
                    </p>
                </div>
                <!-- Benefit 2 -->
                <div class="feature-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-file-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['comprehensive_report']); ?></h3>
                    <p class="text-brand-gray">
                        <?php echo htmlspecialchars($t['report_desc']); ?>
                    </p>
                </div>
                <!-- Benefit 3 -->
                <div class="feature-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['civil_defense_ready']); ?></h3>
                    <p class="text-brand-gray">
                        <?php echo htmlspecialchars($t['civil_defense_desc']); ?>
                    </p>
                </div>
            </div>
            <div class="mt-12 text-center">
                <a href="#offer-form" class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors inline-block">
                    <?php echo htmlspecialchars($t['claim_discount_now']); ?>
                </a>
            </div>
        </div>
    </section>
    <!-- What's Included Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($included_section['title'] ?? $t['whats_included']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($included_section['subtitle'] ?? $t['comprehensive_evaluation']); ?></p>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center mt-1 flex-shrink-0">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($t['complete_inspection']); ?></h3>
                                <p class="text-brand-gray">
                                    <?php echo htmlspecialchars($t['inspection_desc']); ?>
                                </p>
                            </div>
                        </div>
                        <!-- Additional items would go here -->
                    </div>
                </div>
                <div>
                    <div class="space-y-6">
                        <!-- Additional items would go here -->
                    </div>
                </div>
            </div>
            <div class="mt-12 text-center">
                <div class="inline-block bg-brand-red text-white px-6 py-3 rounded-full font-bold text-lg">
                    <?php echo htmlspecialchars($t['total_value']); ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($testimonials_section['title'] ?? $t['what_clients_say']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($testimonials_section['subtitle'] ?? $t['real_feedback']); ?></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card bg-gray-50 rounded-xl p-8 shadow-lg">
                    <div class="flex justify-center mb-4">
                        <div class="flex">
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                            <i class="fas fa-star text-yellow-400"></i>
                        </div>
                    </div>
                    <p class="text-brand-gray italic mb-6">
                        <?php echo htmlspecialchars($t['testimonial_text']); ?>
                    </p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($t['testimonial_name']); ?></p>
                            <p class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['testimonial_position']); ?></p>
                        </div>
                    </div>
                </div>
                <!-- Additional testimonials would go here -->
            </div>
        </div>
    </section>
    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($faq_section['title'] ?? $t['faq_title']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($faq_section['subtitle'] ?? $t['faq_subtitle']); ?></p>
            </div>
            <div class="space-y-4">
                <!-- FAQ items would go here -->
            </div>
        </div>
    </section>
    <!-- Final CTA -->
    <section class="py-20 blueprint-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <?php echo htmlspecialchars($final_cta_section['title'] ?? $t['dont_miss']); ?>
            </h2>
            <p class="text-xl mb-12 text-gray-300">
                <?php echo htmlspecialchars($final_cta_section['content'] ?? $t['book_discounted_assessment']); ?>
            </p>
            <div class="mb-8">
                <a href="#offer-form" class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse inline-block">
                    <?php echo htmlspecialchars($t['claim_20_discount_final']); ?>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">20%</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['discount_label']); ?></div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">5</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['spots_left']); ?></div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">3</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['days_remaining']); ?></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer الخاص بالصفحة -->
    <footer class="bg-brand-black text-white py-8">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-brand-red rounded-lg flex items-center justify-center">
                        <i class="fas fa-fire text-white"></i>
                    </div>
                    <span class="text-lg font-bold">Sphinx Fire</span>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-gray-400 text-sm">© 2025 Sphinx Fire. All rights reserved.</p>
                    <p class="text-gray-500 text-xs mt-1">Offer valid for industrial facilities in Egypt only. Terms and conditions apply.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Sticky CTA الخاص بالصفحة -->
    <div class="sticky-cta fixed bottom-0 left-0 w-full bg-brand-black py-4 px-6 z-40">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between">
            <div class="flex items-center mb-4 sm:mb-0">
                <div class="text-white mr-4">
                    <div class="text-xl font-bold">20% OFF - <?php echo ($lang == 'ar') ? 'عرض محدود' : 'Limited Time'; ?></div>
                    <div class="text-gray-300 text-sm"><?php echo ($lang == 'ar') ? '5 أماكن متبقية فقط' : 'Only 5 spots remaining'; ?></div>
                </div>
                <div class="countdown-timer flex space-x-2 text-white">
                    <div class="bg-brand-red rounded px-2 py-1">
                        <span class="sticky-days">02</span>d
                    </div>
                    <div class="bg-brand-red rounded px-2 py-1">
                        <span class="sticky-hours">12</span>h
                    </div>
                    <div class="bg-brand-red rounded px-2 py-1">
                        <span class="sticky-minutes">45</span>m
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="#offer-form" class="bg-brand-red text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    <?php echo ($lang == 'ar') ? 'احصل على الخصم الآن' : 'Claim Discount Now'; ?>
                </a>
                <a href="?lang=<?php echo ($lang == 'ar') ? 'en' : 'ar'; ?>" class="text-white hover:text-brand-red transition-colors font-semibold">
                    <?php echo ($lang == 'ar') ? 'English' : 'العربية'; ?>
                </a>
            </div>
        </div>
    </div>
    <!-- Exit Intent Modal -->
    <div class="exit-intent-modal fixed inset-0 bg-black bg-opacity-50 z-50 hidden" id="exit-modal">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content bg-white rounded-xl p-8 max-w-md relative">
                <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700" id="close-modal">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2"><?php echo ($lang == 'ar') ? 'انتظر! لا تفوت الفرصة!' : 'Wait! Don\'t Miss Out!'; ?></h3>
                    <p class="text-brand-gray"><?php echo ($lang == 'ar') ? 'خصم 20% لا يزال متاحاً، لكن الوقت ينفد!' : 'Your 20% discount is still available, but time is running out!'; ?></p>
                </div>
                <div class="mb-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-gift text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong><?php echo ($lang == 'ar') ? 'مكافأة:' : 'BONUS:'; ?></strong> 
                                    <?php echo ($lang == 'ar') ? 'احصل الآن واحصل على فحص طفايات حريق مجاني (قيمته 2000 جنيه)' : 'Claim now and get a FREE Fire Extinguisher Inspection (worth EGP 2,000)'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#offer-form" class="block w-full bg-brand-red text-white px-6 py-3 rounded-lg font-bold text-center hover:bg-red-700 transition-colors">
                    <?php echo ($lang == 'ar') ? 'نعم، أريد خصمي!' : 'Yes, I Want My Discount!'; ?>
                </a>
                <button id="no-thanks" class="w-full text-gray-500 mt-4 py-2 hover:text-gray-700">
                    <?php echo ($lang == 'ar') ? 'لا شكراً، سأدفع السعر الكامل' : 'No thanks, I\'ll pay full price'; ?>
                </button>
            </div>
        </div>
    </div>
    <!-- WhatsApp Float Button -->
    <div class="whatsapp-float">
        <a href="https://wa.me/20123456789?text=I'm%20interested%20in%20the%2020%%20discount%20on%20fire%20safety%20assessment" target="_blank" class="block">
            <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition-colors">
                <i class="fab fa-whatsapp text-white text-2xl"></i>
            </div>
        </a>
    </div>
    <script src="main.js"></script>
    <script>
    // ===== Retargeting Page Specific Scripts =====
    $(document).ready(function() {
        console.log('Sphinx Fire Retargeting page loaded! 🔥');
        
        // ===== Countdown Timer =====
        function updateCountdown() {
            // Set the date we're counting down to (3 days from now)
            const countDownDate = new Date();
            countDownDate.setDate(countDownDate.getDate() + 3);
            
            // Update the countdown every 1 second
            const x = setInterval(function() {
                // Get current date and time
                const now = new Date().getTime();
                
                // Find the distance between now and the countdown date
                const distance = countDownDate - now;
                
                // Time calculations for days, hours, minutes and seconds
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Display the result
                $('.countdown-timer .bg-brand-red span').each(function() {
                    const type = $(this).attr('id');
                    if (type === 'days' || $(this).hasClass('sticky-days')) {
                        $(this).text(days.toString().padStart(2, '0'));
                    } else if (type === 'hours' || $(this).hasClass('sticky-hours')) {
                        $(this).text(hours.toString().padStart(2, '0'));
                    } else if (type === 'minutes' || $(this).hasClass('sticky-minutes')) {
                        $(this).text(minutes.toString().padStart(2, '0'));
                    } else if (type === 'seconds') {
                        $(this).text(seconds.toString().padStart(2, '0'));
                    }
                });
                
                // If the countdown is finished, display expired message
                if (distance < 0) {
                    clearInterval(x);
                    $('.countdown-timer span').text('00');
                    $('.limited-spots').text('<?php echo ($lang == 'ar') ? 'انتهى العرض' : 'OFFER EXPIRED'; ?>').removeClass('pulse');
                    $('button[type="submit"]').text('<?php echo ($lang == 'ar') ? 'طلب تقييم عادي' : 'Request Regular Assessment'; ?>').prop('disabled', false);
                }
            }, 1000);
        }
        
        updateCountdown();
        
        // ===== Smooth Scrolling =====
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });
        
        // ===== Sticky CTA =====
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                $('.sticky-cta').addClass('show');
            } else {
                $('.sticky-cta').removeClass('show');
            }
        });
        
        // ===== Exit Intent Modal =====
        let showExitModal = true;
        let exitIntentShown = false;
        
        function handleExitIntent(e) {
            // Only show exit intent if:
            // 1. We haven't shown it before
            // 2. Mouse is leaving from the top of the page
            // 3. Form hasn't been submitted yet
            if (!exitIntentShown && e.clientY < 50 && $('#conversion-form').is(':visible')) {
                $('#exit-modal').removeClass('hidden').addClass('flex');
                exitIntentShown = true;
            }
        }
        
        // Only add exit intent for desktop
        if (window.innerWidth > 768) {
            document.addEventListener('mouseleave', handleExitIntent);
        }
        
        // Close modal
        $('#close-modal, #no-thanks').click(function() {
            $('#exit-modal').removeClass('flex').addClass('hidden');
        });
        
        // Modal CTA click
        $('#exit-modal a[href="#offer-form"]').click(function() {
            $('#exit-modal').removeClass('flex').addClass('hidden');
        });
        
        // ===== Form Validation =====
        function validateForm() {
            let isValid = true;
            
            // Validate name
            if ($('#name').val().trim() === '') {
                $('#name').addClass('border-red-500');
                isValid = false;
            } else {
                $('#name').removeClass('border-red-500');
            }
            
            // Validate company
            if ($('#company').val().trim() === '') {
                $('#company').addClass('border-red-500');
                isValid = false;
            } else {
                $('#company').removeClass('border-red-500');
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test($('#email').val())) {
                $('#email').addClass('border-red-500');
                isValid = false;
            } else {
                $('#email').removeClass('border-red-500');
            }
            
            // Validate phone
            const phoneRegex = /^\+20\d{10}$/;
            if (!phoneRegex.test($('#phone').val().replace(/\s/g, ''))) {
                $('#phone').addClass('border-red-500');
                isValid = false;
            } else {
                $('#phone').removeClass('border-red-500');
            }
            
            // Validate facility type
            if ($('#facility-type').val() === '') {
                $('#facility-type').addClass('border-red-500');
                isValid = false;
            } else {
                $('#facility-type').removeClass('border-red-500');
            }
            
            return isValid;
        }
        
        // ===== Form Submission =====
        $('#conversion-form').submit(function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                return;
            }
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.text('<?php echo ($lang == 'ar') ? 'جاري المعالجة...' : 'Processing...'; ?>').prop('disabled', true);
            
            // Collect form data
            const formData = {
                name: $('#name').val(),
                company: $('#company').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                facilityType: $('#facility-type').val(),
                preferredDate: $('#preferred-date').val() || null,
                urgent: $('#urgent').is(':checked'),
                timestamp: new Date().toISOString(),
                campaign: 'retargeting-20-off'
            };
            
            console.log('Form submitted:', formData);
            
            // Simulate form submission delay
            setTimeout(() => {
                // Show success message
                $('#success-message').show();
                $('#conversion-form').hide();
                
                // Scroll to success message
                $('html, body').animate({
                    scrollTop: $('#success-message').offset().top - 100
                }, 500);
                
            }, 1500);
        });
        
        // ===== Phone number formatting =====
        $('#phone').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.startsWith('20')) {
                value = '+' + value;
            } else if (value.startsWith('0')) {
                value = '+20' + value.substring(1);
            } else if (!value.startsWith('+20')) {
                value = '+20' + value;
            }
            $(this).val(value);
        });
        
        // ===== Show sticky CTA after 2 seconds =====
        setTimeout(function() {
            $('.sticky-cta').addClass('show');
        }, 2000);
        
        console.log('Retargeting page initialized! 🚀');
    });
    </script>
</body>
</html>