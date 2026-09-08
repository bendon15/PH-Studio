<?php
/**
 * PHStudio — Shared Helper Functions
 * Portfolio Demo — Not a real photography booking service.
 */

require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------
// Security / sanitation helpers
// ---------------------------------------------------------------------
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function clean(?string $value): string
{
    return trim(strip_tags($value ?? ''));
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ---------------------------------------------------------------------
// Auth helpers
// ---------------------------------------------------------------------
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function has_role(string ...$roles): bool
{
    $user = current_user();
    return $user && in_array($user['role'], $roles, true);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect(base_url('auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '')));
    }
}

function require_role(string ...$roles): void
{
    require_login();
    if (!has_role(...$roles)) {
        http_response_code(403);
        die('<h1>403 — Access denied</h1><p>You do not have permission to view this page.</p>');
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    unset($user['password_hash']);
    $_SESSION['user'] = $user;
}

function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}

// ---------------------------------------------------------------------
// Navigation / URL helpers
// ---------------------------------------------------------------------
function base_url(string $path = ''): string
{
    return APP_URL . '/' . ltrim($path, '/');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function current_path(): string
{
    return parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
}

// ---------------------------------------------------------------------
// Formatting helpers
// ---------------------------------------------------------------------
function money(float $amount): string
{
    return '₱' . number_format($amount, 2);
}

function pretty_date(string $date, string $format = 'M j, Y'): string
{
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : $date;
}

function pretty_time(string $time): string
{
    $ts = strtotime($time);
    return $ts ? date('g:i A', $ts) : $time;
}

function status_badge(string $status): string
{
    $map = [
        'pending'     => 'badge badge-pending',
        'confirmed'   => 'badge badge-confirmed',
        'in_progress' => 'badge badge-progress',
        'completed'   => 'badge badge-completed',
        'cancelled'   => 'badge badge-cancelled',
        'unpaid'      => 'badge badge-pending',
        'partial'     => 'badge badge-progress',
        'paid'        => 'badge badge-completed',
        'refunded'    => 'badge badge-cancelled',
    ];
    $class = $map[$status] ?? 'badge';
    $label = ucwords(str_replace('_', ' ', $status));
    return '<span class="' . $class . '">' . e($label) . '</span>';
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

// ---------------------------------------------------------------------
// File upload helper
// ---------------------------------------------------------------------
function handle_image_upload(array $file, string $subfolder): ?string
{
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed with error code ' . $file['error']);
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('File exceeds the 5MB upload limit.');
    }

    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES, true)) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName   = bin2hex(random_bytes(12)) . '.' . strtolower($ext);
    $targetDir  = rtrim(UPLOAD_DIR, '/') . '/' . trim($subfolder, '/');

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetPath = $targetDir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Could not save uploaded file.');
    }

    return trim($subfolder, '/') . '/' . $safeName;
}

// ---------------------------------------------------------------------
// Booking helpers
// ---------------------------------------------------------------------
function is_slot_taken(int $photographerId, string $date, string $start, ?int $excludeBookingId = null): bool
{
    $sql = 'SELECT COUNT(*) FROM bookings
            WHERE photographer_id = :pid AND session_date = :d AND start_time = :s
              AND status NOT IN ("cancelled")';
    $params = ['pid' => $photographerId, 'd' => $date, 's' => $start];

    if ($excludeBookingId) {
        $sql .= ' AND id != :bid';
        $params['bid'] = $excludeBookingId;
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}

function generate_share_token(): string
{
    return 'gal_' . bin2hex(random_bytes(8));
}

function generate_invoice_number(): string
{
    return 'INV-' . date('Y') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
}
