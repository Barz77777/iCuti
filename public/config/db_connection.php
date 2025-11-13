<?php
$host = "172.15.10.80";
$userdb = "app_user_iCuti01";
$password = "Cyberark1";
$database = "cuti_app";

$conn = mysqli_connect($host, $userdb, $password, $database);

// Pengecekan koneksi yang benar:
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>