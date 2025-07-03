<?php
session_start();
require '../../config/db_connection.php';

// Cek jika user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit();
}

$username = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🟨 Proses Upload CSV (jika ada)
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === 0) {
        $csvTmp = $_FILES['csv_file']['tmp_name'];

        if (($handle = fopen($csvTmp, "r")) !== false) {
            fgetcsv($handle); // skip header

            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                [$nip, $jabatan, $divisi, $no_hp, $pengganti, $jenis_cuti, $tanggal_mulai, $tanggal_akhir, $catatan] = $row;
                $status = "Menunggu";
                $dokumen = ''; // kosong karena CSV tidak upload file

                $stmt = $conn->prepare("INSERT INTO cuti 
                    (username, nip, jabatan, divisi, no_hp, pengganti, jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, status_pengajuan)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssss", $username, $nip, $jabatan, $divisi, $no_hp, $pengganti, $jenis_cuti, $tanggal_mulai, $tanggal_akhir, $catatan, $dokumen, $status);
                $stmt->execute();
            }

            fclose($handle);
            header("Location: /app/view/user/beranda-user-submission.php?csv_success=1");
            exit();
        } else {
            echo "Gagal membuka file CSV.";
            exit();
        }
    }

    // 🟨 Proses Form Manual
    $nip = $_POST['nip'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $divisi = $_POST['divisi'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $pengganti = $_POST['pengganti'] ?? '';
    $jenis_cuti = $_POST['jenis_cuti'] ?? '';
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_akhir = $_POST['tanggal_akhir'] ?? '';
    $catatan = $_POST['catatan'] ?? '';
    $status = 'Menunggu';

    $dokumen = '';
    if (isset($_FILES['dokumen']) && $_FILES['dokumen']['error'] === 0) {
        $dokumen = $_FILES['dokumen']['name'];
        $tmp_file = $_FILES['dokumen']['tmp_name'];
        $upload_path = '../../uploads/' . basename($dokumen);

        if (!move_uploaded_file($tmp_file, $upload_path)) {
            echo "Upload dokumen gagal.";
            exit();
        }
    }

    // Simpan ke tabel cuti
    $sql = "INSERT INTO cuti 
        (username, nip, jabatan, divisi, no_hp, pengganti, jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, status_pengajuan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssssss',
        $username, $nip, $jabatan, $divisi, $no_hp, $pengganti,
        $jenis_cuti, $tanggal_mulai, $tanggal_akhir, $catatan, $dokumen, $status
    );
    mysqli_stmt_execute($stmt);

    // Notifikasi ke admin
    date_default_timezone_set('Asia/Jakarta');
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

    header("Location: /app/view/user/beranda-user-submission.php?success=1");
    exit();
}
?>
