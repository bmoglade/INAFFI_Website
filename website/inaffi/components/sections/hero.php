<?php
// ============================================================
// SECTION: Hero — Dark landing hero
// ============================================================
// Left:   eyebrow + large headline + subtitle + CTA button
// Right:  collage of 6 outfit images (auto-pulled from DB)
// Bottom: italic tagline centered
// ============================================================

$hero_images = [];
try {
    $stmt = get_db()->prepare('
        SELECT o.image, o.title
        FROM   outfits o
        WHERE  o.is_published = 1
          AND  o.image IS NOT NULL
          AND  o.image != ""
        ORDER  BY o.is_featured DESC, o.updated_at DESC
        LIMIT  6
    ');
    $stmt->execute();
    $hero_images = $stmt->fetchAll();
} catch (Exception $e) {
    // silently fallback to placeholders
}
?>

<section class="hero-dark">
    <div class="container">

        <div class="hero-dark__inner">

            <!-- ── LEFT: Text ──────────────────────────────── -->
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

            </div><!-- /hero-dark__content -->

            <!-- ── RIGHT: Image collage ────────────────────── -->
            <div class="hero-dark__collage">
                <?php for ($i = 0; $i < 6; $i++):
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
            </div><!-- /hero-dark__collage -->

        </div><!-- /hero-dark__inner -->

        <!-- ── Bottom tagline ──────────────────────────────── -->
        <p class="hero-dark__tagline">
            A luxury creator platform designed to turn influence into income.
        </p>

    </div><!-- /container -->
</section>
