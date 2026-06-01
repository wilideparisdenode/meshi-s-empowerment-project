<?php
/**
 * Database Configuration
 * Smart Girl Empowerment Platform
 */

$dbHost = 'localhost';
$dbName = 'smart_girl_empowerment';
$dbUser = 'root';
$dbPass = '';
$dbCharset = 'utf8mb4';

// Optional: create config/local.php to set $dbPass if MySQL root has a password
if (is_readable(__DIR__ . '/local.php')) {
    require __DIR__ . '/local.php';
}

define('DB_HOST', $dbHost);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_CHARSET', $dbCharset);

define('SITE_NAME', 'Smart Girl Empowerment Platform');
define('SITE_EMAIL', 'info@smartgirl.cm');
define('SITE_PHONE', '+237 677 000 000');
define('SITE_ADDRESS', 'Commercial Avenue, Bamenda, Northwest Region, Cameroon');

/** Auto-detect URL so links work even if port or folder differs */
function getSiteUrl(): string
{
    if (php_sapi_name() === 'cli') {
        return 'http://localhost/project';
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
    $path = '';
    if ($docRoot && strpos($projectRoot, $docRoot) === 0) {
        $path = substr($projectRoot, strlen($docRoot));
    } else {
        $path = '/project';
    }
    return rtrim($scheme . '://' . $host . $path, '/');
}

if (!defined('SITE_URL')) {
    define('SITE_URL', getSiteUrl());
}

/** Check if database is installed and reachable */
function isDatabaseInstalled(): bool
{
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $pdo->query('SELECT 1 FROM users LIMIT 1');
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function getDBConnection(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $installUrl = getSiteUrl() . '/install.php';
            $checkUrl = getSiteUrl() . '/check.php';
            $msg = htmlspecialchars($e->getMessage());
            die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Setup Required</title>
            <style>body{font-family:Segoe UI,sans-serif;max-width:620px;margin:3rem auto;padding:2rem;background:#f0f7ff;}
            .box{background:#fff;padding:2rem;border-radius:12px;box-shadow:0 4px 20px rgba(30,86,160,.15);}
            h1{color:#1e56a0;} .btn{display:inline-block;background:#1e56a0;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;margin:8px 8px 0 0;}
            .warn{background:#fef3c7;padding:1rem;border-radius:8px;margin:1rem 0;font-size:14px;}
            code{background:#eee;padding:2px 6px;border-radius:4px;}</style></head><body><div class="box">
            <h1>Database Not Ready</h1>
            <p>MySQL is not connected or the database has not been created yet.</p>
            <div class="warn"><strong>Do this:</strong><ol>
            <li>Open <strong>XAMPP Control Panel</strong></li>
            <li>Click <strong>Start</strong> on <strong>Apache</strong> and <strong>MySQL</strong> (both must be green)</li>
            <li>Click the button below to install the database</li>
            </ol></div>
            <p><small>Error: ' . $msg . '</small></p>
            <a class="btn" href="' . $installUrl . '">Install Database</a>
            <a class="btn" href="' . $checkUrl . '" style="background:#64748b;">Run Diagnostics</a>
            </div></body></html>');
        }
    }
    return $pdo;
}
