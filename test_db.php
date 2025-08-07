<?php
require_once 'config.php';

// قراءة اللغة من الكوكي أو الافتراضية
$lang = 'ar';
if (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en'])) {
    $lang = $_COOKIE['site_lang'];
}

// دالة مرنة لجلب سكشن مع الترجمة حسب اللغة
function fetchSectionHero($pdo, $langCode = 'ar') {
    $sql = "SELECT st.title, st.subtitle, st.content, st.button_text
        FROM sections s
        JOIN section_translations st ON s.id = st.section_id
        JOIN languages l ON st.language_id = l.id
        JOIN pages p ON s.page_id = p.id
        WHERE s.section_key = 'home-hero' AND l.code = ? AND s.is_active = 1 AND p.slug = 'home' LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$langCode]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$section = fetchSectionHero($pdo, $lang);

?><!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>Test Section Language Switch</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; padding: 40px; }
        .hero { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #0001; padding: 32px; max-width: 600px; margin: 0 auto; text-align: center; }
        .hero h1 { color: #DC2626; font-size: 2.2em; margin-bottom: 0.5em; }
        .hero h2 { color: #333; font-size: 1.3em; margin-bottom: 1em; }
        .hero p { color: #555; font-size: 1.1em; margin-bottom: 2em; }
        .cta-btn { background: #DC2626; color: #fff; border: none; border-radius: 6px; padding: 12px 32px; font-size: 1.1em; cursor: pointer; margin-bottom: 1em; }
        .lang-btn { background: #222; color: #fff; border: none; border-radius: 6px; padding: 8px 18px; font-size: 1em; cursor: pointer; margin-top: 1em; }
    </style>
</head>
<body>
    <div class="hero" id="hero-section">
        <h1 id="hero-title"><?= htmlspecialchars($section['title'] ?? '') ?></h1>
        <h2 id="hero-subtitle"><?= htmlspecialchars($section['subtitle'] ?? '') ?></h2>
        <p id="hero-content"><?= htmlspecialchars($section['content'] ?? '') ?></p>
        <button class="cta-btn" id="hero-cta"><?= htmlspecialchars($section['button_text'] ?? '') ?></button>
        <br>
        <button class="lang-btn" id="lang-toggle">
            <?= $lang === 'ar' ? 'English' : 'العربية' ?>
        </button>
    </div>
    <script src="scripts/lang-toggle.js"></script>
</body>
</html> 