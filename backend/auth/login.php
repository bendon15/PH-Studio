<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect(base_url(has_role('client') ? 'client/dashboard.php' : 'admin/dashboard.php'));
}

$redirectTo = clean($_GET['redirect'] ?? $_POST['redirect'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $email    = strtolower(clean($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Incorrect email or password.';
        } else {
            login_user($user);
            $target = $redirectTo ?: ($user['role'] === 'client' ? 'client/dashboard.php' : 'admin/dashboard.php');
            redirect(base_url($target));
        }
    }
}

$pageTitle = 'Login — PHStudio';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <a href="<?= base_url('index.php') ?>" class="brand">PH<span>Studio</span></a>
    <p class="auth-subtitle">Welcome back</p>

    <div class="demo-hint">
      <strong>Demo accounts</strong> (password for all: <code>Demo@1234</code>)<br>
      Admin: admin@phstudio.demo &middot; Photographer: marco@phstudio.demo<br>
      Client: juan.delacruz@example.com
    </div>

    <?php foreach ($errors as $err): ?>
      <div class="d-alert d-alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="redirect" value="<?= e($redirectTo) ?>">
      <div class="d-form-group">
        <label for="email">Email Address</label>
        <input class="d-form-control" type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>" autofocus>
      </div>
      <div class="d-form-group">
        <label for="password">Password</label>
        <input class="d-form-control" type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="d-btn d-btn-gold" style="width:100%;padding:13px;">Log In</button>
    </form>

    <p class="auth-footer-link">New to PHStudio? <a href="<?= base_url('auth/register.php' . ($redirectTo ? '?redirect=' . urlencode($redirectTo) : '')) ?>">Create an account</a></p>
  </div>
</div>
</body>
</html>
