<?php
// ============================================================
// inaffi.com — Homepage
// ============================================================
// Sections:
//   1. Hero (38/62 split: text left, featured outfit card right)
//   2. Brand strip (scrolling platform logos)
//   3. Info section (how it works — 45/55 split)
//
// Featured outfit: pulled from DB (is_featured = 1)
// Fallback: static mockup from includes/landing-mockup.php
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
require_once 'components/platform-badge.php';

// ── Fetch featured outfit from DB ────────────────────────────
$featured_outfit   = null;
$featured_products = [];

try {
    // Get the one featured, published outfit
    $stmt = get_db()->prepare('
        SELECT o.*, c.username, c.display_name
        FROM   outfits o
        JOIN   creators c ON c.id = o.creator_id
        WHERE  o.is_featured  = 1
          AND  o.is_published  = 1
        LIMIT  1
    ');
    $stmt->execute();
    $featured_outfit = $stmt->fetch() ?: null;

    // Get its in-stock products
    if ($featured_outfit) {
        $stmt2 = get_db()->prepare('
            SELECT *
            FROM   products
            WHERE  outfit_id = ?
              AND  in_stock   = 1
            ORDER  BY display_order ASC
            LIMIT  10
        ');
        $stmt2->execute([$featured_outfit['id']]);
        $featured_products = $stmt2->fetchAll();

        // If all products are OOS, treat as no featured outfit
        if (empty($featured_products)) {
            $featured_outfit = null;
        }
    }
} catch (Exception $e) {
    error_log('[inaffi homepage] DB error: ' . $e->getMessage());
    $featured_outfit = null;
}

// ── Static fallback if no featured outfit ───────────────────
$use_fallback = ($featured_outfit === null);
if ($use_fallback) {
    require_once 'includes/landing-mockup.php';
    // landing-mockup.php sets: $mockup_outfit, $mockup_products
}

// ── Page setup ───────────────────────────────────────────────
$creator       = is_logged_in() ? get_current_creator() : null;
$title         = SITE_NAME . ' — ' . SITE_TAGLINE;
$display_outfit    = $use_fallback ? ($mockup_outfit    ?? null) : $featured_outfit;
$display_products  = $use_fallback ? ($mockup_products  ?? [])   : $featured_products;

global $PLATFORMS, $PLATFORM_LOGOS, $PLATFORM_COLORS;

require_once 'components/header.php';
?>

<!-- ============================================================
     HERO SECTION
     ============================================================ -->
<section class="hero">
    <div class="container">

        <!-- Left: Text content -->
        <div class="hero__content animate-fade-in-up">

            <p class="hero__eyebrow">India's Creator Storefront</p>

            <h1 class="hero__title">
                Your Outfits.<br>
                One Link.<br>
                <span style="color: var(--color-gold-accent);">Shop Everything.</span>
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

        <!-- Right: Featured outfit card -->
        <div class="hero__outfit animate-fade-in-up delay-2">
            <?php if ($display_outfit): ?>
                <?php
                    // Mini outfit card for homepage hero (no share link, no full card chrome)
                    $has_img   = !empty($display_outfit['image']);
                    $img_url   = $has_img ? site_url($display_outfit['image']) : '';
                    $out_title = e($display_outfit['title']);
                    $out_cat   = e($display_outfit['category'] ?? '');
                    $out_user  = $use_fallback
                        ? ($display_outfit['username'] ?? '')
                        : ($featured_outfit['username'] ?? '');
                ?>
                <div class="outfit-card" style="box-shadow: var(--shadow-lg);">

                    <!-- Outfit image -->
                    <div class="outfit-card__image-wrap">
                        <?php if ($has_img): ?>
                            <img
                                src="<?= e($img_url) ?>"
                                alt="<?= $out_title ?>"
                                class="outfit-card__image"
                                loading="eager"
                            >
                        <?php else: ?>
                            <div class="outfit-card__image-placeholder">🧥</div>
                        <?php endif; ?>
                        <?php if ($out_cat): ?>
                            <span class="outfit-card__category"><?= $out_cat ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Products -->
                    <div class="outfit-card__body">
                        <h3 class="outfit-card__title"><?= $out_title ?></h3>

                        <div class="outfit-card__products">
                            <?php foreach ($display_products as $product): ?>
                                <?php if (empty($product['in_stock'])) continue; ?>
                                <div class="product-item">

                                    <!-- Logo -->
                                    <div style="flex-shrink:0;">
                                        <?php render_platform_logo($product['platform'], 48); ?>
                                    </div>

                                    <!-- Name -->
                                    <div class="product-item__info">
                                        <p class="product-item__name">
                                            <?= e(truncate($product['name'], 50)) ?>
                                        </p>
                                        <?php render_platform_badge($product['platform']); ?>
                                    </div>

                                    <!-- Shop btn — only links through if real product -->
                                    <?php if (!$use_fallback && !empty($product['id'])): ?>
                                        <a
                                            href="<?= site_url('go?p=' . (int)$product['id']) ?>"
                                            class="btn btn--shop"
                                            target="_blank"
                                            rel="noopener noreferrer nofollow"
                                        >Shop ↗</a>
                                    <?php else: ?>
                                        <span class="btn btn--shop" style="opacity:0.5;cursor:default;">
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
                                style="color: var(--color-gold-accent); font-weight:500;"
                            >
                                View full storefront →
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            <?php else: ?>
                <!-- No featured outfit and no fallback data — show CTA card -->
                <div style="
                    background: var(--color-surface);
                    border: 2px dashed var(--color-border);
                    border-radius: 8px;
                    padding: 48px 32px;
                    text-align: center;
                ">
                    <p style="font-size:3rem; margin-bottom: 16px;">🧥</p>
                    <h3 style="font-family: var(--font-display); margin-bottom: 8px;">
                        Your Outfit, Here
                    </h3>
                    <p style="color: var(--color-text-secondary); margin-bottom: 24px;">
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


<!-- ============================================================
     BRAND STRIP — Scrolling platform logos
     ============================================================ -->
<section class="brand-strip">
    <div class="container">
        <p class="brand-strip__label">Shop from trusted platforms</p>

        <div class="brand-strip__overflow">
            <!-- Track duplicated for seamless infinite scroll -->
            <div class="brand-strip__track">
                <?php
                // Render all platforms twice for seamless loop
                $strip_platforms = array_keys($PLATFORM_LOGOS);
                for ($loop = 0; $loop < 2; $loop++):
                    foreach ($strip_platforms as $pname):
                        $logo_path = $PLATFORM_LOGOS[$pname];
                        $logo_url  = site_url('assets/images/platforms/' . $logo_path);
                        $bg_color  = $PLATFORM_COLORS[$pname][0] ?? '#666666';
                ?>
                    <div class="brand-strip__item">
                        <img
                            src="<?= e($logo_url) ?>"
                            alt="<?= e($pname) ?>"
                            class="brand-strip__logo"
                            loading="lazy"
                            onerror="
                                this.style.display='none';
                                this.nextElementSibling.style.display='flex';
                            "
                        >
                        <span
                            class="brand-strip__logo-fallback"
                            style="background:<?= e($bg_color) ?>; display:none;"
                        >
                            <?= e(platform_initial($pname)) ?>
                        </span>
                        <span class="brand-strip__name"><?= e($pname) ?></span>
                    </div>
                <?php
                    endforeach;
                endfor;
                ?>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     INFO SECTION — How it works
     ============================================================ -->
<section class="info-section">
    <div class="container">

        <!-- Left: How it works -->
        <div class="info-section__content animate-fade-in-up">

            <h2 class="info-section__title">
                The Easiest Way to<br>
                <span style="color: var(--color-gold-accent);">Monetise Your Style</span>
            </h2>

            <div class="info-section__steps">

                <div class="info-step">
                    <div class="info-step__num">1</div>
                    <div class="info-step__text">
                        <strong>Create your storefront</strong>
                        <p>Sign up free. Get your personal URL — <?= e(SITE_URL) ?>/yourname</p>
                    </div>
                </div>

                <div class="info-step">
                    <div class="info-step__num">2</div>
                    <div class="info-step__text">
                        <strong>Add your outfit looks</strong>
                        <p>Upload outfit photos and paste affiliate links from Amazon, Flipkart, Myntra and more.</p>
                    </div>
                </div>

                <div class="info-step">
                    <div class="info-step__num">3</div>
                    <div class="info-step__text">
                        <strong>Share your link</strong>
                        <p>Put your <?= e(SITE_NAME) ?> link in your Instagram bio, YouTube description, or Reels.</p>
                    </div>
                </div>

                <div class="info-step">
                    <div class="info-step__num">4</div>
                    <div class="info-step__text">
                        <strong>Earn affiliate commissions</strong>
                        <p>Every click goes directly to the store. You earn from Amazon Associates, Flipkart Affiliate, and more.</p>
                    </div>
                </div>

            </div>

            <a href="<?= site_url('signup') ?>" class="btn btn--gold btn--lg">
                Start for Free — No Credit Card Needed
            </a>

        </div>

        <!-- Right: Platform grid visual -->
        <div class="info-section__visual animate-fade-in-up delay-2">
            <div style="
                background: var(--color-surface);
                border: 1px solid var(--color-border);
                border-radius: 8px;
                padding: 32px;
            ">
                <p style="
                    font-family: var(--font-display);
                    font-size: 0.875rem;
                    letter-spacing: 0.08em;
                    text-transform: uppercase;
                    color: var(--color-text-secondary);
                    margin-bottom: 20px;
                    font-weight: 600;
                ">Supported Platforms</p>

                <div style="
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 16px;
                    margin-bottom: 24px;
                ">
                    <?php
                    global $PLATFORMS, $PLATFORM_COLORS;
                    $display_platforms = array_slice(array_filter($PLATFORMS, fn($p) => $p !== 'Other'), 0, 9);
                    foreach ($display_platforms as $pname):
                        $colors = $PLATFORM_COLORS[$pname] ?? ['#666', '#fff'];
                    ?>
                        <div style="
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            gap: 6px;
                            padding: 12px 8px;
                            border: 1px solid var(--color-border);
                            border-radius: 4px;
                            transition: border-color 0.2s;
                        ">
                            <?php render_platform_logo($pname, 32); ?>
                            <span style="font-size: 0.6875rem; color: var(--color-text-secondary); text-align:center;">
                                <?= e($pname) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Stats row -->
                <div style="
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 12px;
                    padding-top: 20px;
                    border-top: 1px solid var(--color-border);
                ">
                    <div style="text-align:center;">
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight:700; color: var(--color-gold-accent);">
                            <?= count(array_filter($PLATFORMS, fn($p) => $p !== 'Other')) ?>+
                        </p>
                        <p style="font-size: 0.75rem; color: var(--color-text-secondary);">Platforms</p>
                    </div>
                    <div style="text-align:center;">
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight:700; color: var(--color-gold-accent);">15</p>
                        <p style="font-size: 0.75rem; color: var(--color-text-secondary);">Products / Look</p>
                    </div>
                    <div style="text-align:center;">
                        <p style="font-family: var(--font-display); font-size: 1.5rem; font-weight:700; color: var(--color-gold-accent);">Free</p>
                        <p style="font-size: 0.75rem; color: var(--color-text-secondary);">Forever</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>


<!-- ============================================================
     BOTTOM CTA STRIP
     ============================================================ -->
<?php if (!$creator): ?>
<section style="
    background: var(--color-primary-dark);
    padding: var(--space-2xl) 0;
    text-align: center;
">
    <div class="container">
        <h2 style="
            font-family: var(--font-display);
            color: #FFFFFF;
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            margin-bottom: 16px;
        ">
            Ready to turn your style into income?
        </h2>
        <p style="color: rgba(255,255,255,0.6); margin-bottom: 32px; font-size: 1.0625rem;">
            Join thousands of Indian creators already earning affiliate commissions.
        </p>
        <a href="<?= site_url('signup') ?>" class="btn btn--gold btn--lg">
            Create Your Free Storefront
        </a>
    </div>
</section>
<?php endif; ?>

<?php require_once 'components/footer.php'; ?>
