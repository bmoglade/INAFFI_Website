<?php
// ============================================================
// inaffi.com — Site Configuration
// ============================================================
// SETUP INSTRUCTIONS:
//   1. Copy this file: cp config.example.php config.php
//   2. Fill in all values below in config.php
//   3. config.php is in .gitignore — NEVER commit it
// ============================================================

// ── Site Identity ────────────────────────────────────────────
define('SITE_NAME',    'inaffi');
define('SITE_TAGLINE', 'Shop Creator Looks');
define('SITE_URL',     'https://inaffi.com');          // no trailing slash

// ── Database (Hostinger MySQL) ───────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', '');          // e.g. u789815590_inaffi
define('DB_USER', '');          // e.g. u789815590_admin
define('DB_PASS', '');          // your DB password

// ── Google Analytics (optional) ─────────────────────────────
define('GA_MEASUREMENT_ID', '');   // e.g. G-XXXXXXXXXX  (leave empty to disable)

// ── Image Limits ─────────────────────────────────────────────
define('MAX_UPLOAD_MB',       10);   // max file size accepted before compression
define('OUTFIT_IMG_MAX_PX',  800);   // outfit cover max width/height in pixels
define('PRODUCT_IMG_MAX_PX', 400);   // product image max width/height in pixels
define('PROFILE_IMG_MAX_PX', 300);   // profile photo max width/height in pixels
define('IMAGE_QUALITY',       85);   // JPEG compression quality (0-100)

// ── Business Rules ───────────────────────────────────────────
define('MAX_PRODUCTS_PER_OUTFIT', 15);
define('MAX_BIO_LENGTH',         120);

// ── Categories ───────────────────────────────────────────────
// Add or remove categories here — dropdowns and filters update automatically
$CATEGORIES = [
    'Office',
    'Casual',
    'Festive',
    'Beauty',
    'Home',
    'Other',
];

// ── Platforms ────────────────────────────────────────────────
// Add a new platform here AND in $PLATFORM_COLORS AND $PLATFORM_LOGOS below
$PLATFORMS = [
    'Amazon',
    'Flipkart',
    'Myntra',
    'Nykaa',
    'Ajio',
    'Meesho',
    'Tata Cliq',
    'Bewakoof',
    'H&M',
    'Zara',
    'Other',
];

// ── Platform Badge Colors ────────────────────────────────────
// Format: 'Platform' => ['background_hex', 'text_hex']
$PLATFORM_COLORS = [
    'Amazon'    => ['#FF9900', '#000000'],
    'Flipkart'  => ['#2874F0', '#FFFFFF'],
    'Myntra'    => ['#FF3F6C', '#FFFFFF'],
    'Nykaa'     => ['#FC2779', '#FFFFFF'],
    'Ajio'      => ['#1A1A1A', '#FFFFFF'],
    'Meesho'    => ['#9B2EFA', '#FFFFFF'],
    'Tata Cliq' => ['#E42574', '#FFFFFF'],
    'Bewakoof'  => ['#FDD835', '#000000'],
    'H&M'       => ['#E50010', '#FFFFFF'],
    'Zara'      => ['#000000', '#FFFFFF'],
    'Other'     => ['#666666', '#FFFFFF'],
];

// ── Platform Logo Filenames ──────────────────────────────────
// Files live in: assets/images/platforms/<filename>
// Size: 64×64px PNG recommended
// If a logo file is missing, a colored square with first letter is shown instead
$PLATFORM_LOGOS = [
    'Amazon'    => 'amazon.png',
    'Flipkart'  => 'flipkart.png',
    'Myntra'    => 'myntra.png',
    'Nykaa'     => 'nykaa.png',
    'Ajio'      => 'ajio.png',
    'Meesho'    => 'meesho.png',
    'Tata Cliq' => 'tatacliq.png',
    'Bewakoof'  => 'bewakoof.png',
    'H&M'       => 'hm.png',
    'Zara'      => 'zara.png',
];
