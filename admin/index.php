<?php
require_once '../config.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
checkAdminLogin();

// إعادة التوجيه إلى لوحة التحكم الرئيسية
header('Location: dashboard.php');
exit;
?> 