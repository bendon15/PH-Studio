<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$search = clean($_GET['q'] ?? '');
$sql = 'SELECT u.*, COUNT(b.id) AS booking_count, MAX(b.session_date) AS last_session
        FROM users u LEFT JOIN bookings b ON b.client_id = u.id
        WHERE u.role = "client"';
$params = [];
if ($search !== '') {
    $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ?)';
    $params = ["%$search%", "%$search%"];
}
$sql .= ' GROUP BY u.id ORDER BY u.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$clients = $stmt->fetchAll();

$dashTitle = 'Clients';
$dashSubtitle = 'Every client who has registered with PHStudio.';
$activeNav = 'clients';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3>All Clients (<?= count($clients) ?>)</h3>
    <form method="get" style="display:flex;gap:8px;">
      <input class="d-form-control" style="width:240px;" type="text" name="q" placeholder="Search name or email..." value="<?= e($search) ?>">
      <button type="submit" class="d-btn d-btn-outline">Search</button>
    </form>
  </div>

  <?php if (!$clients): ?>
    <div class="empty-state"><div class="icon">👥</div><p>No clients found.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Bookings</th><th>Last Session</th><th>Joined</th></tr></thead>
        <tbody>
          <?php foreach ($clients as $c): ?>
          <tr>
            <td><?= e($c['full_name']) ?></td>
            <td><?= e($c['email']) ?></td>
            <td><?= e($c['phone'] ?: '—') ?></td>
            <td><?= (int) $c['booking_count'] ?></td>
            <td><?= $c['last_session'] ? pretty_date($c['last_session']) : '—' ?></td>
            <td><?= pretty_date($c['created_at']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
