<?php
session_start();
require 'db_connection.php';

// Cek jika user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data dari session login
    $username = $_SESSION['user'];
    $nip = $_POST['nip'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $divisi = $_POST['divisi'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $pengganti = $_POST['pengganti'] ?? '';

    // Data dari form
    $jenis_cuti = $_POST['jenis_cuti'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $status = 'Pending'; // default status awal

    // Upload dokumen
    $dokumen = $_FILES['dokumen']['name'];
    $tmp_file = $_FILES['dokumen']['tmp_name'];
    $upload_path = 'uploads/' . basename($dokumen);

    if (move_uploaded_file($tmp_file, $upload_path)) {
        // Simpan ke database
        $sql = "INSERT INTO cuti 
            (username, nip, jabatan, divisi, no_hp, pengganti, jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', 
            $username, $nip, $jabatan, $divisi, $no_hp, $pengganti, 
            $jenis_cuti, $tanggal_mulai, $tanggal_akhir, $catatan, $dokumen, $status
        );
        mysqli_stmt_execute($stmt);

        // Berhasil, kembali ke halaman dashboard
        header("Location: beranda-user-submission.php?success=1");
        exit();
    } 
}
?>
