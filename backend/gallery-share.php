<?php
require_once __DIR__ . '/includes/functions.php';

$token = clean($_GET['token'] ?? '');
$gallery = null;
$photos = [];

if ($token) {
    $stmt = db()->prepare('SELECT g.*, u.full_name AS client_name FROM galleries g
                            JOIN users u ON u.id = g.client_id
                            WHERE g.share_token = ? AND g.is_published = 1');
    $stmt->execute([$token]);
    $gallery = $stmt->fetch();

    if ($gallery) {
        $stmt2 = db()->prepare('SELECT * FROM gallery_photos WHERE gallery_id = ? ORDER BY sort_order');
        $stmt2->execute([$gallery['id']]);
        $photos = $stmt2->fetchAll();
    }
}

$pageTitle = $gallery ? e($gallery['title']) . ' — Shared Gallery' : 'Gallery Not Found';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Shared Gallery</span>
    <h1><?= $gallery ? e($gallery['title']) : 'Gallery Not Found' ?></h1>
    <?php if ($gallery): ?><div class="breadcrumb">Shared by <?= e($gallery['client_name']) ?> via PHStudio</div><?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$gallery): ?>
      <div class="text-center">
        <p style="color:var(--grey);">This gallery link is invalid, unpublished, or has expired.</p>
        <a href="<?= base_url('index.php') ?>" class="btn btn-outline-dark mt-40">Return Home</a>
      </div>
    <?php else: ?>
      <?php if ($gallery['photographer_notes']): ?>
        <div class="alert alert-info" style="max-width:700px;margin:0 auto 40px;"><?= nl2br(e($gallery['photographer_notes'])) ?></div>
      <?php endif; ?>
      <div class="masonry">
        <?php foreach ($photos as $photo): ?>
          <div class="m-item" data-lightbox="<?= e($photo['file_path']) ?>">
            <img src="<?= e($photo['file_path']) ?>" alt="<?= e($photo['caption']) ?>" loading="lazy">
            <?php if ($photo['caption']): ?><div class="m-overlay"><?= e($photo['caption']) ?></div><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
