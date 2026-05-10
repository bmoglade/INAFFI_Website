<?php
// ============================================================
// SECTION: Hero
// ============================================================
// Layout: 38/62 horizontal split
//   Left  — headline, tagline, steps, CTA buttons
//   Right — featured outfit card (from DB or fallback)
//
// Variables expected (set in index.php before including):
//   $creator          (array|null)  — logged-in creator or null
//   $display_outfit   (array|null)  — featured outfit row
//   $display_products (array)       — in-stock products for that outfit
//   $use_fallback     (bool)        — true if using mockup data
// ============================================================
?>

<section class="hero">
    <div class="container">

        <!-- ── LEFT: Text content ─────────────────────────── -->
        <div class="hero__content animate-fade-in-up">

            <p class="hero__eyebrow">India's Creator Storefront</p>

            <h1 class="hero__title">
                Your Outfits.<br>
                One Link.<br>
                <span style="color:var(--color-gold-accent);">Shop Everything.</span>
            </h1>

            <p class="hero__tagline">
                "From Instagram to checkout in under 30 seconds."
            </p>

            <p class="hero__steps">
                Build &rarr; Share &rarr; Earn
            </p>

            <div class="hero__actions">
                <?php if ($creator): ?>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn--primary btn--lg">
                        Go to Dashboard
                    </a>
                    <a href="<?= site_url($creator['username']) ?>" class="btn btn--outline btn--lg">
                        My Storefront ↗
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('signup') ?>" class="btn btn--primary btn--lg">
                        Join as Creator — Free
                    </a>
                    <a href="<?= site_url('login') ?>" class="btn btn--outline btn--lg">
                        Log in
                    </a>
                <?php endif; ?>
            </div>

        </div>

        <!-- ── RIGHT: Featured outfit card ──────────────────── -->
        <div class="hero__outfit animate-fade-in-up delay-2">

            <?php if ($display_outfit): ?>

                <?php
                $has_img  = !empty($display_outfit['image']);
                $img_url  = $has_img ? site_url($display_outfit['image']) : '';
                $out_user = $use_fallback ? '' : ($display_outfit['username'] ?? '');
                ?>

                <div class="outfit-card" style="box-shadow:var(--shadow-lg);">

                    <!-- Outfit image -->
                    <div class="outfit-card__image-wrap">
                        <?php if ($has_img): ?>
                            <img
                                src="<?= e($img_url) ?>"
                                alt="<?= e($display_outfit['title']) ?>"
                                class="outfit-card__image"
                                loading="eager"
                            >
                        <?php else: ?>
                            <div class="outfit-card__image-placeholder">🧥</div>
                        <?php endif; ?>

                        <?php if (!empty($display_outfit['category'])): ?>
                            <span class="outfit-card__category">
                                <?= e($display_outfit['category']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Products -->
                    <div class="outfit-card__body">

                        <h3 class="outfit-card__title">
                            <?= e($display_outfit['title']) ?>
                        </h3>

                        <div class="outfit-card__products">
                            <?php foreach ($display_products as $product): ?>
                                <?php if (empty($product['in_stock'])) continue; ?>
                                <div class="product-item">

                                    <div style="flex-shrink:0;">
                                        <?php
                                        if (!empty($product['image'])) {
                                            echo '<img'
                                                . ' src="' . e(site_url($product['image'])) . '"'
                                                . ' alt="' . e($product['name']) . '"'
                                                . ' class="product-item__img" loading="lazy"'
                                                . ' onerror="this.style.display=\'none\'"'
                                                . '>';
                                        } else {
                                            render_platform_logo($product['platform'], 48);
                                        }
                                        ?>
                                    </div>

                                    <div class="product-item__info">
                                        <p class="product-item__name">
                                            <?= e(truncate($product['name'], 50)) ?>
                                        </p>
                                        <?php render_platform_badge($product['platform']); ?>
                                    </div>

                                    <?php if (!$use_fallback && !empty($product['id'])): ?>
                                        <a
                                            href="<?= site_url('go?p=' . (int)$product['id']) ?>"
                                            class="btn btn--shop"
                                            target="_blank"
                                            rel="noopener noreferrer nofollow"
                                        >Shop ↗</a>
                                    <?php else: ?>
                                        <span class="btn btn--shop"
                                            style="opacity:0.5;cursor:default;">
                                            Shop ↗
                                        </span>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($out_user): ?>
                            <div class="outfit-card__footer">
                                <a
                                    href="<?= site_url(e($out_user)) ?>"
                                    style="color:var(--color-gold-accent);font-weight:500;"
                                >View full storefront →</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            <?php else: ?>

                <!-- No outfit at all — placeholder CTA card -->
                <div style="
                    background:var(--color-surface);
                    border:2px dashed var(--color-border);
                    border-radius:8px;
                    padding:48px 32px;
                    text-align:center;
                ">
                    <p style="font-size:3rem;margin-bottom:16px;">🧥</p>
                    <h3 style="font-family:var(--font-display);margin-bottom:8px;">
                        Your Outfit, Here
                    </h3>
                    <p style="color:var(--color-text-secondary);margin-bottom:24px;">
                        Create an account and feature your first look on this homepage.
                    </p>
                    <a href="<?= site_url('signup') ?>" class="btn btn--gold">
                        Get Started Free
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>
</section>
