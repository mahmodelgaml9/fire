<?php
// جلب البيانات الديناميكية للفوتر
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['site_lang']) ? $_COOKIE['site_lang'] : 'en');
$settings = getSiteSettings($lang);
$footerServices = getFooterServices($lang, 5);

// ترجمات الفوتر
$translations = [
    'en' => [
        'services' => 'Services',
        'company' => 'Company',
        'contact_info' => 'Contact Info',
        'home' => 'Home',
        'about_us' => 'About Us',
        'projects' => 'Projects',
        'contact' => 'Contact',
        'scan_catalog' => 'Scan for catalog',
        'all_rights_reserved' => 'All rights reserved.'
    ],
    'ar' => [
        'services' => 'الخدمات',
        'company' => 'الشركة',
        'contact_info' => 'معلومات التواصل',
        'home' => 'الرئيسية',
        'about_us' => 'من نحن',
        'projects' => 'المشاريع',
        'contact' => 'تواصل معنا',
        'scan_catalog' => 'امسح للكتالوج',
        'all_rights_reserved' => 'جميع الحقوق محفوظة.'
    ]
];

$t = $translations[$lang];
?>

<!-- Footer -->
<footer class="bg-brand-black text-white py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 bg-brand-red rounded-lg flex items-center justify-center">
                        <i class="fas fa-fire text-white"></i>
                    </div>
                    <span class="text-lg font-bold"><?php echo htmlspecialchars($settings['site_name'] ?? 'Sphinx Fire'); ?></span>
                </div>
                <p class="text-gray-400 mb-4">
                    <?php echo htmlspecialchars($settings['site_tagline'] ?? 'Your trusted partner for comprehensive fire safety solutions in Egypt.'); ?>
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4"><?php echo $t['services']; ?></h4>
                <ul class="space-y-2">
                    <?php foreach ($footerServices as $service): ?>
                    <li><a href="service.php?slug=<?php echo htmlspecialchars($service['slug']); ?>" class="text-gray-400 hover:text-white"><?php echo htmlspecialchars($service['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4"><?php echo $t['company']; ?></h4>
                <ul class="space-y-2">
                    <li><a href="index.php" class="text-gray-400 hover:text-white"><?php echo $t['home']; ?></a></li>
                    <li><a href="services.php" class="text-gray-400 hover:text-white"><?php echo $t['services']; ?></a></li>
                    <li><a href="about.php" class="text-gray-400 hover:text-white"><?php echo $t['about_us']; ?></a></li>
                    <li><a href="projects.php" class="text-gray-400 hover:text-white"><?php echo $t['projects']; ?></a></li>
                    <li><a href="contact.php" class="text-gray-400 hover:text-white"><?php echo $t['contact']; ?></a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4"><?php echo $t['contact_info']; ?></h4>
                <ul class="space-y-2 text-gray-400">
                    <li><i class="fas fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($settings['office_address'] ?? 'Sadat City, Egypt'); ?></li>
                    <li><i class="fas fa-phone mr-2"></i><?php echo htmlspecialchars($settings['contact_phone'] ?? '+20 123 456 7890'); ?></li>
                    <li><i class="fas fa-envelope mr-2"></i><?php echo htmlspecialchars($settings['contact_email'] ?? 'info@sphinxfire.com'); ?></li>
                </ul>
                <div class="mt-6">
                    <div class="w-20 h-20 bg-gray-800 rounded-lg flex items-center justify-center">
                        <i class="fas fa-qrcode text-white text-2xl"></i>
                    </div>
                    <p class="text-xs text-gray-500 mt-1"><?php echo $t['scan_catalog']; ?></p>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center">
            <p class="text-gray-400">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['site_name'] ?? 'Sphinx Fire'); ?>. <?php echo $t['all_rights_reserved']; ?></p>
        </div>
    </div>
</footer> 