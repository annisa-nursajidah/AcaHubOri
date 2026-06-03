<?php
$host = '127.0.0.1';
$db   = 'acahub';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Drop database if exists
    $pdo->exec("DROP DATABASE IF EXISTS `$db`");
    echo "Database `$db` dropped.\n";
    
    // Create new database
    $pdo->exec("CREATE DATABASE `$db` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci");
    echo "Database `$db` created.\n";
    
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
