<?php
// ============================================================
// inaffi.com — Affiliate Click Tracker & Redirect
// ============================================================
// URL: /go?p=product_id
// Logs the click, then 302-redirects to the affiliate URL.
// Fast — no HTML output, no session needed.
// ============================================================

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$product_id = (int) get_param('p');

if (!$product_id) {
    header('Location: ' . SITE_URL, true, 302);
    exit();
}

// Fetch product — must be in stock
$stmt = get_db()->prepare('
    SELECT p.affiliate_url, p.outfit_id, o.creator_id
    FROM   products p
    JOIN   outfits  o ON o.id = p.outfit_id
    WHERE  p.id       = ?
      AND  p.in_stock  = 1
      AND  o.is_published = 1
    LIMIT  1
');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product || empty($product['affiliate_url'])) {
    // Product not found or out of stock — go home
    header('Location: ' . SITE_URL, true, 302);
    exit();
}

// Log the click (non-blocking — ignore errors)
try {
    $ins = get_db()->prepare('
        INSERT INTO clicks (product_id, outfit_id, creator_id, user_agent, referrer)
        VALUES (?, ?, ?, ?, ?)
    ');
    $ins->execute([
        $product_id,
        $product['outfit_id'],
        $product['creator_id'],
        substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        substr($_SERVER['HTTP_REFERER']    ?? '', 0, 500),
    ]);
} catch (Exception $e) {
    error_log('[inaffi] Click log error: ' . $e->getMessage());
    // Don't block redirect even if logging fails
}

// Redirect to affiliate URL
header('Location: ' . $product['affiliate_url'], true, 302);
exit();
