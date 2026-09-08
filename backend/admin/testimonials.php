<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $name = clean($_POST['client_name'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0) ?: null;
        $rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
        $quote = clean($_POST['quote'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if ($name === '' || $quote === '') {
            $errors[] = 'Please fill in the client name and quote.';
        } else {
            $stmt = db()->prepare('INSERT INTO testimonials (client_name, category_id, rating, quote, is_published) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $categoryId, $rating, $quote, $isPublished]);
            redirect(base_url('admin/testimonials.php'));
        }
    } elseif ($action === 'toggle') {
        db()->prepare('UPDATE testimonials SET is_published = 1 - is_published WHERE id = ?')->execute([(int) $_POST['id']]);
        redirect(base_url('admin/testimonials.php'));
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM testimonials WHERE id = ?')->execute([(int) $_POST['id']]);
        redirect(base_url('admin/testimonials.php'));
    }
}

$categories = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$testimonials = db()->query('SELECT t.*, c.name AS category_name FROM testimonials t LEFT JOIN categories c ON c.id = t.category_id ORDER BY t.created_at DESC')->fetchAll();

$dashTitle = 'Testimonials';
$dashSubtitle = 'Manage client reviews shown on the public site.';
$activeNav = 'testimonials';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Add Testimonial</h3></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Client Name</label>
        <input class="d-form-control" type="text" name="client_name" required>
      </div>
      <div class="d-form-group">
        <label>Category</label>
        <select class="d-form-control" name="category_id">
          <option value="">General</option>
          <?php foreach ($categories as $cat): ?><option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Rating</label>
        <select class="d-form-control" name="rating">
          <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> Stars</option><?php endfor; ?>
        </select>
      </div>
      <div class="d-form-group" style="display:flex;align-items:end;">
        <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="is_published" checked> Publish Immediately</label>
      </div>
    </div>
    <div class="d-form-group">
      <label>Quote</label>
      <textarea class="d-form-control" name="quote" rows="3" required></textarea>
    </div>
    <button type="submit" class="d-btn d-btn-primary">Add Testimonial</button>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h3>All Testimonials</h3></div>
  <div class="table-wrap">
    <table class="d-table">
      <thead><tr><th>Client</th><th>Category</th><th>Rating</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($testimonials as $t): ?>
        <tr>
          <td><?= e($t['client_name']) ?><br><span style="color:var(--d-muted);font-size:12.5px;"><?= e(mb_strimwidth($t['quote'], 0, 60, '...')) ?></span></td>
          <td><?= e($t['category_name'] ?? 'General') ?></td>
          <td><?= str_repeat('★', $t['rating']) ?></td>
          <td><span class="badge <?= $t['is_published'] ? 'badge-completed' : 'badge-pending' ?>"><?= $t['is_published'] ? 'Published' : 'Hidden' ?></span></td>
          <td style="display:flex;gap:6px;">
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $t['id'] ?>"><button type="submit" class="d-btn d-btn-outline d-btn-sm"><?= $t['is_published'] ? 'Hide' : 'Publish' ?></button></form>
            <form method="post" onsubmit="return confirm('Delete this testimonial?');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $t['id'] ?>"><button type="submit" class="d-btn d-btn-danger d-btn-sm">Delete</button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
