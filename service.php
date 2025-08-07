<?php
require_once 'config.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';

// جلب slug الخدمة من URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// جلب بيانات الخدمة
$service = fetchServiceBySlug($slug, $lang);

// إذا لم توجد الخدمة، استخدم خدمة افتراضية
if (!$service) {
    $service = [
        'id' => 1,
        'name' => 'Firefighting Systems',
        'short_description' => 'High-pressure pump systems, foam suppression, sprinkler networks, and deluge systems for comprehensive fire protection.',
        'full_description' => 'Complete fire suppression systems designed for industrial facilities. Our firefighting systems include high-pressure pumps, foam suppression units, sprinkler networks, and deluge systems.',
        'features' => 'High-pressure pumps, Foam suppression, Sprinkler networks, Deluge systems, UL/FM certified, SCADA integration',
        'benefits' => 'Complete protection coverage, Rapid response time, Minimal water damage, Automated operation, Regulatory compliance',
        'specifications' => 'Pump capacity: 500-2000 GPM, Pressure: 150-300 PSI, Coverage area: Up to 50,000 sqm, Response time: <30 seconds',
        'price_range' => '$10,000 - $50,000',
        'duration' => '2-4 weeks',
        'icon' => 'fas fa-fire-extinguisher'
    ];
}

// ترجمات الصفحة
$translations = [
    'en' => [
        'home' => 'Home',
        'services' => 'Services',
        'service_details' => 'Service Details',
        'get_quote' => '🔴 Get Quote',
        'download_brochure' => '⚪ Download Brochure',
        'service_overview' => 'Service Overview',
        'key_features' => 'Key Features',
        'benefits' => 'Benefits',
        'specifications' => 'Technical Specifications',
        'pricing' => 'Pricing & Duration',
        'installation_process' => 'Installation Process',
        'maintenance' => 'Maintenance & Support',
        'related_services' => 'Related Services',
        'similar_solutions' => 'Similar solutions you might be interested in',
        'contact_expert' => '🔴 Contact Our Expert',
        'schedule_consultation' => '⚪ Schedule Consultation',
        'need_this_service' => 'Need this service for your facility?',
        'discuss_requirements' => 'Let\'s discuss your specific requirements and get you a customized solution.',
        'request_quote' => '🔴 Request Free Quote',
        'book_consultation' => '⚪ Book Free Consultation',
        'response_24h' => 'Response within 24 hours • Free consultation • No obligation',
        'price_range' => 'Price Range',
        'duration' => 'Duration',
        'features_list' => 'Features',
        'benefits_list' => 'Benefits',
        'specs_list' => 'Specifications',
        'installation_steps' => 'Installation Steps',
        'maintenance_info' => 'Maintenance Information'
    ],
    'ar' => [
        'home' => 'الرئيسية',
        'services' => 'الخدمات',
        'service_details' => 'تفاصيل الخدمة',
        'get_quote' => '🔴 احصل على عرض سعر',
        'download_brochure' => '⚪ تحميل الكتيب',
        'service_overview' => 'نظرة عامة على الخدمة',
        'key_features' => 'الميزات الرئيسية',
        'benefits' => 'الفوائد',
        'specifications' => 'المواصفات التقنية',
        'pricing' => 'التسعير والمدة',
        'installation_process' => 'عملية التركيب',
        'maintenance' => 'الصيانة والدعم',
        'related_services' => 'خدمات ذات صلة',
        'similar_solutions' => 'حلول مماثلة قد تهمك',
        'contact_expert' => '🔴 اتصل بخبيرنا',
        'schedule_consultation' => '⚪ جدولة استشارة',
        'need_this_service' => 'تحتاج هذه الخدمة لمنشأتك؟',
        'discuss_requirements' => 'دعنا نناقش متطلباتك المحددة ونحصل لك على حل مخصص.',
        'request_quote' => '🔴 اطلب عرض سعر مجاني',
        'book_consultation' => '⚪ احجز استشارة مجانية',
        'response_24h' => 'استجابة خلال 24 ساعة • استشارة مجانية • بدون التزام',
        'price_range' => 'نطاق السعر',
        'duration' => 'المدة',
        'features_list' => 'الميزات',
        'benefits_list' => 'الفوائد',
        'specs_list' => 'المواصفات',
        'installation_steps' => 'خطوات التركيب',
        'maintenance_info' => 'معلومات الصيانة'
    ]
];

$t = $translations[$lang];

// تضمين الهيدر والنافبار
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Breadcrumb -->
<section class="pt-24 pb-4 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="breadcrumb">
            <a href="index.php"><?php echo $t['home']; ?></a> / 
            <a href="services.php"><?php echo $t['services']; ?></a> / 
            <span><?php echo htmlspecialchars($service['name']); ?></span>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section id="hero" class="py-20 bg-gradient-to-r from-brand-black to-brand-red text-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="mb-6">
                    <i class="<?php echo htmlspecialchars($service['icon']); ?> text-6xl text-white mb-4"></i>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <?php echo htmlspecialchars($service['name']); ?>
                </h1>
                <p class="text-xl mb-8 text-gray-300">
                    <?php echo htmlspecialchars($service['short_description']); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="bg-white text-brand-red px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
                        <?php echo $t['get_quote']; ?>
                    </button>
                    <button class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-brand-red transition-colors">
                        <?php echo $t['download_brochure']; ?>
                    </button>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white/10 rounded-xl p-8 backdrop-blur-sm">
                    <h3 class="text-2xl font-bold mb-4"><?php echo $t['pricing']; ?></h3>
                    <div class="space-y-4">
                        <div>
                            <span class="text-gray-300"><?php echo $t['price_range']; ?>:</span>
                            <p class="text-2xl font-bold"><?php echo htmlspecialchars($service['price_range']); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-300"><?php echo $t['duration']; ?>:</span>
                            <p class="text-xl font-semibold"><?php echo htmlspecialchars($service['duration']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Overview -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['service_overview']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo htmlspecialchars($service['full_description']); ?></p>
        </div>
    </div>
</section>

<!-- Features & Benefits -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Key Features -->
            <div>
                <h3 class="text-3xl font-bold mb-8"><?php echo $t['key_features']; ?></h3>
                <div class="space-y-4">
                    <?php 
                    $features = explode(',', $service['features']);
                    foreach ($features as $feature): 
                        $feature = trim($feature);
                        if (!empty($feature)):
                    ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <span class="text-lg"><?php echo htmlspecialchars($feature); ?></span>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            
            <!-- Benefits -->
            <div>
                <h3 class="text-3xl font-bold mb-8"><?php echo $t['benefits']; ?></h3>
                <div class="space-y-4">
                    <?php 
                    $benefits = explode(',', $service['benefits']);
                    foreach ($benefits as $benefit): 
                        $benefit = trim($benefit);
                        if (!empty($benefit)):
                    ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-star text-white text-sm"></i>
                            </div>
                            <span class="text-lg"><?php echo htmlspecialchars($benefit); ?></span>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Technical Specifications -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['specifications']; ?></h2>
            <p class="text-xl text-brand-gray">Detailed technical information about this service</p>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php 
                $specs = explode(',', $service['specifications']);
                foreach ($specs as $spec): 
                    $spec = trim($spec);
                    if (!empty($spec)):
                        $parts = explode(':', $spec, 2);
                        $title = trim($parts[0]);
                        $value = isset($parts[1]) ? trim($parts[1]) : '';
                ?>
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="font-semibold text-brand-red mb-2"><?php echo htmlspecialchars($title); ?></h4>
                        <p class="text-brand-gray"><?php echo htmlspecialchars($value); ?></p>
                    </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
</section>

<!-- Installation Process -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['installation_process']; ?></h2>
            <p class="text-xl text-brand-gray">Our systematic approach to installation</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">1</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Assessment</h3>
                <p class="text-brand-gray">Site survey and requirements analysis</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">2</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Installation</h3>
                <p class="text-brand-gray">Professional installation by certified technicians</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">3</span>
                </div>
                <h3 class="text-xl font-semibold mb-2">Testing</h3>
                <p class="text-brand-gray">Comprehensive testing and commissioning</p>
            </div>
        </div>
    </div>
</section>

<!-- Maintenance & Support -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['maintenance']; ?></h2>
            <p class="text-xl text-brand-gray">Ongoing support to ensure optimal performance</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-2xl font-semibold mb-4">Preventive Maintenance</h3>
                <ul class="space-y-2 text-brand-gray">
                    <li>• Regular system inspections</li>
                    <li>• Performance monitoring</li>
                    <li>• Component testing</li>
                    <li>• Documentation updates</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-2xl font-semibold mb-4">Emergency Support</h3>
                <ul class="space-y-2 text-brand-gray">
                    <li>• 24/7 emergency response</li>
                    <li>• Rapid repair services</li>
                    <li>• Spare parts availability</li>
                    <li>• Technical assistance</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Related Services -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['related_services']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['similar_solutions']; ?></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            // جلب خدمات أخرى (استثناء الخدمة الحالية)
            $allServices = fetchServices($lang);
            $relatedServices = array_filter($allServices, function($s) use ($service) {
                return $s['id'] != $service['id'];
            });
            $relatedServices = array_slice($relatedServices, 0, 3);
            
            foreach ($relatedServices as $relatedService):
            ?>
                <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="w-12 h-12 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="<?php echo htmlspecialchars($relatedService['icon'] ?? 'fas fa-cogs'); ?> text-white"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-center"><?php echo htmlspecialchars($relatedService['name']); ?></h3>
                    <p class="text-brand-gray text-center mb-4"><?php echo htmlspecialchars($relatedService['short_description']); ?></p>
                    <a href="service.php?slug=<?php echo htmlspecialchars($relatedService['slug']); ?>" class="block text-center bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        <?php echo $t['get_quote']; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call-to-Action -->
<section class="py-20 bg-brand-black text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="w-full h-full bg-gradient-to-r from-transparent via-brand-red to-transparent"></div>
    </div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo $t['need_this_service']; ?>
        </h2>
        <p class="text-xl mb-12 text-gray-300">
            <?php echo $t['discuss_requirements']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="contact-expert-btn">
                <?php echo $t['contact_expert']; ?>
            </button>
            <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="schedule-consultation-btn">
                <?php echo $t['schedule_consultation']; ?>
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