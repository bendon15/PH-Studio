<?php
/**
 * GET /api/check-availability.php?date=YYYY-MM-DD
 * Returns open time slots for the studio's photographer on a given date,
 * excluding slots already booked or marked unavailable.
 */
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$date = clean($_GET['date'] ?? '');

if (!$date || !strtotime($date)) {
    echo json_encode(['slots' => []]);
    exit;
}

$photographer = db()->query('SELECT id FROM users WHERE role = "photographer" AND is_active = 1 LIMIT 1')->fetch();
if (!$photographer) {
    echo json_encode(['slots' => []]);
    exit;
}
$photographerId = $photographer['id'];

// Pull defined availability windows for the date that are not blocked.
$stmt = db()->prepare('SELECT * FROM availability_slots WHERE photographer_id = ? AND slot_date = ? AND is_blocked = 0');
$stmt->execute([$photographerId, $date]);
$windows = $stmt->fetchAll();

// Pull already-booked start times for the date.
$bookedStmt = db()->prepare('SELECT start_time FROM bookings WHERE photographer_id = ? AND session_date = ? AND status != "cancelled"');
$bookedStmt->execute([$photographerId, $date]);
$booked = array_column($bookedStmt->fetchAll(), 'start_time');

$slots = [];

foreach ($windows as $window) {
    // Break each availability window into 1-hour bookable slots.
    $cursor = strtotime($window['start_time']);
    $end    = strtotime($window['end_time']);

    while ($cursor + 3600 <= $end) {
        $startStr = date('H:i:s', $cursor);
        if (!in_array($startStr, $booked, true)) {
            $slots[] = [
                'start_time'  => $startStr,
                'end_time'    => date('H:i:s', $cursor + 3600),
                'start_label' => date('g:i A', $cursor),
                'end_label'   => date('g:i A', $cursor + 3600),
            ];
        }
        $cursor += 3600;
    }
}

// If no availability rows exist for that date at all (demo fallback), offer a
// sensible default 9am-5pm range so the demo always feels interactive.
if (!$windows) {
    for ($h = 9; $h < 17; $h++) {
        $startStr = sprintf('%02d:00:00', $h);
        if (!in_array($startStr, $booked, true)) {
            $slots[] = [
                'start_time'  => $startStr,
                'end_time'    => sprintf('%02d:00:00', $h + 1),
                'start_label' => date('g:i A', strtotime($startStr)),
                'end_label'   => date('g:i A', strtotime(sprintf('%02d:00:00', $h + 1))),
            ];
        }
    }
}

echo json_encode(['slots' => $slots]);
