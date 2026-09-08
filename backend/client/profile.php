<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullName = clean($_POST['full_name'] ?? '');
        $phone    = clean($_POST['phone'] ?? '');

        if (mb_strlen($fullName) < 2) $errors[] = 'Please enter your full name.';

        if (!$errors) {
            $stmt = db()->prepare('UPDATE users SET full_name = ?, phone = ? WHERE id = ?');
            $stmt->execute([$fullName, $phone, $user['id']]);
            $_SESSION['user']['full_name'] = $fullName;
            $_SESSION['user']['phone'] = $phone;
            $success = true;
        }
    } elseif ($action === 'change_password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');

        $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $hash = $stmt->fetchColumn();

        if (!password_verify($current, $hash)) {
            $errors[] = 'Current password is incorrect.';
        } elseif (mb_strlen($new) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $errors[] = 'New passwords do not match.';
        } else {
            $newHash = password_hash($new, PASSWORD_BCRYPT);
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $user['id']]);
            $success = true;
        }
    }
}

// Refresh user data
$stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user['id']]);
$freshUser = $stmt->fetch();

$dashTitle = 'My Profile';
$activeNav = 'profile';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php if ($success): ?><div class="d-alert d-alert-success">Your changes have been saved.</div><?php endif; ?>
<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Personal Information</h3></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update_profile">
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Full Name</label>
        <input class="d-form-control" type="text" name="full_name" value="<?= e($freshUser['full_name']) ?>" required>
      </div>
      <div class="d-form-group">
        <label>Email Address</label>
        <input class="d-form-control" type="email" value="<?= e($freshUser['email']) ?>" disabled>
      </div>
    </div>
    <div class="d-form-group">
      <label>Phone Number</label>
      <input class="d-form-control" type="text" name="phone" value="<?= e($freshUser['phone']) ?>">
    </div>
    <button type="submit" class="d-btn d-btn-primary">Save Changes</button>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h3>Change Password</h3></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="change_password">
    <div class="d-form-group">
      <label>Current Password</label>
      <input class="d-form-control" type="password" name="current_password" required>
    </div>
    <div class="d-form-row">
      <div class="d-form-group">
        <label>New Password</label>
        <input class="d-form-control" type="password" name="new_password" minlength="8" required>
      </div>
      <div class="d-form-group">
        <label>Confirm New Password</label>
        <input class="d-form-control" type="password" name="new_password_confirm" minlength="8" required>
      </div>
    </div>
    <button type="submit" class="d-btn d-btn-outline">Update Password</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
