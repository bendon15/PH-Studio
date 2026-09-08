<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('client');

$user = current_user();
$errors = [];

$packages = db()->query('SELECT pk.*, c.name AS category_name, c.id AS cat_id FROM packages pk
                          LEFT JOIN categories c ON c.id = pk.category_id
                          WHERE pk.is_active = 1 ORDER BY pk.category_id, pk.sort_order')->fetchAll();

$photographer = db()->query('SELECT id, full_name FROM users WHERE role = "photographer" AND is_active = 1 LIMIT 1')->fetch();
$photographerId = $photographer['id'] ?? null;

$preselectedPackage = (int) ($_GET['package'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $packageId    = (int) ($_POST['package_id'] ?? 0);
        $sessionDate  = clean($_POST['session_date'] ?? '');
        $startTime    = clean($_POST['start_time'] ?? '');
        $location     = clean($_POST['location'] ?? '');
        $eventDetails = clean($_POST['event_details'] ?? '');

        $pkgStmt = db()->prepare('SELECT * FROM packages WHERE id = ? AND is_active = 1');
        $pkgStmt->execute([$packageId]);
        $selectedPackage = $pkgStmt->fetch();

        if (!$selectedPackage) $errors[] = 'Please select a valid package.';
        if (!$sessionDate || strtotime($sessionDate) < strtotime('today')) $errors[] = 'Please choose a valid future date.';
        if (!$startTime) $errors[] = 'Please choose an available time slot.';
        if (mb_strlen($eventDetails) < 5) $errors[] = 'Please tell us a bit more about your session.';

        if (!$errors && $photographerId && is_slot_taken((int) $photographerId, $sessionDate, $startTime)) {
            $errors[] = 'That time slot was just booked by someone else. Please pick another.';
        }

        if (!$errors) {
            $duration = (float) $selectedPackage['duration_hours'] ?: 1;
            $endTime = date('H:i:s', strtotime($startTime) + $duration * 3600);

            $stmt = db()->prepare('INSERT INTO bookings
                (client_id, photographer_id, package_id, category_id, session_date, start_time, end_time, location, event_details, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "pending")');
            $stmt->execute([
                $user['id'], $photographerId, $selectedPackage['id'], $selectedPackage['category_id'],
                $sessionDate, $startTime, $endTime, $location, $eventDetails,
            ]);
            $bookingId = (int) db()->lastInsertId();

            db()->prepare('INSERT INTO booking_status_history (booking_id, old_status, new_status, changed_by) VALUES (?, NULL, "pending", ?)')
                ->execute([$bookingId, $user['id']]);

            // Reference image uploads (optional, multiple)
            if (!empty($_FILES['reference_images']['name'][0])) {
                $count = count($_FILES['reference_images']['name']);
                for ($i = 0; $i < $count; $i++) {
                    if ($_FILES['reference_images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                    $file = [
                        'name'     => $_FILES['reference_images']['name'][$i],
                        'type'     => $_FILES['reference_images']['type'][$i],
                        'tmp_name' => $_FILES['reference_images']['tmp_name'][$i],
                        'error'    => $_FILES['reference_images']['error'][$i],
                        'size'     => $_FILES['reference_images']['size'][$i],
                    ];
                    try {
                        $path = handle_image_upload($file, 'references');
                        if ($path) {
                            db()->prepare('INSERT INTO booking_references (booking_id, file_path) VALUES (?, ?)')
                                ->execute([$bookingId, $path]);
                        }
                    } catch (RuntimeException $e) {
                        // Skip invalid files silently in demo; log in production.
                    }
                }
            }

            // Auto-generate a draft invoice so the client sees payment status immediately.
            db()->prepare('INSERT INTO invoices (booking_id, invoice_number, amount_total, amount_paid, status, due_date)
                            VALUES (?, ?, ?, 0, "unpaid", ?)')
                ->execute([$bookingId, generate_invoice_number(), $selectedPackage['price'], date('Y-m-d', strtotime($sessionDate . ' -3 days'))]);

            flash('success', 'Your booking request has been submitted! We will confirm shortly.');
            redirect(base_url('client/booking-detail.php?id=' . $bookingId));
        }
    }
}

$dashTitle = 'Book a Session';
$activeNav = 'book';
$extraScripts = '<script>window.APP_BASE_URL = "' . base_url('') . '";</script><script src="' . base_url('assets/js/booking.js') . '"></script>';
require __DIR__ . '/../includes/dash_header.php';
?>

<style>
.booking-step { display: none; }
.booking-step.active { display: block; }
.step-dots { display:flex; gap:10px; margin-bottom:28px; }
.step-dot { width:32px; height:32px; border-radius:50%; background:#eceef1; color:#8a8d97; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; }
.step-dot.active { background: var(--d-gold); color:#14151a; }
.step-dot.done { background:#e4f7e9; color:#1f8a44; }
.package-select-card { border:1px solid var(--d-border); border-radius:10px; padding:18px; cursor:pointer; transition: all 0.2s ease; }
.package-select-card:hover { border-color: var(--d-gold); }
.package-select-card.selected { border-color: var(--d-gold); background: #fff8ec; }
.form-hint { font-size:12.5px; margin-top:6px; color:#767a85; }
.form-hint.success { color:#1f8a44; }
.form-hint.error { color:#b23434; }
.invalid { border-color:#d1495b !important; }
</style>

<?php foreach ($errors as $err): ?><div class="d-alert d-alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="step-dots">
    <div class="step-dot active">1</div>
    <div class="step-dot">2</div>
    <div class="step-dot">3</div>
    <div class="step-dot">4</div>
  </div>

  <form method="post" enctype="multipart/form-data" id="bookingForm">
    <?= csrf_field() ?>
    <input type="hidden" name="package_id" id="package_id" value="<?= e((string) $preselectedPackage) ?>">

    <!-- STEP 1: Package -->
    <div class="booking-step active">
      <h3 style="margin-bottom:6px;">Choose Your Package</h3>
      <p style="color:#767a85;margin-bottom:20px;font-size:14px;">Select the session type that fits your occasion.</p>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;">
        <?php foreach ($packages as $pkg): ?>
          <div class="package-select-card <?= $preselectedPackage === (int) $pkg['id'] ? 'selected' : '' ?>" data-package-id="<?= (int) $pkg['id'] ?>">
            <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.05em;color:var(--d-gold);margin-bottom:6px;"><?= e($pkg['category_name'] ?? 'General') ?></div>
            <h4 style="margin-bottom:8px;"><?= e($pkg['name']) ?></h4>
            <div style="font-weight:600;font-size:18px;"><?= money($pkg['price']) ?></div>
            <div style="color:#767a85;font-size:13px;margin-top:4px;"><?= (float) $pkg['duration_hours'] ?> hour(s)</div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top:24px;"><button type="button" class="d-btn d-btn-primary" data-next>Continue</button></div>
    </div>

    <!-- STEP 2: Date & Time -->
    <div class="booking-step">
      <h3 style="margin-bottom:6px;">Pick a Date &amp; Time</h3>
      <p style="color:#767a85;margin-bottom:20px;font-size:14px;">Availability updates live based on the photographer's calendar.</p>
      <div class="d-form-row">
        <div class="d-form-group">
          <label for="session_date">Session Date</label>
          <input class="d-form-control" type="date" id="session_date" name="session_date" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="d-form-group">
          <label for="start_time">Available Time Slots</label>
          <select class="d-form-control" id="start_time" name="start_time" required>
            <option value="">Select a date first</option>
          </select>
          <div class="form-hint" id="availabilityMsg"></div>
        </div>
      </div>
      <div class="d-form-group">
        <label for="location">Session Location</label>
        <input class="d-form-control" type="text" id="location" name="location" placeholder="e.g. Tagaytay Wedding Garden, or your studio preference" required>
      </div>
      <div style="margin-top:10px;display:flex;gap:10px;">
        <button type="button" class="d-btn d-btn-outline" data-prev>Back</button>
        <button type="button" class="d-btn d-btn-primary" data-next>Continue</button>
      </div>
    </div>

    <!-- STEP 3: Event Details + References -->
    <div class="booking-step">
      <h3 style="margin-bottom:6px;">Tell Us About Your Session</h3>
      <p style="color:#767a85;margin-bottom:20px;font-size:14px;">The more detail, the better we can prepare.</p>
      <div class="d-form-group">
        <label for="event_details">Event Details</label>
        <textarea class="d-form-control" id="event_details" name="event_details" rows="5" required placeholder="Tell us about the occasion, number of guests, style preferences, etc."></textarea>
      </div>
      <div class="d-form-group">
        <label for="reference_images">Reference / Inspiration Images (optional)</label>
        <input class="d-form-control" type="file" id="reference_images" name="reference_images[]" accept="image/*" multiple>
        <div class="form-hint">JPG, PNG, or WEBP. Max 5MB each.</div>
      </div>
      <div style="margin-top:10px;display:flex;gap:10px;">
        <button type="button" class="d-btn d-btn-outline" data-prev>Back</button>
        <button type="button" class="d-btn d-btn-primary" data-next>Review</button>
      </div>
    </div>

    <!-- STEP 4: Review -->
    <div class="booking-step">
      <h3 style="margin-bottom:6px;">Review &amp; Submit</h3>
      <p style="color:#767a85;margin-bottom:20px;font-size:14px;">Double-check your details, then submit your booking request. Our team will confirm within 24 hours.</p>
      <div class="d-alert d-alert-info">Your booking status will start as <strong>Pending</strong> until confirmed by the photographer.</div>
      <div style="margin-top:10px;display:flex;gap:10px;">
        <button type="button" class="d-btn d-btn-outline" data-prev>Back</button>
        <button type="submit" class="d-btn d-btn-gold">Submit Booking Request</button>
      </div>
    </div>
  </form>
</div>

<script>
document.querySelectorAll('.package-select-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('.package-select-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('package_id').value = card.dataset.packageId;
  });
});
</script>

<?php require __DIR__ . '/../includes/dash_footer.php'; ?>
