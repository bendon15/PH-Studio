<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$totalClients = (int) db()->query('SELECT COUNT(*) FROM users WHERE role = "client"')->fetchColumn();
$totalBookings = (int) db()->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$pendingBookings = (int) db()->query('SELECT COUNT(*) FROM bookings WHERE status = "pending"')->fetchColumn();
$upcomingBookings = (int) db()->query('SELECT COUNT(*) FROM bookings WHERE session_date >= CURDATE() AND status IN ("pending","confirmed")')->fetchColumn();

$revenue = (float) db()->query('SELECT COALESCE(SUM(amount_paid), 0) FROM invoices')->fetchColumn();
$outstanding = (float) db()->query('SELECT COALESCE(SUM(amount_total - amount_paid), 0) FROM invoices WHERE status != "paid"')->fetchColumn();

$statusBreakdown = db()->query('SELECT status, COUNT(*) AS c FROM bookings GROUP BY status')->fetchAll();
$statusMap = array_fill_keys(['pending','confirmed','in_progress','completed','cancelled'], 0);
foreach ($statusBreakdown as $row) { $statusMap[$row['status']] = (int) $row['c']; }

$recentBookings = db()->query('SELECT b.*, u.full_name AS client_name, pk.name AS package_name
                                FROM bookings b
                                JOIN users u ON u.id = b.client_id
                                JOIN packages pk ON pk.id = b.package_id
                                ORDER BY b.created_at DESC LIMIT 6')->fetchAll();

$upcomingList = db()->query('SELECT b.*, u.full_name AS client_name, pk.name AS package_name
                              FROM bookings b
                              JOIN users u ON u.id = b.client_id
                              JOIN packages pk ON pk.id = b.package_id
                              WHERE b.session_date >= CURDATE() AND b.status IN ("pending","confirmed")
                              ORDER BY b.session_date ASC LIMIT 5')->fetchAll();

$dashTitle = 'Studio Dashboard';
$dashSubtitle = 'Overview of bookings, clients, and revenue.';
$activeNav = 'dashboard';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Total Clients</div>
    <div class="stat-value"><?= $totalClients ?></div>
    <div class="stat-delta">Registered accounts</div>
    <div class="stat-icon">👥</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Bookings</div>
    <div class="stat-value"><?= $totalBookings ?></div>
    <div class="stat-delta"><?= $pendingBookings ?> awaiting review</div>
    <div class="stat-icon">📅</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Upcoming Sessions</div>
    <div class="stat-value"><?= $upcomingBookings ?></div>
    <div class="stat-delta">Next 90 days</div>
    <div class="stat-icon">🗓️</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Revenue Collected</div>
    <div class="stat-value" style="font-size:24px;"><?= money($revenue) ?></div>
    <div class="stat-delta <?= $outstanding > 0 ? 'down' : '' ?>"><?= money($outstanding) ?> outstanding</div>
    <div class="stat-icon">💰</div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Booking Status Breakdown</h3></div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;">
    <?php foreach ($statusMap as $status => $count): ?>
      <div style="text-align:center;padding:18px;border:1px solid var(--d-border);border-radius:10px;">
        <div style="font-size:26px;font-weight:600;"><?= $count ?></div>
        <div style="margin-top:6px;"><?= status_badge($status) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="d-form-row" style="display:grid;grid-template-columns:1.3fr 1fr;gap:24px;align-items:start;">
  <div class="panel">
    <div class="panel-head">
      <h3>Recent Bookings</h3>
      <a href="<?= base_url('admin/bookings.php') ?>" class="d-btn d-btn-outline d-btn-sm">Manage All</a>
    </div>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Client</th><th>Package</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($recentBookings as $b): ?>
          <tr>
            <td><a href="<?= base_url('admin/booking-detail.php?id=' . $b['id']) ?>"><?= e($b['client_name']) ?></a></td>
            <td><?= e($b['package_name']) ?></td>
            <td><?= pretty_date($b['session_date']) ?></td>
            <td><?= status_badge($b['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Upcoming Sessions</h3></div>
    <?php if (!$upcomingList): ?>
      <p style="color:var(--d-muted);font-size:14px;">No upcoming sessions scheduled.</p>
    <?php endif; ?>
    <?php foreach ($upcomingList as $b): ?>
      <div style="padding:12px 0;border-bottom:1px solid var(--d-border);">
        <div style="font-weight:500;font-size:14px;"><?= e($b['client_name']) ?> — <?= e($b['package_name']) ?></div>
        <div style="color:var(--d-muted);font-size:12.5px;margin-top:3px;"><?= pretty_date($b['session_date']) ?> · <?= pretty_time($b['start_time']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
