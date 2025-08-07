<?php
// إعدادات الاتصال بقاعدة البيانات
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'sphinx_fire_cms';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

try {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

// إعدادات عامة للبيئة
$APP_ENV = getenv('APP_ENV') ?: 'development';
$APP_DEBUG = getenv('APP_DEBUG') ?: true;
$APP_URL = getenv('APP_URL') ?: 'http://localhost'; 