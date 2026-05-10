<?php
// ============================================================
// inaffi.com — Category Filter Pills
// ============================================================
// Variables expected:
//   $categories  (array)  — list of category strings
//   $active_cat  (string) — currently selected category (default 'All')
//   $outfit_count (int)   — total number of outfits shown
//
// Usage: require 'components/category-filter.php';
//
// JS in main.js handles the actual filtering (data-filter-pill,
// data-category attributes). No page reload needed.
// ============================================================

if (!isset($active_cat))   $active_cat   = 'All';
if (!isset($outfit_count)) $outfit_count = 0;

// Ensure $CATEGORIES is available
global $CATEGORIES;
if (!isset($categories)) {
    $categories = $CATEGORIES ?? [];
}
?>

<div class="category-filter">
    <div class="container">
        <div
            class="category-filter__list"
            data-filter-list
            role="list"
            aria-label="Filter outfits by category"
        >
            <!-- "All" pill -->
            <button
                type="button"
                class="category-filter__pill <?= $active_cat === 'All' ? 'active' : '' ?>"
                data-filter-pill="All"
                role="listitem"
                aria-pressed="<?= $active_cat === 'All' ? 'true' : 'false' ?>"
            >
                All
                <span id="outfit-count" style="
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 20px;
                    height: 20px;
                    background: rgba(255,255,255,0.25);
                    border-radius: 999px;
                    font-size: 0.6875rem;
                    font-weight: 700;
                    padding: 0 5px;
                    margin-left: 4px;
                "><?= (int) $outfit_count ?></span>
            </button>

            <!-- Category pills -->
            <?php foreach ($categories as $cat): ?>
                <button
                    type="button"
                    class="category-filter__pill <?= $active_cat === $cat ? 'active' : '' ?>"
                    data-filter-pill="<?= e($cat) ?>"
                    role="listitem"
                    aria-pressed="<?= $active_cat === $cat ? 'true' : 'false' ?>"
                >
                    <?= e($cat) ?>
                </button>
            <?php endforeach; ?>

        </div>
    </div>
</div>
