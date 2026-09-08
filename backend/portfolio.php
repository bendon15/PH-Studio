<?php
require_once __DIR__ . '/includes/functions.php';

$activeSlug = clean($_GET['category'] ?? 'all');
$categories = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$photos = db()->query('SELECT p.*, c.slug AS category_slug, c.name AS category_name
                        FROM portfolio_photos p JOIN categories c ON c.id = p.category_id
                        ORDER BY p.sort_order')->fetchAll();

$pageTitle = 'Portfolio — PHStudio';
$pageDescription = 'Browse our photography portfolio across weddings, portraits, events, graduation, prenatal, and corporate work.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Our Work</span>
    <h1>Portfolio</h1>
    <div class="breadcrumb"><a href="<?= base_url('index.php') ?>">Home</a> / Portfolio</div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filter-bar reveal">
      <button class="filter-btn <?= $activeSlug === 'all' ? 'active' : '' ?>" data-filter="all">All</button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter-btn <?= $activeSlug === $cat['slug'] ? 'active' : '' ?>" data-filter="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="masonry reveal">
      <?php foreach ($photos as $photo): ?>
        <div class="m-item" data-category="<?= e($photo['category_slug']) ?>" data-lightbox="<?= e($photo['image_path']) ?>"
             style="<?= ($activeSlug !== 'all' && $activeSlug !== $photo['category_slug']) ? 'display:none;' : '' ?>">
          <img src="<?= e($photo['image_path']) ?>" alt="<?= e($photo['title']) ?>" loading="lazy">
          <div class="m-overlay"><?= e($photo['category_name']) ?> — <?= e($photo['title']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="container reveal">
    <span class="eyebrow">Like what you see?</span>
    <h2>Your story deserves the same care.</h2>
    <a href="<?= base_url('booking.php') ?>" class="btn btn-gold btn-lg">Book a Session</a>
  </div>
</section>

<script>
  // Pre-activate filter on load if a category query param was passed server-side
  document.addEventListener('DOMContentLoaded', () => {
    const active = document.querySelector('.filter-btn.active');
    if (active) active.click();
  });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
