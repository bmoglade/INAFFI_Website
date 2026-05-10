<?php
// ============================================================
// inaffi.com — Auth Header (minimal — login/signup pages)
// ============================================================
// Used on login.php and signup.php
// Shows only the logo — no nav links
// ============================================================

if (!isset($title)) $title = SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>

    <meta name="csrf-token" content="<?= e(generate_csrf_token()) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= site_url('assets/css/styles.css') ?>">
    <link rel="icon" type="image/png" href="<?= site_url('assets/images/favicon.png') ?>">
</head>
<body>

<?php render_flash(); ?>

<header class="auth-header">
    <a href="<?= site_url() ?>" class="auth-header__logo">
        <?= e(SITE_NAME) ?>
    </a>
</header>

<main>
