<?php 
session_start();
require '../../config/db_connection.php';

// Cek jika user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    //nambah account 
    $user = $_POST['username'] ?? '';
    $nip = $_POST['nip'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $divisi = $_POST['divisi'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';

     // Simpan ke database
    $sql = "INSERT INTO users 
        (username, nip, jabatan, divisi, no_hp)
        VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'sssss',
        $user,
        $nip,
        $jabatan,
        $divisi,
        $no_hp
    );
    mysqli_stmt_execute($stmt);

      header("Location: /app/view/admin/admin-unban.php?status=success=1");
    exit();
}