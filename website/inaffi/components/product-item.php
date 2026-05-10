<?php
// ============================================================
// inaffi.com — Product Item Component
// ============================================================
// Renders a single product row inside an outfit card.
// Used on: storefront.php, index.php (homepage featured card)
//
// Variables expected:
//   $product  (array) — product row from DB
//              keys: id, name, platform, affiliate_url, image, in_stock
//
// Usage:
//   foreach ($products as $product) {
//       require 'components/product-item.php';
//   }
// ============================================================

// Only render in-stock products on public pages
// (Dashboard edit page uses its own product rendering)
if (empty($product['in_stock'])) return;

$go_url       = site_url('go?p=' . (int) $product['id']);
$product_name = truncate($product['name'], 60);
?>

<div class="product-item">

    <!-- Product image OR platform logo -->
    <div class="product-item__media" style="flex-shrink:0;">
        <?php if (!empty($product['image'])): ?>
            <img
                src="<?= e(site_url($product['image'])) ?>"
                alt="<?= e($product['name']) ?>"
                class="product-item__img"
                loading="lazy"
                onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex'"
            >
            <!-- Fallback logo if image fails to load -->
            <span style="display:none;">
                <?php render_platform_logo($product['platform'], 56); ?>
            </span>
        <?php else: ?>
            <?php render_platform_logo($product['platform'], 56); ?>
        <?php endif; ?>
    </div>

    <!-- Product info -->
    <div class="product-item__info">
        <p class="product-item__name"><?= e($product_name) ?></p>
        <?php render_platform_badge($product['platform']); ?>
    </div>

    <!-- Shop button — opens affiliate link in new tab -->
    <div class="product-item__action">
        <a
            href="<?= e($go_url) ?>"
            class="btn btn--shop"
            target="_blank"
            rel="noopener noreferrer nofollow"
            aria-label="Shop <?= e($product['name']) ?> on <?= e($product['platform']) ?>"
        >
            Shop ↗
        </a>
    </div>

</div>
