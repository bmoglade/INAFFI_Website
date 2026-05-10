<?php
// ============================================================
// inaffi.com — Dashboard Overview
// ============================================================
// Shows: stats (outfits, products, clicks), outfit list
// with category filter, publish toggle, featured toggle
// ============================================================

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

require_login();
$creator     = get_current_creator();
$active_page = 'overview';

// ── Stats ────────────────────────────────────────────────────
$db = get_db();

$total_outfits = (int) $db->prepare('SELECT COUNT(*) FROM outfits WHERE creator_id = ?')
    ->execute([$creator['id']]) ? $db->query('SELECT FOUND_ROWS()')->fetchColumn() : 0;

// Simpler individual counts
$stmt = $db->prepare('SELECT COUNT(*) FROM outfits WHERE creator_id = ?');
$stmt->execute([$creator['id']]);
$total_outfits = (int) $stmt->fetchColumn();

$stmt = $db->prepare('
    SELECT COUNT(*) FROM products p
    JOIN outfits o ON o.id = p.outfit_id
    WHERE o.creator_id = ? AND p.in_stock = 1
');
$stmt->execute([$creator['id']]);
$total_products = (int) $stmt->fetchColumn();

$stmt = $db->prepare('
    SELECT COUNT(*) FROM clicks
    WHERE creator_id = ?
      AND clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
');
$stmt->execute([$creator['id']]);
$weekly_clicks = (int) $stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) FROM clicks WHERE creator_id = ?');
$stmt->execute([$creator['id']]);
$total_clicks = (int) $stmt->fetchColumn();

// ── Outfits list ─────────────────────────────────────────────
$stmt = $db->prepare('
    SELECT o.*,
           COUNT(p.id)                                       AS product_count,
           SUM(CASE WHEN p.in_stock = 1 THEN 1 ELSE 0 END)  AS instock_count
    FROM   outfits o
    LEFT   JOIN products p ON p.outfit_id = o.id
    WHERE  o.creator_id = ?
    GROUP  BY o.id
    ORDER  BY o.created_at DESC
');
$stmt->execute([$creator['id']]);
$outfits = $stmt->fetchAll();

global $CATEGORIES;
$title       = 'Dashboard — ' . SITE_NAME;
$csrf        = generate_csrf_token();

require_once '../components/header.php';
require_once '../components/dashboard-sidebar.php';
?>

<div class="dashboard-layout">
    <?php // sidebar already output above ?>
    <div class="dashboard-main">

        <?php render_flash(); ?>

        <h1 class="dashboard-page-title">Overview</h1>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <p class="stat-card__label">Total Outfits</p>
                <p class="stat-card__value"><?= $total_outfits ?></p>
                <p class="stat-card__sub">Published + drafts</p>
            </div>
            <div class="stat-card">
                <p class="stat-card__label">Active Products</p>
                <p class="stat-card__value"><?= $total_products ?></p>
                <p class="stat-card__sub">In-stock products</p>
            </div>
            <div class="stat-card">
                <p class="stat-card__label">Clicks This Week</p>
                <p class="stat-card__value"><?= $weekly_clicks ?></p>
                <p class="stat-card__sub"><?= $total_clicks ?> total all time</p>
            </div>
        </div>

        <!-- Outfit list header -->
        <div class="outfit-list-header">
            <p class="outfit-list-header__title">
                Your Outfits
                <span style="color:var(--color-text-secondary);font-weight:400;font-size:0.875rem;">
                    (<?= count($outfits) ?>)
                </span>
            </p>
            <a href="<?= site_url('dashboard/new-outfit') ?>" class="btn btn--primary btn--sm">
                + New Outfit
            </a>
        </div>

        <!-- Category filter pills -->
        <?php if ($outfits): ?>
        <div class="outfit-list-filters" data-filter-list>
            <button type="button"
                class="category-filter__pill active"
                data-filter-pill="All">
                All (<?= count($outfits) ?>)
            </button>
            <?php foreach ($CATEGORIES as $cat):
                $count = count(array_filter($outfits, fn($o) => $o['category'] === $cat));
                if (!$count) continue;
            ?>
            <button type="button"
                class="category-filter__pill"
                data-filter-pill="<?= e($cat) ?>">
                <?= e($cat) ?> (<?= $count ?>)
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Outfit list -->
        <div class="outfit-list-table">
            <?php if (empty($outfits)): ?>
                <div class="outfit-list-empty">
                    <p>No outfits yet. Create your first look!</p>
                    <a href="<?= site_url('dashboard/new-outfit') ?>" class="btn btn--primary">
                        Create First Outfit
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($outfits as $outfit): ?>
                <div
                    class="outfit-list-item"
                    data-category="<?= e($outfit['category']) ?>"
                >
                    <!-- Thumbnail -->
                    <?php if (!empty($outfit['image'])): ?>
                        <img
                            src="<?= e(site_url($outfit['image'])) ?>"
                            alt=""
                            class="outfit-list-item__thumb"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <div class="outfit-list-item__thumb-placeholder">🧥</div>
                    <?php endif; ?>

                    <!-- Info -->
                    <div class="outfit-list-item__info">
                        <p class="outfit-list-item__title">
                            <?= e($outfit['title']) ?>
                        </p>
                        <p class="outfit-list-item__meta">
                            <?= e($outfit['category']) ?>
                            &middot; <?= (int)$outfit['instock_count'] ?>/<?= (int)$outfit['product_count'] ?> in stock
                            &middot; <?= date('d M Y', strtotime($outfit['created_at'])) ?>
                        </p>

                        <!-- Share link -->
                        <div class="copy-wrap" style="max-width:320px;margin-top:6px;">
                            <?php $share = SITE_URL . '/' . $creator['username'] . '?look=' . $outfit['id']; ?>
                            <input
                                type="text"
                                id="share-<?= $outfit['id'] ?>"
                                class="form-input"
                                value="<?= e($share) ?>"
                                readonly
                                style="font-size:0.8125rem;"
                            >
                            <button
                                type="button"
                                class="btn--copy"
                                data-copy-btn
                                data-copy-target="#share-<?= $outfit['id'] ?>"
                            >Copy</button>
                        </div>
                    </div>

                    <!-- Featured toggle -->
                    <button
                        type="button"
                        class="btn btn--sm <?= $outfit['is_featured'] ? 'btn--gold' : 'btn--ghost' ?>"
                        data-featured-toggle="<?= (int)$outfit['id'] ?>"
                        data-featured="<?= $outfit['is_featured'] ? '1' : '0' ?>"
                        title="Feature on homepage"
                    >
                        <?= $outfit['is_featured'] ? '⭐ Featured' : '☆ Feature' ?>
                    </button>

                    <!-- Publish toggle -->
                    <button
                        type="button"
                        class="btn btn--sm <?= $outfit['is_published'] ? 'btn--outline' : 'btn--ghost' ?>"
                        data-publish-toggle="<?= (int)$outfit['id'] ?>"
                        data-published="<?= $outfit['is_published'] ? '1' : '0' ?>"
                    >
                        <?= $outfit['is_published'] ? 'Published' : 'Draft' ?>
                    </button>

                    <!-- Edit -->
                    <a
                        href="<?= site_url('dashboard/edit-outfit?id=' . (int)$outfit['id']) ?>"
                        class="btn btn--primary btn--sm"
                    >Edit</a>

                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require_once '../components/footer.php'; ?>
