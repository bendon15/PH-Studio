<?php
require_once __DIR__ . '/includes/functions.php';

$packages = db()->query('SELECT pk.*, c.name AS category_name FROM packages pk
                          LEFT JOIN categories c ON c.id = pk.category_id
                          WHERE pk.is_active = 1 ORDER BY pk.category_id, pk.sort_order')->fetchAll();

$pageTitle = 'Packages & Pricing — PHStudio';
$pageDescription = 'Transparent photography packages and pricing for weddings, portraits, events, graduation, prenatal, and corporate sessions.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Investment</span>
    <h1>Packages &amp; Pricing</h1>
    <div class="breadcrumb"><a href="<?= base_url('index.php') ?>">Home</a> / Packages</div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <p>All packages include professional editing and a private online gallery. Need something custom? <a href="<?= base_url('contact.php') ?>" style="color:var(--gold);">Get in touch</a> for a tailored quote.</p>
    </div>
    <div class="package-grid reveal">
      <?php foreach ($packages as $pkg): $items = json_decode($pkg['deliverables'] ?? '[]', true) ?: []; ?>
        <div class="package-card <?= $pkg['is_popular'] ? 'popular' : '' ?>">
          <?php if ($pkg['is_popular']): ?><span class="popular-tag">Most Booked</span><?php endif; ?>
          <span class="package-cat"><?= e($pkg['category_name'] ?? 'All Categories') ?></span>
          <h3><?= e($pkg['name']) ?></h3>
          <p style="color:var(--grey);font-size:14.5px;"><?= e($pkg['description']) ?></p>
          <div class="package-price"><?= money($pkg['price']) ?> <span>/ session</span></div>
          <ul class="package-list">
            <?php foreach ($items as $item): ?>
              <li><?= e($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="<?= base_url('booking.php?package=' . $pkg['id']) ?>" class="btn btn-outline-dark btn-block">Select Package</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
