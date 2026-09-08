<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$bookingsByCategory = db()->query('SELECT c.name, COUNT(b.id) AS total FROM categories c
                                    LEFT JOIN bookings b ON b.category_id = c.id
                                    GROUP BY c.id ORDER BY total DESC')->fetchAll();

$revenueByMonth = db()->query('SELECT DATE_FORMAT(i.issued_at, "%Y-%m") AS ym, SUM(i.amount_paid) AS total
                                FROM invoices i GROUP BY ym ORDER BY ym DESC LIMIT 6')->fetchAll();

$topPackages = db()->query('SELECT pk.name, COUNT(b.id) AS bookings_count FROM packages pk
                             LEFT JOIN bookings b ON b.package_id = pk.id
                             GROUP BY pk.id ORDER BY bookings_count DESC LIMIT 6')->fetchAll();

$completionRate = db()->query('SELECT
    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) AS completed,
    SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) AS cancelled,
    COUNT(*) AS total
    FROM bookings')->fetch();

$dashTitle = 'Reports';
$dashSubtitle = 'Basic performance overview for the studio.';
$activeNav = 'reports';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Completion Rate</div>
    <div class="stat-value"><?= $completionRate['total'] > 0 ? round(($completionRate['completed'] / $completionRate['total']) * 100) : 0 ?>%</div>
    <div class="stat-delta"><?= (int) $completionRate['completed'] ?> of <?= (int) $completionRate['total'] ?> bookings completed</div>
    <div class="stat-icon">✅</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Cancellation Rate</div>
    <div class="stat-value"><?= $completionRate['total'] > 0 ? round(($completionRate['cancelled'] / $completionRate['total']) * 100) : 0 ?>%</div>
    <div class="stat-delta down"><?= (int) $completionRate['cancelled'] ?> cancelled</div>
    <div class="stat-icon">❌</div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Bookings by Category</h3></div>
  <div class="table-wrap">
    <table class="d-table">
      <thead><tr><th>Category</th><th>Bookings</th></tr></thead>
      <tbody>
        <?php foreach ($bookingsByCategory as $row): ?>
        <tr><td><?= e($row['name']) ?></td><td><?= (int) $row['total'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Most Booked Packages</h3></div>
  <div class="table-wrap">
    <table class="d-table">
      <thead><tr><th>Package</th><th>Bookings</th></tr></thead>
      <tbody>
        <?php foreach ($topPackages as $row): ?>
        <tr><td><?= e($row['name']) ?></td><td><?= (int) $row['bookings_count'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Revenue by Month</h3></div>
  <div class="table-wrap">
    <table class="d-table">
      <thead><tr><th>Month</th><th>Revenue Collected</th></tr></thead>
      <tbody>
        <?php foreach ($revenueByMonth as $row): ?>
        <tr><td><?= e(date('F Y', strtotime($row['ym'] . '-01'))) ?></td><td><?= money((float) $row['total']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$revenueByMonth): ?><tr><td colspan="2" style="color:var(--d-muted);">No revenue recorded yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
