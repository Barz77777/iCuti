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
    $status = 'Menunggu';

    // Upload dokumen
    $dokumen = $_FILES['dokumen']['name'];
    $tmp_file = $_FILES['dokumen']['tmp_name'];
    $upload_path = 'uploads/' . basename($dokumen);

    if (move_uploaded_file($tmp_file, $upload_path)) {
        // Simpan ke tabel cuti
        $sql = "INSERT INTO cuti 
            (username, nip, jabatan, divisi, no_hp, pengganti, jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, status_pengajuan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            'ssssssssssss',
            $username,
            $nip,
            $jabatan,
            $divisi,
            $no_hp,
            $pengganti,
            $jenis_cuti,
            $tanggal_mulai,
            $tanggal_akhir,
            $catatan,
            $dokumen,
            $status
        );
        mysqli_stmt_execute($stmt);

        // ✅ Kirim notifikasi ke admin
        $judul_notif = "Pengajuan Cuti Baru";
        $pesan_notif = "Karyawan $username mengajukan cuti dari $tanggal_mulai sampai $tanggal_akhir.";
        $penerima = 'admin';
        $status_notif = 'baru';
        $created_at = date('Y-m-d H:i:s');

        $sql_notif = "INSERT INTO notifications (judul, pesan, penerima_role, status, created_at) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmt_notif = $conn->prepare($sql_notif);
        $stmt_notif->bind_param("sssss", $judul_notif, $pesan_notif, $penerima, $status_notif, $created_at);
        $stmt_notif->execute();

        // ✅ Redirect setelah berhasil
        header("Location: beranda-user-submission.php?success=1");
        exit();
    } else {
        echo "Upload dokumen gagal.";
    }
}
