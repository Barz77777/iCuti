<?php
session_start();
include '../../config/db_connection.php';
require 'icuti_bot_status.php';
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
        // Ambil informasi pengguna cuti (opsional untuk memperjelas isi pesan)
        $sqlUser = "SELECT username, jenis_cuti, tanggal_mulai, tanggal_selesai FROM cuti WHERE id = ?";
        $stmtUser = mysqli_prepare($conn, $sqlUser);
        mysqli_stmt_bind_param($stmtUser, 'i', $id);
        mysqli_stmt_execute($stmtUser);
        mysqli_stmt_bind_result($stmtUser, $nama, $jenis, $mulai, $selesai);
        mysqli_stmt_fetch($stmtUser);

        // Buat isi pesan notifikasi
        $pesan = "📢 <b>Notifikasi Status Pengajuan Cuti</b>\n\n"
               . "👤 <b>Nama:</b> $nama\n"
               . "📌 <b>Jenis Cuti:</b> $jenis\n"
               . "🗓️ <b>Periode:</b> $mulai s/d $selesai\n"
               . "📥 <b>Status:</b> $status\n"
               . "⏰ <b>Diperbarui:</b> $tanggal";

        // Kirim ke Telegram
        kirimTelegram($pesan);

        header("Location: /app/view/admin/beranda-atasan-agreement.php?notif=$status");
        exit();
    } else {
        echo "Gagal memperbarui data.";
    }
}
?>
