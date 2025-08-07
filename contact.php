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
        'urgent_response' => '🚨 URGENT RESPONSE AVAILABLE',
        'lets_talk_safety' => "Let's Talk Safety.",
        'just_call_away' => "We're Just a Call Away.",
        'respond_fast' => "We respond fast because your facility can't wait. Contact Sphinx Fire today.",
        'request_site_visit' => '🔴 Request Site Visit',
        'download_profile' => '⚪ Download Company Profile',
        'response_guarantee' => '⚡ 24-Hour Response Guarantee',
        'multiple_ways' => 'Multiple Ways to Reach Us',
        'choose_method' => 'Choose the method that works best for your urgent needs',
        'visit_office' => 'Visit Our Office',
        'business_hours' => 'Business Hours',
        'saturday_thursday' => 'Saturday - Thursday',
        'hours' => '9:00 AM - 6:00 PM',
        'get_directions' => 'Get Directions',
        'call_message' => 'Call or Message',
        'available_emergency' => 'Available for emergency calls',
        'whatsapp_support' => '24/7 WhatsApp support',
        'call_now' => '📞 Call Now',
        'whatsapp' => '💬 WhatsApp',
        'send_email' => 'Send Email',
        'response_time' => 'Response Time',
        'within_4_hours' => 'Within 4 hours during business days',
        'send_email_btn' => '✉️ Send Email',
        'request_callback' => 'Request a Callback or Consultation',
        'fill_form' => 'Fill out this form and our expert team will contact you within 24 hours to discuss your fire safety requirements and schedule a free site assessment.',
        'free_consultation' => 'Free consultation and site assessment',
        'custom_solution' => 'Custom solution design and quote',
        'compliance_guidance' => 'Compliance and certification guidance',
        'no_obligation' => 'No obligation - completely free',
        'emergency_contact' => 'Emergency Contact',
        'urgent_emergencies' => 'For urgent fire safety emergencies or immediate assistance:',
        'available_24_7' => 'Available 24/7 for emergency response',
        'full_name' => 'Full Name *',
        'company_name' => 'Company Name *',
        'email_address' => 'Email Address *',
        'phone_number' => 'Phone Number *',
        'facility_type' => 'Facility Type',
        'select_facility' => 'Select your facility type',
        'manufacturing' => 'Manufacturing Plant',
        'warehouse' => 'Warehouse/Distribution',
        'chemical' => 'Chemical Processing',
        'retail' => 'Retail/Shopping Center',
        'office' => 'Office Building',
        'other' => 'Other Industrial Facility',
        'need_help' => 'I Need Help With *',
        'select_request' => 'Select your request type',
        'site_visit' => 'Free Site Visit & Assessment',
        'quote' => 'System Design & Quote',
        'emergency' => 'Emergency Fire Safety Issue',
        'maintenance' => 'Maintenance & Service',
        'consultation' => 'General Consultation',
        'compliance' => 'Compliance & Certification',
        'additional_details' => 'Additional Details',
        'tell_us_more' => 'Tell us more about your facility, current systems, or specific requirements...',
        'urgent_request' => 'This is an urgent request requiring immediate attention',
        'send_request' => '🔴 Send Request - Get Response in 24 Hours',
        'form_agreement' => 'By submitting this form, you agree to be contacted by Sphinx Fire regarding your fire safety requirements.',
        'request_submitted' => 'Request Submitted Successfully!',
        'contact_24_hours' => 'Our team will contact you within 24 hours. For urgent matters, call +20 123 456 7890.',
        'find_us' => 'Find Us in Sadat City',
        'strategic_location' => 'Strategically located in Egypt\'s industrial hub for fast response',
        'location_matters' => 'Why Our Location Matters',
        'central_location' => 'Central Industrial Location',
        'central_desc' => 'Located in the heart of Sadat City\'s industrial zone, we\'re minutes away from major manufacturing facilities.',
        'rapid_response' => 'Rapid Response Times',
        'rapid_desc' => 'Our proximity to industrial facilities means faster emergency response and reduced service costs.',
        'equipment_access' => 'Easy Equipment Access',
        'equipment_desc' => 'Direct access to major transportation routes for efficient equipment delivery and installation.',
        'local_knowledge' => 'Local Industry Knowledge',
        'local_desc' => 'Deep understanding of local industrial requirements and regulatory environment.',
        'get_directions_office' => '📍 Get Directions to Our Office',
        'based_zone' => "We're based inside the zone. We're closer than you think.",
        'dont_wait' => "Don't wait for an emergency. Get professional fire safety assessment today.",
        'book_assessment' => '🔴 Book Free Site Assessment',
        'response_time_label' => 'Response Time',
        'free_assessment' => 'Free Assessment',
        'projects_completed' => 'Projects Completed'
    ],
    'ar' => [
        'urgent_response' => '🚨 استجابة عاجلة متاحة',
        'lets_talk_safety' => 'دعنا نتحدث عن السلامة.',
        'just_call_away' => 'نحن على بعد مكالمة واحدة.',
        'respond_fast' => 'نستجيب بسرعة لأن منشأتك لا تستطيع الانتظار. اتصل بسفنكس فاير اليوم.',
        'request_site_visit' => '🔴 طلب زيارة موقع',
        'download_profile' => '⚪ تحميل الملف التعريفي للشركة',
        'response_guarantee' => '⚡ ضمان استجابة 24 ساعة',
        'multiple_ways' => 'طرق متعددة للوصول إلينا',
        'choose_method' => 'اختر الطريقة التي تناسب احتياجاتك العاجلة',
        'visit_office' => 'زيارة مكتبنا',
        'business_hours' => 'ساعات العمل',
        'saturday_thursday' => 'السبت - الخميس',
        'hours' => '9:00 ص - 6:00 م',
        'get_directions' => 'احصل على الاتجاهات',
        'call_message' => 'اتصل أو راسل',
        'available_emergency' => 'متاح للمكالمات العاجلة',
        'whatsapp_support' => 'دعم واتساب 24/7',
        'call_now' => '📞 اتصل الآن',
        'whatsapp' => '💬 واتساب',
        'send_email' => 'إرسال بريد إلكتروني',
        'response_time' => 'وقت الاستجابة',
        'within_4_hours' => 'خلال 4 ساعات في أيام العمل',
        'send_email_btn' => '✉️ إرسال بريد إلكتروني',
        'request_callback' => 'طلب اتصال أو استشارة',
        'fill_form' => 'املأ هذا النموذج وسيتصل بك فريقنا الخبير خلال 24 ساعة لمناقشة متطلبات السلامة من الحريق وجدولة تقييم موقع مجاني.',
        'free_consultation' => 'استشارة مجانية وتقييم موقع',
        'custom_solution' => 'تصميم حل مخصص وعرض سعر',
        'compliance_guidance' => 'إرشادات الامتثال والشهادات',
        'no_obligation' => 'بدون التزام - مجاني تماماً',
        'emergency_contact' => 'اتصال الطوارئ',
        'urgent_emergencies' => 'لطوارئ السلامة من الحريق العاجلة أو المساعدة الفورية:',
        'available_24_7' => 'متاح 24/7 لاستجابة الطوارئ',
        'full_name' => 'الاسم الكامل *',
        'company_name' => 'اسم الشركة *',
        'email_address' => 'عنوان البريد الإلكتروني *',
        'phone_number' => 'رقم الهاتف *',
        'facility_type' => 'نوع المنشأة',
        'select_facility' => 'اختر نوع منشأتك',
        'manufacturing' => 'مصنع تصنيع',
        'warehouse' => 'مستودع/توزيع',
        'chemical' => 'معالجة كيميائية',
        'retail' => 'بيع بالتجزئة/مركز تسوق',
        'office' => 'مبنى مكتبي',
        'other' => 'منشأة صناعية أخرى',
        'need_help' => 'أحتاج مساعدة في *',
        'select_request' => 'اختر نوع طلبك',
        'site_visit' => 'زيارة موقع مجانية وتقييم',
        'quote' => 'تصميم نظام وعرض سعر',
        'emergency' => 'مشكلة طوارئ السلامة من الحريق',
        'maintenance' => 'صيانة وخدمة',
        'consultation' => 'استشارة عامة',
        'compliance' => 'امتثال وشهادات',
        'additional_details' => 'تفاصيل إضافية',
        'tell_us_more' => 'أخبرنا المزيد عن منشأتك والأنظمة الحالية أو المتطلبات المحددة...',
        'urgent_request' => 'هذا طلب عاجل يتطلب اهتماماً فورياً',
        'send_request' => '🔴 إرسال الطلب - احصل على استجابة في 24 ساعة',
        'form_agreement' => 'بإرسال هذا النموذج، توافق على أن يتصل بك سفنكس فاير بخصوص متطلبات السلامة من الحريق.',
        'request_submitted' => 'تم إرسال الطلب بنجاح!',
        'contact_24_hours' => 'سيتصل بك فريقنا خلال 24 ساعة. للمسائل العاجلة، اتصل بـ +20 123 456 7890.',
        'find_us' => 'اعثر علينا في مدينة السادات',
        'strategic_location' => 'موقع استراتيجي في مركز مصر الصناعي لاستجابة سريعة',
        'location_matters' => 'لماذا يهم موقعنا',
        'central_location' => 'موقع صناعي مركزي',
        'central_desc' => 'يقع في قلب المنطقة الصناعية لمدينة السادات، نحن على بعد دقائق من المنشآت الصناعية الكبرى.',
        'rapid_response' => 'أوقات استجابة سريعة',
        'rapid_desc' => 'قربنا من المنشآت الصناعية يعني استجابة طوارئ أسرع وتكاليف خدمة أقل.',
        'equipment_access' => 'وصول سهل للمعدات',
        'equipment_desc' => 'وصول مباشر لطرق النقل الرئيسية لتوصيل وتركيب المعدات بكفاءة.',
        'local_knowledge' => 'معرفة الصناعة المحلية',
        'local_desc' => 'فهم عميق للمتطلبات الصناعية المحلية والبيئة التنظيمية.',
        'get_directions_office' => '📍 احصل على الاتجاهات لمكتبنا',
        'based_zone' => 'نحن داخل المنطقة. نحن أقرب مما تعتقد.',
        'dont_wait' => 'لا تنتظر حتى الطوارئ. احصل على تقييم سلامة حريق احترافي اليوم.',
        'book_assessment' => '🔴 حجز تقييم موقع مجاني',
        'response_time_label' => 'وقت الاستجابة',
        'free_assessment' => 'تقييم مجاني',
        'projects_completed' => 'مشاريع مكتملة'
    ]
];

$t = $translations[$lang];

// جلب محتوى الأقسام من قاعدة البيانات
$hero_section = fetchSections(4, 'hero', $lang, 1)[0] ?? null;
$contact_options_section = fetchSections(4, 'contact_options', $lang, 1)[0] ?? null;
$contact_form_section = fetchSections(4, 'contact_form', $lang, 1)[0] ?? null;
$location_section = fetchSections(4, 'location', $lang, 1)[0] ?? null;
$final_cta_section = fetchSections(4, 'cta', $lang, 1)[0] ?? null;

// جلب إعدادات الموقع
$site_settings = getSiteSettings($lang);

include 'includes/header.php';
include 'includes/navbar.php';
?>

    <!-- Hero Section -->
    <section id="hero" class="hero-bg relative h-screen flex items-center justify-center text-white">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
        
        <div class="relative z-10 text-center max-w-5xl px-6">
            <div class="mb-6 animate-fade-in">
                <div class="urgent-badge inline-block bg-brand-red text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <?php echo htmlspecialchars($t['urgent_response']); ?>
                </div>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                <?php echo htmlspecialchars($hero_section['title'] ?? $t['lets_talk_safety']); ?>
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
                <?php echo htmlspecialchars($hero_section['subtitle'] ?? $t['just_call_away']); ?>
            </h2>
            
            <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
                <?php echo htmlspecialchars($hero_section['content'] ?? $t['respond_fast']); ?>
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center animate-bounce-in">
                <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="request-visit-btn">
                    <?php echo htmlspecialchars($t['request_site_visit']); ?>
                </button>
                <button class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="download-profile-btn">
                    <?php echo htmlspecialchars($t['download_profile']); ?>
                </button>
            </div>
            
            <!-- Response Time Badge -->
            <div class="mt-8 animate-zoom-in">
                <div class="response-time inline-block text-white px-6 py-3 rounded-full font-semibold">
                    <?php echo htmlspecialchars($t['response_guarantee']); ?>
                </div>
            </div>
        </div>
        
        <!-- Scroll Cue -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
            <div class="bounce-arrow text-white text-2xl">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Contact Options -->
    <section id="contact-options" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($contact_options_section['title'] ?? $t['multiple_ways']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($contact_options_section['subtitle'] ?? $t['choose_method']); ?></p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Address -->
                <div class="contact-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4"><?php echo htmlspecialchars($t['visit_office']); ?></h3>
                    <div class="text-brand-gray mb-6">
                        <p class="font-semibold mb-2"><?php echo htmlspecialchars($site_settings['address_line1'] ?? 'Industrial Zone, Block 15'); ?></p>
                        <p><?php echo htmlspecialchars($site_settings['address_line2'] ?? 'Sadat City, Monufia'); ?></p>
                        <p><?php echo htmlspecialchars($site_settings['address_line3'] ?? 'Egypt'); ?></p>
                    </div>
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <p class="text-sm font-semibold text-brand-red"><?php echo htmlspecialchars($t['business_hours']); ?></p>
                        <p class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['saturday_thursday']); ?></p>
                        <p class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['hours']); ?></p>
                    </div>
                    <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" onclick="openGoogleMaps()">
                        <?php echo htmlspecialchars($t['get_directions']); ?>
                    </button>
                </div>
                
                <!-- Phone & WhatsApp -->
                <div class="contact-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4"><?php echo htmlspecialchars($t['call_message']); ?></h3>
                    <div class="text-brand-gray mb-6">
                        <p class="font-semibold mb-2 text-2xl"><?php echo htmlspecialchars($site_settings['phone'] ?? '+20 123 456 7890'); ?></p>
                        <p class="text-sm"><?php echo htmlspecialchars($t['available_emergency']); ?></p>
                        <p class="text-sm"><?php echo htmlspecialchars($t['whatsapp_support']); ?></p>
                    </div>
                    <div class="space-y-3">
                        <button class="w-full bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" onclick="makeCall()">
                            <?php echo htmlspecialchars($t['call_now']); ?>
                        </button>
                        <button class="w-full bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors" onclick="openWhatsApp()">
                            <?php echo htmlspecialchars($t['whatsapp']); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="contact-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4"><?php echo htmlspecialchars($t['send_email']); ?></h3>
                    <div class="text-brand-gray mb-6">
                        <p class="font-semibold mb-2"><?php echo htmlspecialchars($site_settings['email'] ?? 'info@sphinxfire.com'); ?></p>
                        <p class="text-sm">For detailed inquiries</p>
                        <p class="text-sm">Technical specifications</p>
                        <p class="text-sm">Project documentation</p>
                    </div>
                    <div class="bg-white rounded-lg p-4 mb-4">
                        <p class="text-sm font-semibold text-brand-red"><?php echo htmlspecialchars($t['response_time']); ?></p>
                        <p class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['within_4_hours']); ?></p>
                    </div>
                    <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" onclick="sendEmail()">
                        <?php echo htmlspecialchars($t['send_email_btn']); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section id="contact-form" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                <!-- Form Info -->
                <div>
                    <h2 class="text-4xl font-bold mb-6"><?php echo htmlspecialchars($contact_form_section['title'] ?? $t['request_callback']); ?></h2>
                    <p class="text-xl text-brand-gray mb-8 leading-relaxed">
                        <?php echo htmlspecialchars($contact_form_section['content'] ?? $t['fill_form']); ?>
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-lg font-semibold"><?php echo htmlspecialchars($t['free_consultation']); ?></span>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-lg font-semibold"><?php echo htmlspecialchars($t['custom_solution']); ?></span>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-lg font-semibold"><?php echo htmlspecialchars($t['compliance_guidance']); ?></span>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-lg font-semibold"><?php echo htmlspecialchars($t['no_obligation']); ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-8 bg-white rounded-lg p-6 shadow-md">
                        <h4 class="font-semibold text-brand-red mb-2"><?php echo htmlspecialchars($t['emergency_contact']); ?></h4>
                        <p class="text-brand-gray text-sm mb-2">
                            <?php echo htmlspecialchars($t['urgent_emergencies']); ?>
                        </p>
                        <p class="font-semibold text-lg">📞 <?php echo htmlspecialchars($site_settings['phone'] ?? '+20 123 456 7890'); ?></p>
                        <p class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['available_24_7']); ?></p>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="form-container bg-white rounded-xl p-8 shadow-xl">
                    <form id="contact-form-main" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['full_name']); ?></label>
                                <input type="text" id="name" name="name" required 
                                       class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                                       placeholder="<?php echo htmlspecialchars($t['full_name']); ?>">
                            </div>
                            
                            <div>
                                <label for="company" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['company_name']); ?></label>
                                <input type="text" id="company" name="company" required 
                                       class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                                       placeholder="<?php echo htmlspecialchars($t['company_name']); ?>">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['email_address']); ?></label>
                                <input type="email" id="email" name="email" required 
                                       class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                                       placeholder="your.email@company.com">
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['phone_number']); ?></label>
                                <input type="tel" id="phone" name="phone" required 
                                       class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                                       placeholder="+20 XXX XXX XXXX">
                            </div>
                        </div>
                        
                        <div>
                            <label for="facility-type" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['facility_type']); ?></label>
                            <select id="facility-type" name="facility-type" 
                                    class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20">
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
                            <label for="request-type" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['need_help']); ?></label>
                            <select id="request-type" name="request-type" required 
                                    class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20">
                                <option value=""><?php echo htmlspecialchars($t['select_request']); ?></option>
                                <option value="site-visit"><?php echo htmlspecialchars($t['site_visit']); ?></option>
                                <option value="quote"><?php echo htmlspecialchars($t['quote']); ?></option>
                                <option value="emergency"><?php echo htmlspecialchars($t['emergency']); ?></option>
                                <option value="maintenance"><?php echo htmlspecialchars($t['maintenance']); ?></option>
                                <option value="consultation"><?php echo htmlspecialchars($t['consultation']); ?></option>
                                <option value="compliance"><?php echo htmlspecialchars($t['compliance']); ?></option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="message" class="block text-sm font-semibold text-brand-black mb-2"><?php echo htmlspecialchars($t['additional_details']); ?></label>
                            <textarea id="message" name="message" rows="4" 
                                      class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                                      placeholder="<?php echo htmlspecialchars($t['tell_us_more']); ?>"></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="urgent" name="urgent" class="w-4 h-4 text-brand-red border-gray-300 rounded focus:ring-brand-red">
                            <label for="urgent" class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['urgent_request']); ?></label>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-brand-red text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['send_request']); ?>
                        </button>
                        
                        <p class="text-xs text-brand-gray text-center">
                            <?php echo htmlspecialchars($t['form_agreement']); ?>
                        </p>
                    </form>
                    
                    <!-- Success Message -->
                    <div id="success-message" class="success-message bg-green-50 border border-green-200 rounded-lg p-6 mt-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-green-800"><?php echo htmlspecialchars($t['request_submitted']); ?></h4>
                                <p class="text-green-700 text-sm"><?php echo htmlspecialchars($t['contact_24_hours']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Maps -->
    <section id="location" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($location_section['title'] ?? $t['find_us']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($location_section['subtitle'] ?? $t['strategic_location']); ?></p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-semibold mb-6"><?php echo htmlspecialchars($t['location_matters']); ?></h3>
                    <div class="space-y-4 text-brand-gray">
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-map-marker-alt text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['central_location']); ?></p>
                                <p><?php echo htmlspecialchars($t['central_desc']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-clock text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['rapid_response']); ?></p>
                                <p><?php echo htmlspecialchars($t['rapid_desc']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-truck text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['equipment_access']); ?></p>
                                <p><?php echo htmlspecialchars($t['equipment_desc']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-handshake text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['local_knowledge']); ?></p>
                                <p><?php echo htmlspecialchars($t['local_desc']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" onclick="openGoogleMaps()">
                            <?php echo htmlspecialchars($t['get_directions_office']); ?>
                        </button>
                    </div>
                </div>
                
                <div class="map-container rounded-xl overflow-hidden shadow-lg">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3418.7!2d30.8!3d30.4!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDI0JzAwLjAiTiAzMMKwNDgnMDAuMCJF!5e0!3m2!1sen!2seg!4v1234567890"
                        width="100%" 
                        height="400" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Sphinx Fire Location in Sadat City">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section id="final-cta" class="py-20 blueprint-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
        
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <?php echo htmlspecialchars($final_cta_section['title'] ?? $t['based_zone']); ?>
            </h2>
            <p class="text-xl mb-12 text-gray-300">
                <?php echo htmlspecialchars($final_cta_section['content'] ?? $t['dont_wait']); ?>
            </p>
            
            <div class="mb-8">
                <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" id="book-assessment-btn">
                    <?php echo htmlspecialchars($t['book_assessment']); ?>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">24 Hours</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['response_time_label']); ?></div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">100%</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['free_assessment']); ?></div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">50+</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['projects_completed']); ?></div>
                </div>
            </div>
        </div>
    </section>

<?php
include 'includes/footer.php';
include 'includes/whatsapp-float.php';
include 'includes/sticky-cta.php';
include 'includes/scripts.php';
?> 
</body>