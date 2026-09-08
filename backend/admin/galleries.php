<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf() && ($_POST['action'] ?? '') === 'create') {
    $clientId = (int) ($_POST['client_id'] ?? 0);
    $bookingId = (int) ($_POST['booking_id'] ?? 0) ?: null;
    $title = clean($_POST['title'] ?? '');
    $notes = clean($_POST['photographer_notes'] ?? '');
    $allowDownload = isset($_POST['allow_download']) ? 1 : 0;
    $isPublished = isset($_POST['is_published']) ? 1 : 0;

    if (!$clientId) $errors[] = 'Please select a client.';
    if ($title === '') $errors[] = 'Please enter a gallery title.';

    if (!$errors) {
        $coverImage = null;
        try {
            $path = handle_image_upload($_FILES['cover_image'] ?? [], 'galleries');
            if ($path) $coverImage = $path;
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
        if (!$coverImage) {
            $coverImage = 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1200'; // demo fallback
        }

        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO galleries (booking_id, client_id, title, cover_image, photographer_notes, share_token, is_published, allow_download)
                                    VALUES (?,?,?,?,?,?,?,?)');
            $stmt->execute([$bookingId, $clientId, $title, $coverImage, $notes, generate_share_token(), $isPublished, $allowDownload]);
            redirect(base_url('admin/galleries.php'));
        }
    }
}

$clients = db()->query('SELECT id, full_name FROM users WHERE role = "client" ORDER BY full_name')->fetchAll();
$bookings = db()->query('SELECT id, client_id, session_date FROM bookings ORDER BY session_date DESC')->fetchAll();
$galleries = db()->query('SELECT g.*, u.full_name AS client_name, COUNT(gp.id) AS photo_count
                           FROM galleries g JOIN users u ON u.id = g.client_id
                           LEFT JOIN gallery_photos gp ON gp.gallery_id = g.id
                           GROUP BY g.id ORDER BY g.created_at DESC')->fetchAll();

$dashTitle = 'Private Galleries';
$dashSubtitle = 'Create and manage client galleries.';
$activeNav = 'galleries';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Create New Gallery</h3></div>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Gallery Title</label>
        <input class="d-form-control" type="text" name="title" placeholder="e.g. Juan &amp; Maria — Wedding Gallery" required>
      </div>
      <div class="d-form-group">
        <label>Client</label>
        <select class="d-form-control" name="client_id" required>
          <option value="">Select client</option>
          <?php foreach ($clients as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['full_name']) ?></option><?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="d-form-row">
      <div class="d-form-group">
        <label>Linked Booking (optional)</label>
        <select class="d-form-control" name="booking_id">
          <option value="">None</option>
          <?php foreach ($bookings as $b): ?><option value="<?= $b['id'] ?>">#<?= $b['id'] ?> — <?= pretty_date($b['session_date']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="d-form-group">
        <label>Cover Image</label>
        <input class="d-form-control" type="file" name="cover_image" accept="image/*">
      </div>
    </div>
    <div class="d-form-group">
      <label>Photographer Notes (visible to client)</label>
      <textarea class="d-form-control" name="photographer_notes" rows="2"></textarea>
    </div>
    <div class="d-form-group" style="display:flex;gap:20px;">
      <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="allow_download" checked> Allow Downloads</label>
      <label style="display:flex;align-items:center;gap:6px;"><input type="checkbox" name="is_published" checked> Publish Immediately</label>
    </div>
    <button type="submit" class="d-btn d-btn-primary">Create Gallery</button>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h3>All Galleries</h3></div>
  <?php if (!$galleries): ?>
    <div class="empty-state"><div class="icon">🖼️</div><p>No galleries created yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Title</th><th>Client</th><th>Photos</th><th>Published</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($galleries as $g): ?>
          <tr>
            <td><?= e($g['title']) ?></td>
            <td><?= e($g['client_name']) ?></td>
            <td><?= (int) $g['photo_count'] ?></td>
            <td><span class="badge <?= $g['is_published'] ? 'badge-completed' : 'badge-pending' ?>"><?= $g['is_published'] ? 'Published' : 'Draft' ?></span></td>
            <td><a href="<?= base_url('admin/gallery-manage.php?id=' . $g['id']) ?>" class="d-btn d-btn-outline d-btn-sm">Manage Photos</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
