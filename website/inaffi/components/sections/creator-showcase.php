<?php
// ============================================================
// SECTION: Creator Showcase
// ============================================================
// Shows 3 example creator storefronts to demonstrate the
// platform to visitors ("See what creators are doing").
//
// Data source: pulls 3 published creators with at least
// one published outfit from the DB.
// Falls back to static mockup cards if no creators yet.
//
// To disable this section:
//   Comment out its line in index.php
// ============================================================

// Fetch up to 3 creators who have at least 1 published outfit
$showcase_creators = [];
if (db_available()) {
    try {
        $stmt = get_db()->prepare('
            SELECT c.username, c.display_name, c.bio,
                   c.profile_image, c.instagram_handle,
                   COUNT(o.id) AS outfit_count
            FROM   creators c
            JOIN   outfits  o ON o.creator_id = c.id AND o.is_published = 1
            GROUP  BY c.id
            HAVING outfit_count >= 1
            ORDER  BY outfit_count DESC
            LIMIT  3
        ');
        $stmt->execute();
        $showcase_creators = $stmt->fetchAll();
    } catch (Exception $e) {
        $showcase_creators = [];
    }
}

// Don't show section if no creators (local dev without DB — section simply hidden)
if (empty($showcase_creators)) return;
?>

<section class="section" style="background:var(--color-surface);border-top:1px solid var(--color-border);">
    <div class="container">

        <!-- Heading -->
        <div style="text-align:center;max-width:500px;margin:0 auto var(--space-2xl);">
            <p style="
                font-size:0.8125rem;font-weight:600;
                letter-spacing:0.1em;text-transform:uppercase;
                color:var(--color-gold-accent);margin-bottom:12px;
            ">Featured Creators</p>
            <h2 style="margin-bottom:12px;">
                See What Creators
                <span style="color:var(--color-gold-accent);">Are Building</span>
            </h2>
            <p style="color:var(--color-text-secondary);">
                Real storefronts. Real affiliate income.
            </p>
        </div>

        <!-- Creator cards grid -->
        <div style="
            display:grid;
            grid-template-columns:repeat(<?= min(count($showcase_creators), 3) ?>,1fr);
            gap:var(--space-lg);
            margin-bottom:var(--space-2xl);
        ">
            <?php foreach ($showcase_creators as $i => $c): ?>
            <a
                href="<?= site_url(e($c['username'])) ?>"
                style="
                    background:var(--color-background);
                    border:1px solid var(--color-border);
                    border-radius:var(--radius-md);
                    padding:var(--space-xl);
                    display:flex;
                    flex-direction:column;
                    align-items:center;
                    text-align:center;
                    gap:var(--space-md);
                    text-decoration:none;
                    transition:border-color var(--transition-fast),
                               transform var(--transition-base),
                               box-shadow var(--transition-base);
                "
                class="animate-fade-in-up"
                style="animation-delay:<?= $i * 0.1 ?>s;"
                onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-lg)';this.style.borderColor='var(--color-gold-accent)'"
                onmouseout="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--color-border)'"
            >
                <!-- Profile photo -->
                <?php if (!empty($c['profile_image'])): ?>
                    <img
                        src="<?= e(site_url($c['profile_image'])) ?>"
                        alt="<?= e($c['display_name']) ?>"
                        style="
                            width:72px;height:72px;
                            border-radius:50%;
                            object-fit:cover;
                            border:3px solid var(--color-border);
                        "
                        loading="lazy"
                    >
                <?php else: ?>
                    <div style="
                        width:72px;height:72px;
                        border-radius:50%;
                        background:var(--color-border);
                        display:flex;align-items:center;justify-content:center;
                        font-family:var(--font-display);
                        font-size:1.5rem;font-weight:700;
                        color:var(--color-text-secondary);
                    ">
                        <?= e(mb_strtoupper(mb_substr($c['display_name'], 0, 1))) ?>
                    </div>
                <?php endif; ?>

                <!-- Name -->
                <div>
                    <p style="
                        font-family:var(--font-display);
                        font-size:1.0625rem;
                        font-weight:600;
                        color:var(--color-text-primary);
                        margin-bottom:4px;
                    "><?= e($c['display_name']) ?></p>
                    <p style="
                        font-size:0.8125rem;
                        color:var(--color-gold-accent);
                    ">@<?= e($c['username']) ?></p>
                </div>

                <!-- Bio -->
                <?php if (!empty($c['bio'])): ?>
                    <p style="
                        font-size:0.875rem;
                        color:var(--color-text-secondary);
                        line-height:1.5;
                        margin:0;
                    "><?= e(truncate($c['bio'], 80)) ?></p>
                <?php endif; ?>

                <!-- Outfit count -->
                <p style="
                    font-size:0.8125rem;
                    font-weight:600;
                    color:var(--color-text-secondary);
                    background:var(--color-border);
                    padding:4px 12px;
                    border-radius:var(--radius-full);
                ">
                    <?= (int)$c['outfit_count'] ?> look<?= $c['outfit_count'] != 1 ? 's' : '' ?>
                </p>

            </a>
            <?php endforeach; ?>
        </div>

        <!-- View all link -->
        <div style="text-align:center;">
            <a href="<?= site_url('signup') ?>" class="btn btn--outline">
                Start Your Own Storefront →
            </a>
        </div>

    </div>
</section>
