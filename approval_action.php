<?php
session_start();
require 'db_connection.php'; // koneksi ke database

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin_hr') {
    die('Access denied');
}

$cuti_id = $_POST['cuti_id'] ?? null;
$action = $_POST['action'] ?? null;
$admin_id = $_SESSION['user_id'];

if ($cuti_id && in_array($action, ['approve', 'reject'])) {
    $status = $action === 'approve' ? 'Disetujui' : 'Ditolak';

    $stmt = $conn->prepare("UPDATE cuti SET status = ?, disetujui_oleh = ?, tanggal_disetujui = NOW() WHERE id = ?");
    $stmt->bind_param("sii", $status, $admin_id, $cuti_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: beranda-atasan.php");
exit;
