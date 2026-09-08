<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$errors = [];
$editingPackage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id           = (int) ($_POST['id'] ?? 0);
        $name         = clean($_POST['name'] ?? '');
        $categoryId   = (int) ($_POST['category_id'] ?? 0) ?: null;
        $description  = clean($_POST['description'] ?? '');
        $price        = (float) ($_POST['price'] ?? 0);
        $duration     = (float) ($_POST['duration_hours'] ?? 1);
        $deliverables = array_filter(array_map('trim', explode("\n", $_POST['deliverables'] ?? '')));
        $isPopular    = isset($_POST['is_popular']) ? 1 : 0;
        $isActive     = isset($_POST['is_active']) ? 1 : 0;
        $slug         = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));

        if ($name === '') $errors[] = 'Package name is required.';
        if ($price <= 0) $errors[] = 'Price must be greater than zero.';

        if (!$errors) {
            if ($id) {
                $stmt = db()->prepare('UPDATE packages SET category_id=?, name=?, slug=?, description=?, price=?, duration_hours=?, deliverables=?, is_popular=?, is_active=? WHERE id=?');
                $stmt->execute([$categoryId, $name, $slug, $description, $price, $duration, json_encode(array_values($deliverables)), $isPopular, $isActive, $id]);
            } else {
                $stmt = db()->prepare('INSERT INTO packages (category_id, name, slug, description, price, duration_hours, deliverables, is_popular, is_active) VALUES (?,?,?,?,?,?,?,?,?)');
                $stmt->execute([$categoryId, $name, $slug, $description, $price, $duration, json_encode(array_values($deliverables)), $isPopular, $isActive]);
            }
            redirect(base_url('admin/packages.php'));
        }
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM packages WHERE id = ?')->execute([(int) $_POST['id']]);
        redirect(base_url('admin/packages.php'));
    }
}

if (!empty($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM packages WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editingPackage = $stmt->fetch();
}

$categories = db()->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$packages   = db()->query('SELECT pk.*, c.name AS category_name FROM packages pk LEFT JOIN categories c ON c.id = pk.category_id ORDER BY pk.sort_order')->fetchAll();

$dashTitle = 'Packages';
$dashSubtitle = 'Manage photography packages and pricing.';
$activeNav = 'packages';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3><?= $editingPackage ? 'Edit Package' : 'Add New Package' ?></h3></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int) ($editingPackage['id'] ?? 0) ?>">
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Package Name</label>
        <input class="d-form-control" type="text" name="name" required value="<?= e($editingPackage['name'] ?? '') ?>">
      </div>
      <div class="d-form-group">
        <label>Category</label>
        <select class="d-form-control" name="category_id">
          <option value="">— General —</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($editingPackage['category_id'] ?? null) == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="d-form-group">
      <label>Description</label>
      <textarea class="d-form-control" name="description" rows="2"><?= e($editingPackage['description'] ?? '') ?></textarea>
    </div>
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Price (₱)</label>
        <input class="d-form-control" type="number" step="0.01" name="price" required value="<?= e((string) ($editingPackage['price'] ?? '')) ?>">
      </div>
      <div class="d-form-group">
        <label>Duration (hours)</label>
        <input class="d-form-control" type="number" step="0.5" name="duration_hours" value="<?= e((string) ($editingPackage['duration_hours'] ?? '1')) ?>">
      </div>
    </div>
    <div class="d-form-group">
      <label>Deliverables (one per line)</label>
      <textarea class="d-form-control" name="deliverables" rows="4"><?= e(implode("\n", json_decode($editingPackage['deliverables'] ?? '[]', true) ?: [])) ?></textarea>
    </div>
    <div class="d-form-group" style="display:flex;gap:20px;">
      <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="is_popular" <?= !empty($editingPackage['is_popular']) ? 'checked' : '' ?>> Mark as Popular</label>
      <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="is_active" <?= ($editingPackage === null || !empty($editingPackage['is_active'])) ? 'checked' : '' ?>> Active</label>
    </div>
    <button type="submit" class="d-btn d-btn-primary"><?= $editingPackage ? 'Update Package' : 'Add Package' ?></button>
    <?php if ($editingPackage): ?><a href="<?= base_url('admin/packages.php') ?>" class="d-btn d-btn-outline">Cancel</a><?php endif; ?>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h3>All Packages</h3></div>
  <div class="table-wrap">
    <table class="d-table">
      <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($packages as $pkg): ?>
        <tr>
          <td><?= e($pkg['name']) ?><?= $pkg['is_popular'] ? ' ⭐' : '' ?></td>
          <td><?= e($pkg['category_name'] ?? 'General') ?></td>
          <td><?= money($pkg['price']) ?></td>
          <td><span class="badge <?= $pkg['is_active'] ? 'badge-completed' : 'badge-cancelled' ?>"><?= $pkg['is_active'] ? 'Active' : 'Inactive' ?></span></td>
          <td style="display:flex;gap:6px;">
            <a href="?edit=<?= $pkg['id'] ?>" class="d-btn d-btn-outline d-btn-sm">Edit</a>
            <form method="post" onsubmit="return confirm('Delete this package?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $pkg['id'] ?>">
              <button type="submit" class="d-btn d-btn-danger d-btn-sm">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
