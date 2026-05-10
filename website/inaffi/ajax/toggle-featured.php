<?php
// ============================================================
// inaffi.com — AJAX: Toggle Outfit Featured Status
// ============================================================
// MySQL trigger handles unfeaturing previous outfit automatically
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

$outfit_id = (int)($_POST['outfit_id'] ?? 0);
$featured  = (int)($_POST['featured']  ?? 0);
$creator   = get_current_creator();

if (!$outfit_id) {
    echo json_encode(['ok' => false, 'message' => 'Invalid outfit.']);
    exit();
}

// Must also be published to be featured
if ($featured) {
    $check = get_db()->prepare('SELECT is_published FROM outfits WHERE id=? AND creator_id=? LIMIT 1');
    $check->execute([$outfit_id, $creator['id']]);
    $row = $check->fetch();
    if (!$row) {
        echo json_encode(['ok' => false, 'message' => 'Outfit not found.']);
        exit();
    }
    if (!$row['is_published']) {
        echo json_encode(['ok' => false, 'message' => 'Publish the outfit first before featuring it.']);
        exit();
    }
}

$stmt = get_db()->prepare('
    UPDATE outfits SET is_featured = ?
    WHERE id = ? AND creator_id = ?
');
$stmt->execute([$featured ? 1 : 0, $outfit_id, $creator['id']]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['ok' => false, 'message' => 'Outfit not found.']);
    exit();
}

echo json_encode(['ok' => true]);
