<?php
// ============================================================
// inaffi.com — Site Footer — matches WT Footer.tsx
// ============================================================
// Closes <main>, outputs footer, closes </body></html>
// Include analytics snippet (GA4) if configured
// ============================================================
?>

</main><!-- /main -->

<footer class="site-footer">
    <div class="container-content">

        <div class="site-footer__inner">
            <!-- Brand -->
            <div>
                <span class="site-footer__brand-name"><?= e(SITE_NAME) ?></span>
                <p class="site-footer__tagline"><?= e(SITE_TAGLINE) ?></p>
            </div>

            <!-- Links -->
        <nav class="site-footer__links" aria-label="Footer navigation">
                <a href="<?= site_url('privacy') ?>">Privacy</a>
                <a href="<?= site_url('terms') ?>">Terms</a>
                <a href="<?= site_url('disclosure') ?>">Disclosure</a>
        </nav>
    </div>

        <!-- Copyright -->
        <div class="site-footer__copy">
            &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.
        </div>

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

