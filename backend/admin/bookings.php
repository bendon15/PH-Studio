<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$statusFilter = clean($_GET['status'] ?? 'all');
$search = clean($_GET['q'] ?? '');

$sql = 'SELECT b.*, u.full_name AS client_name, u.email AS client_email, pk.name AS package_name
        FROM bookings b
        JOIN users u ON u.id = b.client_id
        JOIN packages pk ON pk.id = b.package_id
        WHERE 1=1';
$params = [];

if ($statusFilter !== 'all') {
    $sql .= ' AND b.status = ?';
    $params[] = $statusFilter;
}
if ($search !== '') {
    $sql .= ' AND (u.full_name LIKE ? OR pk.name LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= ' ORDER BY b.session_date DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$statuses = ['all', 'pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

$dashTitle = 'Bookings';
$dashSubtitle = 'Manage all client booking requests.';
$activeNav = 'bookings';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3>All Bookings (<?= count($bookings) ?>)</h3>
    <form method="get" style="display:flex;gap:8px;">
      <input type="hidden" name="status" value="<?= e($statusFilter) ?>">
      <input class="d-form-control" style="width:220px;" type="text" name="q" placeholder="Search client or package..." value="<?= e($search) ?>">
      <button type="submit" class="d-btn d-btn-outline">Search</button>
    </form>
  </div>

  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
    <?php foreach ($statuses as $s): ?>
      <a href="?status=<?= e($s) ?>&q=<?= urlencode($search) ?>" class="d-btn d-btn-sm <?= $statusFilter === $s ? 'd-btn-primary' : 'd-btn-outline' ?>"><?= ucfirst(str_replace('_', ' ', $s)) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (!$bookings): ?>
    <div class="empty-state"><div class="icon">📅</div><p>No bookings match this filter.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Client</th><th>Package</th><th>Date</th><th>Time</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
          <tr>
            <td><?= e($b['client_name']) ?><br><span style="color:var(--d-muted);font-size:12px;"><?= e($b['client_email']) ?></span></td>
            <td><?= e($b['package_name']) ?></td>
            <td><?= pretty_date($b['session_date']) ?></td>
            <td><?= pretty_time($b['start_time']) ?></td>
            <td><?= status_badge($b['status']) ?></td>
            <td><a href="<?= base_url('admin/booking-detail.php?id=' . $b['id']) ?>" class="d-btn d-btn-outline d-btn-sm">Manage</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
