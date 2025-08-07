<?php
require_once '../config.php';
require_once 'includes/auth.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة حفظ الإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = '';
    $error = '';
    
    try {
        // إعدادات الموقع العامة
        $site_name = $_POST['site_name'] ?? '';
        $site_description = $_POST['site_description'] ?? '';
        $site_keywords = $_POST['site_keywords'] ?? '';
        $site_email = $_POST['site_email'] ?? '';
        $site_phone = $_POST['site_phone'] ?? '';
        $site_address = $_POST['site_address'] ?? '';
        
        // إعدادات التواصل الاجتماعي
        $facebook_url = $_POST['facebook_url'] ?? '';
        $twitter_url = $_POST['twitter_url'] ?? '';
        $instagram_url = $_POST['instagram_url'] ?? '';
        $linkedin_url = $_POST['linkedin_url'] ?? '';
        $youtube_url = $_POST['youtube_url'] ?? '';
        
        // إعدادات SEO
        $default_meta_title = $_POST['default_meta_title'] ?? '';
        $default_meta_description = $_POST['default_meta_description'] ?? '';
        $google_analytics = $_POST['google_analytics'] ?? '';
        $google_verification = $_POST['google_verification'] ?? '';
        
        // إعدادات النظام
        $items_per_page = (int)($_POST['items_per_page'] ?? 10);
        $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
        $debug_mode = isset($_POST['debug_mode']) ? 1 : 0;
        
        // حفظ الإعدادات في قاعدة البيانات
        $settings = [
            'site_name' => $site_name,
            'site_description' => $site_description,
            'site_keywords' => $site_keywords,
            'site_email' => $site_email,
            'site_phone' => $site_phone,
            'site_address' => $site_address,
            'facebook_url' => $facebook_url,
            'twitter_url' => $twitter_url,
            'instagram_url' => $instagram_url,
            'linkedin_url' => $linkedin_url,
            'youtube_url' => $youtube_url,
            'default_meta_title' => $default_meta_title,
            'default_meta_description' => $default_meta_description,
            'google_analytics' => $google_analytics,
            'google_verification' => $google_verification,
            'items_per_page' => $items_per_page,
            'maintenance_mode' => $maintenance_mode,
            'debug_mode' => $debug_mode
        ];
        
        // حفظ كل إعداد
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
            $stmt->execute([$key, $value, $value]);
        }
        
        $message = 'تم حفظ الإعدادات بنجاح';
        logAdminAction('update_settings', 'settings', 'تم تحديث إعدادات الموقع');
        
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حفظ الإعدادات';
        error_log('Save Settings Error: ' . $e->getMessage());
    }
}

// جلب الإعدادات الحالية
$current_settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    error_log('Fetch Settings Error: ' . $e->getMessage());
}

$page_title = 'إعدادات الموقع';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إعدادات الموقع</h1>
                <p class="text-gray-600">إدارة إعدادات الموقع العامة</p>
            </div>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if (isset($message) && $message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Settings Form -->
    <form method="POST" class="space-y-6">
        <!-- إعدادات الموقع العامة -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">إعدادات الموقع العامة</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم الموقع</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($current_settings['site_name'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="site_email" value="<?php echo htmlspecialchars($current_settings['site_email'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                        <input type="text" name="site_phone" value="<?php echo htmlspecialchars($current_settings['site_phone'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                        <input type="text" name="site_address" value="<?php echo htmlspecialchars($current_settings['site_address'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وصف الموقع</label>
                    <textarea name="site_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"><?php echo htmlspecialchars($current_settings['site_description'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الكلمات المفتاحية</label>
                    <input type="text" name="site_keywords" value="<?php echo htmlspecialchars($current_settings['site_keywords'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="كلمة1, كلمة2, كلمة3">
                </div>
            </div>
        </div>

        <!-- إعدادات التواصل الاجتماعي -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">وسائل التواصل الاجتماعي</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                        <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($current_settings['facebook_url'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Twitter</label>
                        <input type="url" name="twitter_url" value="<?php echo htmlspecialchars($current_settings['twitter_url'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                        <input type="url" name="instagram_url" value="<?php echo htmlspecialchars($current_settings['instagram_url'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label>
                        <input type="url" name="linkedin_url" value="<?php echo htmlspecialchars($current_settings['linkedin_url'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">YouTube</label>
                    <input type="url" name="youtube_url" value="<?php echo htmlspecialchars($current_settings['youtube_url'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
        </div>

        <!-- إعدادات SEO -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">إعدادات SEO</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان الافتراضي</label>
                    <input type="text" name="default_meta_title" value="<?php echo htmlspecialchars($current_settings['default_meta_title'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الوصف الافتراضي</label>
                    <textarea name="default_meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"><?php echo htmlspecialchars($current_settings['default_meta_description'] ?? ''); ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Analytics</label>
                        <input type="text" name="google_analytics" value="<?php echo htmlspecialchars($current_settings['google_analytics'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="G-XXXXXXXXXX">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Google Verification</label>
                        <input type="text" name="google_verification" value="<?php echo htmlspecialchars($current_settings['google_verification'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- إعدادات النظام -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">إعدادات النظام</h3>
            </div>
            <div class="px-6 py-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عدد العناصر في الصفحة</label>
                        <input type="number" name="items_per_page" value="<?php echo htmlspecialchars($current_settings['items_per_page'] ?? 10); ?>" min="5" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo ($current_settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="maintenance_mode" class="mr-2 block text-sm text-gray-900">وضع الصيانة</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="debug_mode" id="debug_mode" <?php echo ($current_settings['debug_mode'] ?? 0) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="debug_mode" class="mr-2 block text-sm text-gray-900">وضع التطوير</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-save ml-2"></i>
                حفظ الإعدادات
            </button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?> 