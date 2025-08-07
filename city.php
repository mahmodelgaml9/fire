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
        'serving_sadat' => '📍 SERVING SADAT INDUSTRIAL CITY',
        'fire_protection_sadat' => 'Fire Protection Systems in Sadat City',
        'certified_protection' => 'Certified protection, fast local response, zero logistics delays.',
        'looking_certified' => 'Looking for certified fire protection inside Sadat City? We\'re minutes away—not hours from Cairo.',
        'book_assessment' => '🔴 Book Free Site Assessment',
        'compliance_guide' => '⚪ See Compliance Guide',
        'why_local' => 'Why Choose a Local Provider Inside Sadat City?',
        'strategic_location' => 'Strategic location means better service, faster response, and lower costs',
        'faster_response' => 'Faster Response',
        'faster_desc' => 'We\'re minutes away—not hours from Cairo. Emergency calls answered within 30 minutes.',
        'response_time' => '30 Min Response',
        'lower_cost' => 'Lower Logistics Cost',
        'lower_cost_desc' => 'No transport fees or delays. Equipment delivery and installation at local rates.',
        'cost_savings' => '20% Cost Savings',
        'easier_inspections' => 'Easier Inspections',
        'easier_desc' => 'We speak your industrial authority\'s language and know local compliance requirements.',
        'pass_rate' => '100% Pass Rate',
        'continuous_support' => 'Continuous Support',
        'support_desc' => 'Post-installation check-ins, training, and maintenance support whenever you need it.',
        'support_24_7' => '24/7 Support',
        'complete_services' => 'Complete Fire Safety Services in Sadat City',
        'every_system' => 'Every system designed, installed, and maintained by local certified experts',
        'firefighting_systems' => 'Firefighting Systems',
        'firefighting_desc' => 'UL/FM certified pumps, foam suppression, sprinkler networks designed for Sadat City industrial facilities.',
        'request_service' => 'Request This Service',
        'fire_alarms' => 'Fire Alarms & Control',
        'alarms_desc' => 'Smart detection systems with SCADA integration for industrial automation in Sadat City.',
        'fire_extinguishers' => 'Fire Extinguishers',
        'extinguishers_desc' => 'Complete extinguisher supply and maintenance for all Sadat City industrial facility types.',
        'fire_pumps' => 'Fire Pumps',
        'pumps_desc' => 'High-pressure electric and diesel pumps with local installation and maintenance support.',
        'scada_integration' => 'SCADA Integration',
        'scada_desc' => 'Smart system integration with your existing industrial automation and monitoring systems.',
        'civil_defense_prep' => 'Civil Defense Preparation',
        'civil_defense_desc' => 'Complete documentation and system preparation for Sadat City civil defense inspections.',
        'local_success' => '✅ LOCAL SUCCESS STORY',
        'helped_factories' => 'We Helped 3 Plastic Factories in Sadat City Pass Inspection',
        'real_results' => 'Real results from real facilities in your industrial zone',
        'testimonial_quote' => 'Sphinx Fire installed our complete firefighting network in just 7 days. Being local made all the difference—no delays, no extra costs, and they knew exactly what our civil defense inspector would look for.',
        'testimonial_name' => 'Mohamed El-Shamy',
        'testimonial_position' => 'Production Manager, Sadat Plastics Industries',
        'days_complete' => 'Days to Complete',
        'first_try_pass' => 'First-Try Pass',
        'cost_savings_percent' => 'Cost Savings',
        'get_similar' => 'Get Similar Results →',
        'find_us_sadat' => 'We\'re Right Here in Sadat City',
        'strategic_location_desc' => 'Strategic location for fast response to all industrial facilities',
        'sadat_advantage' => 'Our Sadat City Advantage',
        'inside_zone' => 'Inside the Industrial Zone',
        'inside_desc' => 'Located in Block 15, we\'re at the heart of Sadat City\'s industrial district.',
        'response_radius' => '5-Minute Response Radius',
        'radius_desc' => 'Emergency calls from major factories reached within 5 minutes.',
        'equipment_storage' => 'Local Equipment Storage',
        'storage_desc' => 'UL/FM certified equipment stocked locally for immediate delivery.',
        'authority_relations' => 'Local Authority Relations',
        'relations_desc' => 'Direct relationships with Sadat City civil defense and industrial authority.',
        'get_directions_sadat' => '📍 Get Directions to Our Sadat Office',
        'fire_safety_solutions' => 'Fire Safety Solutions for Sadat Industrial City',
        'looking_pumps' => 'Looking for UL/FM fire pumps in Sadat City?',
        'pumps_intro' => 'Sphinx Fire provides complete fire protection systems specifically designed for industrial facilities in Sadat Industrial City. Our local presence ensures faster response times, lower logistics costs, and better understanding of local compliance requirements.',
        'civil_defense_approval' => 'Civil defense approval in Sadat Industrial Zone',
        'approval_desc' => 'requires proper documentation, certified equipment, and expert installation. Our team has successfully guided over 50 facilities through the inspection process with a 100% first-time pass rate. We understand the specific requirements for industrial facilities in Sadat City.',
        'best_company' => 'Best fire system company near Sadat Industrial Zone?',
        'company_desc' => 'Our strategic location inside the industrial district means we can respond to emergencies within minutes, not hours. From foam suppression systems for chemical storage to high-pressure pump networks for large manufacturing facilities, we deliver complete fire safety solutions tailored to Sadat City\'s industrial requirements.',
        'dont_wait_inspection' => 'Don\'t wait until inspection day. Get a free system design today.',
        'local_expertise' => 'Local expertise, certified equipment, and fast response—all from inside Sadat Industrial City.',
        'book_local_visit' => '🔴 Book a Local Visit',
        'talk_consultant' => '⚪ Talk to a Consultant Now',
        'emergency_response' => 'Emergency Response',
        'local_compliance' => 'Local Compliance',
        'cost_savings_label' => 'Cost Savings'
    ],
    'ar' => [
        'serving_sadat' => '📍 نخدم مدينة السادات الصناعية',
        'fire_protection_sadat' => 'أنظمة الحماية من الحريق في مدينة السادات',
        'certified_protection' => 'حماية معتمدة، استجابة محلية سريعة، بدون تأخير في اللوجستيات.',
        'looking_certified' => 'تبحث عن حماية حريق معتمدة داخل مدينة السادات؟ نحن على بعد دقائق—وليس ساعات من القاهرة.',
        'book_assessment' => '🔴 حجز تقييم موقع مجاني',
        'compliance_guide' => '⚪ راجع دليل الامتثال',
        'why_local' => 'لماذا تختار مزود محلي داخل مدينة السادات؟',
        'strategic_location' => 'الموقع الاستراتيجي يعني خدمة أفضل واستجابة أسرع وتكاليف أقل',
        'faster_response' => 'استجابة أسرع',
        'faster_desc' => 'نحن على بعد دقائق—وليس ساعات من القاهرة. المكالمات العاجلة يتم الرد عليها خلال 30 دقيقة.',
        'response_time' => 'استجابة 30 دقيقة',
        'lower_cost' => 'تكاليف لوجستية أقل',
        'lower_cost_desc' => 'بدون رسوم نقل أو تأخير. توصيل وتركيب المعدات بالأسعار المحلية.',
        'cost_savings' => 'توفير 20% في التكاليف',
        'easier_inspections' => 'فحوصات أسهل',
        'easier_desc' => 'نتحدث لغة السلطة الصناعية الخاصة بك ونعرف متطلبات الامتثال المحلية.',
        'pass_rate' => 'معدل نجاح 100%',
        'continuous_support' => 'دعم مستمر',
        'support_desc' => 'متابعة ما بعد التركيب وتدريب ودعم صيانة متى احتجت.',
        'support_24_7' => 'دعم 24/7',
        'complete_services' => 'خدمات السلامة من الحريق الكاملة في مدينة السادات',
        'every_system' => 'كل نظام مصمم ومركب ومحافظ عليه بواسطة خبراء محليين معتمدين',
        'firefighting_systems' => 'أنظمة مكافحة الحريق',
        'firefighting_desc' => 'مضخات معتمدة UL/FM، قمع رغوي، شبكات رشاشات مصممة للمنشآت الصناعية في مدينة السادات.',
        'request_service' => 'اطلب هذه الخدمة',
        'fire_alarms' => 'أنظمة إنذار الحريق والتحكم',
        'alarms_desc' => 'أنظمة كشف ذكية مع تكامل SCADA للأتمتة الصناعية في مدينة السادات.',
        'fire_extinguishers' => 'طفايات الحريق',
        'extinguishers_desc' => 'إمداد كامل للطفايات وصيانة لجميع أنواع المنشآت الصناعية في مدينة السادات.',
        'fire_pumps' => 'مضخات الحريق',
        'pumps_desc' => 'مضخات كهربائية وديزل عالية الضغط مع دعم التركيب والصيانة المحلية.',
        'scada_integration' => 'تكامل SCADA',
        'scada_desc' => 'تكامل نظام ذكي مع أنظمة الأتمتة والمراقبة الصناعية الموجودة لديك.',
        'civil_defense_prep' => 'تحضير الدفاع المدني',
        'civil_defense_desc' => 'توثيق كامل وتحضير نظام لفحوصات الدفاع المدني في مدينة السادات.',
        'local_success' => '✅ قصة نجاح محلية',
        'helped_factories' => 'ساعدنا 3 مصانع بلاستيك في مدينة السادات في اجتياز الفحص',
        'real_results' => 'نتائج حقيقية من منشآت حقيقية في منطقتك الصناعية',
        'testimonial_quote' => 'سفنكس فاير ركب شبكة مكافحة الحريق الكاملة لدينا في 7 أيام فقط. كونهم محليين أحدث كل الفرق—بدون تأخير، بدون تكاليف إضافية، وكانوا يعرفون بالضبط ما سيبحث عنه مفتش الدفاع المدني لدينا.',
        'testimonial_name' => 'محمد الشامي',
        'testimonial_position' => 'مدير الإنتاج، صناعات السادات للبلاستيك',
        'days_complete' => 'أيام للإكمال',
        'first_try_pass' => 'نجاح من المحاولة الأولى',
        'cost_savings_percent' => 'توفير التكاليف',
        'get_similar' => 'احصل على نتائج مماثلة →',
        'find_us_sadat' => 'نحن هنا في مدينة السادات',
        'strategic_location_desc' => 'موقع استراتيجي لاستجابة سريعة لجميع المنشآت الصناعية',
        'sadat_advantage' => 'ميزة مدينة السادات لدينا',
        'inside_zone' => 'داخل المنطقة الصناعية',
        'inside_desc' => 'يقع في بلوك 15، نحن في قلب المنطقة الصناعية لمدينة السادات.',
        'response_radius' => 'نصف قطر استجابة 5 دقائق',
        'radius_desc' => 'المكالمات العاجلة من المصانع الكبرى يتم الوصول إليها خلال 5 دقائق.',
        'equipment_storage' => 'تخزين معدات محلي',
        'storage_desc' => 'معدات معتمدة UL/FM مخزنة محلياً للتوصيل الفوري.',
        'authority_relations' => 'علاقات السلطة المحلية',
        'relations_desc' => 'علاقات مباشرة مع الدفاع المدني والسلطة الصناعية في مدينة السادات.',
        'get_directions_sadat' => '📍 احصل على الاتجاهات لمكتب السادات',
        'fire_safety_solutions' => 'حلول السلامة من الحريق لمدينة السادات الصناعية',
        'looking_pumps' => 'تبحث عن مضخات حريق UL/FM في مدينة السادات؟',
        'pumps_intro' => 'سفنكس فاير يوفر أنظمة حماية حريق كاملة مصممة خصيصاً للمنشآت الصناعية في مدينة السادات الصناعية. وجودنا المحلي يضمن أوقات استجابة أسرع وتكاليف لوجستية أقل وفهم أفضل لمتطلبات الامتثال المحلية.',
        'civil_defense_approval' => 'موافقة الدفاع المدني في المنطقة الصناعية السادات',
        'approval_desc' => 'تتطلب توثيق مناسب ومعدات معتمدة وتركيب خبير. فريقنا نجح في توجيه أكثر من 50 منشأة عبر عملية الفحص بمعدل نجاح 100% من المحاولة الأولى. نفهم المتطلبات المحددة للمنشآت الصناعية في مدينة السادات.',
        'best_company' => 'أفضل شركة أنظمة حريق قرب المنطقة الصناعية السادات؟',
        'company_desc' => 'موقعنا الاستراتيجي داخل المنطقة الصناعية يعني أننا نستطيع الاستجابة للطوارئ خلال دقائق، وليس ساعات. من أنظمة قمع الرغوة لتخزين المواد الكيميائية إلى شبكات المضخات عالية الضغط للمنشآت التصنيعية الكبيرة، نقدم حلول سلامة حريق كاملة مخصصة لمتطلبات مدينة السادات الصناعية.',
        'dont_wait_inspection' => 'لا تنتظر حتى يوم الفحص. احصل على تصميم نظام مجاني اليوم.',
        'local_expertise' => 'خبرة محلية ومعدات معتمدة واستجابة سريعة—كل ذلك من داخل مدينة السادات الصناعية.',
        'book_local_visit' => '🔴 احجز زيارة محلية',
        'talk_consultant' => '⚪ تحدث مع مستشار الآن',
        'emergency_response' => 'استجابة الطوارئ',
        'local_compliance' => 'الامتثال المحلي',
        'cost_savings_label' => 'توفير التكاليف'
    ]
];

$t = $translations[$lang];

// جلب محتوى الأقسام من قاعدة البيانات
$hero_section = fetchSections(6, 'hero', $lang, 1)[0] ?? null;
$local_advantages_section = fetchSections(6, 'local_advantages', $lang, 1)[0] ?? null;
$services_section = fetchSections(6, 'services', $lang, 1)[0] ?? null;
$local_project_section = fetchSections(6, 'local_project', $lang, 1)[0] ?? null;
$location_map_section = fetchSections(6, 'location_map', $lang, 1)[0] ?? null;
$seo_content_section = fetchSections(6, 'seo_content', $lang, 1)[0] ?? null;
$local_cta_section = fetchSections(6, 'local_cta', $lang, 1)[0] ?? null;

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
                <div class="location-badge mb-4">
                    <?php echo htmlspecialchars($t['serving_sadat']); ?>
                </div>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                <?php echo htmlspecialchars($hero_section['title'] ?? $t['fire_protection_sadat']); ?>
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
                <?php echo htmlspecialchars($hero_section['subtitle'] ?? $t['certified_protection']); ?>
            </h2>
            
            <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
                <?php echo htmlspecialchars($hero_section['content'] ?? $t['looking_certified']); ?>
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center animate-bounce-in">
                <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="book-assessment-btn">
                    <?php echo htmlspecialchars($t['book_assessment']); ?>
                </button>
                <button class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="compliance-guide-btn">
                    <?php echo htmlspecialchars($t['compliance_guide']); ?>
                </button>
            </div>
        </div>
        
        <!-- Scroll Cue -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
            <div class="bounce-arrow text-white text-2xl">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </section>

    <!-- Why Local Matters -->
    <section id="local-advantages" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($local_advantages_section['title'] ?? $t['why_local']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($local_advantages_section['subtitle'] ?? $t['strategic_location']); ?></p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Faster Response -->
                <div class="advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-rocket text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['faster_response']); ?></h3>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($t['faster_desc']); ?>
                    </p>
                    <div class="text-brand-red font-bold text-lg"><?php echo htmlspecialchars($t['response_time']); ?></div>
                </div>
                
                <!-- Lower Logistics Cost -->
                <div class="advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-dollar-sign text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['lower_cost']); ?></h3>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($t['lower_cost_desc']); ?>
                    </p>
                    <div class="text-brand-red font-bold text-lg"><?php echo htmlspecialchars($t['cost_savings']); ?></div>
                </div>
                
                <!-- Easier Inspections -->
                <div class="advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clipboard-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['easier_inspections']); ?></h3>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($t['easier_desc']); ?>
                    </p>
                    <div class="text-brand-red font-bold text-lg"><?php echo htmlspecialchars($t['pass_rate']); ?></div>
                </div>
                
                <!-- Continuous Support -->
                <div class="advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-4"><?php echo htmlspecialchars($t['continuous_support']); ?></h3>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($t['support_desc']); ?>
                    </p>
                    <div class="text-brand-red font-bold text-lg"><?php echo htmlspecialchars($t['support_24_7']); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Offered in Sadat City -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($services_section['title'] ?? $t['complete_services']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($services_section['subtitle'] ?? $t['every_system']); ?></p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Firefighting Systems -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-fire-extinguisher text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($t['firefighting_systems']); ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        <?php echo htmlspecialchars($t['firefighting_desc']); ?>
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['request_service']); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Fire Alarms & Control -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-bell text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($t['fire_alarms']); ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        <?php echo htmlspecialchars($t['alarms_desc']); ?>
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['request_service']); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Fire Extinguishers -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-spray-can text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($t['fire_extinguishers']); ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        <?php echo htmlspecialchars($t['extinguishers_desc']); ?>
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['request_service']); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Fire Pumps -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-cogs text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($t['fire_pumps']); ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        <?php echo htmlspecialchars($t['pumps_desc']); ?>
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['request_service']); ?>
                        </button>
                    </div>
                </div>
                
                <!-- SCADA Integration -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-network-wired text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($t['scada_integration']); ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        <?php echo htmlspecialchars($t['scada_desc']); ?>
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['request_service']); ?>
                        </button>
                    </div>
                </div>
                
                <!-- Civil Defense Preparation -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($t['civil_defense_prep']); ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        <?php echo htmlspecialchars($t['civil_defense_desc']); ?>
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['request_service']); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Local Project Success Story -->
    <section id="local-project" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="inline-block bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <?php echo htmlspecialchars($t['local_success']); ?>
                </div>
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($local_project_section['title'] ?? $t['helped_factories']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($local_project_section['subtitle'] ?? $t['real_results']); ?></p>
            </div>
            
            <div class="testimonial-card rounded-xl p-8 shadow-xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <blockquote class="text-2xl text-brand-gray italic mb-6">
                            <?php echo htmlspecialchars($t['testimonial_quote']); ?>
                        </blockquote>
                        
                        <div class="mb-6">
                            <p class="font-semibold text-lg"><?php echo htmlspecialchars($t['testimonial_name']); ?></p>
                            <p class="text-brand-gray"><?php echo htmlspecialchars($t['testimonial_position']); ?></p>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-6 mb-8">
                            <div class="text-center">
                                <div class="stats-counter">7</div>
                                <div class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['days_complete']); ?></div>
                            </div>
                            <div class="text-center">
                                <div class="stats-counter">100%</div>
                                <div class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['first_try_pass']); ?></div>
                            </div>
                            <div class="text-center">
                                <div class="stats-counter">15%</div>
                                <div class="text-sm text-brand-gray"><?php echo htmlspecialchars($t['cost_savings_percent']); ?></div>
                            </div>
                        </div>
                        
                        <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo htmlspecialchars($t['get_similar']); ?>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <img src="https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop" 
                             alt="Sadat Plastics Factory Fire System Installation" 
                             class="w-full h-80 object-cover rounded-lg shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-black/50 to-transparent rounded-lg"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <p class="text-sm">UL/FM certified system installation at Sadat Plastics Industries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Map -->
    <section id="location-map" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo htmlspecialchars($location_map_section['title'] ?? $t['find_us_sadat']); ?></h2>
                <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($location_map_section['subtitle'] ?? $t['strategic_location_desc']); ?></p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-semibold mb-6"><?php echo htmlspecialchars($t['sadat_advantage']); ?></h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-map-marker-alt text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['inside_zone']); ?></p>
                                <p class="text-brand-gray"><?php echo htmlspecialchars($t['inside_desc']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-clock text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['response_radius']); ?></p>
                                <p class="text-brand-gray"><?php echo htmlspecialchars($t['radius_desc']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-truck text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['equipment_storage']); ?></p>
                                <p class="text-brand-gray"><?php echo htmlspecialchars($t['storage_desc']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-handshake text-white text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-brand-black"><?php echo htmlspecialchars($t['authority_relations']); ?></p>
                                <p class="text-brand-gray"><?php echo htmlspecialchars($t['relations_desc']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" onclick="openGoogleMaps()">
                            <?php echo htmlspecialchars($t['get_directions_sadat']); ?>
                        </button>
                    </div>
                </div>
                
                <div class="map-container rounded-xl overflow-hidden shadow-lg">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3418.7!2d30.8497!3d30.3753!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDIyJzMxLjEiTiAzMMKwNTAnNTguOSJF!5e0!3m2!1sen!2seg!4v1234567890"
                        width="100%" 
                        height="400" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Sphinx Fire Location in Sadat Industrial City">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- SEO Content Block -->
    <section id="seo-content" class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($seo_content_section['title'] ?? $t['fire_safety_solutions']); ?></h2>
            </div>
            
            <div class="prose prose-lg max-w-none text-brand-gray">
                <p class="text-xl leading-relaxed mb-6">
                    <strong><?php echo htmlspecialchars($t['looking_pumps']); ?></strong> <?php echo htmlspecialchars($t['pumps_intro']); ?>
                </p>
                
                <p class="text-lg leading-relaxed mb-6">
                    <strong><?php echo htmlspecialchars($t['civil_defense_approval']); ?></strong> <?php echo htmlspecialchars($t['approval_desc']); ?>
                </p>
                
                <p class="text-lg leading-relaxed">
                    <strong><?php echo htmlspecialchars($t['best_company']); ?></strong> <?php echo htmlspecialchars($t['company_desc']); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Local CTA -->
    <section id="local-cta" class="py-20 blueprint-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
        
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <?php echo htmlspecialchars($local_cta_section['title'] ?? $t['dont_wait_inspection']); ?>
            </h2>
            <p class="text-xl mb-12 text-gray-300">
                <?php echo htmlspecialchars($local_cta_section['content'] ?? $t['local_expertise']); ?>
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center mb-8">
                <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" id="book-local-visit-btn">
                    <?php echo htmlspecialchars($t['book_local_visit']); ?>
                </button>
                <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="talk-consultant-btn">
                    <?php echo htmlspecialchars($t['talk_consultant']); ?>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">30 Min</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['emergency_response']); ?></div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">100%</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['local_compliance']); ?></div>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <div class="text-2xl font-bold text-brand-red">20%</div>
                    <div class="text-sm text-gray-300"><?php echo htmlspecialchars($t['cost_savings_label']); ?></div>
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