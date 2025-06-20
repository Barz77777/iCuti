<?php
$host = "localhost";
$userdb = "root";
$password = ""; // jika pakai password, isi di sini
$database = "cuti_app"; // ganti sesuai nama DB-mu

$conn = new mysqli($host, $userdb, $password, $database);

// Pengecekan koneksi yang benar:
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
