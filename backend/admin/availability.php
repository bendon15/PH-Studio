<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin', 'photographer');

$user = current_user();
// In this single-studio demo, admins manage the primary photographer's calendar.
$photographer = has_role('photographer') ? $user : db()->query('SELECT id, full_name FROM users WHERE role="photographer" LIMIT 1')->fetch();
$photographerId = $photographer['id'];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_slot') {
        $date  = clean($_POST['slot_date'] ?? '');
        $start = clean($_POST['start_time'] ?? '');
        $end   = clean($_POST['end_time'] ?? '');
        $blocked = isset($_POST['is_blocked']) ? 1 : 0;

        if (!$date || !$start || !$end) {
            $errors[] = 'Please fill in all fields.';
        } else {
            try {
                $stmt = db()->prepare('INSERT INTO availability_slots (photographer_id, slot_date, start_time, end_time, is_blocked) VALUES (?,?,?,?,?)
                                        ON DUPLICATE KEY UPDATE end_time = VALUES(end_time), is_blocked = VALUES(is_blocked)');
                $stmt->execute([$photographerId, $date, $start, $end, $blocked]);
            } catch (PDOException $e) {
                $errors[] = 'Could not save this slot.';
            }
        }
    } elseif ($action === 'delete_slot') {
        db()->prepare('DELETE FROM availability_slots WHERE id = ? AND photographer_id = ?')->execute([(int) $_POST['id'], $photographerId]);
    }
}

$slots = db()->prepare('SELECT * FROM availability_slots WHERE photographer_id = ? AND slot_date >= CURDATE() ORDER BY slot_date, start_time');
$slots->execute([$photographerId]);
$slots = $slots->fetchAll();

$bookedDates = db()->prepare('SELECT session_date, start_time FROM bookings WHERE photographer_id = ? AND session_date >= CURDATE() AND status != "cancelled"');
$bookedDates->execute([$photographerId]);
$bookedDates = $bookedDates->fetchAll();

$dashTitle = 'Availability';
$dashSubtitle = 'Manage ' . e($photographer['full_name'] ?? 'the studio') . '\'s working calendar.';
$activeNav = 'availability';
require __DIR__ . '/../includes/dash_header.php';
?>

<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Add Availability Window</h3></div>
  <form method="post" class="d-form-row" style="align-items:end;">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_slot">
    <div class="d-form-group">
      <label>Date</label>
      <input class="d-form-control" type="date" name="slot_date" min="<?= date('Y-m-d') ?>" required>
    </div>
    <div class="d-form-group">
      <label>Start Time</label>
      <input class="d-form-control" type="time" name="start_time" required>
    </div>
    <div class="d-form-group">
      <label>End Time</label>
      <input class="d-form-control" type="time" name="end_time" required>
    </div>
    <div class="d-form-group" style="display:flex;align-items:center;gap:8px;">
      <label style="display:flex;align-items:center;gap:6px;margin:0;"><input type="checkbox" name="is_blocked"> Mark as Blocked (unavailable)</label>
    </div>
    <div class="d-form-group"><button type="submit" class="d-btn d-btn-primary">Save Slot</button></div>
  </form>
</div>

<div class="panel">
  <div class="panel-head"><h3>Upcoming Calendar</h3></div>
  <?php if (!$slots): ?>
    <div class="empty-state"><div class="icon">🗓️</div><p>No availability windows scheduled yet.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="d-table">
        <thead><tr><th>Date</th><th>Time Window</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($slots as $slot):
            $isBooked = false;
            foreach ($bookedDates as $bd) {
              if ($bd['session_date'] === $slot['slot_date'] && $bd['start_time'] >= $slot['start_time'] && $bd['start_time'] < $slot['end_time']) { $isBooked = true; break; }
            }
          ?>
          <tr>
            <td><?= pretty_date($slot['slot_date']) ?></td>
            <td><?= pretty_time($slot['start_time']) ?> – <?= pretty_time($slot['end_time']) ?></td>
            <td>
              <?php if ($slot['is_blocked']): ?><span class="badge badge-cancelled">Blocked</span>
              <?php elseif ($isBooked): ?><span class="badge badge-pending">Has Booking(s)</span>
              <?php else: ?><span class="badge badge-completed">Open</span><?php endif; ?>
            </td>
            <td>
              <form method="post" onsubmit="return confirm('Remove this slot?');">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete_slot">
                <input type="hidden" name="id" value="<?= $slot['id'] ?>">
                <button type="submit" class="d-btn d-btn-danger d-btn-sm">Remove</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
