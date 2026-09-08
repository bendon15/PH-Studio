<?php
require_once __DIR__ . '/includes/functions.php';
$faqs = db()->query('SELECT * FROM faqs ORDER BY sort_order')->fetchAll();
$pageTitle = 'FAQ — PHStudio';
$pageDescription = 'Answers to common questions about booking, pricing, galleries, and more.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Need Help?</span>
    <h1>Frequently Asked Questions</h1>
    <div class="breadcrumb"><a href="<?= base_url('index.php') ?>">Home</a> / FAQ</div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="faq-list reveal">
      <?php foreach ($faqs as $faq): ?>
        <div class="faq-item">
          <button class="faq-question">
            <span><?= e($faq['question']) ?></span>
            <span class="icon">+</span>
          </button>
          <div class="faq-answer"><p><?= e($faq['answer']) ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-40">
      <p style="color:var(--grey);margin-bottom:18px;">Still have questions?</p>
      <a href="<?= base_url('contact.php') ?>" class="btn btn-outline-dark btn-lg">Contact Us</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
