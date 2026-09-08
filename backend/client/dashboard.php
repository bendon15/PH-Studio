<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$clientId = (int) $user['id'];

$upcoming = db()->prepare('SELECT b.*, pk.name AS package_name FROM bookings b
                            JOIN packages pk ON pk.id = b.package_id
                            WHERE b.client_id = ? AND b.session_date >= CURDATE() AND b.status NOT IN ("cancelled","completed")
                            ORDER BY b.session_date ASC LIMIT 1');
$upcoming->execute([$clientId]);
$nextBooking = $upcoming->fetch();

$counts = db()->prepare('SELECT status, COUNT(*) AS c FROM bookings WHERE client_id = ? GROUP BY status');
$counts->execute([$clientId]);
$statusCounts = array_fill_keys(['pending','confirmed','in_progress','completed','cancelled'], 0);
foreach ($counts->fetchAll() as $row) { $statusCounts[$row['status']] = (int) $row['c']; }

$galleryCount = db()->prepare('SELECT COUNT(*) FROM galleries WHERE client_id = ? AND is_published = 1');
$galleryCount->execute([$clientId]);
$galleryCount = (int) $galleryCount->fetchColumn();

$balanceDue = db()->prepare('SELECT COALESCE(SUM(i.amount_total - i.amount_paid), 0) FROM invoices i
                              JOIN bookings b ON b.id = i.booking_id WHERE b.client_id = ? AND i.status != "paid"');
$balanceDue->execute([$clientId]);
$balanceDue = (float) $balanceDue->fetchColumn();

$recentBookings = db()->prepare('SELECT b.*, pk.name AS package_name FROM bookings b
                                  JOIN packages pk ON pk.id = b.package_id
                                  WHERE b.client_id = ? ORDER BY b.created_at DESC LIMIT 5');
$recentBookings->execute([$clientId]);
$recentBookings = $recentBookings->fetchAll();

$dashTitle = 'Welcome back, ' . explode(' ', $user['full_name'])[0];
$dashSubtitle = 'Here\'s what\'s happening with your PHStudio sessions.';
$activeNav = 'dashboard';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Upcoming Session</div>
    <div class="stat-value" style="font-size:18px;"><?= $nextBooking ? pretty_date($nextBooking['session_date']) : 'None Scheduled' ?></div>
    <div class="stat-delta"><?= $nextBooking ? e($nextBooking['package_name']) : 'Book your next session' ?></div>
    <div class="stat-icon">📅</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Active Bookings</div>
    <div class="stat-value"><?= $statusCounts['pending'] + $statusCounts['confirmed'] + $statusCounts['in_progress'] ?></div>
    <div class="stat-delta"><?= $statusCounts['pending'] ?> pending review</div>
    <div class="stat-icon">🗂️</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Published Galleries</div>
    <div class="stat-value"><?= $galleryCount ?></div>
    <div class="stat-delta">Ready to view</div>
    <div class="stat-icon">🖼️</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Balance Due</div>
    <div class="stat-value"><?= money($balanceDue) ?></div>
    <div class="stat-delta <?= $balanceDue > 0 ? 'down' : '' ?>"><?= $balanceDue > 0 ? 'Payment pending' : 'All settled' ?></div>
    <div class="stat-icon">🧾</div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>Recent Bookings</h3>
    <a href="<?= base_url('client/my-bookings.php') ?>" class="d-btn d-btn-outline d-btn-sm">View All</a>
  </div>
  <?php if (!$recentBookings): ?>
    <div class="empty-state">
      <div class="icon">📸</div>
      <p>You haven't booked a session yet.</p>
      <a href="<?= base_url('client/book-session.php') ?>" class="d-btn d-btn-gold" style="margin-top:14px;">Book Your First Session</a>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Package</th><th>Date</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($recentBookings as $b): ?>
          <tr>
            <td><?= e($b['package_name']) ?></td>
            <td><?= pretty_date($b['session_date']) ?></td>
            <td><?= status_badge($b['status']) ?></td>
            <td><a href="<?= base_url('client/booking-detail.php?id=' . $b['id']) ?>" class="d-btn d-btn-outline d-btn-sm">View</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<div class="panel">
  <div class="panel-head"><h3>Quick Actions</h3></div>
  <div class="grid-3" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
    <a href="<?= base_url('client/book-session.php') ?>" class="d-btn d-btn-primary d-btn-block" style="width:100%;">+ New Booking</a>
    <a href="<?= base_url('client/gallery.php') ?>" class="d-btn d-btn-outline" style="width:100%;">View My Galleries</a>
    <a href="<?= base_url('client/invoices.php') ?>" class="d-btn d-btn-outline" style="width:100%;">Check Invoices</a>
  </div>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
