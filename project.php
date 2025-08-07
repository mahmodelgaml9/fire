<?php
require_once 'config.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';

// جلب slug المشروع من URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// جلب بيانات المشروع
$project = fetchProjectBySlug($slug, $lang);

// إذا لم يوجد المشروع، استخدم مشروع افتراضي
if (!$project) {
    $project = [
        'id' => 1,
        'title' => 'Firefighting System for Delta Paint Manufacturing',
        'subtitle' => 'Complete UL/FM certified firefighting network with specialized foam suppression for paint storage areas.',
        'description' => 'From risk assessment to final testing – full compliance in 12 working days.',
        'client_name' => 'Delta Paint Industries',
        'location' => 'Quesna Industrial City',
        'duration_days' => 12,
        'budget_range' => '$50,000 - $75,000',
        'status' => 'completed',
        'featured_image' => 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop',
        'challenge' => 'High-risk paint storage areas required specialized foam suppression systems.',
        'solution' => 'Implemented UL/FM certified firefighting network with foam suppression.',
        'results' => 'Successfully completed in 12 days with full civil defense approval.',
        'testimonial' => 'Sphinx Fire delivered exceptional results under tight deadlines.',
        'testimonial_author' => 'Ahmed Hassan',
        'testimonial_position' => 'Safety Manager'
    ];
}

// ترجمات الصفحة
$translations = [
    'en' => [
        'home' => 'Home',
        'projects' => 'Projects',
        'project_completed' => '✅ PROJECT COMPLETED',
        'view_results' => '🔴 View Project Results',
        'project_overview' => 'Project Overview',
        'key_details' => 'Key details and specifications at a glance',
        'location' => 'Location',
        'client' => 'Client',
        'services' => 'Services',
        'duration' => 'Duration',
        'approved_by' => 'Approved By',
        'budget' => 'Budget',
        'team_size' => 'Team Size',
        'working_days' => 'Working Days',
        'civil_defense' => 'Civil Defense',
        'how_we_solved' => 'How We Solved It',
        'systematic_approach' => 'Our systematic approach to delivering results under pressure',
        'day' => 'Day',
        'days' => 'Days',
        'final_day' => 'Final Day',
        'emergency_survey' => 'Emergency Site Survey',
        'rapid_design' => 'Rapid Design & Approval',
        'express_procurement' => 'Express Equipment Procurement',
        'precision_installation' => 'Precision Installation',
        'testing_commissioning' => 'Testing & Commissioning',
        'training_handover' => 'Training & Handover',
        'project_results' => 'Project Results',
        'measurable_outcomes' => 'Measurable outcomes that exceeded expectations',
        'completion_time' => 'Completion Time',
        'approval_rate' => 'Approval Rate',
        'safety_improvement' => 'Safety Improvement',
        'cost_savings' => 'Cost Savings',
        'client_satisfaction' => 'Client Satisfaction',
        'technical_specs' => 'Technical Specifications',
        'system_details' => 'Complete system specifications and components',
        'pump_specs' => 'Pump Specifications',
        'foam_system' => 'Foam System',
        'piping_network' => 'Piping Network',
        'control_system' => 'Control System',
        'certifications' => 'Certifications',
        'gallery' => 'Project Gallery',
        'installation_photos' => 'Installation photos and progress documentation',
        'related_projects' => 'Related Projects',
        'similar_implementations' => 'Similar implementations you might be interested in',
        'talk_engineer' => '🔴 Talk to Our Engineer',
        'request_similar' => '⚪ Request Similar Project',
        'need_similar' => 'Need a similar solution for your facility?',
        'discuss_requirements' => 'Let\'s discuss your specific requirements and timeline.',
        'request_quote' => '🔴 Request Free Quote',
        'schedule_consultation' => '⚪ Schedule Consultation'
    ],
    'ar' => [
        'home' => 'الرئيسية',
        'projects' => 'المشاريع',
        'project_completed' => '✅ مشروع مكتمل',
        'view_results' => '🔴 عرض نتائج المشروع',
        'project_overview' => 'نظرة عامة على المشروع',
        'key_details' => 'التفاصيل والمواصفات الرئيسية في لمحة',
        'location' => 'الموقع',
        'client' => 'العميل',
        'services' => 'الخدمات',
        'duration' => 'المدة',
        'approved_by' => 'معتمد من',
        'budget' => 'الميزانية',
        'team_size' => 'حجم الفريق',
        'working_days' => 'أيام عمل',
        'civil_defense' => 'الدفاع المدني',
        'how_we_solved' => 'كيف حللنا المشكلة',
        'systematic_approach' => 'نهجنا المنهجي لتقديم النتائج تحت الضغط',
        'day' => 'يوم',
        'days' => 'أيام',
        'final_day' => 'اليوم الأخير',
        'emergency_survey' => 'مسح موقع طارئ',
        'rapid_design' => 'تصميم وموافقة سريعة',
        'express_procurement' => 'شراء معدات سريع',
        'precision_installation' => 'تركيب دقيق',
        'testing_commissioning' => 'اختبار وتشغيل',
        'training_handover' => 'تدريب وتسليم',
        'project_results' => 'نتائج المشروع',
        'measurable_outcomes' => 'نتائج قابلة للقياس تجاوزت التوقعات',
        'completion_time' => 'وقت الإكمال',
        'approval_rate' => 'معدل الموافقة',
        'safety_improvement' => 'تحسين السلامة',
        'cost_savings' => 'توفير التكاليف',
        'client_satisfaction' => 'رضا العميل',
        'technical_specs' => 'المواصفات التقنية',
        'system_details' => 'مواصفات النظام الكاملة والمكونات',
        'pump_specs' => 'مواصفات المضخة',
        'foam_system' => 'نظام الرغوة',
        'piping_network' => 'شبكة الأنابيب',
        'control_system' => 'نظام التحكم',
        'certifications' => 'الشهادات',
        'gallery' => 'معرض المشروع',
        'installation_photos' => 'صور التركيب وتوثيق التقدم',
        'related_projects' => 'مشاريع ذات صلة',
        'similar_implementations' => 'تنفيذات مماثلة قد تهمك',
        'talk_engineer' => '🔴 تحدث مع مهندسنا',
        'request_similar' => '⚪ اطلب مشروع مماثل',
        'need_similar' => 'تحتاج حل مماثل لمنشأتك؟',
        'discuss_requirements' => 'دعنا نناقش متطلباتك المحددة والجدول الزمني.',
        'request_quote' => '🔴 اطلب عرض سعر مجاني',
        'schedule_consultation' => '⚪ جدولة استشارة'
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
            <a href="projects.php"><?php echo $t['projects']; ?></a> / 
            <span><?php echo htmlspecialchars($project['title']); ?></span>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section id="hero" class="hero-bg relative h-screen flex items-center justify-center text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
    
    <div class="relative z-10 text-center max-w-5xl px-6">
        <div class="mb-6 animate-fade-in">
            <div class="project-badge mb-4">
                <?php echo $t['project_completed']; ?>
            </div>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
            <?php echo htmlspecialchars($project['title']); ?>
        </h1>
        <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
            <?php echo htmlspecialchars($project['description']); ?>
        </h2>
        
        <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
            <?php echo htmlspecialchars($project['subtitle']); ?>
        </p>
        
        <div class="animate-bounce-in">
            <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="view-results-btn">
                <?php echo $t['view_results']; ?>
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

<!-- Project Snapshot -->
<section id="snapshot" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['project_overview']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['key_details']; ?></p>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-8 shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo $t['location']; ?></h3>
                    <p class="text-brand-gray"><?php echo htmlspecialchars($project['location']); ?></p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo $t['client']; ?></h3>
                    <p class="text-brand-gray"><?php echo htmlspecialchars($project['client_name']); ?></p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-fire-extinguisher text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo $t['services']; ?></h3>
                    <p class="text-brand-gray"><?php echo htmlspecialchars($project['subtitle']); ?></p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo $t['duration']; ?></h3>
                    <p class="text-brand-gray"><?php echo $project['duration_days']; ?> <?php echo $t['working_days']; ?></p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo $t['approved_by']; ?></h3>
                    <p class="text-brand-gray"><?php echo $t['civil_defense']; ?></p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-dollar-sign text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2"><?php echo $t['budget']; ?></h3>
                    <p class="text-brand-gray"><?php echo htmlspecialchars($project['budget_range'] ?? 'Confidential'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Approach -->
<section id="approach" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['how_we_solved']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['systematic_approach']; ?></p>
        </div>
        
        <div class="relative timeline-line">
            <div class="space-y-12">
                <!-- Step 1 -->
                <div class="approach-step animate">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="bg-gray-50 rounded-xl p-8">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['day']; ?> 1</span>
                                    <h3 class="text-2xl font-semibold"><?php echo $t['emergency_survey']; ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                Our certified consultants conducted a comprehensive risk assessment within 24 hours, 
                                identifying critical areas requiring immediate attention and mapping out the optimal 
                                system design for paint storage zones.
                            </p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-search text-6xl text-brand-red"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="approach-step animate">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="text-center order-2 lg:order-1">
                            <i class="fas fa-drafting-compass text-6xl text-brand-red"></i>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-8 order-1 lg:order-2">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['days']; ?> 2-3</span>
                                    <h3 class="text-2xl font-semibold"><?php echo $t['rapid_design']; ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                Engineering team created detailed system designs with foam suppression calculations, 
                                pump specifications, and piping layouts. All designs were pre-approved with civil 
                                defense to ensure compliance.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="approach-step animate">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="bg-gray-50 rounded-xl p-8">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['days']; ?> 4-5</span>
                                    <h3 class="text-2xl font-semibold"><?php echo $t['express_procurement']; ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                Sourced UL/FM certified pumps, foam concentrate, and specialized nozzles through 
                                our established supplier network. All equipment arrived on-site within 48 hours 
                                with proper certification documentation.
                            </p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-truck text-6xl text-brand-red"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Step 4 -->
                <div class="approach-step animate">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="text-center order-2 lg:order-1">
                            <i class="fas fa-tools text-6xl text-brand-red"></i>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-8 order-1 lg:order-2">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['days']; ?> 6-10</span>
                                    <h3 class="text-2xl font-semibold"><?php echo $t['precision_installation']; ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                Professional installation in three phases: pump room setup, piping network, 
                                and foam system integration. Work continued around factory operations with 
                                minimal disruption to production.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Step 5 -->
                <div class="approach-step animate">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="bg-gray-50 rounded-xl p-8">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['days']; ?> 11-12</span>
                                    <h3 class="text-2xl font-semibold"><?php echo $t['testing_commissioning']; ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                Comprehensive system testing including pump performance, foam concentration, 
                                and coverage patterns. All tests documented and witnessed by client safety team 
                                and civil defense representative.
                            </p>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-clipboard-check text-6xl text-brand-red"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Step 6 -->
                <div class="approach-step animate">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                        <div class="text-center order-2 lg:order-1">
                            <i class="fas fa-graduation-cap text-6xl text-brand-red"></i>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-8 order-1 lg:order-2">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['final_day']; ?></span>
                                    <h3 class="text-2xl font-semibold"><?php echo $t['training_handover']; ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                Conducted comprehensive training for facility staff on system operation, 
                                maintenance procedures, and emergency protocols. Delivered complete 
                                documentation package and maintenance manual.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Results -->
<section id="results" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['project_results']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['measurable_outcomes']; ?></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="results-card animate bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-brand-red mb-2"><?php echo $project['duration_days']; ?></div>
                <div class="text-brand-gray"><?php echo $t['completion_time']; ?></div>
            </div>
            
            <div class="results-card animate bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-brand-red mb-2">100%</div>
                <div class="text-brand-gray"><?php echo $t['approval_rate']; ?></div>
            </div>
            
            <div class="results-card animate bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-brand-red mb-2">95%</div>
                <div class="text-brand-gray"><?php echo $t['safety_improvement']; ?></div>
            </div>
            
            <div class="results-card animate bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-smile text-white text-2xl"></i>
                </div>
                <div class="text-3xl font-bold text-brand-red mb-2">100%</div>
                <div class="text-brand-gray"><?php echo $t['client_satisfaction']; ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Technical Specifications -->
<section id="specs" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['technical_specs']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['system_details']; ?></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['pump_specs']; ?></h3>
                <ul class="text-brand-gray space-y-2">
                    <li>• UL/FM Certified Fire Pumps</li>
                    <li>• 500 GPM Capacity</li>
                    <li>• 150 PSI Operating Pressure</li>
                    <li>• Diesel Engine Driven</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['foam_system']; ?></h3>
                <ul class="text-brand-gray space-y-2">
                    <li>• 3% AFFF Foam Concentrate</li>
                    <li>• Proportioning System</li>
                    <li>• Specialized Nozzles</li>
                    <li>• Coverage: 500 sqm</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['piping_network']; ?></h3>
                <ul class="text-brand-gray space-y-2">
                    <li>• Schedule 40 Steel Pipes</li>
                    <li>• 4" Main Lines</li>
                    <li>• 2" Branch Lines</li>
                    <li>• UL Listed Fittings</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['control_system']; ?></h3>
                <ul class="text-brand-gray space-y-2">
                    <li>• PLC Control Panel</li>
                    <li>• Remote Monitoring</li>
                    <li>• Alarm Integration</li>
                    <li>• Manual Override</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['certifications']; ?></h3>
                <ul class="text-brand-gray space-y-2">
                    <li>• UL Listed Components</li>
                    <li>• FM Approved Systems</li>
                    <li>• Civil Defense Approved</li>
                    <li>• ISO 9001:2015</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-8">
                <h3 class="text-xl font-semibold mb-4"><?php echo $t['gallery']; ?></h3>
                <p class="text-brand-gray"><?php echo $t['installation_photos']; ?></p>
                <button class="mt-4 bg-brand-red text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    View Gallery
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Call-to-Action -->
<section id="cta" class="py-20 blueprint-bg text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo $t['need_similar']; ?>
        </h2>
        <p class="text-xl mb-12 text-gray-300">
            <?php echo $t['discuss_requirements']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" id="talk-engineer-btn">
                <?php echo $t['talk_engineer']; ?>
            </button>
            <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="request-similar-btn">
                <?php echo $t['request_similar']; ?>
            </button>
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