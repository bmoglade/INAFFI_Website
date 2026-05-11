<?php
// ============================================================
// inaffi.com — Site Header
// Matches WT reference: dark surface bg, gold logo left,
// text "Log in" + gold-outline "Join as Creator" right
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

$req          = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$is_dashboard = str_contains($req, '/dashboard');
$is_admin     = str_contains($req, '/admin');
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= site_url('assets/css/styles.css') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= site_url('assets/images/favicon.png') ?>">

    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body>
<?php render_flash(); ?>

<header class="site-header">
    <div class="container-content header-inner">

        <!-- Logo / Brand — gold, display font, left -->
        <a href="<?= site_url() ?>" class="site-header__logo">
            <?= e(strtoupper(SITE_NAME)) ?>
        </a>

        <!-- Nav -->
        <nav class="site-header__nav">
            <?php if ($creator): ?>
                <!-- Logged in -->
                <?php if (!$is_dashboard && !$is_admin): ?>
                    <a href="<?= site_url('dashboard') ?>" class="nav-text-link">
                        ← Dashboard
                    </a>
                <?php endif; ?>
                <a href="<?= site_url('logout') ?>" class="nav-text-link nav-text-link--danger">
                    Log Out
                </a>
            <?php else: ?>
                <!-- Logged out -->
                <a href="<?= site_url('login') ?>" class="nav-text-link">
                    Log in
                </a>
                <a href="<?= site_url('signup') ?>" class="btn btn--gold-outline">
                    Join as Creator
                </a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main>

