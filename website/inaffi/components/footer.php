<?php
// ============================================================
// inaffi.com — Site Footer
// ============================================================
// Closes <main>, outputs footer, closes </body></html>
// Include analytics snippet (GA4) if configured
// ============================================================
?>

</main><!-- /main -->

<footer class="site-footer">
    <div class="container">
        <div>
            <span class="site-footer__brand"><?= e(SITE_NAME) ?></span>
            <p class="text-sm text-secondary mt-sm" style="color:rgba(255,255,255,0.5)">
                <?= e(SITE_TAGLINE) ?>
            </p>
        </div>

        <nav class="site-footer__links" aria-label="Footer navigation">
            <a href="<?= site_url('privacy') ?>">Privacy Policy</a>
            <a href="<?= site_url('terms') ?>">Terms of Service</a>
            <a href="<?= site_url('disclosure') ?>">Affiliate Disclosure</a>
            <a href="<?= site_url('login') ?>">Creator Login</a>
        </nav>
    </div>

    <div class="container">
        <p class="site-footer__copy">
            &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.
            Affiliate links on this platform earn commissions that support creators directly.
        </p>
    </div>
</footer>

<!-- Global JS -->
<script src="<?= site_url('assets/js/main.js') ?>"></script>

<?php
// Google Analytics (only if GA ID is configured)
require_once __DIR__ . '/../includes/analytics.php';
render_analytics();
?>

</body>
</html>
