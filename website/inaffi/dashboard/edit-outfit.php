<?php
// ============================================================
// inaffi.com — Edit Outfit
// ============================================================

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/upload.php';

require_login();
$creator     = get_current_creator();
$active_page = 'overview';

global $CATEGORIES, $PLATFORMS;

// ── Load outfit (must belong to this creator) ────────────────
$outfit_id = (int) get_param('id');
if (!$outfit_id) redirect(site_url('dashboard'));

$stmt = get_db()->prepare('SELECT * FROM outfits WHERE id = ? AND creator_id = ? LIMIT 1');
$stmt->execute([$outfit_id, $creator['id']]);
$outfit = $stmt->fetch();
if (!$outfit) redirect(site_url('dashboard'));

// ── Load existing products ────────────────────────────────────
$stmt = get_db()->prepare('SELECT * FROM products WHERE outfit_id = ? ORDER BY display_order ASC');
$stmt->execute([$outfit_id]);
$existing_products = $stmt->fetchAll();

$errors = [];

// ── Handle DELETE ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_delete'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    // Delete images from disk
    if ($outfit['image']) delete_image($outfit['image']);
    foreach ($existing_products as $p) {
        if ($p['image']) delete_image($p['image']);
    }
    $del = get_db()->prepare('DELETE FROM outfits WHERE id = ? AND creator_id = ?');
    $del->execute([$outfit_id, $creator['id']]);
    set_flash('Outfit deleted.', 'info');
    redirect(site_url('dashboard'));
}

// ── Handle SAVE ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action_delete'])) {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $title_val   = trim($_POST['title']    ?? '');
    $category    = trim($_POST['category'] ?? '');
    $is_published= isset($_POST['is_published']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured'])  ? 1 : 0;
    $products    = $_POST['products'] ?? [];

    if (!$title_val)                              $errors[] = 'Outfit title is required.';
    if (!in_array($category, $CATEGORIES, true)) $errors[] = 'Please select a valid category.';
    if (empty($products))                         $errors[] = 'Add at least one product.';

    foreach ($products as $i => $p) {
        if (!trim($p['name'] ?? ''))
            $errors[] = "Product " . ($i+1) . ": name required.";
        if (!filter_var(trim($p['affiliate_url'] ?? ''), FILTER_VALIDATE_URL))
            $errors[] = "Product " . ($i+1) . ": valid URL required.";
        if (!in_array(trim($p['platform'] ?? ''), $PLATFORMS, true))
            $errors[] = "Product " . ($i+1) . ": select a platform.";
    }

    if (empty($errors)) {
        $db = get_db();
        $db->beginTransaction();
        try {
            // Outfit image
            $outfit_image = $outfit['image'];
            if (!empty($_FILES['outfit_image']['tmp_name'])) {
                if ($outfit_image) delete_image($outfit_image);
                $outfit_image = save_image($_FILES['outfit_image'], 'outfits');
            }

            $stmt = $db->prepare('
                UPDATE outfits
                SET title=?, category=?, image=?, is_published=?, is_featured=?, updated_at=NOW()
                WHERE id=? AND creator_id=?
            ');
            $stmt->execute([
                $title_val, $category, $outfit_image,
                $is_published, $is_featured,
                $outfit_id, $creator['id'],
            ]);

            // Delete old products (re-insert clean)
            foreach ($existing_products as $p) {
                if ($p['image']) delete_image($p['image']);
            }
            $db->prepare('DELETE FROM products WHERE outfit_id = ?')->execute([$outfit_id]);

            // Re-insert products
            $stmt = $db->prepare('
                INSERT INTO products
                    (outfit_id, name, platform, affiliate_url, price, image, display_order, in_stock)
                VALUES (?,?,?,?,?,?,?,1)
            ');
            foreach ($products as $i => $p) {
                $prod_image = null;
                if (!empty($_FILES['products']['tmp_name'][$i]['image'])) {
                    $file = [
                        'tmp_name' => $_FILES['products']['tmp_name'][$i]['image'],
                        'error'    => $_FILES['products']['error'][$i]['image'],
                        'size'     => $_FILES['products']['size'][$i]['image'],
                        'type'     => $_FILES['products']['type'][$i]['image'],
                        'name'     => $_FILES['products']['name'][$i]['image'],
                    ];
                    $prod_image = save_image($file, 'products');
                } elseif (!empty($p['existing_image'])) {
                    $prod_image = $p['existing_image'];
                }
                $stmt->execute([
                    $outfit_id,
                    trim($p['name']),
                    trim($p['platform']),
                    trim($p['affiliate_url']),
                    trim($p['price'] ?? ''),
                    $prod_image,
                    (int)($p['display_order'] ?? $i),
                ]);
            }

            $db->commit();

            // Reload updated data
            $outfit = $db->query("SELECT * FROM outfits WHERE id=$outfit_id")->fetch();
            $stmt2  = $db->prepare('SELECT * FROM products WHERE outfit_id=? ORDER BY display_order');
            $stmt2->execute([$outfit_id]);
            $existing_products = $stmt2->fetchAll();

            set_flash('Outfit updated successfully!', 'success');
            redirect(site_url('dashboard/edit-outfit?id=' . $outfit_id));

        } catch (Exception $e) {
            $db->rollBack();
            error_log('[inaffi] Edit outfit error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$csrf  = generate_csrf_token();
$title = 'Edit: ' . $outfit['title'] . ' — ' . SITE_NAME;
require_once '../components/header.php';
require_once '../components/dashboard-sidebar.php';
?>

<div class="dashboard-layout">
    <div class="dashboard-main">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-xl);">
            <h1 class="dashboard-page-title" style="margin:0;border:none;padding:0;">
                Edit Outfit
            </h1>
            <div style="display:flex;gap:var(--space-sm);">
                <a
                    href="<?= site_url($creator['username'] . '?look=' . $outfit_id) ?>"
                    class="btn btn--outline btn--sm"
                    target="_blank"
                >View Live ↗</a>
                <a href="<?= site_url('dashboard') ?>" class="btn btn--ghost btn--sm">← Dashboard</a>
            </div>
        </div>

        <?php render_flash(); ?>

        <?php if ($errors): ?>
            <div class="flash flash--error">
                <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form
            id="outfit-form"
            method="POST"
            enctype="multipart/form-data"
            novalidate
            data-max-products="<?= MAX_PRODUCTS_PER_OUTFIT ?>"
            data-platforms='<?= json_encode($PLATFORMS) ?>'
        >
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

            <div class="outfit-form">

                <!-- Basic info -->
                <div class="outfit-form__section">
                    <h2 class="outfit-form__section-title">Outfit Details</h2>

                    <div class="form-group">
                        <label class="form-label" for="title">Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" class="form-input"
                            value="<?= e($outfit['title']) ?>" maxlength="255" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category">Category <span class="required">*</span></label>
                        <select id="category" name="category" class="form-select" required>
                            <?php foreach ($CATEGORIES as $cat): ?>
                                <option value="<?= e($cat) ?>" <?= $outfit['category']===$cat?'selected':'' ?>>
                                    <?= e($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Outfit image -->
                    <div class="form-group">
                        <label class="form-label">Outfit Photo</label>
                        <?php if (!empty($outfit['image'])): ?>
                            <div style="margin-bottom:var(--space-sm);">
                                <img src="<?= e(site_url($outfit['image'])) ?>"
                                    id="outfit-preview"
                                    style="max-height:200px;border-radius:4px;object-fit:contain;"
                                    alt="Current outfit image">
                            </div>
                        <?php endif; ?>
                        <div class="image-upload-wrap">
                            <input type="file" name="outfit_image" accept="image/*"
                                data-image-input="outfit-preview">
                            <p class="image-upload-hint">Upload new photo to replace current</p>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-md);">
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="checkbox" name="is_published" value="1"
                                    <?= $outfit['is_published'] ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">Published (public)</span>
                        </div>
                        <div class="featured-toggle-wrap">
                            <span class="featured-toggle-wrap__icon">⭐</span>
                            <div class="featured-toggle-wrap__text">
                                <label class="toggle">
                                    <input type="checkbox" name="is_featured" value="1"
                                        <?= $outfit['is_featured'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <strong>Feature on homepage</strong>
                                <small>Replaces current featured outfit</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="outfit-form__section">
                    <h2 class="outfit-form__section-title">
                        Products
                        <span style="color:var(--color-text-secondary);font-size:0.875rem;font-weight:400;">
                            (<?= count($existing_products) ?> / <?= MAX_PRODUCTS_PER_OUTFIT ?>)
                        </span>
                    </h2>

                    <div id="products-list" class="products-list">
                        <?php foreach ($existing_products as $i => $p): ?>
                        <div class="product-row" data-row-index="<?= $i ?>">
                            <div class="product-row__header">
                                <span class="product-row__num">Product <?= $i+1 ?></span>
                                <button type="button" class="product-row__remove">×</button>
                            </div>
                            <div class="product-row__grid">
                                <div class="form-group">
                                    <label class="form-label">Name <span class="required">*</span></label>
                                    <input type="text" name="products[<?=$i?>][name]" class="form-input"
                                        value="<?= e($p['name']) ?>" maxlength="255" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Platform <span class="required">*</span></label>
                                    <select name="products[<?=$i?>][platform]" class="form-select" required>
                                        <option value="">Select…</option>
                                        <?php foreach ($PLATFORMS as $plt): ?>
                                            <option value="<?= e($plt) ?>" <?= $p['platform']===$plt?'selected':'' ?>>
                                                <?= e($plt) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Affiliate URL <span class="required">*</span></label>
                                    <input type="url" name="products[<?=$i?>][affiliate_url]"
                                        class="form-input" value="<?= e($p['affiliate_url']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Price</label>
                                    <input type="text" name="products[<?=$i?>][price]"
                                        class="form-input" value="<?= e($p['price'] ?? '') ?>" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Product Image</label>
                                    <?php if (!empty($p['image'])): ?>
                                        <img src="<?= e(site_url($p['image'])) ?>"
                                            id="product-preview-<?=$i?>"
                                            style="max-height:80px;border-radius:4px;margin-bottom:6px;display:block;"
                                            alt="">
                                    <?php endif; ?>
                                    <div class="image-upload-wrap" style="padding:12px;">
                                        <input type="file" name="products[<?=$i?>][image]" accept="image/*"
                                            data-image-input="product-preview-<?=$i?>">
                                        <p class="image-upload-hint">Upload to replace</p>
                                    </div>
                                    <input type="hidden" name="products[<?=$i?>][existing_image]"
                                        value="<?= e($p['image'] ?? '') ?>">
                                </div>
                                <input type="hidden" name="products[<?=$i?>][display_order]"
                                    class="display-order-input" value="<?= $i ?>">
                            </div>
                            <div class="product-row__stock">
                                <span class="toggle-label text-muted text-sm">
                                    ⓘ Stock status managed from dashboard
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p id="max-products-note" style="display:none;font-size:0.875rem;color:var(--color-text-secondary);margin-top:var(--space-md);">
                        Maximum <?= MAX_PRODUCTS_PER_OUTFIT ?> products reached.
                    </p>

                    <button type="button" id="add-product-btn" class="add-product-btn" style="margin-top:var(--space-md);">
                        + Add Another Product
                    </button>
                </div>

                <!-- Actions -->
                <div style="display:flex;gap:var(--space-md);flex-wrap:wrap;align-items:center;">
                    <button type="submit" class="btn btn--primary btn--lg">Save Changes</button>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn--ghost btn--lg">Cancel</a>

                    <!-- Delete (separate form to avoid accidental submit) -->
                    <form method="POST" style="margin-left:auto;"
                        onsubmit="return confirm('Delete this outfit permanently? This cannot be undone.')">
                        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                        <input type="hidden" name="action_delete" value="1">
                        <button type="submit" class="btn btn--danger btn--sm">
                            Delete Outfit
                        </button>
                    </form>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="<?= site_url('assets/js/outfit-form.js') ?>"></script>
<?php require_once '../components/footer.php'; ?>
