<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$user = current_user();
$bookingId = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT b.*, u.full_name AS client_name, u.email AS client_email, u.phone AS client_phone,
                        pk.name AS package_name, pk.price, c.name AS category_name
                        FROM bookings b
                        JOIN users u ON u.id = b.client_id
                        JOIN packages pk ON pk.id = b.package_id
                        LEFT JOIN categories c ON c.id = b.category_id
                        WHERE b.id = ?');
$stmt->execute([$bookingId]);
$booking = $stmt->fetch();

if (!$booking) {
    http_response_code(404);
    die('<h1>Booking not found</h1><a href="' . base_url('admin/bookings.php') . '">Back to bookings</a>');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $newStatus = clean($_POST['status'] ?? '');
        $validStatuses = ['pending','confirmed','in_progress','completed','cancelled'];
        if (in_array($newStatus, $validStatuses, true) && $newStatus !== $booking['status']) {
            db()->prepare('UPDATE bookings SET status = ? WHERE id = ?')->execute([$newStatus, $bookingId]);
            db()->prepare('INSERT INTO booking_status_history (booking_id, old_status, new_status, changed_by) VALUES (?, ?, ?, ?)')
                ->execute([$bookingId, $booking['status'], $newStatus, $user['id']]);
            $booking['status'] = $newStatus;
        }
        $success = true;
    } elseif ($action === 'save_notes') {
        $notes = clean($_POST['admin_notes'] ?? '');
        db()->prepare('UPDATE bookings SET admin_notes = ? WHERE id = ?')->execute([$notes, $bookingId]);
        $booking['admin_notes'] = $notes;
        $success = true;
    } elseif ($action === 'send_message' && !empty($_POST['message'])) {
        db()->prepare('INSERT INTO booking_messages (booking_id, sender_id, message) VALUES (?, ?, ?)')
            ->execute([$bookingId, $user['id'], clean($_POST['message'])]);
        redirect(base_url('admin/booking-detail.php?id=' . $bookingId . '#messages'));
    }
}

$references = db()->prepare('SELECT * FROM booking_references WHERE booking_id = ?');
$references->execute([$bookingId]);
$references = $references->fetchAll();

$messages = db()->prepare('SELECT m.*, u.full_name, u.role FROM booking_messages m
                            JOIN users u ON u.id = m.sender_id WHERE m.booking_id = ? ORDER BY m.created_at ASC');
$messages->execute([$bookingId]);
$messages = $messages->fetchAll();

$history = db()->prepare('SELECT h.*, u.full_name FROM booking_status_history h
                           LEFT JOIN users u ON u.id = h.changed_by
                           WHERE h.booking_id = ? ORDER BY h.changed_at ASC');
$history->execute([$bookingId]);
$history = $history->fetchAll();

$dashTitle = 'Booking #' . $bookingId;
$activeNav = 'bookings';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php if ($success): ?><div class="d-alert d-alert-success">Booking updated.</div><?php endif; ?>

<div class="panel">
  <div class="panel-head">
    <h3><?= e($booking['client_name']) ?> — <?= e($booking['package_name']) ?></h3>
    <a href="<?= base_url('admin/bookings.php') ?>" class="d-btn d-btn-outline d-btn-sm">← Back to Bookings</a>
  </div>
  <div class="d-form-row">
    <div><p style="color:var(--d-muted);font-size:13px;">Client Email</p><p style="font-weight:500;"><?= e($booking['client_email']) ?></p></div>
    <div><p style="color:var(--d-muted);font-size:13px;">Client Phone</p><p style="font-weight:500;"><?= e($booking['client_phone'] ?: '—') ?></p></div>
  </div>
  <div class="d-form-row" style="margin-top:14px;">
    <div><p style="color:var(--d-muted);font-size:13px;">Date &amp; Time</p><p style="font-weight:500;"><?= pretty_date($booking['session_date']) ?> at <?= pretty_time($booking['start_time']) ?></p></div>
    <div><p style="color:var(--d-muted);font-size:13px;">Location</p><p style="font-weight:500;"><?= e($booking['location'] ?: '—') ?></p></div>
  </div>
  <div style="margin-top:14px;"><p style="color:var(--d-muted);font-size:13px;">Event Details</p><p><?= nl2br(e($booking['event_details'])) ?></p></div>

  <?php if ($references): ?>
    <div style="margin-top:18px;">
      <p style="color:var(--d-muted);font-size:13px;margin-bottom:8px;">Reference Images</p>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php foreach ($references as $ref): ?><img src="<?= e($ref['file_path']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:8px;"><?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<div class="panel">
  <div class="panel-head"><h3>Booking Status</h3></div>
  <form method="post" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="update_status">
    <div class="d-form-group" style="margin-bottom:0;min-width:220px;">
      <label>Status</label>
      <select class="d-form-control" name="status">
        <?php foreach (['pending','confirmed','in_progress','completed','cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $booking['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="d-btn d-btn-primary">Update Status</button>
  </form>

  <?php if ($history): ?>
    <div style="margin-top:22px;">
      <p style="color:var(--d-muted);font-size:13px;margin-bottom:10px;">Status History</p>
      <?php foreach ($history as $h): ?>
        <div style="font-size:13px;padding:8px 0;border-bottom:1px dashed var(--d-border);">
          <?= status_badge($h['new_status']) ?> by <?= e($h['full_name'] ?? 'Client') ?> — <?= pretty_date($h['changed_at'], 'M j, Y g:ia') ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div class="panel">
  <div class="panel-head"><h3>Notes to Client</h3></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save_notes">
    <div class="d-form-group">
      <textarea class="d-form-control" name="admin_notes" rows="4" placeholder="These notes are visible to the client on their booking page."><?= e($booking['admin_notes']) ?></textarea>
    </div>
    <button type="submit" class="d-btn d-btn-outline">Save Notes</button>
  </form>
</div>

<div class="panel" id="messages">
  <div class="panel-head"><h3>Messages</h3></div>
  <div style="max-height:320px;overflow-y:auto;margin-bottom:20px;">
    <?php foreach ($messages as $m): $isStaff = $m['role'] !== 'client'; ?>
      <div style="display:flex;<?= $isStaff ? 'justify-content:flex-end;' : '' ?>margin-bottom:12px;">
        <div style="max-width:70%;background:<?= $isStaff ? '#14151a' : '#f0f2f5' ?>;color:<?= $isStaff ? '#fff' : '#23252b' ?>;padding:10px 14px;border-radius:12px;">
          <div style="font-size:12px;opacity:0.7;margin-bottom:3px;"><?= e($m['full_name']) ?> · <?= pretty_date($m['created_at'], 'M j, g:ia') ?></div>
          <div><?= nl2br(e($m['message'])) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="send_message">
    <div class="d-form-group"><textarea class="d-form-control" name="message" rows="3" placeholder="Reply to client..." required></textarea></div>
    <button type="submit" class="d-btn d-btn-primary">Send Reply</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
