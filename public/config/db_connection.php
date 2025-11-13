<?php
require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$host = $_ENV['DB_HOST'];
$userdb = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$database = $_ENV['DB_NAME'];

$conn = mysqli_connect($host, $userdb, $password, $database);

// Pengecekan koneksi yang benar:
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>