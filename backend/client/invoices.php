<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$stmt = db()->prepare('SELECT i.*, pk.name AS package_name, b.session_date FROM invoices i
                        JOIN bookings b ON b.id = i.booking_id
                        JOIN packages pk ON pk.id = b.package_id
                        WHERE b.client_id = ? ORDER BY i.issued_at DESC');
$stmt->execute([$user['id']]);
$invoices = $stmt->fetchAll();

$dashTitle = 'Invoices & Payments';
$activeNav = 'invoices';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="panel">
  <div class="panel-head"><h3>Your Invoices</h3></div>
  <?php if (!$invoices): ?>
    <div class="empty-state">
      <div class="icon">🧾</div>
      <p>No invoices yet.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Invoice #</th><th>Package</th><th>Session Date</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($invoices as $inv): ?>
          <tr>
            <td><?= e($inv['invoice_number']) ?></td>
            <td><?= e($inv['package_name']) ?></td>
            <td><?= pretty_date($inv['session_date']) ?></td>
            <td><?= money($inv['amount_total']) ?></td>
            <td><?= money($inv['amount_paid']) ?></td>
            <td><?= status_badge($inv['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
