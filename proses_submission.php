<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // pastikan ini file koneksi ke database kamu

$user = $_SESSION['user'];
$jenis_cuti = $_POST['jenis_cuti'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_akhir = $_POST['tanggal_akhir'];
$catatan = $_POST['catatan'] ?? '';
$status = 'Waiting For Approval';

// File Upload Handling
$uploadDir = 'uploads/';
$allowedTypes = ['pdf', 'jpg', 'jpeg', 'png'];
$fileName = $_FILES['dokumen']['name'];
$fileTmp = $_FILES['dokumen']['tmp_name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowedTypes)) {
    $_SESSION['sukses'] = 'Format dokumen tidak diizinkan.';
    header('Location: beranda-user-submission.php');
    exit();
}

$newFileName = uniqid() . '.' . $fileExt;
$destination = $uploadDir . $newFileName;

if (move_uploaded_file($fileTmp, $destination)) {
    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO submission 
        (username, jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $user, $jenis_cuti, $tanggal_mulai, $tanggal_akhir, $catatan, $newFileName, $status);

    if ($stmt->execute()) {
        $_SESSION['sukses'] = 'Pengajuan cuti Anda berhasil dikirim!';
    } else {
        $_SESSION['sukses'] = 'Gagal menyimpan data ke database.';
    }
    $stmt->close();
} else {
    $_SESSION['sukses'] = 'Gagal mengunggah dokumen.';
}

header('Location: beranda-user-submission.php');
exit();
?>
