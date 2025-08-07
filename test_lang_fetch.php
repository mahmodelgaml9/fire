<?php
require_once 'includes/functions.php';

// قراءة اللغة من الكوكي أو الافتراضية
$lang = 'ar';
if (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], ['ar','en'])) {
    $lang = $_COOKIE['site_lang'];
}

// جلب بيانات عشوائية من عدة دوال
$hero = fetchSections(1, 'hero', $lang, 1); // الصفحة الرئيسية id=1
$services = fetchServices($lang);
$projects = fetchProjects($lang, null, null, 3);
$blogPosts = function_exists('fetchBlogPosts') ? fetchBlogPosts($lang, null) : [];
$team = function_exists('fetchTeamMembers') ? fetchTeamMembers($lang, 3) : [];

?><!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>اختبار جلب البيانات بلغتين</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; padding: 40px; }
        .block { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #0001; padding: 24px; margin-bottom: 32px; }
        h2 { color: #DC2626; margin-bottom: 12px; }
        .lang-btn { background: #222; color: #fff; border: none; border-radius: 6px; padding: 8px 18px; font-size: 1em; cursor: pointer; margin-bottom: 24px; }
        ul { padding-left: 20px; }
        li { margin-bottom: 8px; }
        .key { color: #888; font-size: 0.95em; }
    </style>
</head>
<body>
    <button class="lang-btn" id="lang-toggle">
        <?= $lang === 'ar' ? 'English' : 'العربية' ?>
    </button>

    <div class="block">
        <h2>سكشن الهيرو (home-hero)</h2>
        <?php if ($hero && count($hero)) : $h = $hero[0]; ?>
            <div><span class="key">العنوان:</span> <?= htmlspecialchars($h['title'] ?? '') ?></div>
            <div><span class="key">العنوان الفرعي:</span> <?= htmlspecialchars($h['subtitle'] ?? '') ?></div>
            <div><span class="key">المحتوى:</span> <?= htmlspecialchars($h['content'] ?? '') ?></div>
            <div><span class="key">زر CTA:</span> <?= htmlspecialchars($h['button_text'] ?? '') ?></div>
        <?php else: ?>
            <div>لا توجد بيانات هيرو.</div>
        <?php endif; ?>
    </div>

    <div class="block">
        <h2>الخدمات (Services)</h2>
        <ul>
        <?php foreach ($services as $srv): ?>
            <li><b><?= htmlspecialchars($srv['name'] ?? $srv['slug']) ?></b> — <?= htmlspecialchars($srv['short_description'] ?? '') ?></li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="block">
        <h2>المشاريع (Projects)</h2>
        <ul>
        <?php foreach ($projects as $prj): ?>
            <li><b><?= htmlspecialchars($prj['title'] ?? $prj['slug']) ?></b> — <?= htmlspecialchars($prj['description'] ?? '') ?></li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="block">
        <h2>مقالات المدونة (Blog Posts)</h2>
        <ul>
        <?php foreach ($blogPosts as $post): ?>
            <li><b><?= htmlspecialchars($post['title'] ?? $post['slug']) ?></b> — <?= htmlspecialchars($post['excerpt'] ?? '') ?></li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="block">
        <h2>أعضاء الفريق (Team Members)</h2>
        <ul>
        <?php foreach ($team as $member): ?>
            <li><b><?= htmlspecialchars($member['name'] ?? '') ?></b> — <?= htmlspecialchars($member['position'] ?? '') ?></li>
        <?php endforeach; ?>
        </ul>
    </div>

    <script src="scripts/lang-toggle.js"></script>
    <script src="scripts/rtl-ltr-manager.js"></script>
</body>
</html> 