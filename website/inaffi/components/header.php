<?php
// ============================================================
// inaffi.com — Site Header (session-aware)
// ============================================================
// Variables expected from the including page:
//   $title    (string) — <title> tag content
//   $creator  (array|null) — current logged-in creator (or null)
//
// Usage: require_once 'components/header.php';
// ============================================================

// Ensure we have what we need
if (!isset($title))   $title   = SITE_NAME;
if (!isset($creator)) $creator = is_logged_in() ? get_current_creator() : null;

// Is the current page the storefront (no dashboard link in header)?
$req          = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$is_homepage  = in_array($req, ['', '/website/inaffi', '/website/inaffi/index.php']);
$is_dashboard = str_contains($req, '/dashboard');
$is_admin     = str_contains($req, '/admin');
$dark_header  = $is_homepage; // dark nav on homepage only
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>

    <!-- CSRF token for AJAX requests -->
    <meta name="csrf-token" content="<?= e(generate_csrf_token()) ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= site_url('assets/css/styles.css') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= site_url('assets/images/favicon.png') ?>">

    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body class="<?= $is_homepage ? 'page-home' : '' ?>">
<?php render_flash(); ?>

<header class="site-header <?= $dark_header ? 'site-header--dark' : '' ?>">
    <div class="container">

        <!-- Left nav -->
        <nav class="site-header__nav-left" aria-label="Left navigation">
            <?php if (!$is_dashboard && !$is_admin): ?>
                <a href="<?= site_url('#about') ?>">About us</a>
                <span class="nav-divider">|</span>
                <a href="<?= site_url('signup') ?>">Creators</a>
            <?php endif; ?>
        </nav>

        <!-- Centre: Logo -->
        <a href="<?= site_url() ?>" class="site-header__logo">
            <?php
            $logo_file = rtrim(dirname(__DIR__), '/') . '/assets/images/logo.png';
            if (file_exists($logo_file)): ?>
                <img src="<?= e(site_url('assets/images/logo.png')) ?>"
                     alt="<?= e(SITE_NAME) ?>"
                     class="site-header__logo-img">
            <?php else: ?>
                <span class="site-header__logo-text"><?= e(strtoupper(SITE_NAME)) ?></span>
            <?php endif; ?>
        </a>

        <!-- Right nav — changes based on login state -->
        <nav class="site-header__nav-right" aria-label="Right navigation">
            <?php if ($creator): ?>
                <!-- Logged in -->
                <?php if (!$is_dashboard && !$is_admin): ?>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn--outline-light btn--sm">Dashboard</a>
                <?php endif; ?>
                <a href="<?= site_url('logout') ?>" class="nav-link-light">Log out</a>
            <?php else: ?>
                <!-- Logged out -->
                <a href="<?= site_url('signup') ?>" class="btn btn--outline-light btn--sm">Join as creator</a>
                <a href="<?= site_url('login') ?>" class="nav-link-light">Log in</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main>

