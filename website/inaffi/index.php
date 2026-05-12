<?php
// ============================================================
// inaffi.com — Homepage
// Matches WT page.tsx: Hero → Brand Strip → Info → Footer
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
require_once 'components/platform-badge.php';

$creator = null;

// ── Featured outfit from DB (matches WT getFeaturedOutfit) ──
$featured_outfit   = null;
$featured_products = [];

if (db_available()) {
    try {
        $creator = is_logged_in() ? get_current_creator() : null;

        $stmt = get_db()->prepare('
            SELECT o.*, c.username, c.display_name
            FROM   outfits  o
            JOIN   creators c ON c.id = o.creator_id
            WHERE  o.is_featured  = 1
              AND  o.is_published = 1
            LIMIT  1
        ');
        $stmt->execute();
        $featured_outfit = $stmt->fetch() ?: null;

        if ($featured_outfit) {
            $stmt2 = get_db()->prepare('
                SELECT * FROM products
                WHERE  outfit_id = ? AND in_stock = 1
                ORDER  BY display_order ASC
                LIMIT  5
            ');
            $stmt2->execute([$featured_outfit['id']]);
            $featured_products = $stmt2->fetchAll();
            if (empty($featured_products)) $featured_outfit = null;
        }
    } catch (Exception $e) {
        error_log('[inaffi] Homepage DB error: ' . $e->getMessage());
    }
}

// Fall back to static mockup if no DB or no featured outfit
$use_fallback = ($featured_outfit === null);
if ($use_fallback) {
    require_once 'includes/landing-mockup.php';
}

$display_outfit   = $use_fallback ? ($mockup_outfit   ?? null) : $featured_outfit;
$display_products = $use_fallback ? ($mockup_products ?? [])   : $featured_products;

$title = SITE_NAME . ' — ' . SITE_TAGLINE;

require_once 'components/header.php';

// Section 1: Hero (38/62 split — headline + outfit card)
require_once 'components/sections/hero.php';

// Divider: Brand logo scrolling strip
require_once 'components/sections/brand-strip.php';

// Section 2: How it works + Earnings (45/55 split)
require_once 'components/sections/how-it-works.php';

require_once 'components/footer.php';
