<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
} else {
    require __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
}
$dotenv->load();

$status = [];
$errors = [];

// REDIS
$redis = new \Predis\Client([
    'scheme' => 'tcp',
    'host'   => $_SERVER['REDIS_HOST'] ?? 'redis',
    'port'   => $_SERVER['REDIS_PORT'] ?? 6379,
]);

try {
    /** @var \Predis\Response\Status $response */
    $response = $redis->ping();
    $status['redis'] = $response->getPayload() === 'PONG';
} catch (\Exception $e) {
    $status['redis'] = FALSE;
    $errors['redis'] = $e->getMessage();
}

// PDO mariadb
try {
    $mysql = new PDO('mysql:dbname=' . ($_SERVER['DB1_NAME'] ?? 'madb') . ';host=' . ($_SERVER['DB1_HOST'] ?? 'mariadb'), $_SERVER['DB1_USER'] ?? 'user', $_SERVER['DB1_PASS'] ?? 'password');
    $status['mariadb'] = TRUE;
} catch (\Exception $e) {
    $status['mariadb'] = FALSE;
    $errors['mariadb'] = $e->getMessage();
}

// PDO postgres
try {
    $mysql = new PDO('pgsql:dbname=' . ($_SERVER['DB2_NAME'] ?? 'madb') . ';host=' . ($_SERVER['DB2_HOST'] ?? 'postgres'), $_SERVER['DB2_USER'] ?? 'user', $_SERVER['DB2_PASS'] ?? 'password');
    $status['postgres'] = TRUE;
} catch (\Exception $e) {
    $status['postgres'] = FALSE;
    $errors['postgres'] = $e->getMessage();
}

echo "<ul>";
foreach ($status as $key => $statu) {
    echo "<li>$key : " . ($statu ? '🟩' : '🟥') . '</li>';
}
echo "</ul>";

if (count($errors)) {
    echo "<h1>Tu veux du log d'erreur ?</h1>";
    echo "<ul>";
    foreach ($errors as $key => $error) {
        echo "<li>$key : $error";
    }
    echo "</ul>";
}

echo "<h1>Un peu de debug avec l'ensemble des variables d'environnement</h1>";
echo "<pre>";
var_dump($_SERVER);
echo "<pre>";
