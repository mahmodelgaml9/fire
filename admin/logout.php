<?php
session_start();
require_once '../config.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $user_id = $_SESSION['admin_user_id'] ?? null;
    
    // تسجيل عملية تسجيل الخروج
    if ($user_id) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO admin_logs (user_id, action, module, description, ip_address, user_agent) 
                VALUES (?, 'logout', 'auth', 'تسجيل خروج ناجح', ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (PDOException $e) {
            error_log('Logout Log Error: ' . $e->getMessage());
        }
    }
}

// تنظيف الجلسة
$_SESSION = array();

// حذف كوكيز الجلسة
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول
header('Location: login.php?message=logged_out');
exit;
?> 