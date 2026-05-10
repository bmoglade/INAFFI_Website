<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
$creator = is_logged_in() ? get_current_creator() : null;
$title   = 'Terms of Service — ' . SITE_NAME;
require_once 'components/header.php';
?>
<div class="legal-page">
    <h1>Terms of Service</h1>
    <p class="updated">Last updated: May 2026</p>

    <p>By using <?= e(SITE_NAME) ?> ("the platform"), you agree to these Terms of Service. Please read them carefully.</p>

    <h2>1. Creator Accounts</h2>
    <p>You must be at least 18 years old to create a creator account. You are responsible for maintaining the security of your account and for all activities that occur under your account.</p>

    <h2>2. Affiliate Links</h2>
    <p>Creators may only post affiliate links they are personally enrolled in (Amazon Associates, Flipkart Affiliate, VCommission, etc.). You agree not to post misleading, broken, or spam affiliate links.</p>

    <h2>3. Content</h2>
    <p>You retain ownership of the content (outfit photos, product names) you upload. By uploading content, you grant <?= e(SITE_NAME) ?> a licence to display that content on your public storefront.</p>

    <h2>4. Prohibited Use</h2>
    <ul>
        <li>No fake or fraudulent affiliate links</li>
        <li>No misleading product descriptions</li>
        <li>No spam or automated bot activity</li>
        <li>No content that violates Indian law</li>
        <li>No impersonation of other creators or brands</li>
    </ul>

    <h2>5. Platform Availability</h2>
    <p><?= e(SITE_NAME) ?> is provided "as is". We do not guarantee uninterrupted service and may modify or discontinue features at any time.</p>

    <h2>6. Termination</h2>
    <p>We reserve the right to suspend or terminate accounts that violate these terms, without prior notice.</p>

    <h2>7. Limitation of Liability</h2>
    <p><?= e(SITE_NAME) ?> is not responsible for affiliate commissions, earnings, or disputes between creators and affiliate networks. We are a display platform only.</p>

    <h2>8. Governing Law</h2>
    <p>These Terms are governed by the laws of India.</p>

    <h2>9. Changes</h2>
    <p>We may update these Terms at any time. Continued use of the platform after changes constitutes acceptance of the new Terms.</p>

    <h2>Contact</h2>
    <p>Questions about these Terms? Contact us through our website.</p>
</div>
<?php require_once 'components/footer.php'; ?>
