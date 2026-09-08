<?php
require_once __DIR__ . '/includes/functions.php';

$preselectedPackage = isset($_GET['package']) ? (int) $_GET['package'] : null;

// If already a logged-in client, send straight to the real booking form.
if (has_role('client')) {
    $qs = $preselectedPackage ? ('?package=' . $preselectedPackage) : '';
    redirect(base_url('client/book-session.php' . $qs));
}

$packages = db()->query('SELECT pk.*, c.name AS category_name FROM packages pk
                          LEFT JOIN categories c ON c.id = pk.category_id
                          WHERE pk.is_active = 1 ORDER BY pk.sort_order LIMIT 6')->fetchAll();

$pageTitle = 'Book a Session — PHStudio';
$pageDescription = 'Start your booking with PHStudio — choose a package, pick your date, and tell us about your session.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Let's Get Started</span>
    <h1>Book a Session</h1>
    <div class="breadcrumb"><a href="<?= base_url('index.php') ?>">Home</a> / Booking</div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;max-width:640px;">
      <h2>Create a free account to book</h2>
      <p>Your PHStudio account keeps every booking, invoice, and private gallery in one place — from your first inquiry to the final delivered photo.</p>
    </div>
    <div class="grid-2 reveal" style="max-width:720px;margin:0 auto 60px;">
      <a href="<?= base_url('auth/register.php' . ($preselectedPackage ? '?redirect=' . urlencode('client/book-session.php?package=' . $preselectedPackage) : '')) ?>" class="package-card text-center" style="text-decoration:none;">
        <h3>New Client</h3>
        <p style="color:var(--grey);margin:14px 0 22px;font-size:14.5px;">Create your free account and start your first booking in minutes.</p>
        <span class="btn btn-gold btn-block">Create Account</span>
      </a>
      <a href="<?= base_url('auth/login.php' . ($preselectedPackage ? '?redirect=' . urlencode('client/book-session.php?package=' . $preselectedPackage) : '')) ?>" class="package-card text-center" style="text-decoration:none;">
        <h3>Returning Client</h3>
        <p style="color:var(--grey);margin:14px 0 22px;font-size:14.5px;">Log in to book another session or check your existing bookings.</p>
        <span class="btn btn-outline-dark btn-block">Log In</span>
      </a>
    </div>

    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <span class="eyebrow">Popular Choices</span>
      <h2>Packages to consider</h2>
    </div>
    <div class="package-grid reveal">
      <?php foreach ($packages as $pkg): ?>
        <div class="package-card <?= $pkg['is_popular'] ? 'popular' : '' ?>">
          <?php if ($pkg['is_popular']): ?><span class="popular-tag">Most Booked</span><?php endif; ?>
          <span class="package-cat"><?= e($pkg['category_name'] ?? 'All Categories') ?></span>
          <h3><?= e($pkg['name']) ?></h3>
          <div class="package-price"><?= money($pkg['price']) ?> <span>/ session</span></div>
          <a href="<?= base_url('booking.php?package=' . $pkg['id']) ?>" class="btn btn-outline-dark btn-block">Choose This Package</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
