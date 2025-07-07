<?php
include '../../config/db_connection.php';
include 'icuti_bot.php'; 
session_start();

function isValidDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

$allowedLeaveTypes = ['Annual Leave', 'Sick Leave', 'Maternity Leave'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $fileTmp = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($fileTmp, "r")) !== FALSE) {
        $isHeader = true;
        $rowNumber = 1;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rowNumber++;

            // Lewati baris header
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // Cek jumlah kolom
            if (count($data) < 12) {
                $errors[] = "Baris $rowNumber: Kolom tidak lengkap.";
                continue;
            }

            list($username, $nip, $jabatan, $divisi, $no_hp, $pengganti, $jenis_cuti, $tanggal_mulai, $tanggal_akhir, $catatan, $dokumen, $status_csv) = $data;

            // Validasi data
            if (
                empty($username) || empty($nip) || empty($jabatan) || empty($divisi) ||
                empty($no_hp) || empty($pengganti) || empty($jenis_cuti) ||
                empty($tanggal_mulai) || empty($tanggal_akhir) || empty($catatan)
            ) {
                $errors[] = "Baris $rowNumber: Semua kolom wajib diisi.";
                continue;
            }

            if (!in_array($jenis_cuti, $allowedLeaveTypes)) {
                $errors[] = "Baris $rowNumber: Jenis cuti tidak valid.";
                continue;
            }

            if (!isValidDate($tanggal_mulai) || !isValidDate($tanggal_akhir)) {
                $errors[] = "Baris $rowNumber: Format tanggal salah.";
                continue;
            }

            // Siapkan data untuk insert
            $status_pengajuan = "Menunggu";
            $notified = 0;
            $tanggal_disetujui = null;
            $tanggal_selesai = $tanggal_akhir;
            $created_at = date("Y-m-d H:i:s");

            $stmt = $conn->prepare("INSERT INTO cuti (
                username, nip, jabatan, divisi, no_hp, pengganti,
                jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, csv,
                status_pengajuan, notified, tanggal_disetujui, tanggal_selesai, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                $errors[] = "Baris $rowNumber: Query error: " . $conn->error;
                continue;
            }

            // Bind dan eksekusi
            $stmt->bind_param(
                "sssssssssssssssss",
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
                $status_csv,
                $status_pengajuan,
                $notified,
                $tanggal_disetujui,
                $tanggal_selesai,
                $created_at
            );

            if (!$stmt->execute()) {
                $errors[] = "Baris $rowNumber: Gagal simpan. " . $stmt->error;
            } else {
                // Notifikasi Telegram
                $pesan = "📢 <b>Pengajuan Cuti (CSV)</b>\n"
                    . "👤 User: <b>$username</b>\n"
                    . "📅 Tanggal: <b>$tanggal_mulai</b> s/d <b>$tanggal_akhir</b>\n"
                    . "📄 Jenis: <b>$jenis_cuti</b>\n"
                    . "📝 Catatan: <i>$catatan</i>\n"
                    . "📌 Status: <b>$status_pengajuan</b>";
                kirimTelegram($pesan);
            }

            $stmt->close();
        }

        fclose($handle);

        if (count($errors) > 0) {
            $_SESSION['upload_errors'] = $errors;
            header("Location: /app/view/user/beranda-user-submission.php?status=error");
        } else {
            unset($_SESSION['upload_errors']);
            $_SESSION['csv_upload_success'] = true;
            header("Location: /app/view/user/beranda-user-submission.php?status=success");
        }

    } else {
        header("Location: /app/view/user/beranda-user-submission.php?status=error&msg=Gagal membuka file CSV.");
    }
} else {
    header("Location: /app/view/user/beranda-user-submission.php?status=error&msg=Tidak ada file diunggah.");
}
exit();
