<?php
require_once 'config.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en');

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

// إضافة الحقول المفقودة
$service['pricing'] = $service['price_range'] ?? '$10,000 - $50,000';
$service['duration'] = $service['duration'] ?? '2-4 weeks';
$service['price_range'] = $service['price_range'] ?? '$10,000 - $50,000';

// جلب المحتوى الستاتيكي للصفحة
$staticContent = fetchLandingStaticContent($lang);

// ترجمات الصفحة
$translations = [
    'en' => [
        'hero_title' => 'Professional Fire Safety Solutions',
        'hero_subtitle' => 'Protect your facility with certified fire protection systems',
        'get_free_quote' => '🔴 Get Free Quote',
        'download_brochure' => '⚪ Download Brochure',
        'why_choose_us' => 'Why Choose Sphinx Fire?',
        'certified_experts' => 'Certified Experts',
        'certified_experts_desc' => 'Our team consists of certified fire safety engineers with years of experience',
        'fast_installation' => 'Fast Installation',
        'fast_installation_desc' => 'Quick and efficient installation with minimal disruption to your operations',
        '24_7_support' => '24/7 Support',
        '24_7_support_desc' => 'Round-the-clock technical support and emergency response',
        'compliance_guaranteed' => 'Compliance Guaranteed',
        'compliance_guaranteed_desc' => 'Full compliance with NFPA, OSHA, and Egyptian regulations',
        'service_features' => 'Service Features',
        'service_benefits' => 'Service Benefits',
        'technical_specs' => 'Technical Specifications',
        'installation_process' => 'Installation Process',
        'step_1' => 'Site Assessment',
        'step_1_desc' => 'Comprehensive site survey and risk analysis',
        'step_2' => 'System Design',
        'step_2_desc' => 'Custom system design based on your requirements',
        'step_3' => 'Installation',
        'step_3_desc' => 'Professional installation by certified technicians',
        'step_4' => 'Testing & Commissioning',
        'step_4_desc' => 'Complete system testing and commissioning',
        'step_5' => 'Training & Handover',
        'step_5_desc' => 'Staff training and system handover',
        'step_6' => 'Ongoing Support',
        'step_6_desc' => 'Maintenance contracts and technical support',
        'testimonials' => 'What Our Clients Say',
        'testimonial_1_name' => 'Ahmed Hassan',
        'testimonial_1_position' => 'Factory Manager',
        'testimonial_1_company' => 'Industrial Solutions Co.',
        'testimonial_1_text' => 'Sphinx Fire delivered an excellent fire protection system for our factory. The installation was smooth and the team was very professional.',
        'testimonial_2_name' => 'Fatma Nour',
        'testimonial_2_position' => 'Safety Director',
        'testimonial_2_company' => 'Chemical Plant Ltd.',
        'testimonial_2_text' => 'The fire safety system installed by Sphinx Fire exceeded our expectations. Their expertise and attention to detail are outstanding.',
        'cta_title' => 'Ready to Protect Your Facility?',
        'cta_subtitle' => 'Get a free consultation and quote for your fire safety needs',
        'request_quote' => '🔴 Request Free Quote',
        'schedule_consultation' => '⚪ Schedule Free Consultation',
        'response_24h' => 'Response within 24 hours • Free consultation • No obligation',
        'contact_info' => 'Contact Information',
        'phone' => 'Phone',
        'email' => 'Email',
        'address' => 'Address',
        'sadat_city' => 'Sadat City, Egypt',
        'working_hours' => 'Working Hours',
        'mon_fri' => 'Monday - Friday: 8:00 AM - 6:00 PM',
        'sat' => 'Saturday: 9:00 AM - 2:00 PM',
        'emergency' => 'Emergency: 24/7',
        'pricing' => 'Pricing & Duration',
        'duration' => 'Duration',
        'price_range' => 'Price Range',
        'guaranteed_compliance' => 'Guaranteed Compliance'
    ],
    'ar' => [
        'hero_title' => 'حلول السلامة من الحريق الاحترافية',
        'hero_subtitle' => 'احم منشأتك بأنظمة الحماية من الحريق المعتمدة',
        'get_free_quote' => '🔴 احصل على عرض سعر مجاني',
        'download_brochure' => '⚪ تحميل الكتيب',
        'why_choose_us' => 'لماذا تختار سفينكس فاير؟',
        'certified_experts' => 'خبراء معتمدون',
        'certified_experts_desc' => 'فريقنا يتكون من مهندسي السلامة من الحريق المعتمدين بخبرة سنوات',
        'fast_installation' => 'تركيب سريع',
        'fast_installation_desc' => 'تركيب سريع وفعال مع أقل تعطيل لعملياتك',
        '24_7_support' => 'دعم 24/7',
        '24_7_support_desc' => 'دعم تقني على مدار الساعة واستجابة للطوارئ',
        'compliance_guaranteed' => 'الامتثال مضمون',
        'compliance_guaranteed_desc' => 'امتثال كامل لمعايير NFPA و OSHA واللوائح المصرية',
        'service_features' => 'ميزات الخدمة',
        'service_benefits' => 'فوائد الخدمة',
        'technical_specs' => 'المواصفات التقنية',
        'installation_process' => 'عملية التركيب',
        'step_1' => 'تقييم الموقع',
        'step_1_desc' => 'مسح شامل للموقع وتحليل المخاطر',
        'step_2' => 'تصميم النظام',
        'step_2_desc' => 'تصميم نظام مخصص بناءً على متطلباتك',
        'step_3' => 'التركيب',
        'step_3_desc' => 'تركيب احترافي من قبل فنيين معتمدين',
        'step_4' => 'الاختبار والتشغيل',
        'step_4_desc' => 'اختبار شامل للنظام والتشغيل',
        'step_5' => 'التدريب والتسليم',
        'step_5_desc' => 'تدريب الموظفين وتسليم النظام',
        'step_6' => 'الدعم المستمر',
        'step_6_desc' => 'عقود الصيانة والدعم التقني',
        'testimonials' => 'ماذا يقول عملاؤنا',
        'testimonial_1_name' => 'أحمد حسن',
        'testimonial_1_position' => 'مدير المصنع',
        'testimonial_1_company' => 'شركة الحلول الصناعية',
        'testimonial_1_text' => 'سفينكس فاير قدمت نظام حماية من الحريق ممتاز لمصنعنا. كان التركيب سلس والفريق كان محترف جداً.',
        'testimonial_2_name' => 'فاطمة نور',
        'testimonial_2_position' => 'مديرة السلامة',
        'testimonial_2_company' => 'مصنع الكيماويات المحدودة',
        'testimonial_2_text' => 'نظام السلامة من الحريق المثبت من سفينكس فاير تجاوز توقعاتنا. خبرتهم والاهتمام بالتفاصيل متميز.',
        'cta_title' => 'مستعد لحماية منشأتك؟',
        'cta_subtitle' => 'احصل على استشارة مجانية وعرض سعر لاحتياجات السلامة من الحريق',
        'request_quote' => '🔴 اطلب عرض سعر مجاني',
        'schedule_consultation' => '⚪ جدولة استشارة مجانية',
        'response_24h' => 'استجابة خلال 24 ساعة • استشارة مجانية • بدون التزام',
        'contact_info' => 'معلومات التواصل',
        'phone' => 'الهاتف',
        'email' => 'البريد الإلكتروني',
        'address' => 'العنوان',
        'sadat_city' => 'مدينة السادات، مصر',
        'working_hours' => 'ساعات العمل',
        'mon_fri' => 'الأحد - الخميس: 8:00 ص - 6:00 م',
        'sat' => 'السبت: 9:00 ص - 2:00 م',
        'emergency' => 'الطوارئ: 24/7',
        'pricing' => 'التسعير والمدة',
        'duration' => 'المدة',
        'price_range' => 'نطاق السعر',
        'guaranteed_compliance' => 'الامتثال مضمون'
    ]
];

$t = $translations[$lang];

?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['name']); ?> - Sphinx Fire</title>
    <meta name="description" content="<?php echo htmlspecialchars($service['short_description']); ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-red': '#DC2626',
                        'brand-black': '#1F2937',
                        'brand-gray': '#6B7280'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="style.css">
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1F2937 0%, #DC2626 50%, #1F2937 100%);
        }
        .floating-cta {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-white text-brand-black font-inter">

<!-- Hero Section -->
<section id="hero" class="min-h-screen gradient-bg text-white relative overflow-hidden hero-pattern">
    <div class="absolute inset-0 bg-black/20"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center min-h-screen">
            <div>
                <!-- Badge -->
                <div class="mb-8">
                    <div class="inline-flex items-center px-6 py-3 bg-white/15 rounded-full text-sm font-medium backdrop-blur-sm border border-white/20">
                        <i class="fas fa-fire text-brand-red mr-3 text-lg"></i>
                        <span class="text-white"><?php echo htmlspecialchars($service['name']); ?></span>
                    </div>
                </div>
                
                <!-- Main Headline -->
                <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight text-white">
                    <?php echo $t['hero_title']; ?>
                </h1>
                
                <!-- Subtitle -->
                <p class="text-xl md:text-2xl mb-8 text-gray-200 leading-relaxed">
                    <?php echo $t['hero_subtitle']; ?>
                </p>
                
                <!-- Service Info Cards -->
                <div class="mb-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-shield-alt text-brand-red text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold text-white"><?php echo htmlspecialchars($service['name']); ?></h3>
                            </div>
                            <p class="text-gray-200"><?php echo htmlspecialchars($service['short_description']); ?></p>
                        </div>
                        
                        <div class="bg-white/15 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-clock text-brand-red text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold text-white"><?php echo $t['duration']; ?></h3>
                            </div>
                            <p class="text-gray-200"><?php echo htmlspecialchars($service['duration']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="bg-brand-red text-white px-10 py-5 rounded-xl font-bold text-xl hover:bg-red-700 transition-all transform hover:scale-105 shadow-2xl hover:shadow-red-500/25" id="main-cta">
                        <?php echo $t['get_free_quote']; ?>
                    </button>
                    <button class="border-2 border-white text-white px-10 py-5 rounded-xl font-bold text-xl hover:bg-white hover:text-brand-black transition-all transform hover:scale-105 backdrop-blur-sm" id="secondary-cta">
                        <?php echo $t['download_brochure']; ?>
                    </button>
                </div>
                
                <!-- Trust Indicators -->
                <div class="mt-8 flex items-center space-x-6 text-sm text-gray-300">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        <span><?php echo $t['certified_experts']; ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-400 mr-2"></i>
                        <span><?php echo $t['24_7_support']; ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-yellow-400 mr-2"></i>
                        <span><?php echo $t['guaranteed_compliance']; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Pricing Card -->
            <div class="text-center">
                <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-8 border border-white/20 shadow-2xl">
                    <div class="mb-6">
                        <i class="fas fa-tag text-brand-red text-4xl mb-4"></i>
                        <h3 class="text-3xl font-bold text-white mb-2"><?php echo $t['pricing']; ?></h3>
                    </div>
                    <div class="space-y-6">
                        <div class="bg-white/10 rounded-xl p-4">
                            <span class="text-gray-300 text-lg block mb-2"><?php echo $t['price_range']; ?>:</span>
                            <p class="text-4xl font-bold text-brand-red"><?php echo htmlspecialchars($service['price_range']); ?></p>
                        </div>
                        <div class="bg-white/10 rounded-xl p-4">
                            <span class="text-gray-300 text-lg block mb-2"><?php echo $t['duration']; ?>:</span>
                            <p class="text-2xl font-semibold text-white"><?php echo htmlspecialchars($service['duration']); ?></p>
                        </div>
                        <div class="pt-6 border-t border-white/20">
                            <p class="text-gray-300 text-sm"><?php echo $t['response_24h']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold mb-6"><?php echo $t['why_choose_us']; ?></h2>
            <p class="text-xl text-brand-gray max-w-3xl mx-auto">Professional fire safety solutions with certified expertise and guaranteed compliance</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-certificate text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['certified_experts']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['certified_experts_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-tools text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['fast_installation']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['fast_installation_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['24_7_support']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['24_7_support_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['compliance_guaranteed']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['compliance_guaranteed_desc']; ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Service Features & Benefits -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Features -->
            <div>
                <h3 class="text-3xl font-bold mb-8"><?php echo $t['service_features']; ?></h3>
                <div class="space-y-6">
                    <?php 
                    $features = explode(',', $service['features']);
                    foreach ($features as $feature): 
                        $feature = trim($feature);
                        if (!empty($feature)):
                    ?>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($feature); ?></h4>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
            
            <!-- Benefits -->
            <div>
                <h3 class="text-3xl font-bold mb-8"><?php echo $t['service_benefits']; ?></h3>
                <div class="space-y-6">
                    <?php 
                    $benefits = explode(',', $service['benefits']);
                    foreach ($benefits as $benefit): 
                        $benefit = trim($benefit);
                        if (!empty($benefit)):
                    ?>
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-star text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($benefit); ?></h4>
                            </div>
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
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['technical_specs']; ?></h2>
            <p class="text-xl text-brand-gray">Detailed technical specifications for optimal performance</p>
        </div>
        
        <div class="bg-gray-50 rounded-2xl p-8">
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
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
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
            <p class="text-xl text-brand-gray">Our systematic approach ensures quality and efficiency</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">1</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?php echo $t['step_1']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['step_1_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">2</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?php echo $t['step_2']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['step_2_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">3</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?php echo $t['step_3']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['step_3_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">4</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?php echo $t['step_4']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['step_4_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">5</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?php echo $t['step_5']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['step_5_desc']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-white font-bold text-xl">6</span>
                </div>
                <h3 class="text-xl font-semibold mb-2"><?php echo $t['step_6']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['step_6_desc']; ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['testimonials']; ?></h2>
            <p class="text-xl text-brand-gray">What our satisfied clients say about our services</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gray-50 rounded-2xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-brand-red rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold"><?php echo $t['testimonial_1_name']; ?></h4>
                        <p class="text-sm text-brand-gray"><?php echo $t['testimonial_1_position']; ?>, <?php echo $t['testimonial_1_company']; ?></p>
                    </div>
                </div>
                <p class="text-brand-gray italic">"<?php echo $t['testimonial_1_text']; ?>"</p>
            </div>
            
            <div class="bg-gray-50 rounded-2xl p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-brand-red rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold"><?php echo $t['testimonial_2_name']; ?></h4>
                        <p class="text-sm text-brand-gray"><?php echo $t['testimonial_2_position']; ?>, <?php echo $t['testimonial_2_company']; ?></p>
                    </div>
                </div>
                <p class="text-brand-gray italic">"<?php echo $t['testimonial_2_text']; ?>"</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h2 class="text-4xl font-bold mb-8"><?php echo $t['contact_info']; ?></h2>
                <div class="space-y-6">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-brand-red text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold"><?php echo $t['phone']; ?></h4>
                            <p class="text-brand-gray">+20 123 456 7890</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-brand-red text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold"><?php echo $t['email']; ?></h4>
                            <p class="text-brand-gray">info@sphinxfire.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-brand-red text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold"><?php echo $t['address']; ?></h4>
                            <p class="text-brand-gray"><?php echo $t['sadat_city']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-2xl font-bold mb-6"><?php echo $t['working_hours']; ?></h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-brand-gray"><?php echo $t['mon_fri']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-brand-gray"><?php echo $t['sat']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-brand-gray"><?php echo $t['emergency']; ?></span>
                    </div>
                </div>
            </div>
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
            <?php echo $t['cta_title']; ?>
        </h2>
        <p class="text-xl mb-12 text-gray-300">
            <?php echo $t['cta_subtitle']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-all transform hover:scale-105 shadow-lg">
                <?php echo $t['request_quote']; ?>
            </button>
            <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-all transform hover:scale-105">
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

<!-- Floating CTA -->
<div class="floating-cta">
    <div class="bg-brand-red text-white px-6 py-4 rounded-full shadow-2xl hover:shadow-red-500/25 transition-all transform hover:scale-110 cursor-pointer" id="floating-cta-btn">
        <div class="flex items-center space-x-3">
            <i class="fas fa-fire text-2xl"></i>
            <div>
                <div class="font-bold text-lg"><?php echo $t['get_free_quote']; ?></div>
                <div class="text-sm opacity-90"><?php echo $t['response_24h']; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Float -->
<div class="fixed bottom-20 left-4 z-50">
    <a href="https://wa.me/201234567890?text=Hi, I'm interested in <?php echo urlencode($service['name']); ?>" 
       target="_blank" 
       class="bg-green-500 text-white p-4 rounded-full shadow-2xl hover:shadow-green-500/25 transition-all transform hover:scale-110">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>
</div>

<!-- Service Landing JavaScript -->
<script src="scripts/service-landing.js"></script>

<script>
// Floating CTA functionality
document.getElementById('floating-cta-btn').addEventListener('click', function() {
    // Scroll to contact form or show modal
    const contactSection = document.querySelector('#contact-form') || document.querySelector('#hero');
    contactSection.scrollIntoView({ behavior: 'smooth' });
    
    // Track click
    console.log('Floating CTA clicked');
});

// WhatsApp functionality
document.querySelector('a[href*="wa.me"]').addEventListener('click', function() {
    console.log('WhatsApp clicked');
});

// Main CTA functionality
document.getElementById('main-cta').addEventListener('click', function() {
    console.log('Main CTA clicked');
    // Show quote form or scroll to contact
});

// Secondary CTA functionality
document.getElementById('secondary-cta').addEventListener('click', function() {
    console.log('Secondary CTA clicked');
    // Download brochure
});
</script>
</body>
</html> 