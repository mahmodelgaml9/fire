<?php
require_once 'config.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';

// تحديد page_id للخدمات
$page_id = 3;

// جلب بيانات الصفحة الديناميكية
$hero = fetchSections($page_id, 'hero', $lang, 1);
$services = fetchServices($lang);
$serviceCategories = fetchCategories('service', $lang);
$advantages = fetchSections($page_id, 'advantage', $lang);
$technicalDetails = fetchSections($page_id, 'technical', $lang);
$faq = fetchSections($page_id, 'faq', $lang);
$cta = fetchSections($page_id, 'cta', $lang, 1);

// ترجمات الصفحة
$translations = [
    'en' => [
        'complete_fire_safety' => 'Complete Fire Safety Services',
        'comprehensive_protection' => 'From design to installation to maintenance - we deliver comprehensive protection solutions.',
        'download_catalog' => '🔴 Download Our Full Catalog',
        'compare_systems' => '⚪ Compare Systems →',
        'complete_solutions' => 'Complete Fire Safety Solutions',
        'certified_experts' => 'Every system designed, installed, and maintained by certified experts',
        'firefighting_systems' => 'Firefighting Systems',
        'alarm_detection' => 'Alarm & Detection',
        'fire_extinguishers' => 'Fire Extinguishers',
        'ppe_equipment' => 'PPE Equipment',
        'safety_consulting' => 'Safety Consulting',
        'maintenance_services' => 'Maintenance Services',
        'request_design' => 'Request Design',
        'get_quote' => 'Get Quote',
        'view_catalog' => 'View Catalog',
        'browse_ppe' => 'Browse PPE',
        'book_consultation' => 'Book Consultation',
        'schedule_service' => 'Schedule Service',
        'smart_integration' => 'Smart Integration. Proven Results.',
        'advanced_technology' => 'Advanced technology that works seamlessly with your existing systems',
        'ul_fm_certified' => 'UL/FM Certified',
        'scada_bms_integrated' => 'SCADA & BMS Integrated',
        'custom_high_risk' => 'Custom for High-Risk Facilities',
        'full_extinguisher' => 'Full Extinguisher Library',
        'expert_consultation' => 'Expert Consultation Team',
        'how_we_integrate' => 'How We Integrate With Your Facility →',
        'explore_components' => 'Explore the Components Behind Each System',
        'technical_specs' => 'Technical specifications and detailed breakdowns for engineering teams',
        'fire_network' => 'Fire Network Infrastructure',
        'alarm_detection_systems' => 'Alarm & Detection Systems',
        'personal_protective' => 'Personal Protective Equipment',
        'download_tech_specs' => 'Download Technical Specs (PDF)',
        'frequently_asked' => 'Frequently Asked Questions',
        'quick_answers' => 'Quick answers to common questions about our services',
        'installation_time' => 'How long does installation take?',
        'partial_systems' => 'What if I already have partial systems?',
        'offer_training' => 'Do you offer training?',
        'civil_defense_approval' => 'Is civil defense approval included?',
        'every_day_risk' => 'Every day without protection increases your risk. Let\'s fix that.',
        'join_hundreds' => 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety solutions.',
        'custom_offer' => '🔴 Get a Custom Offer Now',
        'schedule_site_visit' => '⚪ Schedule Free Site Visit',
        'response_24h' => 'Response within 24 hours • Free consultation • No obligation'
    ],
    'ar' => [
        'complete_fire_safety' => 'خدمات السلامة من الحريق الشاملة',
        'comprehensive_protection' => 'من التصميم إلى التركيب إلى الصيانة - نقدم حلول حماية شاملة.',
        'download_catalog' => '🔴 تحميل الكتالوج الكامل',
        'compare_systems' => '⚪ مقارنة الأنظمة →',
        'complete_solutions' => 'حلول السلامة من الحريق الشاملة',
        'certified_experts' => 'كل نظام مصمم ومركب وصيانته بواسطة خبراء معتمدين',
        'firefighting_systems' => 'أنظمة إطفاء الحريق',
        'alarm_detection' => 'الإنذار والكشف',
        'fire_extinguishers' => 'طفايات الحريق',
        'ppe_equipment' => 'معدات الحماية الشخصية',
        'safety_consulting' => 'الاستشارات الأمنية',
        'maintenance_services' => 'خدمات الصيانة',
        'request_design' => 'طلب تصميم',
        'get_quote' => 'احصل على عرض سعر',
        'view_catalog' => 'عرض الكتالوج',
        'browse_ppe' => 'تصفح معدات الحماية',
        'book_consultation' => 'حجز استشارة',
        'schedule_service' => 'جدولة خدمة',
        'smart_integration' => 'تكامل ذكي. نتائج مثبتة.',
        'advanced_technology' => 'تقنية متقدمة تعمل بسلاسة مع أنظمتك الموجودة',
        'ul_fm_certified' => 'معتمد UL/FM',
        'scada_bms_integrated' => 'متكامل مع SCADA و BMS',
        'custom_high_risk' => 'مخصص للمنشآت عالية المخاطر',
        'full_extinguisher' => 'مكتبة طفايات كاملة',
        'expert_consultation' => 'فريق استشارات خبراء',
        'how_we_integrate' => 'كيف نتكامل مع منشأتك →',
        'explore_components' => 'استكشف المكونات خلف كل نظام',
        'technical_specs' => 'المواصفات التقنية والتفاصيل لفريق الهندسة',
        'fire_network' => 'بنية تحتية لشبكة الحريق',
        'alarm_detection_systems' => 'أنظمة الإنذار والكشف',
        'personal_protective' => 'معدات الحماية الشخصية',
        'download_tech_specs' => 'تحميل المواصفات التقنية (PDF)',
        'frequently_asked' => 'الأسئلة الشائعة',
        'quick_answers' => 'إجابات سريعة للأسئلة الشائعة حول خدماتنا',
        'installation_time' => 'كم من الوقت يستغرق التركيب؟',
        'partial_systems' => 'ماذا لو كان لدي أنظمة جزئية بالفعل؟',
        'offer_training' => 'هل تقدمون تدريب؟',
        'civil_defense_approval' => 'هل موافقة الدفاع المدني مرفقة؟',
        'every_day_risk' => 'كل يوم بدون حماية يزيد من مخاطرك. دعنا نصلح ذلك.',
        'join_hundreds' => 'انضم لمئات المنشآت التي تثق في سفنكس فاير لحلول السلامة من الحريق الشاملة.',
        'custom_offer' => '🔴 احصل على عرض مخصص الآن',
        'schedule_site_visit' => '⚪ جدولة زيارة موقع مجانية',
        'response_24h' => 'استجابة خلال 24 ساعة • استشارة مجانية • بدون التزام'
    ]
];

$t = $translations[$lang];

// تضمين الهيدر والنافبار
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section with Carousel -->
<section id="hero" class="relative h-screen overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black via-gray-900 to-brand-red"></div>
    <div class="relative z-10 text-center text-white max-w-4xl px-6 flex flex-col justify-center h-full">
        <div class="mb-6">
            <i class="fas fa-cogs text-6xl text-brand-red mb-4"></i>
        </div>
        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
            <?php echo !empty($hero) ? htmlspecialchars($hero[0]['title']) : $t['complete_fire_safety']; ?>
        </h1>
        <p class="text-xl md:text-2xl mb-8 animate-slide-up">
            <?php echo !empty($hero) ? htmlspecialchars($hero[0]['subtitle']) : $t['comprehensive_protection']; ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-bounce-in">
            <button class="bg-brand-red text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-red-700 transition-colors" id="download-catalog-btn">
                <?php echo $t['download_catalog']; ?>
            </button>
            <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-brand-black transition-colors" id="compare-systems-btn">
                <?php echo $t['compare_systems']; ?>
            </button>
        </div>
    </div>
    
    <!-- Scroll Cue -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-20">
        <div class="bounce-arrow text-white text-2xl">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</section>

<!-- Services Grid Section -->
<section id="services" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['complete_solutions']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['certified_experts']; ?></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                        <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="<?php echo htmlspecialchars($service['icon'] ?? 'fas fa-fire-extinguisher'); ?> text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo htmlspecialchars($service['name']); ?></h3>
                        <p class="text-brand-gray mb-6 text-center">
                            <?php echo htmlspecialchars($service['short_description']); ?>
                        </p>
                        <div class="text-center">
                            <a href="service.php?slug=<?php echo htmlspecialchars($service['slug']); ?>" class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                <?php echo $t['get_quote']; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Services -->
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-fire-extinguisher text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo $t['firefighting_systems']; ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        High-pressure pump systems, foam suppression, sprinkler networks, and deluge systems for comprehensive fire protection.
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $t['request_design']; ?>
                        </button>
                    </div>
                </div>
                
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-bell text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo $t['alarm_detection']; ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        Smart smoke detectors, heat sensors, manual call points, and integrated alarm panels with SCADA connectivity.
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $t['get_quote']; ?>
                        </button>
                    </div>
                </div>
                
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-spray-can text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo $t['fire_extinguishers']; ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        Complete extinguisher library: CO2, foam, powder, wet chemical, and specialized suppression agents.
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $t['view_catalog']; ?>
                        </button>
                    </div>
                </div>
                
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-hard-hat text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo $t['ppe_equipment']; ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        Professional-grade protective equipment: suits, helmets, breathing apparatus, and emergency response gear.
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $t['browse_ppe']; ?>
                        </button>
                    </div>
                </div>
                
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clipboard-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo $t['safety_consulting']; ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        Expert consultation, compliance audits, training programs, and certification assistance for your facility.
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $t['book_consultation']; ?>
                        </button>
                    </div>
                </div>
                
                <div class="service-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-tools text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-center"><?php echo $t['maintenance_services']; ?></h3>
                    <p class="text-brand-gray mb-6 text-center">
                        Regular inspections, preventive maintenance, emergency repairs, and system upgrades to ensure optimal performance.
                    </p>
                    <div class="text-center">
                        <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $t['schedule_service']; ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Smart System Advantage Strip -->
<section class="py-16 bg-brand-black text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4"><?php echo $t['smart_integration']; ?></h2>
            <p class="text-xl text-gray-300"><?php echo $t['advanced_technology']; ?></p>
        </div>
        
        <div class="relative">
            <div class="advantage-strip flex space-x-12 whitespace-nowrap">
                <!-- First Set -->
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-certificate text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['ul_fm_certified']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-cogs text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['scada_bms_integrated']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-industry text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['custom_high_risk']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-fire-extinguisher text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['full_extinguisher']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-user-tie text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['expert_consultation']; ?></span>
                </div>
                
                <!-- Duplicate Set for Seamless Loop -->
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-certificate text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['ul_fm_certified']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-cogs text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['scada_bms_integrated']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-industry text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['custom_high_risk']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-fire-extinguisher text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['full_extinguisher']; ?></span>
                </div>
                
                <div class="flex items-center space-x-4 bg-gray-800 px-8 py-4 rounded-lg">
                    <i class="fas fa-user-tie text-brand-red text-2xl"></i>
                    <span class="text-lg font-semibold"><?php echo $t['expert_consultation']; ?></span>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" id="integration-cta">
                <?php echo $t['how_we_integrate']; ?>
            </button>
        </div>
    </div>
</section>

<!-- Technical Details Accordion -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['explore_components']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['technical_specs']; ?></p>
        </div>
        
        <div class="max-w-4xl mx-auto">
            <?php if (!empty($technicalDetails)): ?>
                <?php foreach ($technicalDetails as $detail): ?>
                    <div class="accordion-item border-b border-gray-200">
                        <button class="accordion-header w-full py-6 px-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <i class="<?php echo htmlspecialchars($detail['icon'] ?? 'fas fa-cogs'); ?> text-brand-red text-xl"></i>
                                <span class="text-xl font-semibold"><?php echo htmlspecialchars($detail['title']); ?></span>
                            </div>
                            <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                        </button>
                        <div class="accordion-content">
                            <div class="px-4 pb-6">
                                <p class="text-brand-gray mb-4">
                                    <?php echo htmlspecialchars($detail['content']); ?>
                                </p>
                                <button class="mt-4 bg-brand-red text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                    <?php echo $t['download_tech_specs']; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback Technical Details -->
                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header w-full py-6 px-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-network-wired text-brand-red text-xl"></i>
                            <span class="text-xl font-semibold"><?php echo $t['fire_network']; ?></span>
                        </div>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="accordion-content">
                        <div class="px-4 pb-6">
                            <p class="text-brand-gray mb-4">
                                Complete network infrastructure including high-pressure pumps, distribution manifolds, 
                                sprinkler heads, and monitoring systems. All components are UL/FM certified and designed 
                                for industrial environments.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Pump Systems</h4>
                                    <p class="text-sm text-brand-gray">Electric, diesel, and jockey pumps with automatic controls</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Distribution</h4>
                                    <p class="text-sm text-brand-gray">Wet, dry, and deluge piping systems with zone control</p>
                                </div>
                            </div>
                            <button class="mt-4 bg-brand-red text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                <?php echo $t['download_tech_specs']; ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header w-full py-6 px-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-bell text-brand-red text-xl"></i>
                            <span class="text-xl font-semibold"><?php echo $t['alarm_detection_systems']; ?></span>
                        </div>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="accordion-content">
                        <div class="px-4 pb-6">
                            <p class="text-brand-gray mb-4">
                                Intelligent detection systems with addressable panels, multi-sensor detectors, 
                                and integration capabilities with building management systems.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Smoke Detection</h4>
                                    <p class="text-sm text-brand-gray">Photoelectric and ionization sensors</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Heat Detection</h4>
                                    <p class="text-sm text-brand-gray">Fixed temperature and rate-of-rise</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Manual Stations</h4>
                                    <p class="text-sm text-brand-gray">Break glass and push button types</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item border-b border-gray-200">
                    <button class="accordion-header w-full py-6 px-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-hard-hat text-brand-red text-xl"></i>
                            <span class="text-xl font-semibold"><?php echo $t['personal_protective']; ?></span>
                        </div>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="accordion-content">
                        <div class="px-4 pb-6">
                            <p class="text-brand-gray mb-4">
                                Complete PPE solutions including fire suits, breathing apparatus, helmets, 
                                and emergency response equipment for industrial fire safety teams.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Fire Suits</h4>
                                    <p class="text-sm text-brand-gray">Aluminized and proximity suits with full protection</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-semibold mb-2">Breathing Equipment</h4>
                                    <p class="text-sm text-brand-gray">SCBA units and emergency escape respirators</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['frequently_asked']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['quick_answers']; ?></p>
        </div>
        
        <div class="space-y-4">
            <?php if (!empty($faq)): ?>
                <?php foreach ($faq as $faqItem): ?>
                    <div class="faq-item bg-white rounded-lg shadow-md">
                        <button class="faq-header w-full py-6 px-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <span class="text-lg font-semibold"><?php echo htmlspecialchars($faqItem['title']); ?></span>
                            <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                        </button>
                        <div class="faq-content">
                            <div class="px-6 pb-6">
                                <p class="text-brand-gray">
                                    <?php echo htmlspecialchars($faqItem['content']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback FAQ -->
                <div class="faq-item bg-white rounded-lg shadow-md">
                    <button class="faq-header w-full py-6 px-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <span class="text-lg font-semibold"><?php echo $t['installation_time']; ?></span>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="faq-content">
                        <div class="px-6 pb-6">
                            <p class="text-brand-gray">
                                Installation time varies by system complexity. Simple extinguisher installations take 1-2 days, 
                                while complete fire suppression systems typically require 1-3 weeks depending on facility size and requirements.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item bg-white rounded-lg shadow-md">
                    <button class="faq-header w-full py-6 px-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <span class="text-lg font-semibold"><?php echo $t['partial_systems']; ?></span>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="faq-content">
                        <div class="px-6 pb-6">
                            <p class="text-brand-gray">
                                We can integrate with existing systems and upgrade components as needed. Our team will assess 
                                your current setup and recommend the most cost-effective approach to achieve full compliance.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item bg-white rounded-lg shadow-md">
                    <button class="faq-header w-full py-6 px-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <span class="text-lg font-semibold"><?php echo $t['offer_training']; ?></span>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="faq-content">
                        <div class="px-6 pb-6">
                            <p class="text-brand-gray">
                                Yes, we provide comprehensive training programs for your staff including system operation, 
                                emergency procedures, and maintenance protocols. Training is included with all major installations.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-item bg-white rounded-lg shadow-md">
                    <button class="faq-header w-full py-6 px-6 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <span class="text-lg font-semibold"><?php echo $t['civil_defense_approval']; ?></span>
                        <i class="fas fa-chevron-down accordion-arrow text-brand-gray"></i>
                    </button>
                    <div class="faq-content">
                        <div class="px-6 pb-6">
                            <p class="text-brand-gray">
                                Absolutely. We handle all regulatory approvals including civil defense, municipality permits, 
                                and insurance compliance documentation. Our team manages the entire approval process for you.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Conversion Section -->
<section class="py-20 bg-brand-black text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="w-full h-full bg-gradient-to-r from-transparent via-brand-red to-transparent"></div>
    </div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo !empty($cta) ? htmlspecialchars($cta[0]['title']) : $t['every_day_risk']; ?>
        </h2>
        <p class="text-xl mb-12 text-gray-300">
            <?php echo !empty($cta) ? htmlspecialchars($cta[0]['subtitle']) : $t['join_hundreds']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-main" id="custom-offer-btn">
                <?php echo $t['custom_offer']; ?>
            </button>
            <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="site-visit-btn">
                <?php echo $t['schedule_site_visit']; ?>
            </button>
        </div>
        
        <div class="mt-8">
            <p class="text-gray-400 text-sm">
                <?php echo $t['response_24h']; ?>
            </p>
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
</html>