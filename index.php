<?php
require_once 'config.php';
require_once 'includes/functions.php';
include 'includes/header.php';
include 'includes/navbar.php';

$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';
$page_id = 1; // الصفحة الرئيسية
$hero = fetchSections($page_id, 'hero', $lang);
$advantages = fetchSections($page_id, 'advantage', $lang);
$featuredServices = fetchFeaturedServices($lang, 4); // جلب 4 خدمات مميزة
$featuredProjects = fetchFeaturedProjects($lang, 3); // جلب 3 مشاريع مميزة
$testimonials = fetchTestimonials($lang, 2); // جلب 2 شهادات
$industrialZones = fetchIndustrialZones($lang, 3); // جلب 3 مناطق صناعية
?>
<!-- Hero Section with Carousel -->
<section id="hero" class="relative h-screen overflow-hidden">
    <?php if ($hero && count($hero) > 0): ?>
        <?php foreach ($hero as $i => $slide): ?>
        <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>">
            <div class="hero-bg absolute inset-0"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
            <div class="relative z-10 h-full flex items-center justify-center">
                <div class="text-center text-white max-w-5xl px-6">
                    <div class="mb-6 animate-fade-in">
                        <i class="fas fa-fire-extinguisher text-6xl text-brand-red mb-4"></i>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                        <?= htmlspecialchars($slide['title'] ?? '') ?>
                    </h1>
                    <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
                        <?= htmlspecialchars($slide['subtitle'] ?? '') ?>
                    </h2>
                    <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
                        <?= htmlspecialchars($slide['content'] ?? '') ?>
                    </p>
                    <div class="flex flex-col sm:flex-row gap-6 justify-center animate-bounce-in">
                        <?php if (!empty($slide['button_text'])): ?>
                        <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors">
                            <?= htmlspecialchars($slide['button_text']) ?>
                        </button>
                        <?php endif; ?>
                        <?php if (!empty($slide['button_url'])): ?>
                        <a href="<?= htmlspecialchars($slide['button_url']) ?>" class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors">
                            <?= $lang === 'ar' ? 'تحميل الكتالوج' : 'Download Catalog' ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="hero-slide active">
            <div class="hero-bg absolute inset-0"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
            <div class="relative z-10 h-full flex items-center justify-center">
                <div class="text-center text-white max-w-5xl px-6">
                    <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">Welcome</h1>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Carousel Controls and Scroll Cue (ابقهم كما هم) -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex space-x-3">
        <div class="carousel-dot active" data-slide="0"></div>
        <div class="carousel-dot" data-slide="1"></div>
        <div class="carousel-dot" data-slide="2"></div>
    </div>
    <button class="carousel-arrow carousel-prev">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="carousel-arrow carousel-next">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20">
        <div class="bounce-arrow text-white text-2xl">
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</section>

<!-- Key Advantages -->
<section id="advantages" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="badge-reveal inline-block bg-brand-red text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                🔥 WHY CHOOSE SPHINX FIRE
            </div>
            <h2 class="text-4xl font-bold mb-4">Industrial Fire Safety Experts</h2>
            <p class="text-xl text-brand-gray">Five key advantages that make us your ideal safety partner</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($advantages && count($advantages) > 0): ?>
                <?php 
                $advantage_icons = [
                    'fas fa-map-marker-alt',
                    'fas fa-cogs', 
                    'fas fa-user-tie',
                    'fas fa-clock',
                    'fas fa-certificate',
                    'fas fa-shield-alt'
                ];
                foreach ($advantages as $i => $advantage): 
                    $icon = $advantage_icons[$i] ?? 'fas fa-star';
                ?>
                <div class="advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="<?= $icon ?> text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($advantage['title'] ?? '') ?></h3>
                    <p class="text-brand-gray mb-4">
                        <?= htmlspecialchars($advantage['content'] ?? '') ?>
                    </p>
                    <div class="text-brand-red font-semibold"><?= htmlspecialchars($advantage['subtitle'] ?? '') ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback content if no advantages found -->
                <div class="advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-star text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Professional Service</h3>
                    <p class="text-brand-gray mb-4">
                        Expert fire safety solutions for industrial facilities.
                    </p>
                    <div class="text-brand-red font-semibold">Certified Team</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="badge-reveal inline-block bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                🛡️ COMPLETE PROTECTION
            </div>
            <h2 class="text-4xl font-bold mb-4">Our Fire Safety Services</h2>
            <p class="text-xl text-brand-gray">Comprehensive solutions for every aspect of industrial fire protection</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featuredServices as $service): ?>
            <div class="service-card bg-white rounded-xl overflow-hidden shadow-lg">
                <div class="relative h-48">
                    <img src="<?php echo htmlspecialchars($service['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($service['name']); ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($service['name']); ?></h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-brand-gray mb-6">
                        <?php echo htmlspecialchars($service['short_description']); ?>
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="service.php?slug=<?php echo htmlspecialchars($service['slug']); ?>" class="text-brand-red font-semibold hover:underline">
                            <?php echo $lang === 'ar' ? 'اعرف المزيد' : 'Learn More'; ?>
                        </a>
                        <button class="bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $lang === 'ar' ? 'اطلب عرض سعر' : 'Get Quote'; ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="services.html" class="inline-block bg-brand-black text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                View All Services →
            </a>
        </div>
    </div>
</section>

<!-- Featured Projects -->
<section id="projects" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="badge-reveal inline-block bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                ✅ PROVEN RESULTS
            </div>
            <h2 class="text-4xl font-bold mb-4">Featured Projects</h2>
            <p class="text-xl text-brand-gray">Real implementations, real impact, real safety</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featuredProjects as $project): ?>
            <div class="project-card bg-white rounded-xl overflow-hidden shadow-lg">
                <div class="relative h-48">
                    <img src="<?php echo htmlspecialchars($project['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($project['title']); ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        <?php echo $lang === 'ar' ? 'مكتمل' : 'COMPLETED'; ?>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p class="text-brand-red font-semibold mb-2"><?php echo htmlspecialchars($project['subtitle']); ?></p>
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($project['description']); ?>
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-brand-gray">
                            <i class="fas fa-calendar mr-1"></i> <?php echo htmlspecialchars($project['duration']); ?>
                        </span>
                        <a href="project.php?slug=<?php echo htmlspecialchars($project['slug']); ?>" class="bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            <?php echo $lang === 'ar' ? 'عرض التفاصيل' : 'View Details'; ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="projects.html" class="inline-block bg-brand-black text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                View All Projects →
            </a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section id="testimonials" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="badge-reveal inline-block bg-yellow-500 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                ⭐ CLIENT TESTIMONIALS
            </div>
            <h2 class="text-4xl font-bold mb-4">What Our Clients Say</h2>
            <p class="text-xl text-brand-gray">Real feedback from industrial facilities we've protected</p>
        </div>
        
        <div class="testimonial-carousel">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card bg-white rounded-xl p-8 shadow-lg">
                    <div class="flex items-center mb-6">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($testimonial['client_name']); ?></h3>
                            <p class="text-brand-gray"><?php echo htmlspecialchars($testimonial['client_position']); ?>, <?php echo htmlspecialchars($testimonial['company_name']); ?></p>
                        </div>
                    </div>
                    <blockquote class="text-lg text-brand-gray italic mb-6">
                        "<?php echo htmlspecialchars($testimonial['testimonial_text']); ?>"
                    </blockquote>
                    <div class="flex text-yellow-400">
                        <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <button class="inline-block bg-brand-black text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors" id="request-references-btn">
                Request References
            </button>
        </div>
    </div>
</section>

<!-- Industrial Zones We Serve -->
<section id="locations" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="badge-reveal inline-block bg-purple-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                🏭 INDUSTRIAL COVERAGE
            </div>
            <h2 class="text-4xl font-bold mb-4">Industrial Zones We Serve</h2>
            <p class="text-xl text-brand-gray">Local expertise in Egypt's major industrial cities</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($industrialZones as $zone): ?>
            <a href="city.php?slug=<?php echo htmlspecialchars($zone['slug']); ?>" class="block bg-gray-50 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="relative h-48">
                    <img src="images/locations/<?php echo htmlspecialchars($zone['slug']); ?>.jpg" 
                         alt="<?php echo htmlspecialchars($zone['name']); ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($zone['name']); ?></h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-brand-gray mb-4">
                        <?php echo htmlspecialchars($zone['description']); ?>
                    </p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-brand-gray">
                            <i class="fas fa-industry mr-1"></i> <?php echo htmlspecialchars($zone['project_count']); ?>+ Projects
                        </span>
                        <span class="text-brand-red font-semibold">
                            <?php echo $lang === 'ar' ? 'عرض الخدمات' : 'View Services'; ?> →
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <a href="locations.html" class="inline-block bg-brand-black text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                View All Locations →
            </a>
        </div>
    </div>
</section>

<?php
// جلب بيانات السكشن الأخير من قاعدة البيانات
$finalCta = fetchSections(1, 'cta', $lang, 1);

// ترجمات السكشن الأخير
$ctaTranslations = [
    'en' => [
        'title' => "Let's build a safer tomorrow—starting with your facility today.",
        'subtitle' => 'Join hundreds of facilities that trust Sphinx Fire for complete fire safety excellence.',
        'primary_btn' => '🔴 Request Free Consultation',
        'secondary_btn' => '⚪ Download Company Profile',
        'features' => 'Free consultation • Expert assessment • Response within 24 hours'
    ],
    'ar' => [
        'title' => 'دعنا نبني غداً أكثر أماناً—بدءاً بمنشأتك اليوم.',
        'subtitle' => 'انضم إلى مئات المنشآت التي تثق في سفينكس فاير للتميز الكامل في السلامة من الحريق.',
        'primary_btn' => '🔴 اطلب استشارة مجانية',
        'secondary_btn' => '⚪ حمل ملف الشركة',
        'features' => 'استشارة مجانية • تقييم خبير • استجابة خلال 24 ساعة'
    ]
];

$cta = $ctaTranslations[$lang];
?>

<!-- Final CTA -->
<section id="cta" class="py-20 blueprint-bg text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo htmlspecialchars($finalCta[0]['title'] ?? $cta['title']); ?>
        </h2>
        <p class="text-xl mb-12 text-gray-300">
            <?php echo htmlspecialchars($finalCta[0]['content'] ?? $cta['subtitle']); ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center mb-8">
            <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" id="final-cta-btn">
                <?php echo $cta['primary_btn']; ?>
            </button>
            <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="download-pdf-btn">
                <?php echo $cta['secondary_btn']; ?>
            </button>
        </div>
        
        <div class="text-gray-400 text-sm">
            <?php echo $cta['features']; ?>
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