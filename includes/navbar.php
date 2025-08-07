<?php
// تحديد اللغة إذا لم تكن محددة
if (!isset($lang)) {
    $lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en');
}
?>
<!-- Sticky Header/Navbar -->
<header class="sticky-header fixed top-0 w-full z-50 py-4 px-6">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <a href="index.php">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-brand-red rounded-lg flex items-center justify-center">
                    <i class="fas fa-fire text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-white">Sphinx Fire</span>
            </div>
        </a>
        <nav class="hidden md:flex items-center space-x-8">
            <a href="services.php<?php echo ($lang == 'ar') ? '?lang=ar' : ''; ?>" class="text-white hover:text-brand-red transition-colors">
                <?php echo ($lang == 'ar') ? 'الخدمات' : 'Services'; ?>
            </a>
            <a href="about.php<?php echo ($lang == 'ar') ? '?lang=ar' : ''; ?>" class="text-white hover:text-brand-red transition-colors">
                <?php echo ($lang == 'ar') ? 'من نحن' : 'About'; ?>
            </a>
            <a href="projects.php<?php echo ($lang == 'ar') ? '?lang=ar' : ''; ?>" class="text-white hover:text-brand-red transition-colors">
                <?php echo ($lang == 'ar') ? 'المشاريع' : 'Projects'; ?>
            </a>
            <a href="blog.php<?php echo ($lang == 'ar') ? '?lang=ar' : ''; ?>" class="text-white hover:text-brand-red transition-colors">
                <?php echo ($lang == 'ar') ? 'المدونة' : 'Blog'; ?>
            </a>
            <a href="contact.php<?php echo ($lang == 'ar') ? '?lang=ar' : ''; ?>" class="text-white hover:text-brand-red transition-colors">
                <?php echo ($lang == 'ar') ? 'اتصل بنا' : 'Contact'; ?>
            </a>
        </nav>
        <div class="flex items-center space-x-4">
            <button class="bg-brand-red text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors" id="header-cta">
                <?php echo ($lang == 'ar') ? 'طلب زيارة مجانية' : 'Request Free Visit'; ?>
            </button>
            <a href="?lang=<?php echo ($lang == 'ar') ? 'en' : 'ar'; ?>" class="text-white hover:text-brand-red transition-colors font-semibold">
                <?php echo ($lang == 'ar') ? 'English' : 'العربية'; ?>
            </a>
        </div>
        <button class="md:hidden text-white" id="mobile-menu-btn">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</header>
