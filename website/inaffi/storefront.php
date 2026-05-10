<?php
// ============================================================
// inaffi.com — Creator Public Storefront
// ============================================================
// URL: inaffi.com/username
// Most important public page — what followers see
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
require_once 'components/platform-badge.php';

// ── Get username from URL ────────────────────────────────────
$username = trim(get_param('username'));
if (!$username || !is_valid_username($username)) {
    http_response_code(404);
    include '404.php';
    exit();
}

// ── Fetch creator ────────────────────────────────────────────
$stmt = get_db()->prepare('SELECT * FROM creators WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$profile = $stmt->fetch();

if (!$profile) {
    http_response_code(404);
    include '404.php';
    exit();
}

// ── Fetch published outfits ──────────────────────────────────
$stmt = get_db()->prepare('
    SELECT * FROM outfits
    WHERE  creator_id  = ?
      AND  is_published = 1
    ORDER  BY created_at DESC
');
$stmt->execute([$profile['id']]);
$outfits = $stmt->fetchAll();

// ── Fetch all products for these outfits ─────────────────────
$products_by_outfit = [];
if ($outfits) {
    $ids          = array_column($outfits, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt         = get_db()->prepare("
        SELECT * FROM products
        WHERE  outfit_id IN ($placeholders)
        ORDER  BY display_order ASC
    ");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $p) {
        $products_by_outfit[$p['outfit_id']][] = $p;
    }
}

// ── Filter outfits with at least 1 in-stock product ──────────
$outfits = array_filter($outfits, function($o) use ($products_by_outfit) {
    $prods = $products_by_outfit[$o['id']] ?? [];
    return count(array_filter($prods, fn($p) => $p['in_stock'])) > 0;
});
$outfits = array_values($outfits);

// ── Category list for filter ─────────────────────────────────
global $CATEGORIES;
$used_cats = array_unique(array_column($outfits, 'category'));
$categories = array_values(array_filter($CATEGORIES, fn($c) => in_array($c, $used_cats)));

// ── Logged-in creator ────────────────────────────────────────
$creator      = is_logged_in() ? get_current_creator() : null;
$is_own_page  = $creator && $creator['id'] === $profile['id'];

// ── Page meta ────────────────────────────────────────────────
$title       = e($profile['display_name']) . ' — ' . SITE_NAME;
$outfit_count = count($outfits);

require_once 'components/header.php';
?>

<!-- ── Creator profile header ──────────────────────────────── -->
<section class="creator-header">
    <div class="container">
        <div class="creator-header__inner">

            <!-- Profile photo -->
            <?php if (!empty($profile['profile_image'])): ?>
                <img
                    src="<?= e(site_url($profile['profile_image'])) ?>"
                    alt="<?= e($profile['display_name']) ?>"
                    class="creator-header__photo"
                    loading="eager"
                >
            <?php else: ?>
                <div class="creator-header__photo-placeholder" aria-hidden="true">
                    <?= e(mb_strtoupper(mb_substr($profile['display_name'], 0, 1))) ?>
                </div>
            <?php endif; ?>

            <!-- Info -->
            <div class="creator-header__info">

                <h1 class="creator-header__name">
                    <?= e($profile['display_name']) ?>
                </h1>

                <?php if (!empty($profile['bio'])): ?>
                    <p class="creator-header__bio">
                        <?= e($profile['bio']) ?>
                    </p>
                <?php endif; ?>

                <!-- Social links -->
                <?php
                $socials = [
                    'instagram_handle' => ['Instagram', 'https://instagram.com/'],
                    'youtube_handle'   => ['YouTube',   'https://youtube.com/@'],
                    'facebook_handle'  => ['Facebook',  'https://facebook.com/'],
                    'pinterest_handle' => ['Pinterest', 'https://pinterest.com/'],
                ];
                $has_socials = false;
                foreach ($socials as $key => [$label, $base]) {
                    if (!empty($profile[$key])) { $has_socials = true; break; }
                }
                ?>

                <?php if ($has_socials): ?>
                <div class="creator-header__socials">
                    <?php foreach ($socials as $key => [$label, $base]): ?>
                        <?php if (!empty($profile[$key])): ?>
                            <a
                                href="<?= e($base . ltrim($profile[$key], '@')) ?>"
                                class="creator-header__social-link"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="<?= e($profile['display_name']) ?> on <?= e($label) ?>"
                            >
                                ↗ <?= e($label) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>

            <!-- Edit profile button (own page only) -->
            <?php if ($is_own_page): ?>
                <a
                    href="<?= site_url('dashboard/settings') ?>"
                    class="btn btn--outline btn--sm"
                    style="margin-left:auto; align-self:flex-start;"
                >
                    Edit Profile
                </a>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- ── Category filter ──────────────────────────────────────── -->
<?php if ($outfit_count > 0): ?>
    <?php
    $active_cat = 'All';
    require_once 'components/category-filter.php';
    ?>
<?php endif; ?>

<!-- ── Outfit grid ───────────────────────────────────────────── -->
<div class="container">

    <?php if ($outfit_count === 0): ?>

        <!-- Empty state -->
        <div style="
            text-align:center;
            padding: var(--space-2xl) 0;
            color: var(--color-text-secondary);
        ">
            <p style="font-size:3rem;margin-bottom:16px;">🧥</p>
            <h2 style="font-family:var(--font-display);margin-bottom:8px;color:var(--color-text-primary);">
                No looks yet
            </h2>
            <p style="margin-bottom:var(--space-lg);">
                <?= e($profile['display_name']) ?> hasn't published any outfits yet.
                Check back soon!
            </p>
            <?php if ($is_own_page): ?>
                <a href="<?= site_url('dashboard/new-outfit') ?>" class="btn btn--primary">
                    Create Your First Look
                </a>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <div class="outfit-grid" id="outfit-grid">
            <?php foreach ($outfits as $outfit):
                $products = $products_by_outfit[$outfit['id']] ?? [];
                $show_share = true;
                require 'components/outfit-card.php';
            endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php require_once 'components/footer.php'; ?>
