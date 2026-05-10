<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
if (!headers_sent()) http_response_code(404);
$creator = is_logged_in() ? get_current_creator() : null;
$title   = 'Page Not Found — ' . SITE_NAME;
require_once 'components/header.php';
?>
<div style="
    min-height:60vh;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding:var(--space-2xl) var(--space-lg);
">
    <p style="font-size:4rem;margin-bottom:16px;">🧥</p>
    <h1 style="font-family:var(--font-display);font-size:2rem;margin-bottom:12px;">
        Look Not Found
    </h1>
    <p style="color:var(--color-text-secondary);margin-bottom:var(--space-xl);max-width:400px;">
        The page you're looking for doesn't exist or the creator hasn't published anything yet.
    </p>
    <a href="<?= site_url() ?>" class="btn btn--primary">← Back to Home</a>
</div>
<?php require_once 'components/footer.php'; ?>
