<?php
require_once 'config.php';
require_once 'includes/functions.php';
include 'includes/header.php';
include 'includes/navbar.php';

$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';
$page_id = 5; // صفحة المدونة

// جلب البيانات الديناميكية
$hero = fetchSections($page_id, 'hero', $lang, 1);
$featuredPost = fetchFeaturedBlogPost($lang);
$blogPosts = fetchBlogPosts($lang, null, 9); // أول 9 مقالات
$categories = fetchBlogCategories($lang);

// ترجمات المحتوى الستاتيكي
$translations = [
    'en' => [
        'search_placeholder' => 'Search articles, guides, regulations...',
        'all_articles' => 'All Articles',
        'read_more' => 'Read More',
        'min_read' => 'min read',
        'load_more' => 'Load More Articles',
        'newsletter_title' => 'Want to get the latest safety tips and system updates?',
        'newsletter_desc' => 'Join 2,500+ safety professionals who receive our weekly insights on fire protection, compliance updates, and industry best practices.',
        'email_placeholder' => 'Enter your email address',
        'subscribe' => '🔴 Subscribe',
        'newsletter_footer' => 'Free weekly insights • No spam • Unsubscribe anytime',
        'subscribers' => 'Subscribers',
        'weekly_insights' => 'Weekly',
        'expert_insights' => 'Expert Insights',
        'free_content' => 'Free Content'
    ],
    'ar' => [
        'search_placeholder' => 'ابحث في المقالات، الدلائل، اللوائح...',
        'all_articles' => 'جميع المقالات',
        'read_more' => 'اقرأ المزيد',
        'min_read' => 'دقائق قراءة',
        'load_more' => 'تحميل المزيد من المقالات',
        'newsletter_title' => 'تريد الحصول على أحدث نصائح السلامة وتحديثات الأنظمة؟',
        'newsletter_desc' => 'انضم إلى أكثر من 2,500 متخصص سلامة يتلقون رؤانا الأسبوعية حول الحماية من الحريق، تحديثات الامتثال، وأفضل الممارسات في الصناعة.',
        'email_placeholder' => 'أدخل عنوان بريدك الإلكتروني',
        'subscribe' => '🔴 اشترك',
        'newsletter_footer' => 'رؤى أسبوعية مجانية • لا رسائل مزعجة • إلغاء الاشتراك في أي وقت',
        'subscribers' => 'مشتركين',
        'weekly_insights' => 'أسبوعي',
        'expert_insights' => 'رؤى خبيرة',
        'free_content' => 'محتوى مجاني'
    ]
];

$t = $translations[$lang];
?>

<!-- Hero Section -->
<section id="hero" class="hero-bg relative h-screen flex items-center justify-center text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
    
    <div class="relative z-10 text-center max-w-5xl px-6">
        <?php if ($hero && count($hero) > 0): ?>
            <div class="mb-6 animate-fade-in">
                <div class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    <?= htmlspecialchars($hero[0]['title'] ?? '📚 EXPERT KNOWLEDGE BASE') ?>
                </div>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                <?= htmlspecialchars($hero[0]['subtitle'] ?? 'Insights That Keep Your Facility Safe') ?>
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
                <?= htmlspecialchars($hero[0]['content'] ?? 'Expert fire protection guides, updates, and industrial safety knowledge.') ?>
            </h2>
            
            <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
                Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.
            </p>
        <?php else: ?>
            <div class="mb-6 animate-fade-in">
                <div class="inline-block bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    📚 EXPERT KNOWLEDGE BASE
                </div>
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                Insights That Keep Your Facility Safe
            </h1>
            <h2 class="text-3xl md:text-4xl font-semibold mb-6 text-gray-200 animate-slide-up">
                Expert fire protection guides, updates, and industrial safety knowledge.
            </h2>
            
            <p class="text-xl md:text-2xl mb-12 text-gray-300 animate-slide-up">
                Stay ahead of safety regulations with insights curated by certified fire protection engineers and safety consultants.
            </p>
        <?php endif; ?>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center animate-bounce-in">
            <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors" id="subscribe-tips-btn">
                🔴 Subscribe to Safety Tips
            </button>
            <button class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" id="read-latest-btn">
                ⚪ Read Latest Articles
            </button>
        </div>
    </div>
</section>

<!-- Search & Filter Section -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row gap-6 items-center justify-between">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" 
                           placeholder="<?php echo $t['search_placeholder']; ?>" 
                           class="w-full px-4 py-3 pl-12 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-red focus:border-transparent">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button class="bg-brand-red text-white px-6 py-2 rounded-full text-sm font-semibold hover:bg-red-700 transition-colors">
                    <?php echo $t['all_articles']; ?>
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="bg-white text-gray-700 px-6 py-2 rounded-full text-sm font-semibold hover:bg-gray-100 transition-colors border border-gray-200">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Featured Article Section -->
<?php if ($featuredPost): ?>
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <img src="<?php echo htmlspecialchars($featuredPost['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($featuredPost['title']); ?>" 
                     class="w-full h-96 object-cover rounded-xl shadow-lg">
                <div class="absolute top-4 left-4 bg-brand-red text-white px-3 py-1 rounded-full text-sm font-semibold">
                    ⭐ FEATURED GUIDE
                </div>
            </div>
            
            <div>
                <h2 class="text-4xl font-bold mb-4">Most Popular This Month</h2>
                <p class="text-xl text-brand-gray">Essential reading for industrial safety professionals</p>
                
                <div class="flex items-center gap-4 mt-6 mb-4">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                        <?php echo htmlspecialchars($featuredPost['category_slug'] ?? 'Safety'); ?>
                    </span>
                    <span class="text-gray-500">
                        <i class="fas fa-clock mr-1"></i> <?php echo $featuredPost['reading_time'] ?? 5; ?> <?php echo $t['min_read']; ?>
                    </span>
                </div>
                
                <h3 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($featuredPost['title']); ?></h3>
                <p class="text-brand-gray mb-6"><?php echo htmlspecialchars($featuredPost['excerpt']); ?></p>
                
                <div class="flex items-center gap-6 mb-6">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-download text-brand-red"></i>
                        <span class="text-sm text-gray-600">1.2K Downloads</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fas fa-star text-yellow-400"></i>
                        <span class="text-sm text-gray-600">4.8/5</span>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <a href="blog-article.php?slug=<?php echo htmlspecialchars($featuredPost['slug']); ?>" 
                       class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        🔴 Download Free PDF Guide
                    </a>
                    <button class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                        <i class="fas fa-bookmark mr-2"></i> Save
                    </button>
                </div>
                
                <p class="text-sm text-gray-500 mt-4">Updated for 2024 regulations</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Articles Grid -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4">Latest Safety Insights</h2>
            <p class="text-xl text-brand-gray">Expert knowledge to keep your facility compliant and safe</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($blogPosts as $post): ?>
            <article class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="relative h-48">
                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                         class="w-full h-full object-cover">
                    <div class="absolute top-4 left-4 bg-brand-red text-white px-2 py-1 rounded text-xs font-semibold">
                        <?php echo htmlspecialchars($post['category_slug'] ?? 'Safety'); ?>
                    </div>
                    <?php if (strtotime($post['published_at']) > strtotime('-7 days')): ?>
                    <div class="absolute top-4 right-4 bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold">
                        NEW
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i> <?php echo $post['reading_time'] ?? 5; ?> <?php echo $t['min_read']; ?>
                        </span>
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-calendar mr-1"></i> <?php echo date('M j', strtotime($post['published_at'])); ?>
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-3 line-clamp-2"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p class="text-brand-gray mb-4 line-clamp-3"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    
                    <a href="blog-article.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" 
                       class="inline-flex items-center text-brand-red font-semibold hover:underline">
                        <?php echo $t['read_more']; ?>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors" id="load-more-btn">
                <?php echo $t['load_more']; ?>
            </button>
        </div>
    </div>
</section>

<!-- Newsletter Subscription CTA -->
<section class="py-20 bg-brand-black text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold mb-6"><?php echo $t['newsletter_title']; ?></h2>
        <p class="text-xl mb-12 text-gray-300"><?php echo $t['newsletter_desc']; ?></p>
        
        <div class="max-w-md mx-auto">
            <div class="flex gap-4">
                <input type="email" 
                       placeholder="<?php echo $t['email_placeholder']; ?>" 
                       class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:ring-2 focus:ring-brand-red focus:border-transparent">
                <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    <?php echo $t['subscribe']; ?>
                </button>
            </div>
            <p class="text-sm text-gray-400 mt-4"><?php echo $t['newsletter_footer']; ?></p>
        </div>
        
        <div class="grid grid-cols-3 gap-8 mt-16 max-w-2xl mx-auto">
            <div class="text-center">
                <div class="text-sm text-gray-300"><?php echo $t['subscribers']; ?></div>
                <div class="text-2xl font-bold text-brand-red">2,500+</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand-red"><?php echo $t['weekly_insights']; ?></div>
                <div class="text-sm text-gray-300"><?php echo $t['expert_insights']; ?></div>
            </div>
            <div class="text-center">
                <div class="text-sm text-gray-300"><?php echo $t['free_content']; ?></div>
                <div class="text-2xl font-bold text-brand-red">100%</div>
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
</html>