<?php
$host = "localhost";
$user = "root";
$password = ""; // jika pakai password, isi di sini
$database = "cuti_app"; // ganti sesuai nama DB-mu

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
