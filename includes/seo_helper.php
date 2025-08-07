<!-- SEO Helper -->
<?php
// متغيرات افتراضية (يمكن تخصيصها لاحقًا)
$page_title = $page_title ?? 'Sphinx Fire - Leading Fire Safety Solutions Provider in Egypt';
$page_description = $page_description ?? "Sphinx Fire is Egypt's premier fire safety company. Expert fire protection systems, consultation, and compliance services for industrial facilities based in Sadat City.";
$page_keywords = $page_keywords ?? "fire safety Egypt, industrial fire protection, Sphinx Fire, fire safety systems, NFPA compliance, fire safety consultation, Sadat City";
$og_title = $og_title ?? $page_title;
$og_description = $og_description ?? $page_description;
$og_image = $og_image ?? 'https://sphinxfire.com/logo.png';
?>
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_description) ?>">
<meta name="keywords" content="<?= htmlspecialchars($page_keywords) ?>">
<meta name="author" content="Sphinx Fire">
<meta property="og:title" content="<?= htmlspecialchars($og_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($og_description) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
<meta name="robots" content="index, follow">
<!-- Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Sphinx Fire",
    "url": "https://sphinxfire.com",
    "logo": "https://sphinxfire.com/logo.png",
    "description": "Leading fire safety solutions provider in Egypt specializing in industrial fire protection systems",
    "address": {
        "@type": "PostalAddress",
        "addressLocality": "Sadat City",
        "addressCountry": "Egypt"
    },
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+20-123-456-7890",
        "contactType": "customer service"
    },
    "sameAs": [
        "https://facebook.com/sphinxfire",
        "https://linkedin.com/company/sphinxfire"
    ]
}
</script> 