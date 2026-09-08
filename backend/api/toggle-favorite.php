<?php
/**
 * POST /api/toggle-favorite.php
 * Body: photo_id, csrf_token
 * Toggles the current client's favorite status on a gallery photo they own.
 */
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if (!is_logged_in() || !has_role('client')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if (!verify_csrf()) {
    http_response_code(419);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$user = current_user();
$photoId = (int) ($_POST['photo_id'] ?? 0);

// Ensure the photo belongs to a gallery owned by this client.
$stmt = db()->prepare('SELECT gp.id FROM gallery_photos gp
                        JOIN galleries g ON g.id = gp.gallery_id
                        WHERE gp.id = ? AND g.client_id = ?');
$stmt->execute([$photoId, $user['id']]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Photo not found']);
    exit;
}

$existing = db()->prepare('SELECT id FROM gallery_favorites WHERE gallery_photo_id = ? AND client_id = ?');
$existing->execute([$photoId, $user['id']]);

if ($existing->fetch()) {
    db()->prepare('DELETE FROM gallery_favorites WHERE gallery_photo_id = ? AND client_id = ?')->execute([$photoId, $user['id']]);
    echo json_encode(['success' => true, 'favorited' => false]);
} else {
    db()->prepare('INSERT INTO gallery_favorites (gallery_photo_id, client_id) VALUES (?, ?)')->execute([$photoId, $user['id']]);
    echo json_encode(['success' => true, 'favorited' => true]);
}
