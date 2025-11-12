<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

require '../../../config/db_connection.php';

// Ambil history cuti yang sudah disetujui atau ditolak
$search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

$sql = "SELECT * FROM cuti";

if (!empty($search)) {
    $sql .= " AND (
        username LIKE '%$search%' OR
        nip LIKE '%$search%' OR
        jabatan LIKE '%$search%' OR
        divisi LIKE '%$search%' OR
        jenis_cuti LIKE '%$search%' OR
        tanggal_mulai LIKE '%$search%' OR
        tanggal_akhir LIKE '%$search%' OR
        pengganti LIKE '%$search%' OR
        no_hp LIKE '%$search%' OR
        catatan LIKE '%$search%' OR
        status_pengajuan LIKE '%$search%'
    )";
}

$sql .= " ORDER BY created_at DESC";


$result = mysqli_query($conn, $sql);
$history = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = $row;
    }
}

// Tombol "Tandai semua dibaca"
if (isset($_GET['read_all'])) {
    $conn->query("UPDATE notifications SET status = 'dibaca' WHERE penerima_role = 'admin'");
    header("Location: beranda-atasan-history.php");
    exit();
}

// Ambil notifikasi baruz
$notifQuery = "SELECT * FROM notifications WHERE penerima_role = 'admin' AND status = 'baru' ORDER BY created_at DESC";
$notifResult = $conn->query($notifQuery);


// === 1. NOTIFIKASI UNTUK ATASAN ===
$sqlNotif = "SELECT * FROM notifications WHERE penerima_role = 'admin' ORDER BY created_at DESC LIMIT 10";
$resNotif = $conn->query($sqlNotif);
if ($resNotif === false) {
    die("Error executing query: " . $conn->error);
}
$notifs = $resNotif->fetch_all(MYSQLI_ASSOC);

// Pastikan status konsisten: gunakan 'baru' untuk jumlah notifikasi baru
$sqlJumlah = "SELECT COUNT(*) as total FROM notifications WHERE penerima_role = 'atasan' AND status = 'baru'";
$resJumlah = $conn->query($sqlJumlah);
if (!$resJumlah) {
    die("Error executing query: " . $conn->error);
}
$jumlahNotifBaru = $resJumlah->fetch_assoc()['total'] ?? 0;
// Otomatis update status cuti
mysqli_query($conn, "
    UPDATE cuti 
    SET status_pengajuan = 'Selesai' 
    WHERE tanggal_akhir < CURDATE() AND status_pengajuan != 'Selesai'
");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/public/asset/iC.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/beranda-atasan-history.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>iCuti</title>
    <style>
        @keyframes notifSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-notif {
            animation: notifSlideIn 0.3s ease-out forwards;
        }
    </style>
    <style>
        /* Loader full screen */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .morphing-squares {
            display: flex;
            gap: 8px;
        }

        .morphing-squares div {
            width: 15px;
            height: 15px;
            background: linear-gradient(45deg, #9AD914, #8ABF17);
            border-radius: 2px;
            animation: morphSquares 1.5s ease-in-out infinite;
        }

        .morphing-squares div:nth-child(1) {
            animation-delay: 0s;
        }

        .morphing-squares div:nth-child(2) {
            animation-delay: 0.1s;
        }

        .morphing-squares div:nth-child(3) {
            animation-delay: 0.2s;
        }

        .morphing-squares div:nth-child(4) {
            animation-delay: 0.3s;
        }

        .morphing-squares div:nth-child(5) {
            animation-delay: 0.4s;
        }

        @keyframes morphSquares {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
                border-radius: 2px;
            }

            50% {
                transform: scale(1.5) rotate(180deg);
                border-radius: 50%;
            }
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div id="loader">
        <div class="morphing-squares">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- Alert Tidak Ada Aktivitas -->
    <div id="idleWarningModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 9999; justify-content: center; align-items: center; overflow: auto; padding: 20px;">
        <div style="background: #fff; padding: 40px 30px; border-radius: 25px; text-align: center; max-width: 400px; width: 100%; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);">

            <!-- Ikon Peringatan -->
            <img src="/asset/alert.svg" alt="Warning Icon" style="display: block; margin: 0 auto 25px; width: 120px; max-width: 100%; height: auto;">

            <!-- Judul -->
            <h2 style="font-size: 20px; color: #333; margin-bottom: 10px; font-weight: 700;">Tidak Ada Aktivitas!</h2>

            <!-- Subjudul -->
            <p style="font-size: 15px; color: #555; margin-bottom: 20px;">Anda akan logout dalam <span id="countdown" style="font-weight: bold; color: #e74c3c">30</span> detik.</p>

            <!-- Tombol Aksi -->
            <button onclick="stayLoggedIn()" style="padding: 10px 24px; background-color: #9AD914; border: none; color: white; font-weight: bold; font-size: 14px;border-radius: 8px; cursor: pointer; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); transition: background 0.3s ease;">Saya Ada di Sini!</button>
        </div>
    </div>

    <div class="layout">
        <div class="sidebar">
            <!-- Logo -->
            <div class="icon-button top-icon profile-toggle" onclick="toggleProfileMenu()"><img src="/asset/default-avatar.png">
                <span class="text-icon">Profile</span>
                <i class="menu bi bi-list"></i>
            </div>

            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-content">
                    <div class="user-info">
                        <p class="user-name"><?= ($user) ?></p>
                        <p class="user-role"><?= ($role) ?></p>
                    </div>
                </div>
                <button class="logout-btn" onclick="window.location.href='/logout.php';">Logout</button>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <form action="/app/controller/switch_role.php" method="post" style="display:inline;">
                        <button type="submit" style="font-size: 16px;">
                            Ganti ke <?= $_SESSION['active_role'] === 'admin' ? 'user' : 'admin' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Menu Icons -->
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-atasan-overview.php';">
                <i class="bi bi-grid-fill"></i>
                <span class="text-icon">Overview</span>
            </div>
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-atasan-agreement.php';">
                <i class="bi bi-envelope-paper"></i>
                <span class="text-icon">Agreement</span>
            </div>
            <div class="icon-button active sidebar-link" onclick="window.location.href='beranda-atasan-history.php';">
                <i class="bi bi-clock-history"></i>
                <span class="text-icon">History</span>
            </div>
            <div class="icon-button" onclick="window.location.href='admin-unban.php';">
                <i class="bi bi-person-circle"></i>
                <span class="text-icon">Account</span>
            </div>

            <!-- Bottom Icon -->
            <div class="toggle-container" style="margin-top:auto;">
                <div id="lightBtn" class="icon-btn active sidebar-link"><i class="bi bi-brightness-high"></i></div>
                <div id="darkBtn" class="icon-btn sidebar-link"><i class="bi bi-moon"></i></div>
            </div>
        </div>

        <main class="main-content flex-grow max-w-7xl mx-auto flex flex-col gap-8">

            <!-- Search icon -->

            <header class="flex items-center justify-between space-x-4">
                <div class="flex-grow relative max-w-lg">
                    <form method="GET" action="" class="w-full relative max-w-lg">
                        <input type="search" name="q" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                            aria-label="Search anything here"
                            placeholder="Search anything here"
                            class="box-shadow w-full rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2 pl-10 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-lime-500" />
                        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </form>
                </div>

                <!-- notif -->
                <div class="relative">
                    <button id="notifBtn" aria-label="Notifications" class="bg-white relative p-2 rounded-full hover:bg-lime-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-lime-400">
                        <i class="bi bi-bell text-2xl text-gray-600 dark:text-gray-300"></i>
                        <?php if ($notifResult->num_rows > 0): ?>
                            <span id="notifDot" class="absolute top-2 right-2 inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                        <?php endif; ?>
                    </button>
                    <div id="notifDropdown"
                        class="notifikasi bg-white dark:bg-gray-800 rounded-3xl p-4 shadow-md absolute right-0 mt-2 w-80 sm:w-96 max-w-[90vw] z-50"
                        style="display:none;">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Notifikasi Pengajuan Cuti</h2>
                            <a href="?read_all=true" class="text-sm text-blue-600 hover:underline">Tandai semua dibaca</a>
                        </div>
                        <ul>
                            <?php if ($notifResult->num_rows > 0): ?>
                                <?php while ($row = $notifResult->fetch_assoc()): ?>
                                    <li class="mb-2 border-b pb-1 text-gray-700 dark:text-gray-300">
                                        <?= htmlspecialchars($row['pesan']) ?>
                                        <br><small class="text-gray-500"><?= $row['created_at'] ?></small>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="text-gray-500 italic">Tidak ada notifikasi baru</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </header>

            <script>
                // Toggle notification dropdown
                const notifBtn = document.getElementById('notifBtn');
                const notifDropdown = document.getElementById('notifDropdown');
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifDropdown.style.display = notifDropdown.style.display === 'none' || notifDropdown.style.display === '' ? 'block' : 'none';
                    // Sembunyikan dot merah saat dropdown dibuka
                    const notifDot = document.getElementById('notifDot');
                    if (notifDropdown.style.display === 'block' && notifDot) {
                        notifDot.style.display = 'none';
                    }
                });
                document.addEventListener('click', function(e) {
                    if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
                        notifDropdown.style.display = 'none';
                    }
                });
            </script>


            <?php
            // PAGINATION LOGIC
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 5;

            // Count total data
            $countSql = "SELECT COUNT(*) as total FROM cuti WHERE (status_pengajuan = 'Ditolak' OR status_pengajuan = 'Disetujui' OR status_pengajuan = 'Selesai')";
            if (!empty($search)) {
                $countSql .= " AND (
                    username LIKE '%$search%' OR
                    nip LIKE '%$search%' OR
                    jabatan LIKE '%$search%' OR
                    divisi LIKE '%$search%' OR
                    jenis_cuti LIKE '%$search%' OR
                    tanggal_mulai LIKE '%$search%' OR
                    tanggal_akhir LIKE '%$search%' OR
                    pengganti LIKE '%$search%' OR
                    no_hp LIKE '%$search%' OR
                    catatan LIKE '%$search%' OR
                    status_pengajuan LIKE '%$search%'
                )";
            }
            $countResult = mysqli_query($conn, $countSql);
            $totalRows = ($countResult && $row = mysqli_fetch_assoc($countResult)) ? (int)$row['total'] : 0;
            $totalPages = ceil($totalRows / $perPage);
            $offset = ($page - 1) * $perPage;

            // Query with LIMIT for pagination
            $sql = "SELECT * FROM cuti WHERE (status_pengajuan = 'Selesai' OR status_pengajuan = 'Disetujui' OR status_pengajuan = 'Ditolak')"; 
            if (!empty($search)) {
                $sql .= " AND (
                    username LIKE '%$search%' OR
                    nip LIKE '%$search%' OR
                    jabatan LIKE '%$search%' OR
                    divisi LIKE '%$search%' OR
                    jenis_cuti LIKE '%$search%' OR
                    tanggal_mulai LIKE '%$search%' OR
                    tanggal_akhir LIKE '%$search%' OR
                    pengganti LIKE '%$search%' OR
                    no_hp LIKE '%$search%' OR
                    catatan LIKE '%$search%' OR
                    status_pengajuan LIKE '%$search%'
                )";
            }
            $sql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";

            $result = mysqli_query($conn, $sql);
            $history = [];
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $history[] = $row;
                }
            }
            ?>
            <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md h-fit animate__animated animate__fadeIn" style="--animate-duration: 1.2s;">
                <header class="mb-4 flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">History</h2>
                </header>

                <!-- MOBILE VIEW -->
                <div class="block md:hidden space-y-4">
                    <?php foreach ($history as $row): ?>
                        <div class="border rounded-xl p-4 bg-white dark:bg-gray-800">
                            <p class="text-sm font-semibold text-lime-700"><?= htmlspecialchars($row['username']) ?></p>
                            <p class="text-xs text-gray-500 mb-2">NIP: <?= htmlspecialchars($row['nip']) ?> • <?= htmlspecialchars($row['divisi']) ?></p>
                            <ul class="grid grid-cols-3 gap-x-3 gap-y-1 text-sm text-gray-700 dark:text-gray-200 m-0 p-0">
                                <li class="font-semibold col-span-1">Jabatan:</li>
                                <li class="col-span-2"><?= htmlspecialchars($row['jabatan']) ?></li>

                                <li class="font-semibold col-span-1">No HP:</li>
                                <li class="col-span-2"><?= htmlspecialchars($row['no_hp']) ?></li>

                                <li class="font-semibold col-span-1">Pengganti:</li>
                                <li class="col-span-2"><?= htmlspecialchars($row['pengganti']) ?></li>

                                <li class="font-semibold col-span-1">Jenis Cuti:</li>
                                <li class="col-span-2"><?= htmlspecialchars($row['jenis_cuti']) ?></li>

                                <li class="font-semibold col-span-1">Tanggal:</li>
                                <li class="col-span-2"><?= htmlspecialchars($row['tanggal_mulai']) ?> - <?= htmlspecialchars($row['tanggal_akhir']) ?></li>

                                <li class="font-semibold col-span-1">Catatan:</li>
                                <li class="col-span-2"><?= htmlspecialchars($row['catatan']) ?></li>

                                <?php
                                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                $dokumen = $row['dokumen'] ?? '';
                                $dokumen_path = 'uploads/' . urlencode($dokumen);
                                $file_ext = strtolower(pathinfo($dokumen, PATHINFO_EXTENSION));
                                $is_image = in_array($file_ext, $allowed_extensions);
                                ?>

                                <li class="font-semibold col-span-1">Dokumen:</li>
                                <li class="col-span-2">
                                    <?php if (!empty($dokumen) && file_exists($dokumen_path)): ?>
                                        <?php if ($is_image): ?>
                                            <button onclick="openModal('<?= $dokumen_path ?>')" class="text-blue-600 underline">🖼️ lihat</button>
                                        <?php else: ?>
                                            <a href="<?= $dokumen_path ?>" target="_blank" class="text-blue-600 underline">📄</a>
                                        <?php endif; ?>
                                        <?php else: ?>- <?php endif; ?>
                                </li>

                                <li class="font-semibold col-span-1">Status:</li>
                                <li class="col-span-2">
                                    <?php
                                    $status = $row['status_pengajuan'];
                                    $statusClass = '';
                                    $statusText = '';
                                    if ($status === 'Disetujui') {
                                        $statusClass = 'border-green-400 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300';
                                        $statusText = 'Disetujui';
                                    } elseif ($status === 'Ditolak') {
                                        $statusClass = 'border-red-400 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300';
                                        $statusText = 'Ditolak';
                                    } elseif ($status === 'Selesai') {
                                        $statusClass = 'border-blue-400 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300';
                                        $statusText = 'Selesai';
                                    } else {
                                        $statusClass = 'border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-300';
                                        $statusText = htmlspecialchars($status);
                                    }
                                    echo "<span class='inline-block px-3 py-1 border $statusClass rounded-full text-xs font-semibold'>$statusText</span>";
                                    ?>
                                </li>

                                <li class="font-semibold col-span-1">Tgl Konfirmasi:</li>
                                <li class="col-span-1"><?= htmlspecialchars($row['tanggal_disetujui']) ?></li>

                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="hidden md:block overflow-x-auto max-h-[400px] overflow-y-auto scroll-custom">
                    <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="text-gray-900 text-xs uppercase font-semibold" style="background-color: #9AD914; text-align:center">
                            <tr>
                                <th class="px-5 py-3">Name</th>
                                <th class="px-5 py-3">NIP/ID Karyawan</th>
                                <th class="px-5 py-3">Jabatan</th>
                                <th class="px-5 py-3">Divisi</th>
                                <th class="px-5 py-3">No HP</th>
                                <th class="px-5 py-3">Pengganti</th>
                                <th class="px-5 py-3">Jenis Cuti</th>
                                <th class="px-5 py-3">Tanggal Mulai</th>
                                <th class="px-5 py-3">Tanggal Akhir</th>
                                <th class="px-5 py-3">Catatan</th>
                                <th class="px-5 py-3">Dokumen</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Tanggal Konfirmasi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (empty($history)): ?>
                                <tr>
                                    <td colspan="13" class="text-center py-4 text-gray-400">Belum ada data history.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($history as $c): ?>
                                    <tr>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['username']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['nip']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['jabatan']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['divisi']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['no_hp']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['pengganti']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['jenis_cuti']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['tanggal_mulai']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['tanggal_akhir']) ?></td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['catatan']) ?></td>
                                        <?php
                                        $dokumen = $c['dokumen'] ?? '';
                                        $dokumen_path = '../../../uploads/' . urlencode($dokumen);
                                        $file_ext = strtolower(pathinfo($dokumen, PATHINFO_EXTENSION));
                                        $is_image = in_array($file_ext, $allowed_extensions);
                                        ?>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <?php if (!empty($dokumen) && file_exists($dokumen_path)): ?>
                                                <?php if ($is_image): ?>
                                                    <button onclick="openModal('<?= $dokumen_path ?>')" class="text-blue-600 underline">🖼️ lihat</button>
                                                <?php else: ?>
                                                    <a href="<?= $dokumen_path ?>" target="_blank" class="text-blue-600 underline">📄</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <em>Tidak ada</em>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <?php
                                            $status = $c['status_pengajuan'];
                                            $statusClass = '';
                                            $statusText = '';
                                            if ($status === 'Disetujui') {
                                                $statusClass = 'border-green-400 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300';
                                                $statusText = 'Disetujui';
                                            } elseif ($status === 'Ditolak') {
                                                $statusClass = 'border-red-400 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300';
                                                $statusText = 'Ditolak';
                                            } elseif ($status === 'Selesai') {
                                                $statusClass = 'border-blue-400 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300';
                                                $statusText = 'Selesai';
                                            } else {
                                                $statusClass = 'border-gray-300 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-300';
                                                $statusText = htmlspecialchars($status);
                                            }
                                            echo "<span class='inline-block px-3 py-1 border $statusClass rounded-full text-xs font-semibold'>$statusText</span>";
                                            ?>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['tanggal_disetujui']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="flex justify-center mt-4 overflow-x-auto">
                        <ul class="inline-flex -space-x-px">
                            <?php
                            $queryString = $_GET;
                            // Tombol prev
                            if ($page > 1) {
                                $queryString['page'] = $page - 1;
                                $urlPrev = '?' . http_build_query($queryString);
                                echo "<li>
          <a href=\"$urlPrev\" class=\"px-3 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-lime-100 rounded-l-lg flex items-center gap-1 font-semibold transition-all\">
        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M15 19l-7-7 7-7\"/></svg>
        Prev
          </a>
        </li>";
                            } else {
                                echo "<li>
          <span class=\"px-3 py-1 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-l-lg flex items-center gap-1 cursor-not-allowed\">
        <svg class=\"w-4 h-4 mr-1\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M15 19l-7-7 7-7\"/></svg>
        Prev
          </span>
        </li>";
                            }

                            // Nomor halaman
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $queryString['page'] = $i;
                                $url = '?' . http_build_query($queryString);
                                $activeClass = $i == $page
                                    ? 'bg-lime-500 text-white border-lime-500 shadow font-bold scale-110'
                                    : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-lime-100';
                                echo "<li>
          <a href=\"$url\" class=\"px-3 py-1 border border-gray-300 dark:border-gray-600 $activeClass rounded transition-all mx-1\">$i</a>
        </li>";
                            }

                            // Tombol next
                            if ($page < $totalPages) {
                                $queryString['page'] = $page + 1;
                                $urlNext = '?' . http_build_query($queryString);
                                echo "<li>
          <a href=\"$urlNext\" class=\"px-3 py-1 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-lime-100 rounded-r-lg flex items-center gap-1 font-semibold transition-all\">
        Next
        <svg class=\"w-4 h-4 ml-1\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M9 5l7 7-7 7\"/></svg>
          </a>
        </li>";
                            } else {
                                echo "<li>
          <span class=\"px-3 py-1 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-r-lg flex items-center gap-1 cursor-not-allowed\">
        Next
        <svg class=\"w-4 h-4 ml-1\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M9 5l7 7-7 7\"/></svg>
          </span>
        </li>";
                            }
                            ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </article>


            <!-- Animate.css CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
            <script>
                // Optional: re-trigger animation if needed
                document.addEventListener('DOMContentLoaded', function() {
                    const article = document.querySelector('article.animate__animated');
                    if (article) {
                        article.classList.remove('animate__fadeIn');
                        void article.offsetWidth; // trigger reflow
                        article.classList.add('animate__fadeIn');
                    }
                });
            </script>
            <!-- Bootstrap JS (required for modal) -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        </main>

        <!-- image preview -->
        <div id="imageModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); align-items:center; justify-content:center; transition:background 0.3s;">
            <span onclick="closeModal()" style="position:absolute; top:30px; right:40px; color:white; font-size:2rem; cursor:pointer; z-index:10001; transition:color 0.2s;">&times;</span>
            <img id="modalImg" src="" alt="Dokumen" style="max-width:95vw; max-height:90vh; display:block; margin:auto; border-radius:8px; opacity:0; transform:scale(0.85); transition:opacity 0.35s cubic-bezier(.4,2,.6,1), transform 0.35s cubic-bezier(.4,2,.6,1);">
        </div>
        <script>
            function openModal(src) {
                const modal = document.getElementById('imageModal');
                const img = document.getElementById('modalImg');
                img.src = src;
                modal.style.display = 'flex';
                setTimeout(() => {
                    img.style.opacity = '1';
                    img.style.transform = 'scale(1)';
                }, 10);
            }

            function closeModal() {
                const modal = document.getElementById('imageModal');
                const img = document.getElementById('modalImg');
                img.style.opacity = '0';
                img.style.transform = 'scale(0.85)';
                setTimeout(() => {
                    modal.style.display = 'none';
                    img.src = '';
                }, 350);
            }
            // Close modal on outside click
            document.getElementById('imageModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
        </script>


        <!-- mode dark dan light -->
        <script>
            const body = document.body;
            const lightBtn = document.getElementById("lightBtn");
            const darkBtn = document.getElementById("darkBtn");
            const sidebar = document.querySelector(".sidebar");
            const toggleContainer = document.querySelector(".toggle-container");
            const savedSidebar = localStorage.getItem("sidebar-expanded");
            // Saat halaman dimuat, ambil mode dari localStorage
            const savedMode = localStorage.getItem("mode");
            if (savedMode) {
                document.body.classList.add(savedMode);
            } else {
                document.body.classList.add("light-mode"); // default mode
            }


            lightBtn.addEventListener("click", () => {
                body.classList.remove("dark-mode");
                body.classList.add("light-mode");
                lightBtn.classList.add("active");
                darkBtn.classList.remove("active");
                localStorage.setItem("theme", "light");
            });

            darkBtn.addEventListener("click", () => {
                body.classList.remove("light-mode");
                body.classList.add("dark-mode");
                lightBtn.classList.remove("active");
                darkBtn.classList.add("active");
                localStorage.setItem("theme", "dark");
            });

            function toggleProfileMenu() {
                const dropdown = document.getElementById('profileDropdown');
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }

            function logout() {
                alert('Anda Telah Logout');
                window.location.href = 'logout.php';
            }

            document.addEventListener('click', function(event) {
                const profile = document.querySelector('.profile-toggle');
                const dropdown = document.getElementById('profileDropdown');
                if (!profile.contains(event.target)) {
                    dropdown.style.display = 'none';
                }
            });
        </script>


        <!-- Notif -->
        <script>
            document.getElementById('notifBtn').addEventListener('click', function() {
                const panel = document.getElementById('notifPanel');
                const audio = document.getElementById('notifSound');

                panel.classList.toggle('hidden');

                if (!panel.classList.contains('hidden')) {
                    panel.classList.remove('animate-notif');
                    void panel.offsetWidth; // restart animation
                    panel.classList.add('animate-notif');

                    if (audio) {
                        audio.play();
                    }
                }
            });
        </script>

        <?php if ($jumlahNotifBaru > 0): ?>
            <audio id="notifSound" src="asset/notification.mp3" preload="auto"></audio>
        <?php endif; ?>

        <!-- Loader JS -->
        <script>
            async function ambilData() {
                try {
                    const response = await fetch('https://jsonplaceholder.typicode.com/posts/1'); // simulasi API
                    const data = await response.json();

                    document.getElementById('dataOutput').innerText = JSON.stringify(data, null, 2);
                } catch (error) {
                    document.getElementById('dataOutput').innerText = "Gagal memuat data. Coba periksa koneksi Anda.";
                } finally {
                    // Sembunyikan loader setelah data selesai diambil (berhasil/gagal)
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('mainContent').style.display = 'block';
                }
            }

            window.addEventListener('load', ambilData);
        </script>

        <!-- ketika user diam akan keluar -->
        <script>
            // ================================
            // KONFIGURASI & VARIABEL GLOBAL
            // ================================
            const CONFIG = {
                LOGOUT_TIME: 600, // 15 detik untuk testing (ganti ke 600 untuk produksi)
                WARNING_TIME: 10, // 10 detik untuk warning (ganti ke 570 untuk produksi)
                COUNTDOWN_DURATION: 30, // 5 detik countdown untuk testing (ganti ke 30 untuk produksi)
                GRACE_PERIOD: 2000, // 2 detik grace period setelah page load
                ACTIVITY_DELAY: 500, // 0.5 detik delay untuk mendeteksi aktivitas user
                LOADING_DELAY: 1000 // 1 detik delay untuk page loading
            };

            let state = {
                idleTime: 0,
                countdown: CONFIG.COUNTDOWN_DURATION,
                countdownInterval: null,
                idleInterval: null,
                warningShown: false,
                userStarted: false,
                isPageLoading: true,
                pageLoadTime: Date.now()
            };

            // ================================
            // FUNGSI UTILITAS
            // ================================
            function getTimeSinceLoad() {
                return Date.now() - state.pageLoadTime;
            }

            function isValidToShowModal() {
                return state.userStarted &&
                    !state.isPageLoading &&
                    !state.warningShown &&
                    getTimeSinceLoad() >= CONFIG.GRACE_PERIOD;
            }

            function isValidToStartTimer() {
                return state.userStarted &&
                    !state.isPageLoading &&
                    !state.idleInterval;
            }

            function clearAllIntervals() {
                if (state.idleInterval) {
                    clearInterval(state.idleInterval);
                    state.idleInterval = null;
                }
                if (state.countdownInterval) {
                    clearInterval(state.countdownInterval);
                    state.countdownInterval = null;
                }
            }

            // ================================
            // FUNGSI MODAL
            // ================================
            function showModal() {
                if (!isValidToShowModal()) return;

                const modal = document.getElementById("idleWarningModal");
                const countdownEl = document.getElementById("countdown");

                if (modal && countdownEl) {
                    modal.style.display = "flex";
                    countdownEl.innerText = state.countdown;
                    state.warningShown = true;

                    if (state.countdownInterval) {
                        clearInterval(state.countdownInterval);
                    }

                    state.countdownInterval = setInterval(() => {
                        state.countdown--;
                        countdownEl.innerText = state.countdown;

                        if (state.countdown <= 0) {
                            clearInterval(state.countdownInterval);
                            state.countdownInterval = null;
                            window.location.href = "/logout.php";
                        }
                    }, 1000);
                }
            }

            function hideModal() {
                const modal = document.getElementById("idleWarningModal");
                if (modal) {
                    modal.style.display = "none";
                }

                if (state.countdownInterval) {
                    clearInterval(state.countdownInterval);
                    state.countdownInterval = null;
                }

                state.countdown = CONFIG.COUNTDOWN_DURATION;
                state.warningShown = false;
            }

            // ================================
            // FUNGSI TIMER
            // ================================
            function resetIdle() {
                state.idleTime = 0;
                state.warningShown = false;
                hideModal();
            }

            function startIdleTimer() {
                if (!isValidToStartTimer()) return;

                console.log('Starting idle timer'); // Debug
                state.idleInterval = setInterval(() => {
                    state.idleTime++;
                    console.log(`Idle time: ${state.idleTime}s`); // Debug

                    if (state.idleTime >= CONFIG.WARNING_TIME && !state.warningShown &&
                        state.userStarted && !state.isPageLoading) {
                        console.log('Showing warning modal'); // Debug
                        showModal();
                    }

                    if (state.idleTime >= CONFIG.LOGOUT_TIME &&
                        state.userStarted && !state.isPageLoading) {
                        console.log('Auto logout triggered'); // Debug
                        window.location.href = "/logout.php";
                    }
                }, 1000);
            }

            function stayLoggedIn() {
                resetIdle();
                if (state.userStarted && !state.isPageLoading) {
                    startIdleTimer();
                }
            }

            // ================================
            // FUNGSI AKTIVITAS USER
            // ================================
            function handleUserActivity() {
                if (getTimeSinceLoad() < CONFIG.ACTIVITY_DELAY) return;

                if (!state.userStarted && !state.isPageLoading) {
                    console.log('User activity detected - starting timer'); // Debug
                    state.userStarted = true;
                    resetIdle();
                    startIdleTimer();
                } else if (state.userStarted && !state.isPageLoading) {
                    console.log('User activity - resetting idle timer'); // Debug
                    resetIdle();
                    if (!state.idleInterval) {
                        startIdleTimer();
                    }
                }
            }

            function cleanup() {
                clearAllIntervals();
                state.userStarted = false;
                state.isPageLoading = true;
                state.warningShown = false;
                hideModal();
            }

            // ================================
            // EVENT LISTENERS
            // ================================
            function initializeEventListeners() {
                // User activity events
                ["mousemove", "keydown", "click", "scroll", "touchstart"].forEach(evt => {
                    document.addEventListener(evt, handleUserActivity, {
                        passive: true
                    });
                });

                // Page lifecycle events
                window.addEventListener('DOMContentLoaded', () => {
                    state.pageLoadTime = Date.now();
                    state.idleTime = 0;
                    state.userStarted = false;
                    state.isPageLoading = true;
                    state.warningShown = false;
                    hideModal();

                    setTimeout(() => {
                        state.isPageLoading = false;
                        console.log('Page loading complete, user can now interact');

                        // FIX: Jika user tidak langsung gerak, paksa mulai idle
                        if (!state.userStarted && !state.idleInterval) {
                            console.log('No user interaction detected after load, starting idle timer');
                            state.userStarted = true; // anggap user sudah siap
                            startIdleTimer();
                        }
                    }, CONFIG.LOADING_DELAY);
                });

                window.addEventListener('beforeunload', cleanup);

                window.addEventListener('load', () => {
                    state.pageLoadTime = Date.now();
                    cleanup();
                });

                // Visibility change events
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        if (state.idleInterval) {
                            clearInterval(state.idleInterval);
                            state.idleInterval = null;
                        }
                    } else {
                        if (state.userStarted && !state.isPageLoading && !state.idleInterval) {
                            if (getTimeSinceLoad() > CONFIG.GRACE_PERIOD) {
                                startIdleTimer();
                            }
                        }
                    }
                });

                window.addEventListener('focus', () => {
                    if (getTimeSinceLoad() < CONFIG.GRACE_PERIOD) {
                        cleanup();
                    }
                });
            }

            // ================================
            // INISIALISASI
            // ================================
            initializeEventListeners();
        </script>
</body>

</html>