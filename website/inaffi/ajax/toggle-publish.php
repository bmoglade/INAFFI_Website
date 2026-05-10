<?php
// ============================================================
// inaffi.com — AJAX: Toggle Outfit Publish Status
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
$published = (int)($_POST['published'] ?? 0);
$creator   = get_current_creator();

if (!$outfit_id) {
    echo json_encode(['ok' => false, 'message' => 'Invalid outfit.']);
    exit();
}

$stmt = get_db()->prepare('
    UPDATE outfits SET is_published = ?
    WHERE id = ? AND creator_id = ?
');
$stmt->execute([$published ? 1 : 0, $outfit_id, $creator['id']]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['ok' => false, 'message' => 'Outfit not found.']);
    exit();
}

echo json_encode(['ok' => true]);
