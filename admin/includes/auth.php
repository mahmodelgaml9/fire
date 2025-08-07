<?php
session_start();

// التحقق من تسجيل الدخول
function checkAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

// التحقق من الصلاحيات
function checkPermission($module, $action = 'read') {
    if (!isset($_SESSION['admin_permissions'])) {
        return false;
    }
    
    $permissions = $_SESSION['admin_permissions'];
    
    // مدير النظام الكامل له جميع الصلاحيات
    if (isset($permissions['all']) && $permissions['all'] === true) {
        return true;
    }
    
    // التحقق من صلاحيات الوحدة
    if (isset($permissions[$module])) {
        if (is_bool($permissions[$module])) {
            return $permissions[$module];
        }
        
        if (is_array($permissions[$module])) {
            return isset($permissions[$module][$action]) ? $permissions[$module][$action] : false;
        }
    }
    
    return false;
}

// الحصول على معلومات المستخدم الحالي
function getCurrentAdminUser() {
    if (!isset($_SESSION['admin_user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_user_id'],
        'username' => $_SESSION['admin_username'],
        'email' => $_SESSION['admin_email'],
        'first_name' => $_SESSION['admin_first_name'],
        'last_name' => $_SESSION['admin_last_name'],
        'role_id' => $_SESSION['admin_role_id'],
        'role_name' => $_SESSION['admin_role_name'],
        'permissions' => $_SESSION['admin_permissions']
    ];
}

// تسجيل العملية
function logAdminAction($action, $module, $description = '', $data = null) {
    global $pdo;
    
    if (!isset($_SESSION['admin_user_id'])) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (user_id, action, module, description, data, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $_SESSION['admin_user_id'],
            $action,
            $module,
            $description,
            $data ? json_encode($data) : null,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    } catch (PDOException $e) {
        error_log('Admin Log Error: ' . $e->getMessage());
        return false;
    }
}

// تسجيل النشاط
function logAdminActivity($activity_type, $activity_data = null) {
    global $pdo;
    
    if (!isset($_SESSION['admin_user_id'])) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_activity_logs (user_id, activity_type, activity_data, ip_address) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $_SESSION['admin_user_id'],
            $activity_type,
            $activity_data ? json_encode($activity_data) : null,
            $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
    } catch (PDOException $e) {
        error_log('Admin Activity Log Error: ' . $e->getMessage());
        return false;
    }
}

// إنشاء إشعار
function createNotification($user_id, $title, $message, $type = 'info') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_notifications (user_id, title, message, type) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([$user_id, $title, $message, $type]);
    } catch (PDOException $e) {
        error_log('Create Notification Error: ' . $e->getMessage());
        return false;
    }
}

// الحصول على إشعارات المستخدم
function getUserNotifications($user_id, $limit = 10) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM admin_notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Get Notifications Error: ' . $e->getMessage());
        return [];
    }
}

// تحديث حالة الإشعار
function markNotificationAsRead($notification_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE admin_notifications 
            SET is_read = 1 
            WHERE id = ?
        ");
        
        return $stmt->execute([$notification_id]);
    } catch (PDOException $e) {
        error_log('Mark Notification Read Error: ' . $e->getMessage());
        return false;
    }
}

// الحصول على إحصائيات سريعة
function getQuickStats() {
    global $pdo;
    
    $stats = [];
    
    try {
        // عدد الصفحات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM pages WHERE is_active = 1");
        $stats['pages'] = $stmt->fetch()['count'];
        
        // عدد الخدمات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM services WHERE is_active = 1");
        $stats['services'] = $stmt->fetch()['count'];
        
        // عدد المشاريع
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects WHERE is_active = 1");
        $stats['projects'] = $stmt->fetch()['count'];
        
        // عدد المقالات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_posts WHERE is_active = 1");
        $stats['blog_posts'] = $stmt->fetch()['count'];
        
        // عدد الشهادات
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM testimonials WHERE is_active = 1");
        $stats['testimonials'] = $stmt->fetch()['count'];
        
        // عدد المناطق
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM locations WHERE is_active = 1");
        $stats['locations'] = $stmt->fetch()['count'];
        
    } catch (PDOException $e) {
        error_log('Get Quick Stats Error: ' . $e->getMessage());
    }
    
    return $stats;
}

// الحصول على آخر النشاطات
function getRecentActivities($limit = 10) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT al.*, au.first_name, au.last_name, au.username
            FROM admin_logs al
            LEFT JOIN admin_users au ON al.user_id = au.id
            ORDER BY al.created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Get Recent Activities Error: ' . $e->getMessage());
        return [];
    }
}

// التحقق من CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// تنظيف المدخلات
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// التحقق من صحة الملف المرفوع
function validateUploadedFile($file, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // التحقق من الحجم
    if ($file['size'] > $max_size) {
        return false;
    }
    
    // التحقق من النوع
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    // التحقق من نوع MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowed_mimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif'
    ];
    
    if (!isset($allowed_mimes[$file_extension]) || $mime_type !== $allowed_mimes[$file_extension]) {
        return false;
    }
    
    return true;
}

// رفع الملف وتحويله لـ WebP
function uploadFile($file, $destination_dir, $filename = null, $convert_to_webp = true) {
    if (!validateUploadedFile($file)) {
        return false;
    }
    
    if (!is_dir($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    if (!$filename) {
        $filename = uniqid() . '_' . time();
    }
    
    // تحويل لـ WebP إذا كان مطلوب
    if ($convert_to_webp && in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
        $webp_filename = $filename . '.webp';
        $webp_destination = $destination_dir . '/' . $webp_filename;
        
        if (convertToWebP($file['tmp_name'], $webp_destination)) {
            return $webp_filename;
        }
    }
    
    // إذا فشل التحويل أو لم يكن مطلوب، رفع الملف الأصلي
    $original_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $original_filename = $filename . '.' . $original_extension;
    $original_destination = $destination_dir . '/' . $original_filename;
    
    if (move_uploaded_file($file['tmp_name'], $original_destination)) {
        return $original_filename;
    }
    
    return false;
}

// تحويل الصورة لـ WebP
function convertToWebP($source_path, $destination_path, $quality = 85) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $width = $image_info[0];
    $height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // إنشاء الصورة حسب النوع
    switch ($mime_type) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    if (!$image) {
        return false;
    }
    
    // حفظ كـ WebP
    $result = imagewebp($image, $destination_path, $quality);
    imagedestroy($image);
    
    return $result;
}

// إنشاء نسخ مختلفة من الصورة
function createImageVariants($source_path, $destination_dir, $filename, $variants = []) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $results = [];
    
    foreach ($variants as $variant_name => $dimensions) {
        $width = $dimensions['width'] ?? 800;
        $height = $dimensions['height'] ?? 600;
        $quality = $dimensions['quality'] ?? 85;
        
        $variant_filename = $filename . '_' . $variant_name . '.webp';
        $variant_path = $destination_dir . '/' . $variant_filename;
        
        if (resizeAndConvertImage($source_path, $variant_path, $width, $height, $quality)) {
            $results[$variant_name] = $variant_filename;
        }
    }
    
    return $results;
}

// تغيير حجم الصورة وتحويلها
function resizeAndConvertImage($source_path, $destination_path, $target_width, $target_height, $quality = 85) {
    if (!extension_loaded('gd')) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $original_width = $image_info[0];
    $original_height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // حساب النسب الجديدة
    $ratio = min($target_width / $original_width, $target_height / $original_height);
    $new_width = round($original_width * $ratio);
    $new_height = round($original_height * $ratio);
    
    // إنشاء الصورة حسب النوع
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    if (!$source_image) {
        return false;
    }
    
    // إنشاء الصورة الجديدة
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // الحفاظ على الشفافية للـ PNG
    if ($mime_type === 'image/png') {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
        imagefill($new_image, 0, 0, $transparent);
    }
    
    // تغيير الحجم
    imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    
    // حفظ كـ WebP
    $result = imagewebp($new_image, $destination_path, $quality);
    
    imagedestroy($source_image);
    imagedestroy($new_image);
    
    return $result;
}

// حذف الملف
function deleteFile($file_path) {
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    return false;
}

// تنسيق التاريخ
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

// تنسيق الحجم
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// إنشاء رابط آمن
function createSecureLink($url, $params = []) {
    $params['csrf_token'] = generateCSRFToken();
    return $url . '?' . http_build_query($params);
}

// التحقق من صحة الرابط
function validateSecureLink($params) {
    return isset($params['csrf_token']) && verifyCSRFToken($params['csrf_token']);
}

// إعادة توجيه مع رسالة
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['admin_message'] = $message;
    $_SESSION['admin_message_type'] = $type;
    header('Location: ' . $url);
    exit;
}

// عرض الرسائل
function displayMessages() {
    if (isset($_SESSION['admin_message'])) {
        $message = $_SESSION['admin_message'];
        $type = $_SESSION['admin_message_type'] ?? 'info';
        
        unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
        
        $alert_class = [
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700'
        ];
        
        $icon_class = [
            'success' => 'fas fa-check-circle',
            'error' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle'
        ];
        
        echo '<div class="' . $alert_class[$type] . ' border px-4 py-3 rounded mb-4">';
        echo '<i class="' . $icon_class[$type] . ' ml-2"></i>';
        echo htmlspecialchars($message);
        echo '</div>';
    }
}

// =====================================================
// دوال إدارة الوسائط والملفات
// =====================================================

/**
 * حذف الصورة وجميع نسخها
 */
function deleteImageWithVariants($image_path, $variants = []) {
    $deleted = [];
    
    // حذف الصورة الأصلية
    if (deleteFile($image_path)) {
        $deleted[] = basename($image_path);
    }
    
    // حذف النسخ المختلفة
    $directory = dirname($image_path);
    $filename = pathinfo($image_path, PATHINFO_FILENAME);
    
    foreach ($variants as $variant_name) {
        $variant_path = $directory . '/' . $filename . '_' . $variant_name . '.webp';
        if (deleteFile($variant_path)) {
            $deleted[] = basename($variant_path);
        }
    }
    
    return $deleted;
}

/**
 * الحصول على حجم الملف
 */
function getFileSize($file_path) {
    if (file_exists($file_path)) {
        return filesize($file_path);
    }
    return 0;
}

/**
 * الحصول على معلومات الصورة
 */
function getImageInfo($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    $image_info = getimagesize($file_path);
    if (!$image_info) {
        return false;
    }
    
    return [
        'width' => $image_info[0],
        'height' => $image_info[1],
        'mime_type' => $image_info['mime'],
        'size' => filesize($file_path),
        'extension' => strtolower(pathinfo($file_path, PATHINFO_EXTENSION))
    ];
}

/**
 * إنشاء رابط آمن للملف
 */
function createSecureFileUrl($file_path, $expires_in = 3600) {
    $timestamp = time() + $expires_in;
    $token = hash_hmac('sha256', $file_path . $timestamp, $_ENV['APP_KEY'] ?? 'default_key');
    
    return 'download.php?file=' . urlencode($file_path) . '&token=' . $token . '&expires=' . $timestamp;
}

/**
 * التحقق من صحة رابط الملف
 */
function validateSecureFileUrl($file_path, $token, $expires) {
    if (time() > $expires) {
        return false;
    }
    
    $expected_token = hash_hmac('sha256', $file_path . $expires, $_ENV['APP_KEY'] ?? 'default_key');
    return hash_equals($expected_token, $token);
}

/**
 * تنظيف الملفات المؤقتة
 */
function cleanupTempFiles($directory, $max_age = 3600) {
    if (!is_dir($directory)) {
        return false;
    }
    
    $files = glob($directory . '/temp_*');
    $deleted = 0;
    
    foreach ($files as $file) {
        if (filemtime($file) < (time() - $max_age)) {
            if (unlink($file)) {
                $deleted++;
            }
        }
    }
    
    return $deleted;
}

/**
 * إنشاء مجلد للوسائط
 */
function createMediaDirectory($type = 'images') {
    $base_dir = '../uploads/' . $type;
    $year = date('Y');
    $month = date('m');
    
    $directory = $base_dir . '/' . $year . '/' . $month;
    
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    return $directory;
}

/**
 * الحصول على مسار الوسائط
 */
function getMediaPath($filename, $type = 'images') {
    $base_dir = '../uploads/' . $type;
    $year = date('Y');
    $month = date('m');
    
    return $base_dir . '/' . $year . '/' . $month . '/' . $filename;
}

/**
 * إنشاء رابط الوسائط
 */
function getMediaUrl($filename, $type = 'images') {
    $base_url = '/uploads/' . $type;
    $year = date('Y');
    $month = date('m');
    
    return $base_url . '/' . $year . '/' . $month . '/' . $filename;
}
?> 