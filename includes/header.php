<?php
/**
 * ============================================================
 *  RavenWarp :: includes/header.php  (Command Deck rebuild)
 * ------------------------------------------------------------
 *  100% self-hosted assets now — no CDN requests, no third-party
 *  DNS lookups, nothing about a visitor leaves this server before
 *  they've even logged in.
 *
 *  USAGE — unchanged from before:
 *      define('RAVENWARP_APP', true);
 *      $pageTitle       = '...';
 *      $pageDescription = '...';
 *      $pageBodyClass   = '...';
 *      require_once __DIR__ . '/includes/header.php';
 *
 *  FONT FILES: this assumes Google's standard static-weight
 *  naming (e.g. Orbitron-Regular.ttf, Orbitron-Bold.ttf) inside
 *  assets/vendors/GoogleFonts/<Family>/. If your actual filenames
 *  differ, adjust main.css's @font-face block to match — see the
 *  note left there.
 * ============================================================
 */

if (!defined('RAVENWARP_APP')) {
    define('RAVENWARP_APP', true);
}

require_once __DIR__ . '/cookies.php';
require_once __DIR__ . '/remember-me.php';
require_once __DIR__ . '/icons.php';

// ------------------------------------------------------------
// Dynamic page metadata (same contract as before).
// ------------------------------------------------------------
$pageTitle       = $pageTitle       ?? 'RavenWarp — Fly the Realms. Share the Signal.';
$pageDescription = $pageDescription ?? 'RavenWarp is a secure, next-gen social network fusing Norse mythology with sci-fi futurism.';
$pageBodyClass   = $pageBodyClass   ?? '';
$pageOgImage     = $pageOgImage     ?? '/assets/img/media/og-default.png';

$safeTitle       = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');
$safeDescription = htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8');
$safeBodyClass   = htmlspecialchars($pageBodyClass, ENT_QUOTES, 'UTF-8');

$rw_canonical_url = 'https://ravenwarp.beardedviking.org' . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

// Sitewide Organization JSON-LD — every page gets this baseline;
// contact.php layers on richer ContactPoint/location data separately.
$rw_org_ld = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => 'RavenWarp',
    'url'      => 'https://ravenwarp.beardedviking.org',
    'sameAs'   => [
        'https://beardedviking.medium.com/',
        'https://www.linkedin.com/in/bearded-viking-3112a8431/',
        'https://www.facebook.com/BeardedVikingTX',
        'https://x.com/TXBeardedViking',
        'https://www.tiktok.com/@beardedvikingtx',
        'https://github.com/BeardedVikingTX',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $safeDescription ?>">
    <meta name="theme-color" content="#05070d">
    <link rel="canonical" href="<?= htmlspecialchars($rw_canonical_url, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Security-related meta -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <!-- Open Graph / social preview -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="RavenWarp">
    <meta property="og:title" content="<?= $safeTitle ?>">
    <meta property="og:description" content="<?= $safeDescription ?>">
    <meta property="og:url" content="<?= htmlspecialchars($rw_canonical_url, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($pageOgImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $safeTitle ?>">
    <meta name="twitter:description" content="<?= $safeDescription ?>">

    <title><?= $safeTitle ?></title>

    <link rel="icon" type="image/png" href="/assets/img/favicon.png">

    <!-- ================= Self-hosted CSS (no CDN) ================= -->
    <link href="/assets/vendors/Bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">

    <script type="application/ld+json"><?= json_encode($rw_org_ld, JSON_UNESCAPED_SLASHES) ?></script>
</head>
<body class="<?= $safeBodyClass ?>" x-data="{ mobileNavOpen: false }">

<a href="#main-content" class="visually-hidden-focusable">Skip to main content</a>

<?php require_once __DIR__ . '/nav.php'; ?>

<main id="main-content">