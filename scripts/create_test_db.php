<?php

// Helper script used only during local development to make PHPUnit tests work.
// It creates the PostgreSQL database expected by phpunit.xml: RestAPI_test.

$host = '127.0.0.1';
$port = 5432;
$user = 'postgres';
$pass = 'root';
$db = 'RestAPI_test';

$dsn = "pgsql:host={$host};port={$port};dbname=postgres";

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $pdo->query("SELECT 1 FROM pg_database WHERE datname = '{$db}'");
$exists = (bool) $stmt->fetchColumn();

if ($exists) {
    echo "Database {$db} already exists\n";
    exit(0);
}

$pdo->exec("CREATE DATABASE \"{$db}\"");
echo "Created database {$db}\n";

