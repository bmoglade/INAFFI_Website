<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
$creator = is_logged_in() ? get_current_creator() : null;
$title   = 'Privacy Policy — ' . SITE_NAME;
require_once 'components/header.php';
?>
<div class="legal-page">
    <h1>Privacy Policy</h1>
    <p class="updated">Last updated: May 2026</p>

    <p><?= e(SITE_NAME) ?> ("we", "our", "us") operates <?= e(SITE_URL) ?>. This page informs you of our policies regarding the collection, use, and disclosure of personal information.</p>

    <h2>Information We Collect</h2>
    <p>We collect information you provide when creating an account: your name, email address, username, and social media handles. We also collect usage data such as affiliate link clicks (product clicked, time, browser type, referrer URL).</p>

    <h2>How We Use Your Information</h2>
    <ul>
        <li>To provide and maintain your creator storefront</li>
        <li>To track affiliate link clicks and show you stats</li>
        <li>To communicate with you about your account</li>
        <li>To improve our platform</li>
    </ul>

    <h2>Cookies</h2>
    <p>We use session cookies to keep you logged in as a creator. We do not use tracking cookies on visitor (consumer) pages. If Google Analytics is enabled, it may set its own cookies per Google's privacy policy.</p>

    <h2>Third-Party Services</h2>
    <p>When a visitor clicks a product link, they are redirected to third-party e-commerce platforms (Amazon, Flipkart, Myntra, etc.). We are not responsible for the privacy practices of those platforms.</p>

    <h2>Data Retention</h2>
    <p>Your account data is retained as long as your account is active. Click tracking data may be retained for up to 2 years for analytics purposes. You may request deletion of your account at any time by contacting us.</p>

    <h2>Security</h2>
    <p>We implement reasonable security measures including encrypted passwords (bcrypt), HTTPS, and CSRF protection. No method of transmission over the Internet is 100% secure.</p>

    <h2>Contact</h2>
    <p>If you have questions about this Privacy Policy, please contact us through our website.</p>
</div>
<?php require_once 'components/footer.php'; ?>
