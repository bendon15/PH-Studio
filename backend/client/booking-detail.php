<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$bookingId = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT b.*, pk.name AS package_name, pk.price, c.name AS category_name
                        FROM bookings b
                        JOIN packages pk ON pk.id = b.package_id
                        LEFT JOIN categories c ON c.id = b.category_id
                        WHERE b.id = ? AND b.client_id = ?');
$stmt->execute([$bookingId, $user['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    http_response_code(404);
    die('<h1>Booking not found</h1><a href="' . base_url('client/my-bookings.php') . '">Back to bookings</a>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf() && !empty($_POST['message'])) {
    $msg = clean($_POST['message']);
    if ($msg !== '') {
        db()->prepare('INSERT INTO booking_messages (booking_id, sender_id, message) VALUES (?, ?, ?)')
            ->execute([$bookingId, $user['id'], $msg]);
        redirect(base_url('client/booking-detail.php?id=' . $bookingId . '#messages'));
    }
}

$references = db()->prepare('SELECT * FROM booking_references WHERE booking_id = ?');
$references->execute([$bookingId]);
$references = $references->fetchAll();

$messages = db()->prepare('SELECT m.*, u.full_name, u.role FROM booking_messages m
                            JOIN users u ON u.id = m.sender_id
                            WHERE m.booking_id = ? ORDER BY m.created_at ASC');
$messages->execute([$bookingId]);
$messages = $messages->fetchAll();

$invoice = db()->prepare('SELECT * FROM invoices WHERE booking_id = ?');
$invoice->execute([$bookingId]);
$invoice = $invoice->fetch();

$successMsg = flash('success');

$dashTitle = 'Booking Details';
$activeNav = 'bookings';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php if ($successMsg): ?><div class="d-alert d-alert-success"><?= e($successMsg) ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3><?= e($booking['package_name']) ?> <?= status_badge($booking['status']) ?></h3>
    <a href="<?= base_url('client/my-bookings.php') ?>" class="d-btn d-btn-outline d-btn-sm">← Back to Bookings</a>
  </div>
  <div class="d-form-row">
    <div>
      <p style="color:#767a85;font-size:13px;margin-bottom:4px;">Category</p>
      <p style="font-weight:500;"><?= e($booking['category_name'] ?? '—') ?></p>
    </div>
    <div>
      <p style="color:#767a85;font-size:13px;margin-bottom:4px;">Date &amp; Time</p>
      <p style="font-weight:500;"><?= pretty_date($booking['session_date']) ?> at <?= pretty_time($booking['start_time']) ?></p>
    </div>
  </div>
  <div class="d-form-row" style="margin-top:14px;">
    <div>
      <p style="color:#767a85;font-size:13px;margin-bottom:4px;">Location</p>
      <p style="font-weight:500;"><?= e($booking['location'] ?: '—') ?></p>
    </div>
    <div>
      <p style="color:#767a85;font-size:13px;margin-bottom:4px;">Package Price</p>
      <p style="font-weight:500;"><?= money($booking['price']) ?></p>
    </div>
  </div>
  <div style="margin-top:14px;">
    <p style="color:#767a85;font-size:13px;margin-bottom:4px;">Event Details</p>
    <p><?= nl2br(e($booking['event_details'])) ?></p>
  </div>

  <?php if ($booking['admin_notes']): ?>
    <div class="d-alert d-alert-info" style="margin-top:18px;">
      <strong>Note from your photographer:</strong><br><?= nl2br(e($booking['admin_notes'])) ?>
    </div>
  <?php endif; ?>

  <?php if ($references): ?>
    <div style="margin-top:20px;">
      <p style="color:#767a85;font-size:13px;margin-bottom:8px;">Your Reference Images</p>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php foreach ($references as $ref): ?>
          <img src="<?= e($ref['file_path']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:8px;">
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php if ($invoice): ?>
<div class="panel">
  <div class="panel-head"><h3>Invoice</h3></div>
  <div class="d-form-row">
    <div><p style="color:#767a85;font-size:13px;">Invoice #</p><p style="font-weight:500;"><?= e($invoice['invoice_number']) ?></p></div>
    <div><p style="color:#767a85;font-size:13px;">Status</p><p><?= status_badge($invoice['status']) ?></p></div>
  </div>
  <div class="d-form-row" style="margin-top:14px;">
    <div><p style="color:#767a85;font-size:13px;">Total Amount</p><p style="font-weight:500;"><?= money($invoice['amount_total']) ?></p></div>
    <div><p style="color:#767a85;font-size:13px;">Amount Paid</p><p style="font-weight:500;"><?= money($invoice['amount_paid']) ?></p></div>
  </div>
</div>
<?php endif; ?>

<div class="panel" id="messages">
  <div class="panel-head"><h3>Messages &amp; Notes</h3></div>
  <div style="max-height:340px;overflow-y:auto;margin-bottom:20px;">
    <?php if (!$messages): ?>
      <p style="color:#767a85;">No messages yet. Send a note to your photographer below.</p>
    <?php endif; ?>
    <?php foreach ($messages as $m): $isMe = $m['role'] === 'client'; ?>
      <div style="display:flex;<?= $isMe ? 'justify-content:flex-end;' : '' ?>margin-bottom:12px;">
        <div style="max-width:70%;background:<?= $isMe ? '#14151a' : '#f0f2f5' ?>;color:<?= $isMe ? '#fff' : '#23252b' ?>;padding:10px 14px;border-radius:12px;">
          <div style="font-size:12px;opacity:0.7;margin-bottom:3px;"><?= e($m['full_name']) ?> · <?= pretty_date($m['created_at'], 'M j, g:ia') ?></div>
          <div><?= nl2br(e($m['message'])) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <form method="post">
    <?= csrf_field() ?>
    <div class="d-form-group">
      <textarea class="d-form-control" name="message" rows="3" placeholder="Write a message to your photographer..." required></textarea>
    </div>
    <button type="submit" class="d-btn d-btn-primary">Send Message</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
