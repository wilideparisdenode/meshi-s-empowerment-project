<?php
/**
 * Setup diagnostics - open in browser when site won't run
 * http://localhost/project/check.php
 */
header('Content-Type: text/html; charset=utf-8');
$checks = [];

// 1. PHP
$checks[] = ['PHP version ' . PHP_VERSION, version_compare(PHP_VERSION, '7.4', '>=')];

// 2. Extensions
$checks[] = ['PDO extension', extension_loaded('pdo')];
$checks[] = ['PDO MySQL extension', extension_loaded('pdo_mysql')];
$checks[] = ['mbstring extension', extension_loaded('mbstring')];

// 3. Project files
$checks[] = ['index.php exists', file_exists(__DIR__ . '/index.php')];
$checks[] = ['database/schema.sql exists', file_exists(__DIR__ . '/database/schema.sql')];

// 4. Apache / running via web server (not file://)
$isWeb = !empty($_SERVER['HTTP_HOST']);
$checks[] = ['Opened via web server (not file://)', $isWeb];

// 5. MySQL
$mysqlOk = false;
$mysqlMsg = '';
try {
    $pdo = new PDO('mysql:host=localhost', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $mysqlOk = true;
    $mysqlMsg = 'Connected as root@localhost';
} catch (Throwable $e) {
    $mysqlMsg = $e->getMessage();
}
$checks[] = ['MySQL server running', $mysqlOk, $mysqlMsg];

// 6. Database installed
$dbOk = false;
$dbMsg = '';
if ($mysqlOk) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=smart_girl_empowerment;charset=utf8mb4', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $count = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $dbOk = true;
        $dbMsg = "Database OK ($count users in table)";
    } catch (Throwable $e) {
        $dbMsg = $e->getMessage();
    }
}
$checks[] = ['Database smart_girl_empowerment', $dbOk, $dbMsg];

$allOk = true;
foreach ($checks as $c) {
    if (!$c[1]) $allOk = false;
}

$baseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/project'), '/\\');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup Check - Smart Girl Platform</title>
    <style>
        body{font-family:Segoe UI,sans-serif;max-width:700px;margin:2rem auto;padding:1.5rem;background:#f0f7ff;}
        .card{background:#fff;padding:2rem;border-radius:12px;box-shadow:0 4px 15px rgba(30,86,160,.15);}
        h1{color:#1e56a0;}
        table{width:100%;border-collapse:collapse;margin:1rem 0;}
        td,th{padding:10px;text-align:left;border-bottom:1px solid #e2e8f0;}
        .pass{color:#065f46;font-weight:bold;}
        .fail{color:#991b1b;font-weight:bold;}
        .btn{display:inline-block;background:#1e56a0;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;margin:5px 5px 0 0;}
        .steps{background:#dbeafe;padding:1rem;border-radius:8px;margin-top:1rem;}
        code{background:#f1f5f9;padding:2px 6px;border-radius:4px;}
    </style>
</head>
<body>
<div class="card">
    <h1>Setup Diagnostics</h1>
    <p>Overall: <?= $allOk ? '<span class="pass">All checks passed - site should work!</span>' : '<span class="fail">Some checks failed - fix below</span>' ?></p>

    <table>
        <tr><th>Check</th><th>Status</th><th>Details</th></tr>
        <?php foreach ($checks as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c[0]) ?></td>
            <td class="<?= $c[1] ? 'pass' : 'fail' ?>"><?= $c[1] ? 'PASS' : 'FAIL' ?></td>
            <td><small><?= htmlspecialchars($c[2] ?? '') ?></small></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php if (!$isWeb): ?>
    <div class="steps">
        <strong>Wrong way to open the site!</strong>
        <p>Do NOT double-click PHP files. Use your browser and go to:</p>
        <p><code><?= htmlspecialchars($baseUrl) ?>/</code></p>
    </div>
    <?php endif; ?>

    <?php if (!$mysqlOk): ?>
    <div class="steps">
        <strong>Start XAMPP:</strong>
        <ol>
            <li>Open XAMPP Control Panel</li>
            <li>Start <strong>Apache</strong> and <strong>MySQL</strong></li>
            <li>Refresh this page</li>
        </ol>
    </div>
    <?php elseif (!$dbOk): ?>
    <div class="steps">
        <strong>Install the database:</strong>
        <p><a class="btn" href="install.php">Open Installer</a></p>
    </div>
    <?php else: ?>
    <p><a class="btn" href="index.php">Open Homepage</a>
    <a class="btn" href="login.php" style="background:#64748b;">Login Page</a></p>
    <?php endif; ?>

    <div class="steps" style="margin-top:1.5rem;">
        <strong>Correct URLs to use:</strong>
        <ul>
            <li>Home: <code><?= htmlspecialchars($baseUrl) ?>/index.php</code></li>
            <li>Install: <code><?= htmlspecialchars($baseUrl) ?>/install.php</code></li>
            <li>phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
        </ul>
    </div>
</div>
</body>
</html>
