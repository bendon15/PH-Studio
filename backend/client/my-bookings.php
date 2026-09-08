<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$statusFilter = clean($_GET['status'] ?? 'all');

$sql = 'SELECT b.*, pk.name AS package_name FROM bookings b
        JOIN packages pk ON pk.id = b.package_id
        WHERE b.client_id = ?';
$params = [$user['id']];
if ($statusFilter !== 'all') {
    $sql .= ' AND b.status = ?';
    $params[] = $statusFilter;
}
$sql .= ' ORDER BY b.session_date DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$statuses = ['all', 'pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

$dashTitle = 'My Bookings';
$activeNav = 'bookings';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3>Booking History</h3>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <?php foreach ($statuses as $s): ?>
        <a href="?status=<?= e($s) ?>" class="d-btn d-btn-sm <?= $statusFilter === $s ? 'd-btn-primary' : 'd-btn-outline' ?>"><?= ucfirst(str_replace('_', ' ', $s)) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (!$bookings): ?>
    <div class="empty-state">
      <div class="icon">🗂️</div>
      <p>No bookings found for this filter.</p>
      <a href="<?= base_url('client/book-session.php') ?>" class="d-btn d-btn-gold" style="margin-top:14px;">Book a Session</a>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Package</th><th>Date</th><th>Time</th><th>Location</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
          <tr>
            <td><?= e($b['package_name']) ?></td>
            <td><?= pretty_date($b['session_date']) ?></td>
            <td><?= pretty_time($b['start_time']) ?></td>
            <td><?= e($b['location'] ?: '—') ?></td>
            <td><?= status_badge($b['status']) ?></td>
            <td><a href="<?= base_url('client/booking-detail.php?id=' . $b['id']) ?>" class="d-btn d-btn-outline d-btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
