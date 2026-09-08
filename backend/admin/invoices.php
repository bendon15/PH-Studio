<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf() && ($_POST['action'] ?? '') === 'record_payment') {
    $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);

    $stmt = db()->prepare('SELECT * FROM invoices WHERE id = ?');
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch();

    if ($invoice && $amount > 0) {
        $newPaid = min($invoice['amount_total'], $invoice['amount_paid'] + $amount);
        $status = $newPaid >= $invoice['amount_total'] ? 'paid' : 'partial';
        $paidAt = $status === 'paid' ? date('Y-m-d H:i:s') : $invoice['paid_at'];
        db()->prepare('UPDATE invoices SET amount_paid = ?, status = ?, paid_at = ? WHERE id = ?')
            ->execute([$newPaid, $status, $paidAt, $invoiceId]);
    }
    redirect(base_url('admin/invoices.php'));
}

$statusFilter = clean($_GET['status'] ?? 'all');
$sql = 'SELECT i.*, u.full_name AS client_name, pk.name AS package_name, b.session_date
        FROM invoices i
        JOIN bookings b ON b.id = i.booking_id
        JOIN users u ON u.id = b.client_id
        JOIN packages pk ON pk.id = b.package_id
        WHERE 1=1';
$params = [];
if ($statusFilter !== 'all') { $sql .= ' AND i.status = ?'; $params[] = $statusFilter; }
$sql .= ' ORDER BY i.issued_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$invoices = $stmt->fetchAll();

$totalRevenue = (float) db()->query('SELECT COALESCE(SUM(amount_paid),0) FROM invoices')->fetchColumn();
$totalOutstanding = (float) db()->query('SELECT COALESCE(SUM(amount_total-amount_paid),0) FROM invoices WHERE status != "paid"')->fetchColumn();

$dashTitle = 'Invoices & Payments';
$activeNav = 'invoices';
require __DIR__ . '/../includes/dash_header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-label">Total Collected</div>
    <div class="stat-value" style="font-size:24px;"><?= money($totalRevenue) ?></div>
    <div class="stat-icon">💰</div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Outstanding Balance</div>
    <div class="stat-value" style="font-size:24px;"><?= money($totalOutstanding) ?></div>
    <div class="stat-icon">⏳</div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <h3>All Invoices</h3>
    <div style="display:flex;gap:8px;">
      <?php foreach (['all','unpaid','partial','paid','refunded'] as $s): ?>
        <a href="?status=<?= $s ?>" class="d-btn d-btn-sm <?= $statusFilter === $s ? 'd-btn-primary' : 'd-btn-outline' ?>"><?= ucfirst($s) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if (!$invoices): ?>
    <div class="empty-state"><div class="icon">🧾</div><p>No invoices found.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Invoice #</th><th>Client</th><th>Package</th><th>Total</th><th>Paid</th><th>Status</th><th>Record Payment</th></tr></thead>
        <tbody>
          <?php foreach ($invoices as $inv): ?>
          <tr>
            <td><?= e($inv['invoice_number']) ?></td>
            <td><?= e($inv['client_name']) ?></td>
            <td><?= e($inv['package_name']) ?></td>
            <td><?= money($inv['amount_total']) ?></td>
            <td><?= money($inv['amount_paid']) ?></td>
            <td><?= status_badge($inv['status']) ?></td>
            <td>
              <?php if ($inv['status'] !== 'paid'): ?>
              <form method="post" style="display:flex;gap:6px;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="record_payment">
                <input type="hidden" name="invoice_id" value="<?= $inv['id'] ?>">
                <input class="d-form-control d-btn-sm" style="width:100px;" type="number" step="0.01" name="amount" placeholder="Amount" required>
                <button type="submit" class="d-btn d-btn-gold d-btn-sm">Add</button>
              </form>
              <?php else: ?>—<?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
