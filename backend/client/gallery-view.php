<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$galleryId = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM galleries WHERE id = ? AND client_id = ? AND is_published = 1');
$stmt->execute([$galleryId, $user['id']]);
$gallery = $stmt->fetch();

if (!$gallery) {
    http_response_code(404);
    die('<h1>Gallery not found</h1><a href="' . base_url('client/gallery.php') . '">Back to galleries</a>');
}

$photos = db()->prepare('SELECT gp.*,
        EXISTS(SELECT 1 FROM gallery_favorites f WHERE f.gallery_photo_id = gp.id AND f.client_id = ?) AS is_favorited
        FROM gallery_photos gp WHERE gp.gallery_id = ? ORDER BY gp.sort_order');
$stmt2 = $photos;
$stmt2->execute([$user['id'], $galleryId]);
$photos = $stmt2->fetchAll();

$shareUrl = base_url('gallery-share.php?token=' . $gallery['share_token']);

$dashTitle = $gallery['title'];
$activeNav = 'galleries';
$extraScripts = '<script>window.APP_BASE_URL = "' . base_url('') . '"; window.CSRF_TOKEN = "' . csrf_token() . '";</script>';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="panel">
  <div class="panel-head">
    <div>
      <h3><?= e($gallery['title']) ?></h3>
      <p style="color:#767a85;font-size:13.5px;margin-top:4px;"><?= count($photos) ?> photos</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <button id="copyShareLink" class="d-btn d-btn-outline d-btn-sm" data-link="<?= e($shareUrl) ?>">🔗 Copy Share Link</button>
      <?php if ($gallery['allow_download']): ?>
        <button id="downloadAllBtn" class="d-btn d-btn-gold d-btn-sm">⬇ Download All</button>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($gallery['photographer_notes']): ?>
    <div class="d-alert d-alert-info"><strong>Note from your photographer:</strong><br><?= nl2br(e($gallery['photographer_notes'])) ?></div>
  <?php endif; ?>

  <div class="gallery-grid">
    <?php foreach ($photos as $photo): ?>
      <div class="gallery-photo" data-photo-id="<?= $photo['id'] ?>">
        <img src="<?= e($photo['file_path']) ?>" alt="<?= e($photo['caption']) ?>">
        <div class="gallery-photo-actions">
          <button class="icon-btn favorite-btn <?= $photo['is_favorited'] ? 'favorited' : '' ?>" data-photo-id="<?= $photo['id'] ?>"><?= $photo['is_favorited'] ? '♥' : '♡' ?></button>
          <?php if ($gallery['allow_download']): ?>
            <a href="<?= e($photo['file_path']) ?>" download class="icon-btn" title="Download">⬇</a>
          <?php endif; ?>
        </div>
        <?php if ($photo['caption']): ?>
          <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(0deg,rgba(0,0,0,0.7),transparent);color:#fff;padding:10px;font-size:12.5px;"><?= e($photo['caption']) ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
