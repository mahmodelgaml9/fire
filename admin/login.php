<?php
session_start();
require_once '../config.php';

// التحقق من تسجيل الدخول المسبق
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        try {
            // البحث عن المستخدم
            $stmt = $pdo->prepare("
                SELECT au.*, ar.name as role_name, ar.permissions 
                FROM admin_users au 
                LEFT JOIN admin_roles ar ON au.role_id = ar.id 
                WHERE au.username = ? AND au.is_active = 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // تسجيل الدخول ناجح
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_first_name'] = $user['first_name'];
                $_SESSION['admin_last_name'] = $user['last_name'];
                $_SESSION['admin_role_id'] = $user['role_id'];
                $_SESSION['admin_role_name'] = $user['role_name'];
                $_SESSION['admin_permissions'] = json_decode($user['permissions'], true);
                
                // تحديث آخر تسجيل دخول
                $update_stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$user['id']]);
                
                // تسجيل العملية
                $log_stmt = $pdo->prepare("
                    INSERT INTO admin_logs (user_id, action, module, description, ip_address, user_agent) 
                    VALUES (?, 'login', 'auth', 'تسجيل دخول ناجح', ?, ?)
                ");
                $log_stmt->execute([
                    $user['id'],
                    $_SERVER['REMOTE_ADDR'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
                
                // تسجيل محاولة فاشلة
                if ($user) {
                    $log_stmt = $pdo->prepare("
                        INSERT INTO admin_logs (user_id, action, module, description, ip_address, user_agent) 
                        VALUES (?, 'login_failed', 'auth', 'محاولة تسجيل دخول فاشلة', ?, ?)
                    ");
                    $log_stmt->execute([
                        $user['id'],
                        $_SERVER['REMOTE_ADDR'] ?? '',
                        $_SERVER['HTTP_USER_AGENT'] ?? ''
                    ]);
                }
            }
        } catch (PDOException $e) {
            $error_message = 'حدث خطأ في النظام. يرجى المحاولة مرة أخرى.';
            error_log('Admin Login Error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - لوحة التحكم | Sphinx Fire</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .login-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- شعار الشركة -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-lg mb-4">
                <i class="fas fa-fire text-3xl text-red-500"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Sphinx Fire</h1>
            <p class="text-white/80">لوحة التحكم</p>
        </div>
        
        <!-- نموذج تسجيل الدخول -->
        <div class="glass-effect rounded-2xl p-8 shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">تسجيل الدخول</h2>
                <p class="text-white/70">أدخل بياناتك للوصول إلى لوحة التحكم</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle ml-2"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <!-- اسم المستخدم -->
                <div>
                    <label for="username" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-user ml-2"></i>
                        اسم المستخدم
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 input-focus transition-all duration-200"
                        placeholder="أدخل اسم المستخدم"
                        required
                        autocomplete="username"
                    >
                </div>
                
                <!-- كلمة المرور -->
                <div>
                    <label for="password" class="block text-sm font-medium text-white mb-2">
                        <i class="fas fa-lock ml-2"></i>
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 input-focus transition-all duration-200 pr-12"
                            placeholder="أدخل كلمة المرور"
                            required
                            autocomplete="current-password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute left-3 top-1/2 transform -translate-y-1/2 text-white/50 hover:text-white transition-colors"
                        >
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- تذكرني -->
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="w-4 h-4 text-primary-600 bg-white/10 border-white/20 rounded focus:ring-primary-500"
                    >
                    <label for="remember" class="mr-2 text-sm text-white/80">
                        تذكرني
                    </label>
                </div>
                
                <!-- زر تسجيل الدخول -->
                <button 
                    type="submit" 
                    class="w-full bg-white text-primary-600 py-3 px-6 rounded-lg font-semibold hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 shadow-lg"
                >
                    <i class="fas fa-sign-in-alt ml-2"></i>
                    تسجيل الدخول
                </button>
            </form>
            
            <!-- روابط إضافية -->
            <div class="text-center mt-6">
                <a href="#" class="text-white/70 hover:text-white text-sm transition-colors">
                    <i class="fas fa-question-circle ml-1"></i>
                    نسيت كلمة المرور؟
                </a>
            </div>
        </div>
        
        <!-- معلومات إضافية -->
        <div class="text-center mt-8">
            <p class="text-white/60 text-sm">
                &copy; 2024 Sphinx Fire. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
        
        // إضافة تأثيرات بصرية
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-white/20');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-white/20');
                });
            });
        });
    </script>
</body>
</html> 