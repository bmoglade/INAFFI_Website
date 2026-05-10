<?php
// ============================================================
// inaffi.com — AJAX: Toggle Product Stock Status
// ============================================================
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
    exit();
}

verify_csrf_token($_POST['csrf_token'] ?? '');

$product_id = (int)($_POST['product_id'] ?? 0);
$in_stock   = (int)($_POST['in_stock']   ?? 0);
$creator    = get_current_creator();

if (!$product_id) {
    echo json_encode(['ok' => false, 'message' => 'Invalid product.']);
    exit();
}

// Verify product belongs to this creator's outfit
$stmt = get_db()->prepare('
    SELECT p.id FROM products p
    JOIN outfits o ON o.id = p.outfit_id
    WHERE p.id = ? AND o.creator_id = ?
    LIMIT 1
');
$stmt->execute([$product_id, $creator['id']]);
if (!$stmt->fetch()) {
    echo json_encode(['ok' => false, 'message' => 'Product not found.']);
    exit();
}

$upd = get_db()->prepare('UPDATE products SET in_stock = ? WHERE id = ?');
$upd->execute([$in_stock ? 1 : 0, $product_id]);

echo json_encode(['ok' => true]);
