<?php
/**
 * PHStudio — Shared Dashboard Layout (Header)
 * Expects: $dashTitle, $dashSubtitle (optional), $activeNav (string key)
 * Requires functions.php to already be loaded and require_role() already called.
 */
$user = current_user();
$isAdminArea = has_role('admin', 'photographer');
$initials = '';
foreach (explode(' ', $user['full_name'] ?? '') as $part) {
    $initials .= mb_substr($part, 0, 1);
}
$initials = mb_strtoupper(mb_substr($initials, 0, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($dashTitle ?? 'Dashboard') ?> — PHStudio</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body class="dashboard">

<aside class="dash-sidebar">
  <a href="<?= base_url('index.php') ?>" class="dash-brand">PH<span>Studio</span></a>
  <span class="dash-role-tag"><?= $isAdminArea ? ucfirst($user['role']) . ' Panel' : 'Client Portal' ?></span>

  <nav class="dash-nav">
  <?php if ($isAdminArea): ?>
    <a href="<?= base_url('admin/dashboard.php') ?>" class="<?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
    <a href="<?= base_url('admin/bookings.php') ?>" class="<?= ($activeNav ?? '') === 'bookings' ? 'active' : '' ?>">📅 Bookings</a>
    <a href="<?= base_url('admin/availability.php') ?>" class="<?= ($activeNav ?? '') === 'availability' ? 'active' : '' ?>">🗓️ Availability</a>
    <a href="<?= base_url('admin/clients.php') ?>" class="<?= ($activeNav ?? '') === 'clients' ? 'active' : '' ?>">👥 Clients</a>
    <span class="nav-section-title">Content</span>
    <a href="<?= base_url('admin/galleries.php') ?>" class="<?= ($activeNav ?? '') === 'galleries' ? 'active' : '' ?>">🖼️ Galleries</a>
    <a href="<?= base_url('admin/packages.php') ?>" class="<?= ($activeNav ?? '') === 'packages' ? 'active' : '' ?>">📦 Packages</a>
    <a href="<?= base_url('admin/testimonials.php') ?>" class="<?= ($activeNav ?? '') === 'testimonials' ? 'active' : '' ?>">💬 Testimonials</a>
    <span class="nav-section-title">Finance</span>
    <a href="<?= base_url('admin/invoices.php') ?>" class="<?= ($activeNav ?? '') === 'invoices' ? 'active' : '' ?>">🧾 Invoices</a>
    <a href="<?= base_url('admin/reports.php') ?>" class="<?= ($activeNav ?? '') === 'reports' ? 'active' : '' ?>">📈 Reports</a>
  <?php else: ?>
    <a href="<?= base_url('client/dashboard.php') ?>" class="<?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
    <a href="<?= base_url('client/book-session.php') ?>" class="<?= ($activeNav ?? '') === 'book' ? 'active' : '' ?>">📅 Book a Session</a>
    <a href="<?= base_url('client/my-bookings.php') ?>" class="<?= ($activeNav ?? '') === 'bookings' ? 'active' : '' ?>">🗂️ My Bookings</a>
    <a href="<?= base_url('client/gallery.php') ?>" class="<?= ($activeNav ?? '') === 'galleries' ? 'active' : '' ?>">🖼️ My Galleries</a>
    <a href="<?= base_url('client/invoices.php') ?>" class="<?= ($activeNav ?? '') === 'invoices' ? 'active' : '' ?>">🧾 Invoices</a>
    <a href="<?= base_url('client/profile.php') ?>" class="<?= ($activeNav ?? '') === 'profile' ? 'active' : '' ?>">👤 Profile</a>
  <?php endif; ?>
  </nav>

  <div class="dash-user-box">
    <div class="initials"><?= e($initials ?: 'U') ?></div>
    <div>
      <div class="name"><?= e($user['full_name'] ?? 'User') ?></div>
      <a href="<?= base_url('auth/logout.php') ?>" class="logout">Log out</a>
    </div>
  </div>
</aside>

<main class="dash-main">
  <div class="dash-topbar">
    <div>
      <h1><?= e($dashTitle ?? 'Dashboard') ?></h1>
      <?php if (!empty($dashSubtitle)): ?><div class="subtitle"><?= e($dashSubtitle) ?></div><?php endif; ?>
    </div>
    <a href="<?= base_url('index.php') ?>" class="d-btn d-btn-outline d-btn-sm">View Public Site ↗</a>
  </div>
  <div class="dash-content">
