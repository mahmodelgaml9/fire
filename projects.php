<?php
require_once 'config.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';

// جلب البيانات الديناميكية
$hero = fetchSections(4, 'hero', $lang, 1); // page_id = 4 للمشاريع
$stats = fetchSections(4, 'stats', $lang, 1);
$projects = fetchProjects($lang, null, null, 12); // جلب جميع المشاريع
$categories = fetchCategories('project', $lang);
$featuredProject = fetchFeaturedProjects($lang, 1);

// ترجمات الصفحة
$translations = [
    'en' => [
        'home' => 'Home',
        'projects' => 'Projects',
        'civil_defense_approved' => '✅ CIVIL DEFENSE APPROVED',
        'real_safety_real_sites' => 'Real Safety. Real Sites. Real Impact.',
        'explore_projects' => 'Explore our recent projects and see how we bring industrial safety to life.',
        'witness_track_record' => 'From design to installation to approval - witness our proven track record across Egypt\'s industrial facilities.',
        'talk_engineers' => '🔴 Talk to Our Engineers',
        'download_portfolio' => '⚪ Download Company Portfolio',
        'projects_completed' => 'Projects Completed',
        'civil_defense_approval' => 'Civil Defense Approval',
        'industrial_sectors' => 'Industrial Sectors',
        'support_available' => 'Support Available',
        'filter_projects' => 'Filter Projects by Category',
        'explore_work' => 'Explore our work across different industries and system types',
        'all_projects' => 'All Projects',
        'manufacturing' => 'Manufacturing',
        'chemical' => 'Chemical',
        'retail_malls' => 'Retail & Malls',
        'warehouses' => 'Warehouses',
        'food_industry' => 'Food Industry',
        'project_portfolio' => 'Our Project Portfolio',
        'real_implementations' => 'Real implementations, proven results, satisfied clients',
        'view_details' => 'View Details',
        'completed' => 'COMPLETED',
        'ongoing' => 'ONGOING',
        'certified' => 'CERTIFIED',
        'project_month' => '🏆 PROJECT OF THE MONTH',
        'client_spotlight' => 'Client Spotlight',
        'outstanding_results' => 'Outstanding results that exceed expectations',
        'days_complete' => 'Days to Complete',
        'first_try_approval' => 'First-Try Approval',
        'safety_incidents' => 'Safety Incidents Since',
        'talk_similar' => 'Talk to Us About Similar Projects →',
        'need_similar' => 'Need a similar safety solution for your facility?',
        'every_project' => 'Every project starts with understanding your unique requirements. Let\'s discuss your needs.',
        'request_site_visit' => '🔴 Request Free Site Visit',
        'custom_design' => '⚪ Custom Design Consultation'
    ],
    'ar' => [
        'home' => 'الرئيسية',
        'projects' => 'المشاريع',
        'civil_defense_approved' => '✅ معتمد من الدفاع المدني',
        'real_safety_real_sites' => 'سلامة حقيقية. مواقع حقيقية. تأثير حقيقي.',
        'explore_projects' => 'استكشف مشاريعنا الحديثة وشاهد كيف نجعل السلامة الصناعية حية.',
        'witness_track_record' => 'من التصميم إلى التركيب إلى الموافقة - شاهد سجلنا المثبت عبر المنشآت الصناعية في مصر.',
        'talk_engineers' => '🔴 تحدث مع مهندسينا',
        'download_portfolio' => '⚪ حمل ملف الشركة',
        'projects_completed' => 'مشروع مكتمل',
        'civil_defense_approval' => 'موافقة الدفاع المدني',
        'industrial_sectors' => 'قطاع صناعي',
        'support_available' => 'دعم متاح',
        'filter_projects' => 'تصفية المشاريع حسب الفئة',
        'explore_work' => 'استكشف عملنا عبر الصناعات المختلفة وأنواع الأنظمة',
        'all_projects' => 'جميع المشاريع',
        'manufacturing' => 'تصنيع',
        'chemical' => 'كيميائي',
        'retail_malls' => 'تجاري ومولات',
        'warehouses' => 'مخازن',
        'food_industry' => 'صناعة غذائية',
        'project_portfolio' => 'محفظة مشاريعنا',
        'real_implementations' => 'تنفيذات حقيقية، نتائج مثبتة، عملاء راضون',
        'view_details' => 'عرض التفاصيل',
        'completed' => 'مكتمل',
        'ongoing' => 'قيد التنفيذ',
        'certified' => 'معتمد',
        'project_month' => '🏆 مشروع الشهر',
        'client_spotlight' => 'نظرة على العميل',
        'outstanding_results' => 'نتائج متميزة تتجاوز التوقعات',
        'days_complete' => 'يوم للإكمال',
        'first_try_approval' => 'موافقة من المحاولة الأولى',
        'safety_incidents' => 'حادث سلامة منذ',
        'talk_similar' => 'تحدث معنا عن مشاريع مماثلة →',
        'need_similar' => 'تحتاج حل سلامة مماثل لمنشأتك؟',
        'every_project' => 'كل مشروع يبدأ بفهم متطلباتك الفريدة. دعنا نناقش احتياجاتك.',
        'request_site_visit' => '🔴 اطلب زيارة موقع مجانية',
        'custom_design' => '⚪ استشارة تصميم مخصص'
    ]
];

$t = $translations[$lang];

// تضمين الهيدر والنافبار
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section id="hero" class="hero-bg relative h-screen flex items-center justify-center text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
    
    <div class="relative z-10 text-center max-w-5xl px-6">
        <div class="mb-6 animate-fade-in">
            <div class="inline-block bg-green-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <?php echo $t['civil_defense_approved']; ?>
            </div>
        </div>
        
        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
            <?php echo isset($hero[0]['title']) ? $hero[0]['title'] : $t['real_safety_real_sites']; ?>
        </h1>
        <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
            <?php echo isset($hero[0]['subtitle']) ? $hero[0]['subtitle'] : $t['explore_projects']; ?>
        </h2>
        
        <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
            <?php echo isset($hero[0]['content']) ? $hero[0]['content'] : $t['witness_track_record']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center animate-bounce-in">
            <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="talk-engineers-btn">
                <?php echo $t['talk_engineers']; ?>
            </button>
            <button class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="download-portfolio-btn">
                <?php echo $t['download_portfolio']; ?>
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

<!-- Stats Section -->
<section id="stats" class="py-16 bg-brand-black text-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div class="animate-zoom-in">
                <div class="stats-counter">50+</div>
                <div class="text-gray-300"><?php echo $t['projects_completed']; ?></div>
            </div>
            <div class="animate-zoom-in">
                <div class="stats-counter">100%</div>
                <div class="text-gray-300"><?php echo $t['civil_defense_approval']; ?></div>
            </div>
            <div class="animate-zoom-in">
                <div class="stats-counter">15</div>
                <div class="text-gray-300"><?php echo $t['industrial_sectors']; ?></div>
            </div>
            <div class="animate-zoom-in">
                <div class="stats-counter">24/7</div>
                <div class="text-gray-300"><?php echo $t['support_available']; ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section id="filters" class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-4"><?php echo $t['filter_projects']; ?></h2>
            <p class="text-brand-gray"><?php echo $t['explore_work']; ?></p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-4">
            <button class="filter-tag active bg-gray-200 text-brand-black px-6 py-3 rounded-full font-semibold" data-filter="all">
                <?php echo $t['all_projects']; ?>
            </button>
            <?php foreach ($categories as $category): ?>
            <button class="filter-tag bg-gray-200 text-brand-black px-6 py-3 rounded-full font-semibold" data-filter="<?php echo htmlspecialchars($category['slug']); ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Projects Grid -->
<section id="projects-grid" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['project_portfolio']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['real_implementations']; ?></p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="projects-container">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                <div class="project-card bg-white rounded-xl shadow-lg overflow-hidden" data-category="<?php echo htmlspecialchars($project['category_slug'] ?? 'all'); ?>">
                    <div class="project-image relative">
                        <img src="<?php echo htmlspecialchars($project['featured_image'] ?? 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop'); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                             class="w-full h-64 object-cover">
                        <div class="project-badge completion-badge"><?php echo strtoupper($project['status']); ?></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($project['title']); ?></h3>
                        <p class="text-brand-red font-semibold mb-2"><?php echo htmlspecialchars($project['subtitle'] ?? ''); ?></p>
                        <p class="text-brand-gray mb-4">
                            <?php echo htmlspecialchars($project['description'] ?? ''); ?>
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-brand-gray">
                                <i class="fas fa-calendar mr-1"></i> 
                                <?php if ($project['duration_days']): ?>
                                    <?php echo $project['duration_days']; ?> <?php echo $lang == 'ar' ? 'يوم' : 'days'; ?>
                                <?php else: ?>
                                    <?php echo $project['status'] == 'completed' ? ($lang == 'ar' ? 'مكتمل' : 'Completed') : ($lang == 'ar' ? 'قيد التنفيذ' : 'In progress'); ?>
                                <?php endif; ?>
                            </div>
                            <a href="project.php?slug=<?php echo htmlspecialchars($project['slug']); ?>" class="bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                <?php echo $t['view_details']; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- مشاريع افتراضية إذا لم توجد مشاريع -->
                <div class="project-card bg-white rounded-xl shadow-lg overflow-hidden" data-category="manufacturing">
                    <div class="project-image relative">
                        <img src="https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop" 
                             alt="Delta Paint Manufacturing Fire System" 
                             class="w-full h-64 object-cover">
                        <div class="project-badge completion-badge"><?php echo $t['completed']; ?></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Delta Paint Manufacturing</h3>
                        <p class="text-brand-red font-semibold mb-2">Complete Fire Network + Foam Suppression</p>
                        <p class="text-brand-gray mb-4">
                            UL/FM certified firefighting system with specialized foam suppression for paint storage areas. 
                            Passed civil defense inspection on first attempt.
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-brand-gray">
                                <i class="fas fa-calendar mr-1"></i> <?php echo $lang == 'ar' ? 'مكتمل في 12 يوم' : 'Completed in 12 days'; ?>
                            </div>
                            <a href="project-delta-paint.html" class="bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                <?php echo $t['view_details']; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Highlighted Project -->
<?php if (!empty($featuredProject)): ?>
<section id="highlighted-project" class="py-20 highlight-project">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <div class="inline-block bg-brand-red text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                <?php echo $t['project_month']; ?>
            </div>
            <h2 class="text-4xl font-bold mb-4"><?php echo $t['client_spotlight']; ?></h2>
            <p class="text-xl text-brand-gray"><?php echo $t['outstanding_results']; ?></p>
        </div>
        
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-12">
                    <h3 class="text-3xl font-bold mb-6">
                        <?php echo htmlspecialchars($featuredProject[0]['title']); ?>
                    </h3>
                    
                    <?php if (!empty($featuredProject[0]['testimonial'])): ?>
                    <blockquote class="text-xl text-brand-gray italic mb-6">
                        "<?php echo htmlspecialchars($featuredProject[0]['testimonial']); ?>"
                    </blockquote>
                    
                    <div class="mb-6">
                        <p class="font-semibold"><?php echo htmlspecialchars($featuredProject[0]['testimonial_author'] ?? 'Client'); ?></p>
                        <p class="text-brand-gray"><?php echo htmlspecialchars($featuredProject[0]['testimonial_position'] ?? 'Project Manager'); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-3 gap-6 mb-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-brand-red"><?php echo $featuredProject[0]['duration_days'] ?? 12; ?></div>
                            <div class="text-sm text-brand-gray"><?php echo $t['days_complete']; ?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-brand-red">100%</div>
                            <div class="text-sm text-brand-gray"><?php echo $t['first_try_approval']; ?></div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-brand-red">0</div>
                            <div class="text-sm text-brand-gray"><?php echo $t['safety_incidents']; ?></div>
                        </div>
                    </div>
                    
                    <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        <?php echo $t['talk_similar']; ?>
                    </button>
                </div>
                
                <div class="relative">
                    <img src="<?php echo htmlspecialchars($featuredProject[0]['featured_image'] ?? 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop'); ?>" 
                         alt="<?php echo htmlspecialchars($featuredProject[0]['title']); ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-black/50 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-white">
                        <p class="text-sm"><?php echo htmlspecialchars($featuredProject[0]['subtitle'] ?? 'Project installation'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call-to-Action Strip -->
<section id="cta-strip" class="py-20 blueprint-bg text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/90 via-brand-black/80 to-brand-red/20"></div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6">
            <?php echo $t['need_similar']; ?>
        </h2>
        <p class="text-xl mb-12 text-gray-300">
            <?php echo $t['every_project']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center mb-8">
            <button class="bg-brand-red text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" id="request-site-visit-btn">
                <?php echo $t['request_site_visit']; ?>
            </button>
            <button class="border-2 border-white text-white px-12 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="custom-design-btn">
                <?php echo $t['custom_design']; ?>
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