<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$stmt = db()->prepare('SELECT g.*, COUNT(gp.id) AS photo_count FROM galleries g
                        LEFT JOIN gallery_photos gp ON gp.gallery_id = g.id
                        WHERE g.client_id = ? AND g.is_published = 1
                        GROUP BY g.id ORDER BY g.created_at DESC');
$stmt->execute([$user['id']]);
$galleries = $stmt->fetchAll();

$dashTitle = 'My Galleries';
$activeNav = 'galleries';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="panel">
  <div class="panel-head"><h3>Private Galleries</h3></div>
  <?php if (!$galleries): ?>
    <div class="empty-state">
      <div class="icon">🖼️</div>
      <p>No galleries have been published for you yet. Your photographer will notify you once your gallery is ready.</p>
    </div>
  <?php else: ?>
    <div class="gallery-grid">
      <?php foreach ($galleries as $g): ?>
        <a href="<?= base_url('client/gallery-view.php?id=' . $g['id']) ?>" class="gallery-photo" style="aspect-ratio:4/3;">
          <img src="<?= e($g['cover_image']) ?>" alt="<?= e($g['title']) ?>">
          <div style="position:absolute;inset:0;background:linear-gradient(0deg,rgba(0,0,0,0.75),transparent 60%);display:flex;align-items:flex-end;padding:16px;">
            <div style="color:#fff;">
              <div style="font-weight:600;font-size:15px;"><?= e($g['title']) ?></div>
              <div style="font-size:12.5px;opacity:0.85;"><?= (int) $g['photo_count'] ?> photos</div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
