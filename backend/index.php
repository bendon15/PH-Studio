<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'PHStudio — Premium Photography Studio';
$pageDescription = 'Editorial wedding, portrait, event, graduation, prenatal, and corporate photography.';

$categories = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$featured   = db()->query('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                            FROM portfolio_photos p JOIN categories c ON c.id = p.category_id
                            WHERE p.is_featured = 1 ORDER BY p.sort_order LIMIT 6')->fetchAll();
$packages   = db()->query('SELECT pk.*, c.name AS category_name FROM packages pk
                            LEFT JOIN categories c ON c.id = pk.category_id
                            WHERE pk.is_active = 1 ORDER BY pk.is_popular DESC, pk.sort_order LIMIT 3')->fetchAll();
$testimonials = db()->query('SELECT * FROM testimonials WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-content">
    <span class="eyebrow">Metro Manila &middot; Available for Travel</span>
    <h1>Photography that feels <em>like you.</em></h1>
    <p>PHStudio documents weddings, portraits, and milestone moments with an editorial eye and a warm, honest style — so your gallery feels timeless, not staged.</p>
    <div class="hero-actions">
      <a href="<?= base_url('booking.php') ?>" class="btn btn-gold btn-lg">Book a Session</a>
      <a href="<?= base_url('portfolio.php') ?>" class="btn btn-ghost btn-lg">View Portfolio</a>
    </div>
  </div>
  <div class="hero-scroll"><span>Scroll</span><span class="line"></span></div>
</section>

<!-- Studio Introduction -->
<section class="section">
  <div class="container intro-grid">
    <div class="intro-images reveal">
      <img class="img-a" src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=900" alt="Photographer at work">
      <img class="img-b" src="https://images.unsplash.com/photo-1554048612-b6a482bc67e5?w=700" alt="Camera detail">
    </div>
    <div class="intro-text reveal">
      <span class="eyebrow">The Studio</span>
      <h2>Led by Marco Villanueva, <br>built for real moments.</h2>
      <p>PHStudio began in 2016 with a single mission: capture people as they truly are. Today our small team of photographers has documented over 500 weddings, portrait sessions, and milestone events across the Philippines.</p>
      <p>We believe the best photographs happen when people forget the camera is there — that's the moment we chase, every single time.</p>
      <div class="stat-row">
        <div><div class="num">500+</div><div class="label">Sessions Shot</div></div>
        <div><div class="num">9</div><div class="label">Years Experience</div></div>
        <div><div class="num">4.9★</div><div class="label">Client Rating</div></div>
      </div>
    </div>
  </div>
</section>

<!-- Categories -->
<section class="section section-charcoal">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">What We Shoot</span>
      <h2>Every milestone, <br>one studio.</h2>
    </div>
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="<?= base_url('portfolio.php?category=' . e($cat['slug'])) ?>" class="category-card reveal">
          <img src="<?= e($cat['cover_image'] ? 'https://source.unsplash.com/collection/' : '') ?><?= $cat['id'] % 2 === 0 ? 'https://images.unsplash.com/photo-1521543387479-1ff3457cef01?w=800' : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=800' ?>" alt="<?= e($cat['name']) ?>">
          <div class="category-info">
            <span class="cat-count">Photography</span>
            <h3><?= e($cat['name']) ?></h3>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured Portfolio -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <span class="eyebrow">Selected Work</span>
      <h2>From the portfolio</h2>
      <p>A glimpse at recent sessions across every category we shoot.</p>
    </div>
    <div class="masonry reveal">
      <?php foreach ($featured as $photo): ?>
        <div class="m-item" data-lightbox="<?= e($photo['image_path']) ?>">
          <img src="<?= e($photo['image_path']) ?>" alt="<?= e($photo['title']) ?>" loading="lazy">
          <div class="m-overlay"><?= e($photo['category_name']) ?> — <?= e($photo['title']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-40">
      <a href="<?= base_url('portfolio.php') ?>" class="btn btn-outline-dark btn-lg">View Full Portfolio</a>
    </div>
  </div>
</section>

<!-- Packages teaser -->
<section class="section section-charcoal">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <span class="eyebrow">Investment</span>
      <h2>Packages &amp; Pricing</h2>
      <p>Transparent pricing across our most-booked sessions. Full package list available on the pricing page.</p>
    </div>
    <div class="package-grid reveal">
      <?php foreach ($packages as $pkg): $items = json_decode($pkg['deliverables'] ?? '[]', true) ?: []; ?>
        <div class="package-card <?= $pkg['is_popular'] ? 'popular' : '' ?>" style="background:var(--charcoal-light);border-color:var(--line);color:#fff;">
          <?php if ($pkg['is_popular']): ?><span class="popular-tag">Most Booked</span><?php endif; ?>
          <span class="package-cat"><?= e($pkg['category_name'] ?? 'All Categories') ?></span>
          <h3 style="color:#fff;"><?= e($pkg['name']) ?></h3>
          <div class="package-price" style="color:var(--gold);"><?= money($pkg['price']) ?> <span style="color:var(--grey-light);">/ session</span></div>
          <ul class="package-list">
            <?php foreach (array_slice($items, 0, 4) as $item): ?>
              <li style="color:var(--grey-light);border-color:var(--line);"><?= e($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="<?= base_url('booking.php?package=' . $pkg['id']) ?>" class="btn btn-gold btn-block">Select Package</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="section section-black">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <span class="eyebrow">Kind Words</span>
      <h2>What clients say</h2>
    </div>
    <div class="testimonial-track reveal">
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <div class="stars"><?= str_repeat('★', (int) $t['rating']) . str_repeat('☆', 5 - (int) $t['rating']) ?></div>
          <p class="quote">&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
          <div class="testimonial-author">
            <div class="avatar-circle"><?= e(strtoupper(substr($t['client_name'], 0, 1))) ?></div>
            <div>
              <div class="author-name"><?= e($t['client_name']) ?></div>
              <div class="author-role">Verified Client</div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-banner">
  <div class="container reveal">
    <span class="eyebrow">Ready when you are</span>
    <h2>Let's create something timeless together.</h2>
    <p>Tell us your date, your story, and your vision — we'll handle the rest.</p>
    <a href="<?= base_url('booking.php') ?>" class="btn btn-gold btn-lg">Start Your Booking</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
