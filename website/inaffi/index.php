<?php
// ============================================================
// inaffi.com — Homepage (Layout Controller)
// ============================================================
// This file ONLY:
//   1. Loads dependencies
//   2. Fetches shared data (featured outfit, login state)
//   3. Includes sections in order
//
// To add a section:    add one require line below
// To remove a section: comment out its require line
// To reorder sections: move its require line up or down
// To edit a section:   open components/sections/<name>.php
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
require_once 'components/platform-badge.php';

// ── Shared data ──────────────────────────────────────────────

// Logged-in creator (or null)
$creator = is_logged_in() ? get_current_creator() : null;

// Featured outfit from DB
$featured_outfit   = null;
$featured_products = [];

try {
    $stmt = get_db()->prepare('
        SELECT o.*, c.username, c.display_name
        FROM   outfits  o
        JOIN   creators c ON c.id = o.creator_id
        WHERE  o.is_featured = 1
          AND  o.is_published = 1
        LIMIT  1
    ');
    $stmt->execute();
    $featured_outfit = $stmt->fetch() ?: null;

    if ($featured_outfit) {
        $stmt2 = get_db()->prepare('
            SELECT * FROM products
            WHERE outfit_id = ? AND in_stock = 1
            ORDER BY display_order ASC
            LIMIT 10
        ');
        $stmt2->execute([$featured_outfit['id']]);
        $featured_products = $stmt2->fetchAll();

        if (empty($featured_products)) $featured_outfit = null;
    }
} catch (Exception $e) {
    error_log('[inaffi] Homepage DB error: ' . $e->getMessage());
}

// Fall back to static mockup if no featured outfit
$use_fallback = ($featured_outfit === null);
if ($use_fallback) {
    require_once 'includes/landing-mockup.php';
}

$display_outfit   = $use_fallback ? ($mockup_outfit   ?? null) : $featured_outfit;
$display_products = $use_fallback ? ($mockup_products ?? [])   : $featured_products;

// ── Page meta ────────────────────────────────────────────────
$title = SITE_NAME . ' — ' . SITE_TAGLINE;

// ── Render ───────────────────────────────────────────────────
require_once 'components/header.php';

// ── Sections ─────────────────────────────────────────────────
// To reorder: move lines. To disable: comment out. To add: require new file.

require_once 'components/sections/hero.php';           // Featured outfit card + headline

require_once 'components/sections/brand-strip.php';    // Scrolling platform logos

require_once 'components/sections/how-it-works.php';   // 4-step process

require_once 'components/sections/platforms-grid.php'; // Platform grid + stats bar

require_once 'components/sections/creator-showcase.php'; // Real creator cards (auto from DB)

require_once 'components/sections/testimonials.php';   // Creator quotes

require_once 'components/sections/cta-strip.php';      // Bottom dark CTA (hidden if logged in)

// ── Footer ───────────────────────────────────────────────────
require_once 'components/footer.php';
