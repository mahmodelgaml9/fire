<?php
require_once 'config.php';
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'ar';
$page_id = 2; // صفحة about

// جلب السكاشن ديناميكيًا
$hero = fetchSections($page_id, 'hero', $lang, 1);
$overview = fetchSections($page_id, 'overview', $lang, 1);
$values = fetchAboutValues($page_id, $lang);
$advantages = fetchAboutAdvantages($lang);
$processSteps = fetchAboutProcessSteps($lang);
$teamMembers = fetchTeamMembers($lang);

// ترجمات الصفحة
$translations = [
    'en' => [
        'our_foundation' => 'Our Foundation',
        'foundation_subtitle' => 'The principles that drive everything we do',
        'what_sets_us_apart' => 'What Sets Us Apart',
        'advantages_subtitle' => 'Five key advantages that make Sphinx Fire your ideal safety partner',
        'from_consultation' => 'From Consultation to Certification',
        'process_subtitle' => 'Our proven 6-step process ensures complete project success',
        'meet_experts' => 'Meet the Experts',
        'experts_subtitle' => 'The certified professionals behind your safety',
        'trusted_partners' => 'Trusted Partners & Certifications',
        'partners_subtitle' => 'Working with industry leaders to deliver excellence',
        'technology_integration' => 'Technology Integration',
        'certifications_standards' => 'Certifications & Standards',
        'step' => 'Step',
        'free_consultation' => 'Free consultation',
        'expert_assessment' => 'Expert assessment',
        'response_24h' => 'Response within 24 hours',
        'request_consultation' => '🔴 Request Consultation',
        'download_profile' => '⚪ Download Company Profile'
    ],
    'ar' => [
        'our_foundation' => 'أساسياتنا',
        'foundation_subtitle' => 'المبادئ التي تقود كل ما نقوم به',
        'what_sets_us_apart' => 'ما يميزنا',
        'advantages_subtitle' => 'خمس مزايا رئيسية تجعل سفينكس فاير شريكك المثالي للسلامة',
        'from_consultation' => 'من الاستشارة إلى الشهادة',
        'process_subtitle' => 'عملية الـ 6 خطوات المثبتة تضمن نجاح المشروع الكامل',
        'meet_experts' => 'تعرف على الخبراء',
        'experts_subtitle' => 'المحترفون المعتمدون وراء سلامتك',
        'trusted_partners' => 'الشركاء الموثوقون والشهادات',
        'partners_subtitle' => 'نعمل مع قادة الصناعة لتقديم التميز',
        'technology_integration' => 'تكامل التكنولوجيا',
        'certifications_standards' => 'الشهادات والمعايير',
        'step' => 'خطوة',
        'free_consultation' => 'استشارة مجانية',
        'expert_assessment' => 'تقييم خبير',
        'response_24h' => 'استجابة خلال 24 ساعة',
        'request_consultation' => '🔴 اطلب استشارة',
        'download_profile' => '⚪ حمل ملف الشركة'
    ]
];

$t = $translations[$lang];
?>

    <!-- Hero Section -->
    <section id="hero" class="hero-bg relative h-screen flex items-center justify-center text-white">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
        <div class="relative z-10 text-center max-w-5xl px-6">
            <div class="mb-6 animate-fade-in">
                <i class="fas fa-shield-alt text-6xl text-brand-red mb-4"></i>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                <?= htmlspecialchars($hero && isset($hero[0]['title']) ? $hero[0]['title'] : '') ?>
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
                <?= htmlspecialchars($hero && isset($hero[0]['subtitle']) ? $hero[0]['subtitle'] : '') ?>
            </h2>
            <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
                <?= htmlspecialchars($hero && isset($hero[0]['content']) ? $hero[0]['content'] : '') ?>
            </p>
            <div class="animate-bounce-in">
                <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="explore-services-btn">
                    <?= htmlspecialchars($hero && isset($hero[0]['button_text']) ? $hero[0]['button_text'] : '') ?>
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

    <!-- Company Overview -->
    <section id="overview" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6">
                        <?= htmlspecialchars($overview && isset($overview[0]['title']) ? $overview[0]['title'] : '') ?>
                    </h2>
                    <div class="space-y-6 text-lg text-brand-gray leading-relaxed">
                        <p><?= htmlspecialchars($overview && isset($overview[0]['content']) ? $overview[0]['content'] : '') ?></p>
                    </div>
                </div>
                <div class="relative">
                    <div class="bg-gray-100 rounded-xl p-8 text-center">
                        <img src="https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop" 
                             alt="Sphinx Fire team at industrial site" 
                             class="w-full h-64 object-cover rounded-lg mb-6">
                        <h3 class="text-2xl font-bold mb-4">Your Safety Partners</h3>
                        <p class="text-brand-gray">
                            Professional team conducting site assessment and system installation at a major industrial facility.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision Mission Values -->
    <section id="values" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Our Foundation</h2>
                <p class="text-xl text-brand-gray">The principles that drive everything we do</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($values as $val): ?>
                <div class="value-card bg-white rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <?php if (stripos($val['title'], 'vision') !== false || stripos($val['title'], 'الرؤية') !== false): ?>
                            <i class="fas fa-eye text-white text-2xl"></i>
                        <?php elseif (stripos($val['title'], 'mission') !== false || stripos($val['title'], 'الرسالة') !== false): ?>
                            <i class="fas fa-target text-white text-2xl"></i>
                        <?php else: ?>
                            <i class="fas fa-heart text-white text-2xl"></i>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($val['title']) ?></h3>
                    <p class="text-brand-gray">
                        <?= htmlspecialchars($val['content']) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Why Sphinx Fire -->
    <section id="advantages" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo $t['what_sets_us_apart']; ?></h2>
                <p class="text-xl text-brand-gray"><?php echo $t['advantages_subtitle']; ?></p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($advantages as $advantage): ?>
                <div class="value-card bg-gray-50 rounded-xl p-8 text-center">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <?php 
                        $icon = 'fas fa-star';
                        if (stripos($advantage['title'], 'location') !== false || stripos($advantage['title'], 'موقع') !== false) {
                            $icon = 'fas fa-map-marker-alt';
                        } elseif (stripos($advantage['title'], 'integration') !== false || stripos($advantage['title'], 'تكامل') !== false) {
                            $icon = 'fas fa-cogs';
                        } elseif (stripos($advantage['title'], 'expert') !== false || stripos($advantage['title'], 'خبير') !== false) {
                            $icon = 'fas fa-user-tie';
                        } elseif (stripos($advantage['title'], 'responsive') !== false || stripos($advantage['title'], 'استجابة') !== false) {
                            $icon = 'fas fa-clock';
                        } elseif (stripos($advantage['title'], 'compliance') !== false || stripos($advantage['title'], 'امتثال') !== false) {
                            $icon = 'fas fa-certificate';
                        }
                        ?>
                        <i class="<?php echo $icon; ?> text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4"><?php echo htmlspecialchars($advantage['title']); ?></h3>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($advantage['content']); ?>
                    </p>
                    <?php if (!empty($advantage['subtitle'])): ?>
                    <div class="text-brand-red font-semibold"><?php echo htmlspecialchars($advantage['subtitle']); ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Our Process -->
    <section id="process" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo $t['from_consultation']; ?></h2>
                <p class="text-xl text-brand-gray"><?php echo $t['process_subtitle']; ?></p>
            </div>
            
            <div class="relative timeline-line">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($processSteps as $index => $step): ?>
                    <div class="process-step">
                        <div class="bg-white rounded-xl p-8 shadow-lg">
                            <div class="flex items-center mb-4">
                                <div class="timeline-dot"></div>
                                <div class="ml-6">
                                    <span class="text-brand-red font-bold text-lg"><?php echo $t['step']; ?> <?php echo $index + 1; ?></span>
                                    <h3 class="text-2xl font-semibold"><?php echo htmlspecialchars($step['title']); ?></h3>
                                </div>
                            </div>
                            <p class="text-brand-gray">
                                <?php echo htmlspecialchars($step['content']); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Meet the Experts -->
    <section id="team" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4"><?php echo $t['meet_experts']; ?></h2>
                <p class="text-xl text-brand-gray"><?php echo $t['experts_subtitle']; ?></p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($teamMembers as $member): ?>
                <div class="team-card bg-gray-50 rounded-xl p-8 text-center">
                    <div class="w-24 h-24 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <?php 
                        $icon = 'fas fa-user-tie';
                        if (stripos($member['title'], 'engineering') !== false || stripos($member['title'], 'هندسة') !== false) {
                            $icon = 'fas fa-hard-hat';
                        } elseif (stripos($member['title'], 'consultation') !== false || stripos($member['title'], 'استشارة') !== false) {
                            $icon = 'fas fa-user-tie';
                        } elseif (stripos($member['title'], 'technical') !== false || stripos($member['title'], 'تقني') !== false) {
                            $icon = 'fas fa-tools';
                        }
                        ?>
                        <i class="<?php echo $icon; ?> text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-2"><?php echo htmlspecialchars($member['title']); ?></h3>
                    <p class="text-brand-red font-semibold mb-4"><?php echo htmlspecialchars($member['subtitle']); ?></p>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($member['content']); ?>
                    </p>
                    <?php if (!empty($member['subtitle'])): ?>
                    <div class="flex justify-center space-x-4 text-sm">
                        <span class="bg-white px-3 py-1 rounded-full"><?php echo htmlspecialchars($member['subtitle']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Partnerships & Certifications -->
    <section id="partnerships" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Trusted Partners & Certifications</h2>
                <p class="text-xl text-brand-gray">Working with industry leaders to deliver excellence</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Technology Partners -->
                <div>
                    <h3 class="text-2xl font-semibold mb-8 text-center">Technology Integration</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-cogs text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">SCADA Systems</p>
                        </div>
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-microchip text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">Siemens</p>
                        </div>
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-network-wired text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">BMS Integration</p>
                        </div>
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-server text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">Control Systems</p>
                        </div>
                    </div>
                </div>
                
                <!-- Certifications -->
                <div>
                    <h3 class="text-2xl font-semibold mb-8 text-center">Certifications & Standards</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-certificate text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">UL/FM Listed</p>
                        </div>
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-fire text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">NFPA Standards</p>
                        </div>
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-shield-alt text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">OSHA Compliant</p>
                        </div>
                        <div class="partner-logo bg-white p-6 rounded-lg text-center shadow-md">
                            <i class="fas fa-flag text-4xl text-brand-red mb-2"></i>
                            <p class="font-semibold">Civil Defense</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section id="cta" class="py-20 blueprint-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
        
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">
                <?php echo $t['request_consultation']; ?>
            </h2>
            <p class="text-xl mb-12 text-gray-300">
                Join hundreds of facilities that trust Sphinx Fire for complete fire safety excellence.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center mb-8">
                <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" id="request-consultation-btn">
                    <?php echo $t['request_consultation']; ?>
                </button>
                <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="download-profile-btn">
                    <?php echo $t['download_profile']; ?>
                </button>
            </div>
            
            <div class="text-gray-400 text-sm">
                <?php echo $t['free_consultation']; ?> • <?php echo $t['expert_assessment']; ?> • <?php echo $t['response_24h']; ?>
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