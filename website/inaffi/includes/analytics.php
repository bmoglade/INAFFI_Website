<?php
// ============================================================
// inaffi.com — Google Analytics 4 Snippet
// ============================================================
// Include in components/footer.php
// Only outputs script tags if GA_MEASUREMENT_ID is configured
// ============================================================

/**
 * Output the GA4 gtag.js script tags.
 * Does nothing if GA_MEASUREMENT_ID is not set or empty.
 */
function render_analytics(): void {
    if (!defined('GA_MEASUREMENT_ID') || GA_MEASUREMENT_ID === '') {
        return;
    }
    $id = htmlspecialchars(GA_MEASUREMENT_ID, ENT_QUOTES, 'UTF-8');
    echo <<<HTML
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{$id}');
    </script>
    HTML;
}
