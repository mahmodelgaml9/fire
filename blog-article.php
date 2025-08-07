<?php
require_once 'config.php';
require_once 'includes/functions.php';

// تحديد اللغة المختارة
$lang = isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en']) ? $_COOKIE['site_lang'] : 'en';

// جلب slug المقال من URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : 'civil-defense-inspection-guide';

// جلب بيانات المقال
$post = fetchBlogPostBySlug($slug, $lang);

// إذا لم يتم العثور على المقال، استخدم مقال افتراضي
if (!$post) {
    $post = [
        'id' => 1,
        'title' => 'How to Pass Civil Defense Inspection in Egypt',
        'content' => '<h2>Article Not Found</h2><p>This article is not available in the selected language or does not exist. Please check back later or contact our support team.</p>',
        'excerpt' => 'Comprehensive guide for passing Civil Defense inspection',
        'category_name' => 'Compliance',
        'category_id' => 1,
        'published_at' => '2024-07-07',
        'reading_time' => 12,
        'author' => 'Sphinx Fire Team',
        'featured_image' => 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop'
    ];
}

// جلب مقالات ذات صلة
$relatedPosts = [];
if (isset($post['id']) && isset($post['category_id'])) {
    $relatedPosts = fetchRelatedBlogPosts($post['id'], $post['category_id'], $lang, 3);
}

// ترجمات الصفحة
$translations = [
    'en' => [
        'home' => 'Home',
        'blog' => 'Blog',
        'compliance_guide' => 'COMPLIANCE GUIDE',
        'category' => 'Category',
        'author' => 'Author',
        'reading_time' => 'min read',
        'related_articles' => 'Related Articles',
        'read_more' => 'Read More →',
        'need_help' => 'Need Help Preparing for Your Civil Defense Inspection?',
        'dont_leave_chance' => 'Don\'t leave compliance to chance. Our certified consultants ensure you pass on the first try.',
        'request_consultation' => '🔴 Request Free Consultation',
        'talk_consultant' => '⚪ Talk to a Safety Consultant',
        'free_assessment' => 'Free assessment • Expert guidance • 24-hour response guarantee',
        'want_consultation' => 'Want a Free Pre-Inspection Consultation?',
        'consultation_desc' => 'Our certified consultants can assess your facility and identify potential issues before the official inspection.',
        'book_consultation' => '🔴 Book Free Consultation',
        'download_checklist' => '⚪ Download Pre-Inspection Checklist',
        'critical_statistic' => 'Critical Statistic',
        'what_is_inspection' => 'What is Civil Defense Inspection?',
        'key_areas' => 'Key Areas of Assessment',
        'common_mistakes' => 'Common Mistakes That Lead to Failure',
        'documentation_checklist' => 'Required Documentation Checklist',
        'how_we_help' => 'How Sphinx Fire Helps You Pass Inspection',
        'final_checklist' => 'Final Checklist Before the Inspection Visit',
        'success_record' => 'Our Success Record',
        'conclusion' => 'Conclusion'
    ],
    'ar' => [
        'home' => 'الرئيسية',
        'blog' => 'المدونة',
        'compliance_guide' => 'دليل الامتثال',
        'category' => 'الفئة',
        'author' => 'الكاتب',
        'reading_time' => 'دقيقة قراءة',
        'related_articles' => 'مقالات ذات صلة',
        'read_more' => 'اقرأ المزيد →',
        'need_help' => 'تحتاج مساعدة في الاستعداد لفحص الدفاع المدني؟',
        'dont_leave_chance' => 'لا تترك الامتثال للصدفة. مستشارونا المعتمدون يضمنون نجاحك من المحاولة الأولى.',
        'request_consultation' => '🔴 اطلب استشارة مجانية',
        'talk_consultant' => '⚪ تحدث مع مستشار السلامة',
        'free_assessment' => 'تقييم مجاني • إرشاد خبير • ضمان استجابة 24 ساعة',
        'want_consultation' => 'تريد استشارة مجانية قبل الفحص؟',
        'consultation_desc' => 'يمكن لمستشارينا المعتمدين تقييم منشأتك وتحديد المشاكل المحتملة قبل الفحص الرسمي.',
        'book_consultation' => '🔴 احجز استشارة مجانية',
        'download_checklist' => '⚪ حمل قائمة التحقق قبل الفحص',
        'critical_statistic' => 'إحصائية حرجة',
        'what_is_inspection' => 'ما هو فحص الدفاع المدني؟',
        'key_areas' => 'المناطق الرئيسية للتقييم',
        'common_mistakes' => 'الأخطاء الشائعة التي تؤدي إلى الفشل',
        'documentation_checklist' => 'قائمة الوثائق المطلوبة',
        'how_we_help' => 'كيف يساعدك سفنكس فاير في اجتياز الفحص',
        'final_checklist' => 'القائمة النهائية قبل زيارة الفحص',
        'success_record' => 'سجل نجاحنا',
        'conclusion' => 'الخاتمة'
    ]
];

$t = $translations[$lang];

// تضمين الهيدر والنافبار
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Reading Progress Bar -->
<div class="reading-progress" id="reading-progress"></div>

<!-- Breadcrumb -->
<section class="pt-24 pb-4 bg-gray-50">
    <div class="max-w-4xl mx-auto px-6">
        <div class="breadcrumb">
            <a href="index.php"><?php echo $t['home']; ?></a> / 
            <a href="blog.php"><?php echo $t['blog']; ?></a> / 
            <span><?php echo htmlspecialchars($post['title']); ?></span>
        </div>
    </div>
</section>

<!-- Hero Section -->
<section id="hero" class="hero-bg relative py-20 text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-black/80 via-transparent to-brand-red/20"></div>
    
    <div class="relative z-10 max-w-4xl mx-auto px-6">
        <div class="mb-6 animate-fade-in">
            <div class="inline-block bg-brand-red text-white px-4 py-2 rounded-full text-sm font-semibold mb-4">
                📋 <?php echo $t['compliance_guide']; ?>
            </div>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in">
            <?php echo htmlspecialchars($post['title']); ?>
        </h1>
        
        <div class="flex flex-wrap items-center gap-6 text-gray-300 animate-slide-up">
            <div class="flex items-center space-x-2">
                <i class="fas fa-folder text-brand-red"></i>
                <span><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-calendar text-brand-red"></i>
                <span><?php echo date('F j, Y', strtotime($post['published_at'] ?? '2024-07-07')); ?></span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-user text-brand-red"></i>
                <span><?php echo htmlspecialchars($post['author'] ?? 'Sphinx Fire Team'); ?></span>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-clock text-brand-red"></i>
                <span><?php echo ($post['reading_time'] ?? 12) . ' ' . $t['reading_time']; ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Share Buttons (Desktop) -->
<div class="share-buttons hidden lg:flex">
    <a href="#" class="share-btn bg-blue-600 hover:bg-blue-700" onclick="shareLinkedIn()" title="Share on LinkedIn">
        <i class="fab fa-linkedin-in"></i>
    </a>
    <a href="#" class="share-btn bg-green-500 hover:bg-green-600" onclick="shareWhatsApp()" title="Share on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="#" class="share-btn bg-gray-600 hover:bg-gray-700" onclick="copyLink()" title="Copy Link">
        <i class="fas fa-link"></i>
    </a>
</div>

<!-- Article Content -->
<article class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-6">
        <div class="article-content mx-auto">
            
            <?php if (isset($post['content']) && !empty($post['content'])): ?>
                <?php echo $post['content']; ?>
            <?php else: ?>
                <h2>Article Content Not Available</h2>
                <p>This article content is not available in the selected language or does not exist. Please check back later or contact our support team.</p>
            <?php endif; ?>

            <!-- Mobile Share Buttons -->
            <div class="share-buttons lg:hidden">
                <a href="#" class="share-btn bg-blue-600 hover:bg-blue-700" onclick="shareLinkedIn()" title="Share on LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" class="share-btn bg-green-500 hover:bg-green-600" onclick="shareWhatsApp()" title="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="share-btn bg-gray-600 hover:bg-gray-700" onclick="copyLink()" title="Copy Link">
                    <i class="fas fa-link"></i>
                </a>
            </div>

        </div>
    </div>
</article>

<!-- Related Articles -->
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12"><?php echo $t['related_articles']; ?></h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php if (!empty($relatedPosts)): ?>
                <?php foreach ($relatedPosts as $relatedPost): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                    <img src="<?php echo htmlspecialchars($relatedPost['featured_image'] ?? 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=400&h=200&fit=crop'); ?>" 
                         alt="<?php echo htmlspecialchars($relatedPost['title']); ?>" 
                         class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-brand-red text-sm font-semibold mb-2"><?php echo htmlspecialchars($relatedPost['category_name'] ?? 'General'); ?></div>
                        <h3 class="text-lg font-bold mb-3"><?php echo htmlspecialchars($relatedPost['title']); ?></h3>
                        <p class="text-brand-gray text-sm mb-4">
                            <?php echo htmlspecialchars($relatedPost['excerpt'] ?? ''); ?>
                        </p>
                        <a href="blog-article.php?slug=<?php echo htmlspecialchars($relatedPost['slug']); ?>" class="text-brand-red font-semibold hover:underline">
                            <?php echo $t['read_more']; ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- مقالات افتراضية إذا لم توجد مقالات ذات صلة -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                    <img src="https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=400&h=200&fit=crop" 
                         alt="NFPA Standards Guide" 
                         class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-brand-red text-sm font-semibold mb-2">Fire Systems</div>
                        <h3 class="text-lg font-bold mb-3">NFPA 20: Fire Pump Standards Explained</h3>
                        <p class="text-brand-gray text-sm mb-4">
                            Complete breakdown of NFPA 20 requirements for fire pump installations and testing procedures.
                        </p>
                        <a href="blog-article.php?slug=nfpa20" class="text-brand-red font-semibold hover:underline">
                            <?php echo $t['read_more']; ?>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                    <img src="https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=400&h=200&fit=crop" 
                         alt="Fire Extinguisher Guide" 
                         class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-brand-red text-sm font-semibold mb-2">Extinguishers</div>
                        <h3 class="text-lg font-bold mb-3">Fire Extinguisher Selection Guide</h3>
                        <p class="text-brand-gray text-sm mb-4">
                            How to choose the right extinguisher type for different fire classes and facility requirements.
                        </p>
                        <a href="blog-article.php?slug=extinguishers" class="text-brand-red font-semibold hover:underline">
                            <?php echo $t['read_more']; ?>
                        </a>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300">
                    <img src="https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=400&h=200&fit=crop" 
                         alt="OSHA Requirements" 
                         class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="text-brand-red text-sm font-semibold mb-2">OSHA</div>
                        <h3 class="text-lg font-bold mb-3">OSHA Fire Safety Requirements</h3>
                        <p class="text-brand-gray text-sm mb-4">
                            Essential OSHA regulations for workplace fire safety and emergency action plans.
                        </p>
                        <a href="blog-article.php?slug=osha" class="text-brand-red font-semibold hover:underline">
                            <?php echo $t['read_more']; ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-16 bg-brand-black text-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            <?php echo $t['need_help']; ?>
        </h2>
        <p class="text-xl text-gray-300 mb-8">
            <?php echo $t['dont_leave_chance']; ?>
        </p>
        
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <button class="bg-brand-red text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-red-700 transition-colors cta-pulse" onclick="requestConsultation()">
                <?php echo $t['request_consultation']; ?>
            </button>
            <button class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-xl hover:bg-white hover:text-brand-black transition-colors" onclick="talkToConsultant()">
                <?php echo $t['talk_consultant']; ?>
            </button>
        </div>
        
        <div class="mt-6 text-gray-400 text-sm">
            <?php echo $t['free_assessment']; ?>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
    <img src="" alt="" id="lightbox-img">
    <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300" onclick="closeLightbox()">
        <i class="fas fa-times"></i>
    </button>
</div>

<?php 
include 'includes/footer.php';
include 'includes/whatsapp-float.php';
include 'includes/sticky-cta.php';
include 'includes/scripts.php';
?>
</body>
</html>