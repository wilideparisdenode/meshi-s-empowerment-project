<?php
/**
 * Authentication Helpers
 */

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) return null;
    return $_SESSION['user'] ?? null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user && ($user['role'] ?? '') === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in to access this page.');
        redirect(SITE_URL . '/login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        setFlash('error', 'Access denied. Administrator privileges required.');
        redirect(SITE_URL . '/dashboard.php');
    }
}

function loginUser(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user'] = [
        'id' => $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'location' => $user['location'] ?? '',
        'profile_image' => $user['profile_image'] ?? 'default-avatar.png',
        'phone' => $user['phone'] ?? '',
    ];
    session_regenerate_id(true);
}

function refreshUserSession(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user) {
        loginUser($user);
    }
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function registerUser(PDO $pdo, array $data): array
{
    $errors = [];
    if (empty($data['full_name'])) $errors[] = 'Full name is required.';
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($data['password']) || strlen($data['password']) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($data['password'] !== ($data['confirm_password'] ?? '')) {
        $errors[] = 'Passwords do not match.';
    }
    if (!empty($errors)) return ['success' => false, 'errors' => $errors];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return ['success' => false, 'errors' => ['Email is already registered.']];
    }

    $hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $profileImage = 'default-avatar.png';

    if (!empty($data['profile_image'])) {
        $profileImage = $data['profile_image'];
    }

    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, location, profile_image, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
    $stmt->execute([
        trim($data['full_name']),
        strtolower(trim($data['email'])),
        $hash,
        trim($data['phone'] ?? ''),
        trim($data['location'] ?? ''),
        $profileImage,
    ]);
    return ['success' => true, 'user_id' => (int) $pdo->lastInsertId()];
}

function authenticateUser(PDO $pdo, string $email, string $password): array
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([strtolower(trim($email))]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'error' => 'Invalid email or password.'];
    }
    loginUser($user);
    return ['success' => true, 'user' => $user];
}
