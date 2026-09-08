<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Your session expired. Please try submitting again.';
    } else {
        $name    = clean($_POST['name'] ?? '');
        $email   = clean($_POST['email'] ?? '');
        $subject = clean($_POST['subject'] ?? '');
        $message = clean($_POST['message'] ?? '');

        if ($name === '') $errors[] = 'Please enter your name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if ($message === '') $errors[] = 'Please enter a message.';

        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $subject, $message]);
            $success = true;
        }
    }
}

$pageTitle = 'Contact — PHStudio';
$pageDescription = 'Get in touch with PHStudio to ask about availability, pricing, or custom packages.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <span class="eyebrow">Get In Touch</span>
    <h1>Contact Us</h1>
    <div class="breadcrumb"><a href="<?= base_url('index.php') ?>">Home</a> / Contact</div>
  </div>
</section>

<section class="section section-charcoal">
  <div class="container contact-grid">
    <div class="contact-info reveal">
      <span class="eyebrow">Say Hello</span>
      <h3>Let's talk about your session</h3>
      <p>Whether you have a date in mind or just want to explore packages, we'd love to hear from you. We typically respond within one business day.</p>

      <div class="contact-detail"><span class="dot"></span><div><strong>Email</strong><br>hello@phstudio.demo</div></div>
      <div class="contact-detail"><span class="dot"></span><div><strong>Phone</strong><br>+63 917 000 0000</div></div>
      <div class="contact-detail"><span class="dot"></span><div><strong>Studio</strong><br>Makati City, Metro Manila, Philippines</div></div>
      <div class="contact-detail"><span class="dot"></span><div><strong>Hours</strong><br>Mon – Sat, 9:00 AM – 6:00 PM</div></div>
    </div>

    <div class="reveal">
      <?php if ($success): ?>
        <div class="alert alert-success">Thanks for reaching out! We'll get back to you shortly.</div>
      <?php endif; ?>
      <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
      <?php endforeach; ?>

      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="form-row">
          <div class="form-group">
            <label for="name">Full Name</label>
            <input class="form-control" type="text" id="name" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="email">Email Address</label>
            <input class="form-control" type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="subject">Subject</label>
          <input class="form-control" type="text" id="subject" name="subject" value="<?= e($_POST['subject'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea class="form-control" id="message" name="message" required rows="6"><?= e($_POST['message'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-gold btn-lg btn-block">Send Message</button>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
