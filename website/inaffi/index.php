<?php
// ============================================================
// inaffi.com — Homepage (Layout Controller)
// ============================================================
// This file ONLY:
//   1. Loads dependencies
//   2. Fetches shared data (featured outfit, login state)
//   3. Includes sections in order
//
// To add a section:    add one require line below
// To remove a section: comment out its require line
// To reorder sections: move its require line up or down
// To edit a section:   open components/sections/<name>.php
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
require_once 'components/platform-badge.php';

// ── Shared data ──────────────────────────────────────────────

// Logged-in creator (or null)
$creator = is_logged_in() ? get_current_creator() : null;

// ── Page meta ────────────────────────────────────────────────
$title = SITE_NAME . ' — ' . SITE_TAGLINE;

// ── Render ───────────────────────────────────────────────────
require_once 'components/header.php';

// ── Sections ─────────────────────────────────────────────────
// To reorder: move lines. To disable: comment out. To add: require new file.

require_once 'components/sections/hero.php';           // Featured outfit card + headline

require_once 'components/sections/brand-strip.php';    // Scrolling platform logos

require_once 'components/sections/how-it-works.php';   // 4-step process

require_once 'components/sections/creator-showcase.php'; // Real creator cards (auto from DB)

require_once 'components/sections/cta-strip.php';      // Bottom dark CTA (hidden if logged in)

// ── Footer ───────────────────────────────────────────────────
require_once 'components/footer.php';

