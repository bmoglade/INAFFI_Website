<?php
// ============================================================
// inaffi.com — Dashboard Sidebar
// ============================================================
// Included by all dashboard pages via dashboard/layout wrapper
// Expects $creator (array) and $active_page (string) to be set
// ============================================================

if (!isset($creator))     $creator     = get_current_creator();
if (!isset($active_page)) $active_page = '';

$storefront_url = site_url($creator['username']);
?>

<!-- Mobile bar (visible only on small screens) -->
<div class="dashboard-mobile-bar" id="dashboard-mobile-bar">
    <span class="dashboard-mobile-bar__brand"><?= e(SITE_NAME) ?></span>
    <button
        class="hamburger"
        id="hamburger-btn"
        aria-label="Toggle navigation"
        aria-expanded="false"
        aria-controls="dashboard-sidebar"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>
</div>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>

<!-- Sidebar -->
<aside class="dashboard-sidebar" id="dashboard-sidebar" aria-label="Dashboard navigation">

    <!-- Brand -->
    <div class="dashboard-sidebar__brand">
        <a href="<?= site_url() ?>" class="dashboard-sidebar__brand-name">
            <?= e(SITE_NAME) ?>
        </a>
    </div>

    <!-- Storefront link -->
    <a
        href="<?= e($storefront_url) ?>"
        class="dashboard-sidebar__storefront"
        title="View your public storefront"
    >
        ↗ <?= e($creator['username']) ?>
    </a>

    <!-- Nav links -->
    <nav class="dashboard-sidebar__nav">

        <a
            href="<?= site_url('dashboard') ?>"
            class="<?= $active_page === 'overview' ? 'active' : '' ?>"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
            Overview
        </a>

        <a
            href="<?= site_url('dashboard/new-outfit') ?>"
            class="<?= $active_page === 'new-outfit' ? 'active' : '' ?>"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            New Outfit
        </a>

        <a
            href="<?= site_url('dashboard/settings') ?>"
            class="<?= $active_page === 'settings' ? 'active' : '' ?>"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Settings
        </a>

        <?php if (!empty($creator['is_admin'])): ?>
        <a
            href="<?= site_url('admin') ?>"
            class="<?= $active_page === 'admin' ? 'active' : '' ?>"
            style="margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px;"
        >
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                <path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
            </svg>
            Admin Panel
        </a>
        <?php endif; ?>

    </nav>

    <!-- Storefront URL badge -->
    <div class="dashboard-sidebar__footer">
        <p class="text-xs" style="color: rgba(255,255,255,0.3); margin-bottom: 4px;">Your storefront</p>
        <p class="text-xs" style="color: rgba(255,255,255,0.5); word-break: break-all;">
            <?= e(SITE_URL . '/' . $creator['username']) ?>
        </p>

        <!-- Logout -->
        <a
            href="<?= site_url('logout') ?>"
            class="btn btn--danger btn--sm btn--full"
            style="margin-top: 16px;"
            data-confirm="Are you sure you want to log out?"
        >
            Log Out
        </a>
    </div>

</aside>
