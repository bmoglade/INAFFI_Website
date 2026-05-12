<?php
// ============================================================
// SECTION: Hero
// ============================================================
// Matches WT reference layout exactly:
//   Left  38%: h1 "Monetize Your Taste." + "Build→Share→Earn" + CTA
//   Right 62%: outfit card (image left + product list right) + tagline
//
// Outfit card data: featured outfit from DB, falls back to mockup
// ============================================================

$hero_images = [];
if (db_available()) {
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
}
?>

<section class="hero-section">
    <div class="container-content">
        <div class="hero-inner">

            <!-- ── LEFT 38%: Headline + CTA ──────────────── -->
            <div class="hero-left">

                <h1 class="hero-title">
                    Monetize<br>Your Taste.
                </h1>

                <p class="hero-steps">
                    Build &rarr; Share &rarr; Earn
                </p>

                <div class="hero-cta">
                <?php if ($creator): ?>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn--cta-outline">
                            Go to Dashboard
                    </a>
                    <?php else: ?>
                        <a href="<?= site_url('signup') ?>" class="btn btn--cta-outline">
                            Start for Free
                        </a>
                    <?php endif; ?>
                </div>

                        </div>

            <!-- ── RIGHT 62%: Outfit card + tagline ─────── -->
            <div class="hero-right">

                <div class="hero-outfit-card">

                    <!-- Outfit image — left panel -->
                    <div class="hero-outfit-card__image-panel">
                        <?php if (!empty($display_outfit['image'])): ?>
                            <img
                                src="<?= e(site_url($display_outfit['image'])) ?>"
                                alt="<?= e($display_outfit['title'] ?? '') ?>"
                                class="hero-outfit-card__image"
                                loading="eager"
                            >
                        <?php else: ?>
                            <div class="hero-outfit-card__image-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="0.8" stroke-linecap="round">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product list — right panel -->
                    <div class="hero-outfit-card__body">
                        <h3 class="hero-outfit-card__title">
                            <?= e($display_outfit['title'] ?? 'Featured Look') ?>
                        </h3>
                        <p class="hero-outfit-card__category">
                            <?= e($display_outfit['category'] ?? '') ?>
        </p>

                        <div class="hero-product-list">
                            <?php foreach (array_slice($display_products, 0, 5) as $product):
                                if (empty($product['in_stock'])) continue;
                                global $PLATFORM_COLORS;
                                $colors = $PLATFORM_COLORS[$product['platform']] ?? ['#666666','#FFFFFF'];
                            ?>
                            <div class="hero-product-row">
                                <!-- Platform logo / fallback square -->
                                <div class="hero-product-row__logo"
                                    style="background:<?= e($colors[0]) ?>;">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?= e(site_url($product['image'])) ?>"
                                            alt="<?= e($product['name']) ?>">
                                    <?php else: ?>
                                        <span style="color:<?= e($colors[1]) ?>;">
                                            <?= e(mb_strtoupper(mb_substr($product['platform'],0,1))) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Name + platform -->
                                <div class="hero-product-row__info">
                                    <p class="hero-product-row__name">
                                        <?= e(mb_strlen($product['name']) > 40
                                            ? mb_substr($product['name'],0,40).'…'
                                            : $product['name']) ?>
                                    </p>
                                    <p class="hero-product-row__platform"
                                        style="color:<?= e($colors[0]) ?>;">
                                        <?= e($product['platform']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div><!-- /hero-outfit-card -->

                <!-- Tagline below card -->
                <?php if (!empty($display_outfit['title'])): ?>
                <p class="hero-tagline">
                    &ldquo;From Instagram to checkout in under 30 seconds.&rdquo;
                </p>
                <?php endif; ?>
            </div><!-- /hero-right -->

        </div><!-- /hero-inner -->
    </div><!-- /container-content -->
</section>

