<?php
/**
 * PHStudio — Public Site Header
 * Expects optional $pageTitle and $pageDescription to be set before include.
 */
require_once __DIR__ . '/functions.php';
$pageTitle       = $pageTitle ?? 'PHStudio — Photography Studio';
$pageDescription = $pageDescription ?? 'Premium photography studio for weddings, portraits, events, and more.';
$user            = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDescription) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="demo-notice">Portfolio Demo — Not a real photography booking service.</div>

<header class="site-header" id="siteHeader">
  <div class="container header-inner">
    <a href="<?= base_url('index.php') ?>" class="brand">PH<span>Studio</span></a>

    <nav class="main-nav" id="mainNav">
      <a href="<?= base_url('index.php') ?>">Home</a>
      <a href="<?= base_url('portfolio.php') ?>">Portfolio</a>
      <a href="<?= base_url('packages.php') ?>">Packages</a>
      <a href="<?= base_url('about.php') ?>">About</a>
      <a href="<?= base_url('faq.php') ?>">FAQ</a>
      <a href="<?= base_url('contact.php') ?>">Contact</a>
    </nav>

    <div class="header-actions">
      <?php if ($user): ?>
        <a href="<?= base_url($user['role'] === 'client' ? 'client/dashboard.php' : 'admin/dashboard.php') ?>" class="btn btn-ghost">My Account</a>
      <?php else: ?>
        <a href="<?= base_url('auth/login.php') ?>" class="btn btn-ghost">Login</a>
      <?php endif; ?>
      <a href="<?= base_url('booking.php') ?>" class="btn btn-gold">Book a Session</a>
    </div>

    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
