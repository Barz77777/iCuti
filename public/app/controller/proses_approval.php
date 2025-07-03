<?php
session_start();
include '../../config/db_connection.php';
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cuti_id'], $_POST['aksi'])) {
    $id = intval($_POST['cuti_id']);
    $aksi = $_POST['aksi'];

    if ($aksi === 'setujui') {
        $status = 'Disetujui';
    } elseif ($aksi === 'tolak') {
        $status = 'Ditolak';
    } else {
        $status = 'Menunggu';
    }

    $tanggal = date('Y-m-d H:i:s');

    $query = "UPDATE cuti SET status_pengajuan = ?, tanggal_disetujui = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssi', $status, $tanggal, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: /app/view/admin/beranda-atasan-agreement.php?notif=$status");
        exit();
    } else {
        echo "Gagal memperbarui data.";
    }
}
?>
