<?php
// ============================================================
// inaffi.com — Outfit Card Component
// ============================================================
// WearThis-style layout: outfit image LEFT, product list RIGHT
// Used on: storefront.php, index.php (homepage featured card)
//
// Variables expected:
//   $outfit   (array) — outfit row from DB
//              keys: id, title, category, image, creator_id, is_published
//   $products (array) — array of product rows for this outfit
//   $username (string) — creator's username (for share link)
//   $show_share (bool) — show copy-link row (default true)
//
// Usage:
//   foreach ($outfits as $outfit) {
//       $products = ...; // products for this outfit
//       require 'components/outfit-card.php';
//   }
// ============================================================

// Ensure functions are available
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/platform-badge.php';

if (!isset($show_share)) $show_share = true;
if (!isset($username))   $username   = '';

// Filter to only in-stock products for public display
$visible_products = array_filter($products, fn($p) => !empty($p['in_stock']));
if (empty($visible_products)) return;  // don't render outfit with no in-stock products

// Share URL for this outfit
$share_url = SITE_URL . '/' . rawurlencode($username) . '?look=' . (int) $outfit['id'];

// Image
$has_image  = !empty($outfit['image']);
$image_url  = $has_image ? site_url($outfit['image']) : '';
?>

<article
    class="outfit-card"
    data-category="<?= e($outfit['category']) ?>"
    id="outfit-<?= (int) $outfit['id'] ?>"
>

    <!-- LEFT: Outfit image -->
    <div class="outfit-card__image-wrap">
        <?php if ($has_image): ?>
            <img
                src="<?= e($image_url) ?>"
                alt="<?= e($outfit['title']) ?>"
                class="outfit-card__image"
                loading="lazy"
            >
        <?php else: ?>
            <div class="outfit-card__image-placeholder" aria-hidden="true">
                🧥
            </div>
        <?php endif; ?>

        <!-- Category badge overlay -->
        <span class="outfit-card__category">
            <?= e($outfit['category']) ?>
        </span>
    </div>

    <!-- RIGHT: Title + share link + products -->
    <div class="outfit-card__body">

        <h2 class="outfit-card__title"><?= e($outfit['title']) ?></h2>

        <!-- Share / copy link -->
        <?php if ($show_share && $username): ?>
        <div class="outfit-card__share">
            <div class="copy-wrap" style="flex:1;">
                <input
                    type="text"
                    class="form-input"
                    id="share-<?= (int) $outfit['id'] ?>"
                    value="<?= e($share_url) ?>"
                    readonly
                    aria-label="Share link for <?= e($outfit['title']) ?>"
                >
                <button
                    type="button"
                    class="btn--copy"
                    data-copy-btn
                    data-copy-target="#share-<?= (int) $outfit['id'] ?>"
                    aria-label="Copy link"
                >
                    Copy
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- Product list -->
        <div class="outfit-card__products">
            <?php foreach ($visible_products as $product): ?>
                <?php require __DIR__ . '/product-item.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Footer: product count -->
        <div class="outfit-card__footer">
            <?= count($visible_products) ?> item<?= count($visible_products) !== 1 ? 's' : '' ?> in this look
        </div>

    </div>

</article>
