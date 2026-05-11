<?php
// ============================================================
// SECTION: Hero — Influra-style dark landing hero
// ============================================================
// Left: eyebrow + large headline + subtitle + CTA button
// Right: collage of 6 outfit images (admin-managed)
// Bottom: italic tagline centered
// ============================================================
//
// Variables expected (set in index.php before including):
//   $creator          (array|null)  — logged-in creator or null
//   $display_outfit   (array|null)  — featured outfit row
//   $display_products (array)       — in-stock products for that outfit
//   $use_fallback     (bool)        — true if using mockup data
// ============================================================

// Fetch admin-uploaded hero images (up to 6) from DB
// stored as hero_images in a settings table OR from featured outfits
$hero_images = [];
try {
    $stmt = get_db()->prepare('
        SELECT o.image, o.title
        FROM outfits o
        WHERE o.is_published = 1
          AND o.image IS NOT NULL
          AND o.image != ""
        ORDER BY o.is_featured DESC, o.updated_at DESC
        LIMIT 6
    ');
    $stmt->execute();
    $hero_images = $stmt->fetchAll();
} catch (Exception $e) {
    // silently fallback
}
?>

<section class="hero-dark">
    <div class="container">
        <div class="hero-dark__inner">

        <!-- ── LEFT: Text content ─────────────────────────── -->
            <div class="hero-dark__content">

                <p class="hero-dark__eyebrow">INDIA'S #1 CREATOR PLATFORM</p>
                <h1 class="hero-dark__title">
                    Monetize<br>Your Taste
                </h1>

                <p class="hero-dark__subtitle">
                    Create, influence, and earn —<br>all in one place.
                </p>

                <div class="hero-dark__actions">
                <?php if ($creator): ?>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn--hero-cta">
                            GO TO DASHBOARD
                    </a>
                    <?php else: ?>
                        <a href="<?= site_url('signup') ?>" class="btn btn--hero-cta">
                            JOIN INAFFI
                    </a>
                <?php endif; ?>
                </div>
            </div>

            <!-- ── RIGHT: Outfit image collage ──────────────────── -->
            <div class="hero-dark__collage">
                <?php
                // 6 slots — use DB images or placeholder boxes
                $slots = 6;
                for ($i = 0; $i < $slots; $i++):
                    $img = $hero_images[$i] ?? null;
                ?>
                <div class="hero-collage__item hero-collage__item--<?= $i + 1 ?>">
                    <?php if ($img && !empty($img['image'])): ?>
                            <img
                            src="<?= e(site_url($img['image'])) ?>"
                            alt="<?= e($img['title'] ?? 'Fashion look') ?>"
                            loading="<?= $i < 2 ? 'eager' : 'lazy' ?>"
                            >
                        <?php else: ?>
                        <div class="hero-collage__placeholder">
                            <span>🧥</span>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
                                    </div>

                                    </div>

        <!-- ── Bottom tagline ─────────────────────────── -->
        <p class="hero-dark__tagline">
            A luxury creator platform designed to turn influence into income.
                    </p>
                </div>
</section>

