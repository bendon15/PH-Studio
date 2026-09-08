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
        $fullName = clean($_POST['full_name'] ?? '');
        $email    = strtolower(clean($_POST['email'] ?? ''));
        $phone    = clean($_POST['phone'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $confirm  = (string) ($_POST['password_confirm'] ?? '');

        if (mb_strlen($fullName) < 2) $errors[] = 'Please enter your full name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (mb_strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if (!$errors) {
            $check = db()->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = 'An account with this email already exists. Try logging in instead.';
            }
        }

        if (!$errors) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = db()->prepare('INSERT INTO users (role, full_name, email, phone, password_hash) VALUES ("client", ?, ?, ?, ?)');
            $stmt->execute([$fullName, $email, $phone, $hash]);
            $userId = (int) db()->lastInsertId();

            login_user([
                'id' => $userId,
                'role' => 'client',
                'full_name' => $fullName,
                'email' => $email,
            ]);

            redirect($redirectTo ? base_url($redirectTo) : base_url('client/dashboard.php'));
        }
    }
}

$pageTitle = 'Create Account — PHStudio';
require_once __DIR__ . '/../includes/functions.php';
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
    <p class="auth-subtitle">Create your free client account</p>

    <div class="demo-hint">Portfolio Demo — Not a real photography booking service. Any email/password combination you register here works only within this demo database.</div>

    <?php foreach ($errors as $err): ?>
      <div class="d-alert d-alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="redirect" value="<?= e($redirectTo) ?>">
      <div class="d-form-group">
        <label for="full_name">Full Name</label>
        <input class="d-form-control" type="text" id="full_name" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>">
      </div>
      <div class="d-form-group">
        <label for="email">Email Address</label>
        <input class="d-form-control" type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
      </div>
      <div class="d-form-group">
        <label for="phone">Phone Number</label>
        <input class="d-form-control" type="text" id="phone" name="phone" value="<?= e($_POST['phone'] ?? '') ?>">
      </div>
      <div class="d-form-row">
        <div class="d-form-group">
          <label for="password">Password</label>
          <input class="d-form-control" type="password" id="password" name="password" required minlength="8">
        </div>
        <div class="d-form-group">
          <label for="password_confirm">Confirm Password</label>
          <input class="d-form-control" type="password" id="password_confirm" name="password_confirm" required minlength="8">
        </div>
      </div>
      <button type="submit" class="d-btn d-btn-gold" style="width:100%;padding:13px;">Create Account</button>
    </form>

    <p class="auth-footer-link">Already have an account? <a href="<?= base_url('auth/login.php' . ($redirectTo ? '?redirect=' . urlencode($redirectTo) : '')) ?>">Log in</a></p>
  </div>
</div>
</body>
</html>
