<?php
/**
 * Helper Functions
 */

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function formatDate(?string $date, string $format = 'M d, Y'): string
{
    if (!$date) return 'N/A';
    return date($format, strtotime($date));
}

function formatDateTime(?string $date): string
{
    return formatDate($date, 'M d, Y \a\t h:i A');
}

function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return formatDate($datetime);
}

function truncate(string $text, int $length = 150): string
{
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

function getCourseCategories(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT DISTINCT category FROM courses WHERE is_active = 1 ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getStats(PDO $pdo): array
{
    return [
        'users' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND is_active = 1")->fetchColumn(),
        'courses' => (int) $pdo->query("SELECT COUNT(*) FROM courses WHERE is_active = 1")->fetchColumn(),
        'mentors' => (int) $pdo->query("SELECT COUNT(*) FROM mentors WHERE is_available = 1")->fetchColumn(),
        'events' => (int) $pdo->query("SELECT COUNT(*) FROM events WHERE is_active = 1 AND event_date >= NOW()")->fetchColumn(),
        'scholarships' => (int) $pdo->query("SELECT COUNT(*) FROM scholarships WHERE is_active = 1 AND deadline >= CURDATE()")->fetchColumn(),
        'enrollments' => (int) $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn(),
    ];
}

function paginate(int $total, int $perPage, int $page): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    return ['page' => $page, 'per_page' => $perPage, 'offset' => $offset, 'total' => $total, 'total_pages' => $totalPages];
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/** Ensure upload directory exists */
function ensureUploadDir(string $subdir): string
{
    $dir = dirname(__DIR__) . '/uploads/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $index = $dir . '/index.html';
    if (!file_exists($index)) {
        file_put_contents($index, '<!-- Access denied -->');
    }
    return $dir;
}

/** Upload profile or mentor photo (max 2MB, JPG/PNG/WEBP) */
function uploadPhoto(array $file, string $subdir, ?string $oldFilename = null): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => null];
    }
    if (($file['error'] ?? 0) !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Photo upload failed. Please try again.'];
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$mime])) {
        return ['success' => false, 'error' => 'Only JPG, PNG, or WEBP images are allowed.'];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Photo must be 2MB or smaller.'];
    }

    $filename = uniqid('img_', true) . '.' . $allowed[$mime];
    $dir = ensureUploadDir($subdir);
    $dest = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Could not save the uploaded photo.'];
    }

    if ($oldFilename && $oldFilename !== 'default-avatar.png') {
        $oldPath = $dir . '/' . basename($oldFilename);
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    return ['success' => true, 'filename' => $filename];
}

/** Get public URL for uploaded photo */
function photoUrl(?string $filename, string $subdir): ?string
{
    if (!$filename || $filename === 'default-avatar.png') {
        return null;
    }
    $path = dirname(__DIR__) . '/uploads/' . $subdir . '/' . basename($filename);
    if (is_file($path)) {
        return SITE_URL . '/uploads/' . $subdir . '/' . rawurlencode(basename($filename));
    }
    return null;
}

/** Developer (Joy Meshi) profile photo on About page */
function developerPhotoUrl(): ?string
{
    $dir = ensureUploadDir('profiles');
    foreach (['developer.jpg', 'developer.png', 'developer.webp'] as $name) {
        if (is_file($dir . '/' . $name)) {
            return SITE_URL . '/uploads/profiles/' . $name;
        }
    }
    return null;
}

/** Render avatar with photo or initials fallback */
function renderAvatar(?string $filename, string $name, string $subdir = 'users', string $class = 'user-avatar'): string
{
    $url = photoUrl($filename, $subdir);
    if ($url) {
        return '<img src="' . e($url) . '" alt="' . e($name) . '" class="' . e($class) . ' profile-photo">';
    }
    $initial = strtoupper(substr(trim($name), 0, 1));
    return '<div class="' . e($class) . '">' . e($initial) . '</div>';
}
