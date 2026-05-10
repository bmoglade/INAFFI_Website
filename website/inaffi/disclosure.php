<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
$creator = is_logged_in() ? get_current_creator() : null;
$title   = 'Affiliate Disclosure — ' . SITE_NAME;
require_once 'components/header.php';
?>
<div class="legal-page">
    <h1>Affiliate Disclosure</h1>
    <p class="updated">Last updated: May 2026</p>

    <p><?= e(SITE_NAME) ?> is a creator platform where fashion and lifestyle influencers share outfit collections with shoppable affiliate links.</p>

    <h2>What This Means</h2>
    <p>Product links on creator storefronts on <?= e(SITE_NAME) ?> may be affiliate links. This means that when you click a link and make a purchase, the creator whose storefront you are visiting may earn a commission from the retailer (Amazon, Flipkart, Myntra, etc.).</p>

    <h2>At No Extra Cost to You</h2>
    <p>Using affiliate links does not change the price you pay. You pay the same price you would pay by visiting the store directly. The commission comes from the retailer, not from you.</p>

    <h2>Who Earns the Commission</h2>
    <p>Commissions are earned by the individual creator whose storefront you are visiting. <?= e(SITE_NAME) ?> (the platform) does not earn commissions on purchases — we simply provide the technology to display and track the links.</p>

    <h2>Affiliate Programs Used</h2>
    <p>Creators on <?= e(SITE_NAME) ?> may be participants in the following affiliate programs:</p>
    <ul>
        <li>Amazon Associates Program</li>
        <li>Flipkart Affiliate Program</li>
        <li>VCommission (Myntra, Nykaa, Ajio, etc.)</li>
        <li>Other Indian e-commerce affiliate programs</li>
    </ul>

    <h2>Our Commitment</h2>
    <p>We require all creators to only link to products they genuinely recommend. However, <?= e(SITE_NAME) ?> does not verify individual product recommendations and is not responsible for the quality or availability of products sold by third-party retailers.</p>

    <h2>Questions</h2>
    <p>If you have questions about affiliate links on a specific storefront, please contact the creator directly through their social media handles listed on their storefront page.</p>
</div>
<?php require_once 'components/footer.php'; ?>
