<?php
/**
 * Database Installation - Run once via browser
 * URL: http://localhost/project/install.php
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'smart_girl_empowerment';

$messages = [];
$success = false;

function runSqlFile(PDO $pdo, string $filePath): array
{
    $errors = [];
    if (!is_readable($filePath)) {
        return ['SQL file not found: ' . $filePath];
    }

    $sql = file_get_contents($filePath);
    // Remove single-line comments
    $sql = preg_replace('/--[^\r\n]*/m', '', $sql);
    // Split on semicolons followed by newline
    $parts = preg_split('/;\s*[\r\n]+/', $sql);

    foreach ($parts as $statement) {
        $statement = trim($statement);
        if ($statement === '' || stripos($statement, 'USE ') === 0) {
            continue;
        }
        try {
            $pdo->exec($statement);
        } catch (PDOException $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            // Ignore "already exists" on re-run
            if (stripos($msg, 'already exists') !== false || stripos($msg, 'Duplicate') !== false) {
                continue;
            }
            $errors[] = substr($statement, 0, 60) . '... → ' . $msg;
        }
    }
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!extension_loaded('pdo_mysql')) {
            throw new Exception('PHP extension pdo_mysql is not enabled. Enable it in php.ini and restart Apache.');
        }

        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbName`");

        $sqlErrors = runSqlFile($pdo, __DIR__ . '/database/schema.sql');
        if (!empty($sqlErrors)) {
            foreach ($sqlErrors as $err) {
                $messages[] = 'SQL warning: ' . $err;
            }
        }

        // Ensure admin password works: Admin@123
        $adminHash = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@smartgirl.cm'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@smartgirl.cm'")->execute([$adminHash]);
        } else {
            $pdo->prepare("INSERT INTO users (full_name, email, password, role, location) VALUES (?,?,?,?,?)")
                ->execute(['Platform Administrator', 'admin@smartgirl.cm', $adminHash, 'admin', 'Bamenda, Cameroon']);
        }

        // Verify tables exist
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        if (count($tables) < 5) {
            throw new Exception('Installation incomplete. Only ' . count($tables) . ' tables found. Check MySQL is running.');
        }

        $success = true;
        $messages[] = 'Database installed successfully! (' . count($tables) . ' tables created)';
        $messages[] = 'Admin login: admin@smartgirl.cm / Admin@123';
        $messages[] = 'Open homepage: ' . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) : 'http://localhost/project');
    } catch (Exception $e) {
        $messages[] = 'Installation failed: ' . $e->getMessage();
    }
}

// Pre-check MySQL
$mysqlOk = false;
$mysqlError = '';
try {
    new PDO("mysql:host=$host", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $mysqlOk = true;
} catch (Exception $e) {
    $mysqlError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - Smart Girl Platform</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 640px; margin: 2rem auto; padding: 1.5rem; background: #f0f7ff; }
        .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(30,86,160,0.15); }
        h1 { color: #1e56a0; margin-bottom: 0.5rem; }
        .btn { background: #1e56a0; color: white; border: none; padding: 0.85rem 2rem; border-radius: 8px; cursor: pointer; font-size: 1rem; text-decoration: none; display: inline-block; }
        .btn:hover { background: #163e75; }
        .success { color: #065f46; background: #d1fae5; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .error { color: #991b1b; background: #fee2e2; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .info { background: #dbeafe; padding: 1rem; border-radius: 8px; margin: 1rem 0; font-size: 0.9rem; }
        ul { margin: 0.5rem 0; padding-left: 1.25rem; }
        .status { padding: 0.5rem 0; }
        .ok { color: #065f46; }
        .bad { color: #991b1b; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Smart Girl Platform Installer</h1>
        <p>Install the database before using the website.</p>

        <div class="info">
            <strong>System check:</strong>
            <div class="status">PHP version: <?= PHP_VERSION ?> <?= version_compare(PHP_VERSION, '7.4', '>=') ? '<span class="ok">OK</span>' : '<span class="bad">Need 7.4+</span>' ?></div>
            <div class="status">PDO MySQL: <?= extension_loaded('pdo_mysql') ? '<span class="ok">OK</span>' : '<span class="bad">Missing - enable in php.ini</span>' ?></div>
            <div class="status">MySQL connection: <?= $mysqlOk ? '<span class="ok">OK</span>' : '<span class="bad">FAILED - Start MySQL in XAMPP</span>' ?></div>
            <?php if ($mysqlError): ?><small class="bad"><?= htmlspecialchars($mysqlError) ?></small><?php endif; ?>
        </div>

        <?php if ($messages): ?>
            <div class="<?= $success ? 'success' : 'error' ?>">
                <ul><?php foreach ($messages as $m): ?><li><?= htmlspecialchars($m) ?></li><?php endforeach; ?></ul>
            </div>
            <?php if ($success): ?>
                <a href="index.php" class="btn">Go to Homepage &rarr;</a>
            <?php endif; ?>
        <?php elseif ($mysqlOk): ?>
            <form method="POST">
                <p>Click below to create database <code>smart_girl_empowerment</code> and all tables.</p>
                <button type="submit" class="btn">Install Database Now</button>
            </form>
        <?php else: ?>
            <div class="error">
                <p><strong>MySQL is not running.</strong></p>
                <ol>
                    <li>Open <strong>XAMPP Control Panel</strong></li>
                    <li>Click <strong>Start</strong> next to <strong>MySQL</strong></li>
                    <li>Also start <strong>Apache</strong></li>
                    <li>Refresh this page</li>
                </ol>
            </div>
        <?php endif; ?>

        <p style="margin-top:1.5rem;font-size:0.85rem;"><a href="check.php">Run full diagnostics</a></p>
    </div>
</body>
</html>
