<?php
$host = "db";
$userdb = "root";
$password = "rootpass"; // jika pakai password, isi di sini
$database = "cuti_app"; // ganti sesuai nama DB-mu

$conn = mysqli_connect($host, $userdb, $password, $database);

// Pengecekan koneksi yang benar:
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
