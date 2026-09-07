<?php
/**
 * ============================================================
 *  RavenWarp :: includes/header.php
 * ------------------------------------------------------------
 *  The dynamic top-level shell for every page on the site.
 *
 *  USAGE (at the top of any page, e.g. index.php):
 *
 *      <?php
 *      define('RAVENWARP_APP', true);
 *      $pageTitle       = 'RavenWarp — Fly the Realms';
 *      $pageDescription = 'Connect, post, and build reputation on RavenWarp.';
 *      $pageBodyClass   = 'page-home';   // optional, for page-specific CSS hooks
 *      require_once __DIR__ . '/includes/header.php';
 *      ?>
 *
 *      ... your page content goes here ...
 *
 *      <?php require_once __DIR__ . '/includes/footer.php'; ?>
 * ============================================================
 */

// Guard flag so cookies.php (and future includes) know they were
// pulled in through the proper front door, not accessed directly.
if (!defined('RAVENWARP_APP')) {
    define('RAVENWARP_APP', true);
}

require_once __DIR__ . '/cookies.php';

// ------------------------------------------------------------
// Dynamic page metadata — every page can override these before
// including this file. Sensible defaults live here as a fallback.
// ------------------------------------------------------------
$pageTitle       = $pageTitle       ?? 'RavenWarp — Fly the Realms. Share the Signal.';
$pageDescription = $pageDescription ?? 'RavenWarp is a secure, next-gen social network fusing Norse mythology with sci-fi futurism.';
$pageBodyClass   = $pageBodyClass   ?? '';

// Never trust raw output into HTML — always escape.
$safeTitle       = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');
$safeDescription = htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8');
$safeBodyClass   = htmlspecialchars($pageBodyClass, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $safeDescription ?>">
    <meta name="theme-color" content="#0b0e1a">

    <!-- Security-related meta / headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta name="referrer" content="strict-origin-when-cross-origin">

    <title><?= $safeTitle ?></title>

    <!-- Favicon (swap for your real asset once branding is locked) -->
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">

    <!-- ================= Google Fonts ================= -->
    <!-- Orbitron: sci-fi display headers | Rajdhani: techy sub-headers | Exo 2: clean body copy -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Rajdhani:wght@500;600;700&family=Exo+2:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- ================= Bootstrap 5 ================= -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- ================= Font Awesome ================= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- ================= RavenWarp Custom Theme ================= -->
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="<?= $safeBodyClass ?>">

<!-- Skip-to-content link for keyboard/screen-reader users -->
<a href="#main-content" class="visually-hidden-focusable">Skip to main content</a>

<?php require_once __DIR__ . '/nav.php'; ?>

<main id="main-content">