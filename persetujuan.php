<?php

session_start();
require 'db_connection.php';

// Cek apakah user sudah login dan rolenya atasan
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'atasan') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cuti_id = intval($_POST['cuti_id']);
    $action = $_POST['action'];

    // Validasi aksi
    if (!in_array($action, ['approve', 'reject'])) {
        $_SESSION['error'] = 'Aksi tidak valid.';
        header('Location: beranda-atasan.php');
        exit();
    }

    // Set status baru
    $new_status = $action === 'approve' ? 'Approved' : 'Rejected';
    $approved_by = $_SESSION['username']; // Atau user_id jika lebih aman
    $approved_date = date('Y-m-d H:i:s');

    // Update ke tabel submission
    $stmt = $conn->prepare("UPDATE submission SET approved_status = ?, approved_by = ?, approved_date = ? WHERE id = ?");
    $stmt->bind_param("sssi", $new_status, $approved_by, $approved_date, $cuti_id);

    if ($stmt->execute()) {
        $_SESSION['sukses'] = "Pengajuan cuti berhasil diubah menjadi $new_status.";
    } else {
        $_SESSION['error'] = "Gagal mengubah status: " . $stmt->error;
    }

    header('Location: beranda-atasan.php');
    exit();
}
?>
