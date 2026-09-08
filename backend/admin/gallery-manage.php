<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$galleryId = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT g.*, u.full_name AS client_name FROM galleries g JOIN users u ON u.id = g.client_id WHERE g.id = ?');
$stmt->execute([$galleryId]);
$gallery = $stmt->fetch();

if (!$gallery) {
    http_response_code(404);
    die('<h1>Gallery not found</h1><a href="' . base_url('admin/galleries.php') . '">Back to galleries</a>');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload_photos' && !empty($_FILES['photos']['name'][0])) {
        $count = count($_FILES['photos']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $file = [
                'name' => $_FILES['photos']['name'][$i], 'type' => $_FILES['photos']['type'][$i],
                'tmp_name' => $_FILES['photos']['tmp_name'][$i], 'error' => $_FILES['photos']['error'][$i], 'size' => $_FILES['photos']['size'][$i],
            ];
            try {
                $path = handle_image_upload($file, 'galleries/' . $galleryId);
                if ($path) {
                    db()->prepare('INSERT INTO gallery_photos (gallery_id, file_path) VALUES (?, ?)')->execute([$galleryId, $path]);
                }
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }
    } elseif ($action === 'update_settings') {
        $notes = clean($_POST['photographer_notes'] ?? '');
        $title = clean($_POST['title'] ?? $gallery['title']);
        $allowDownload = isset($_POST['allow_download']) ? 1 : 0;
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        db()->prepare('UPDATE galleries SET title=?, photographer_notes=?, allow_download=?, is_published=? WHERE id=?')
            ->execute([$title, $notes, $allowDownload, $isPublished, $galleryId]);
        $gallery = array_merge($gallery, compact('title', 'notes', 'allowDownload', 'isPublished'));
        $gallery['photographer_notes'] = $notes;
        $gallery['is_published'] = $isPublished;
        $gallery['allow_download'] = $allowDownload;
        $gallery['title'] = $title;
    } elseif ($action === 'delete_photo') {
        db()->prepare('DELETE FROM gallery_photos WHERE id = ? AND gallery_id = ?')->execute([(int) $_POST['photo_id'], $galleryId]);
    }
}

$photos = db()->prepare('SELECT * FROM gallery_photos WHERE gallery_id = ? ORDER BY sort_order, id');
$photos->execute([$galleryId]);
$photos = $photos->fetchAll();

$shareUrl = base_url('gallery-share.php?token=' . $gallery['share_token']);

$dashTitle = $gallery['title'];
$dashSubtitle = 'Gallery for ' . $gallery['client_name'];
$activeNav = 'galleries';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head">
    <h3>Gallery Settings</h3>
    <a href="<?= base_url('admin/galleries.php') ?>" class="d-btn d-btn-outline d-btn-sm">← Back to Galleries</a>
  </div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update_settings">
    <div class="d-form-group">
      <label>Title</label>
      <input class="d-form-control" type="text" name="title" value="<?= e($gallery['title']) ?>" required>
    </div>
    <div class="d-form-group">
      <label>Photographer Notes</label>
      <textarea class="d-form-control" name="photographer_notes" rows="2"><?= e($gallery['photographer_notes']) ?></textarea>
    </div>
    <div class="d-form-group" style="display:flex;gap:20px;">
      <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="allow_download" <?= $gallery['allow_download'] ? 'checked' : '' ?>> Allow Downloads</label>
      <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="is_published" <?= $gallery['is_published'] ? 'checked' : '' ?>> Published</label>
    </div>
    <button type="submit" class="d-btn d-btn-primary">Save Settings</button>
  </form>
  <div class="d-alert d-alert-info" style="margin-top:18px;">Share link: <code><?= e($shareUrl) ?></code></div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Upload Photos</h3></div>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="upload_photos">
    <div class="d-form-group">
      <input class="d-form-control" type="file" name="photos[]" accept="image/*" multiple required>
    </div>
    <button type="submit" class="d-btn d-btn-gold">Upload</button>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h3>Photos (<?= count($photos) ?>)</h3></div>
  <?php if (!$photos): ?>
    <div class="empty-state"><div class="icon">🖼️</div><p>No photos uploaded yet.</p></div>
  <?php else: ?>
    <div class="gallery-grid">
      <?php foreach ($photos as $p): ?>
        <div class="gallery-photo">
          <img src="<?= e($p['file_path']) ?>" alt="">
          <div class="gallery-photo-actions">
            <form method="post" onsubmit="return confirm('Remove this photo?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete_photo">
              <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
              <button type="submit" class="icon-btn" title="Delete">✕</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
