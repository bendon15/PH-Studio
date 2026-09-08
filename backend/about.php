<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'About — PHStudio';
$pageDescription = 'Meet the studio behind PHStudio and the philosophy driving every session.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Our Story</span>
    <h1>About PHStudio</h1>
    <div class="breadcrumb"><a href="<?= base_url('index.php') ?>">Home</a> / About</div>
  </div>
</section>

<section class="section">
  <div class="container intro-grid">
    <div class="intro-images reveal">
      <img class="img-a" src="https://images.unsplash.com/photo-1520854221256-17451cc331bf?w=900" alt="Marco Villanueva, Lead Photographer">
      <img class="img-b" src="https://images.unsplash.com/photo-1471341971476-ae15ff5dd4ea?w=700" alt="Studio equipment">
    </div>
    <div class="intro-text reveal">
      <span class="eyebrow">Founder &amp; Lead Photographer</span>
      <h2>Marco Villanueva</h2>
      <p>Marco started PHStudio in 2016 after years of shooting editorial fashion campaigns. He wanted to bring that same intentional, story-first eye to everyday milestones — weddings, growing families, graduations, and the businesses built by people who work hard.</p>
      <p>Today Marco leads a small collective of photographers who share one belief: your photos should feel like a memory, not a performance.</p>
    </div>
  </div>
</section>

<section class="section section-charcoal">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <span class="eyebrow">Our Values</span>
      <h2>How we work</h2>
    </div>
    <div class="grid-3 reveal">
      <div class="testimonial-card">
        <h3 style="color:#fff;margin-bottom:14px;font-size:22px;">Honest Storytelling</h3>
        <p style="color:var(--grey-light);">We document real emotion over posed perfection — the in-between moments are usually the best ones.</p>
      </div>
      <div class="testimonial-card">
        <h3 style="color:#fff;margin-bottom:14px;font-size:22px;">Consistent Craft</h3>
        <p style="color:var(--grey-light);">A signature editing style across every gallery, so your photos age well and always feel like "you."</p>
      </div>
      <div class="testimonial-card">
        <h3 style="color:#fff;margin-bottom:14px;font-size:22px;">Reliable Process</h3>
        <p style="color:var(--grey-light);">From booking to delivery, every step — contracts, timelines, galleries — is tracked and communicated clearly.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head center reveal" style="margin-left:auto;margin-right:auto;">
      <span class="eyebrow">The Team</span>
      <h2>Behind the lens</h2>
    </div>
    <div class="grid-3 reveal">
      <?php
      $team = [
        ['name' => 'Marco Villanueva', 'role' => 'Founder & Lead Photographer', 'img' => 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=500'],
        ['name' => 'Isabela Cruz', 'role' => 'Studio Manager', 'img' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=500'],
        ['name' => 'Renz Aquino', 'role' => 'Associate Photographer', 'img' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500'],
      ];
      foreach ($team as $member): ?>
        <div class="package-card text-center">
          <img src="<?= e($member['img']) ?>" alt="<?= e($member['name']) ?>" style="width:110px;height:110px;border-radius:50%;object-fit:cover;margin:0 auto 18px;">
          <h3><?= e($member['name']) ?></h3>
          <p style="color:var(--gold);font-size:13px;text-transform:uppercase;letter-spacing:0.06em;"><?= e($member['role']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
