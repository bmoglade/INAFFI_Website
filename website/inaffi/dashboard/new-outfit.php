<?php
// ============================================================
// inaffi.com — Create New Outfit
// ============================================================

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';
require_once '../includes/upload.php';

require_login();
$creator     = get_current_creator();
$active_page = 'new-outfit';

global $CATEGORIES, $PLATFORMS;
$errors = [];

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');

    $title_val   = trim($_POST['title']       ?? '');
    $category    = trim($_POST['category']    ?? '');
    $is_published= isset($_POST['is_published']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured'])  ? 1 : 0;
    $products    = $_POST['products'] ?? [];

    // Validate
    if (!$title_val)                              $errors[] = 'Outfit title is required.';
    if (!in_array($category, $CATEGORIES, true)) $errors[] = 'Please select a valid category.';
    if (empty($products))                         $errors[] = 'Add at least one product.';
    if (count($products) > MAX_PRODUCTS_PER_OUTFIT)
        $errors[] = 'Maximum ' . MAX_PRODUCTS_PER_OUTFIT . ' products allowed.';

    // Validate each product
    foreach ($products as $i => $p) {
        $pname = trim($p['name'] ?? '');
        $purl  = trim($p['affiliate_url'] ?? '');
        $pplat = trim($p['platform'] ?? '');
        if (!$pname) $errors[] = "Product " . ($i+1) . ": name is required.";
        if (!$purl || !filter_var($purl, FILTER_VALIDATE_URL))
            $errors[] = "Product " . ($i+1) . ": valid affiliate URL is required.";
        if (!in_array($pplat, $PLATFORMS, true))
            $errors[] = "Product " . ($i+1) . ": select a valid platform.";
    }

    if (empty($errors)) {
        $db = get_db();
        $db->beginTransaction();
        try {
            // Upload outfit image
            $outfit_image = null;
            if (!empty($_FILES['outfit_image']['tmp_name'])) {
                $outfit_image = save_image($_FILES['outfit_image'], 'outfits');
            }

            // Insert outfit
            $stmt = $db->prepare('
                INSERT INTO outfits
                    (creator_id, title, category, image, is_published, is_featured)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $creator['id'], $title_val, $category,
                $outfit_image, $is_published, $is_featured,
            ]);
            $outfit_id = (int) $db->lastInsertId();

            // Insert products
            $stmt = $db->prepare('
                INSERT INTO products
                    (outfit_id, name, platform, affiliate_url, price, image, display_order, in_stock)
                VALUES (?, ?, ?, ?, ?, ?, ?, 1)
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
            set_flash('Outfit created successfully!', 'success');
            redirect(site_url('dashboard'));

        } catch (Exception $e) {
            $db->rollBack();
            error_log('[inaffi] Create outfit error: ' . $e->getMessage());
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$csrf  = generate_csrf_token();
$title = 'New Outfit — ' . SITE_NAME;
require_once '../components/header.php';
require_once '../components/dashboard-sidebar.php';
?>

<div class="dashboard-layout">
    <div class="dashboard-main">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-xl);">
            <h1 class="dashboard-page-title" style="margin:0;border:none;padding:0;">New Outfit</h1>
            <a href="<?= site_url('dashboard') ?>" class="btn btn--ghost btn--sm">← Back</a>
        </div>

        <?php if ($errors): ?>
            <div class="flash flash--error">
                <?php foreach ($errors as $err): ?>
                    <p><?= e($err) ?></p>
                <?php endforeach; ?>
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

                <!-- Section 1: Basic info -->
                <div class="outfit-form__section">
                    <h2 class="outfit-form__section-title">Outfit Details</h2>

                    <div class="form-group">
                        <label class="form-label" for="title">
                            Outfit Title <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-input"
                            placeholder="e.g. Summer Office Look"
                            maxlength="255"
                            required
                            value="<?= e($_POST['title'] ?? '') ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category">
                            Category <span class="required">*</span>
                        </label>
                        <select id="category" name="category" class="form-select" required>
                            <option value="">Select a category…</option>
                            <?php foreach ($CATEGORIES as $cat): ?>
                                <option value="<?= e($cat) ?>"
                                    <?= (($_POST['category'] ?? '') === $cat) ? 'selected' : '' ?>>
                                    <?= e($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Outfit image -->
                    <div class="form-group">
                        <label class="form-label">Outfit Photo</label>
                        <div class="image-upload-wrap">
                            <input
                                type="file"
                                name="outfit_image"
                                accept="image/*"
                                data-image-input="outfit-preview"
                            >
                            <img
                                id="outfit-preview"
                                class="image-upload-preview"
                                src="" alt="Preview"
                                style="display:none;"
                            >
                            <p class="image-upload-hint">
                                JPG, PNG or WebP · Auto-compressed · Recommended: portrait ratio
                            </p>
                        </div>
                    </div>

                    <!-- Publish + Featured -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-md);">
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="checkbox" name="is_published" value="1"
                                    <?= !empty($_POST['is_published']) ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">Publish (make public)</span>
                        </div>

                        <div class="featured-toggle-wrap">
                            <span class="featured-toggle-wrap__icon">⭐</span>
                            <div class="featured-toggle-wrap__text">
                                <label class="toggle">
                                    <input type="checkbox" name="is_featured" value="1"
                                        <?= !empty($_POST['is_featured']) ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <strong>Feature on homepage</strong>
                                <small>Replaces the current featured outfit</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Products -->
                <div class="outfit-form__section">
                    <h2 class="outfit-form__section-title">
                        Products
                        <span style="color:var(--color-text-secondary);font-size:0.875rem;font-weight:400;">
                            (max <?= MAX_PRODUCTS_PER_OUTFIT ?>)
                        </span>
                    </h2>

                    <div id="products-list" class="products-list">
                        <!-- First product row (always present) -->
                        <?php for ($i = 0; $i < 1; $i++): ?>
                        <div class="product-row" data-row-index="<?= $i ?>">
                            <div class="product-row__header">
                                <span class="product-row__num">Product <?= $i+1 ?></span>
                                <button type="button" class="product-row__remove" aria-label="Remove product <?= $i+1 ?>">×</button>
                            </div>
                            <div class="product-row__grid">
                                <div class="form-group">
                                    <label class="form-label">Name <span class="required">*</span></label>
                                    <input type="text" name="products[<?=$i?>][name]" class="form-input" placeholder="e.g. Floral Kurti" maxlength="255" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Platform <span class="required">*</span></label>
                                    <select name="products[<?=$i?>][platform]" class="form-select" required>
                                        <option value="">Select…</option>
                                        <?php foreach ($PLATFORMS as $p): ?>
                                            <option value="<?= e($p) ?>"><?= e($p) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group full-width">
                                    <label class="form-label">Affiliate URL <span class="required">*</span></label>
                                    <input type="url" name="products[<?=$i?>][affiliate_url]" class="form-input" placeholder="https://www.amazon.in/..." required>
                                    <p class="form-hint">Your affiliate link from Amazon Associates, Flipkart Affiliate, etc.</p>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Price (optional)</label>
                                    <input type="text" name="products[<?=$i?>][price]" class="form-input" placeholder="₹1,499" maxlength="50">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Product Image (optional)</label>
                                    <div class="image-upload-wrap" style="padding:12px;">
                                        <input type="file" name="products[<?=$i?>][image]" accept="image/*" data-image-input="product-preview-<?=$i?>">
                                        <img id="product-preview-<?=$i?>" class="image-upload-preview" src="" alt="Preview" style="display:none;max-height:120px;">
                                        <p class="image-upload-hint">Auto-compressed</p>
                                    </div>
                                </div>
                                <input type="hidden" name="products[<?=$i?>][display_order]" class="display-order-input" value="<?=$i?>">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>

                    <p id="max-products-note" style="display:none;color:var(--color-text-secondary);font-size:0.875rem;margin-top:var(--space-md);">
                        Maximum of <?= MAX_PRODUCTS_PER_OUTFIT ?> products reached.
                    </p>

                    <button type="button" id="add-product-btn" class="add-product-btn" style="margin-top:var(--space-md);">
                        + Add Another Product
                    </button>
                </div>

                <!-- Submit -->
                <div style="display:flex;gap:var(--space-md);">
                    <button type="submit" class="btn btn--primary btn--lg">
                        Save Outfit
                    </button>
                    <a href="<?= site_url('dashboard') ?>" class="btn btn--ghost btn--lg">
                        Cancel
                    </a>
                </div>

            </div>
        </form>

    </div>
</div>

<script src="<?= site_url('assets/js/outfit-form.js') ?>"></script>
<?php require_once '../components/footer.php'; ?>
