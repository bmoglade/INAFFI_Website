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
$is_dashboard = str_starts_with($_SERVER['REQUEST_URI'], '/dashboard');
$is_admin     = str_starts_with($_SERVER['REQUEST_URI'], '/admin');
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= site_url('assets/css/styles.css') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= site_url('assets/images/favicon.png') ?>">

    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body>

<?php render_flash(); ?>

<header class="site-header">
    <div class="container">

        <!-- Logo -->
        <a href="<?= site_url() ?>" class="site-header__logo">
            <?= e(SITE_NAME) ?>
        </a>

        <!-- Navigation — changes based on login state -->
        <nav class="site-header__nav" aria-label="Main navigation">
            <?php if ($creator): ?>
                <!-- Logged in -->
                <?php if (!$is_dashboard && !$is_admin): ?>
                    <a href="<?= site_url('dashboard') ?>">← Dashboard</a>
                <?php endif; ?>
                <a href="<?= site_url('logout') ?>" class="btn btn--outline btn--sm">Log Out</a>
            <?php else: ?>
                <!-- Logged out -->
                <a href="<?= site_url('login') ?>" class="btn btn--outline btn--sm">Log in</a>
                <a href="<?= site_url('signup') ?>" class="btn btn--primary btn--sm">Join as Creator</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main>
