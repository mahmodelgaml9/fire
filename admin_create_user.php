<?php
require_once 'config.php';

// بيانات المستخدم الجديد
$username = 'fireadmin';
$email = 'fireadmin@sphinxfire.com';
$password = 'Fire@2024';
$first_name = 'Fire';
$last_name = 'Admin';
$role_id = 1; // super_admin
$is_active = 1;

// تشفير كلمة المرور
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // تحقق من وجود المستخدم
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // تحديث بيانات المستخدم
        $update = $pdo->prepare("UPDATE admin_users SET email=?, password_hash=?, first_name=?, last_name=?, role_id=?, is_active=?, updated_at=NOW() WHERE id=?");
        $update->execute([$email, $password_hash, $first_name, $last_name, $role_id, $is_active, $user['id']]);
        echo "<h2 style='color:green'>تم تحديث بيانات المستخدم بنجاح!</h2>";
    } else {
        // إضافة مستخدم جديد
        $insert = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, first_name, last_name, role_id, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $insert->execute([$username, $email, $password_hash, $first_name, $last_name, $role_id, $is_active]);
        echo "<h2 style='color:green'>تم إنشاء مستخدم أدمن جديد بنجاح!</h2>";
    }
    echo "<div style='background:#f8fafc;padding:20px;border-radius:8px;max-width:400px;margin:20px auto;font-size:18px;'>";
    echo "<b>بيانات الدخول:</b><br>";
    echo "Username: <span style='color:#1e3a8a;font-weight:bold;'>fireadmin</span><br>";
    echo "Password: <span style='color:#059669;font-weight:bold;'>Fire@2024</span><br>";
    echo "Role: <span style='color:#d97706;'>super_admin</span>";
    echo "</div>";
    echo "<a href='admin/login.php' style='display:block;text-align:center;margin-top:30px;font-size:20px;color:#3b82f6;'>الذهاب لتسجيل الدخول</a>";
} catch (PDOException $e) {
    echo "<h2 style='color:red'>حدث خطأ: ".$e->getMessage()."</h2>";
}
?> 