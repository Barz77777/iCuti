<?php
session_start();
require '../../../config/db_connection.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

// Tombol "Tandai semua dibaca"
if (isset($_GET['read_all'])) {
    $conn->query("UPDATE notifications SET status = 'dibaca' WHERE penerima_role = 'admin'");
    header("Location: beranda-atasan-agreement.php");
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/asset/iC.png">
    <link rel="icon" href="/asset/user-avatar.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style/beranda-atasan-agreement.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        hijau: '#9AD914'
                    }
                }
            }
        }
    </script>
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
</head>

<body>
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
            <div class="icon-button active sidebar-link" onclick="window.location.href='beranda-atasan-agreement.php';">
                <i class="bi bi-envelope-paper"></i>
                <span class="text-icon">Agreement</span>
            </div>
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-atasan-history.php';">
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

                <!-- Container relatif agar dropdown tidak ganggu layout -->
                <!-- Panel Dropdown Notifikasi -->
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

            <!-- Tabel  -->
            <?php
            // ambil data dari database
            include '../../../config/db_connection.php'; // pastikan ini konek ke database

            $search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

            $query = "SELECT * FROM cuti WHERE status_pengajuan = 'Menunggu' AND username != ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!empty($search)) {
                $query .= " AND (
                    username LIKE '%$search%' OR
                    nip LIKE '%$search%' OR
                    jabatan LIKE '%$search%' OR
                    divisi LIKE '%$search%' OR
                    jenis_cuti LIKE '%$search%' OR
                    tanggal_mulai LIKE '%$search%' OR
                    tanggal_akhir LIKE '%$search%'OR
                    pengganti LIKE '%$search%'
                )";
            }

            $cuti = [];
                while ($row = $result->fetch_assoc()) {
                    $cuti[] = $row;
            }



            ?>

            <?php
            // Pagination logic
            $perPage = 5;
            $totalRows = count($cuti);
            $totalPages = ceil($totalRows / $perPage);
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $start = ($page - 1) * $perPage;
            $cutiPage = array_slice($cuti, $start, $perPage);
            ?>

            <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md h-fit animate__animated animate__fadeIn" style="--animate-duration: 1.2s;">
                <header class="mb-4 flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Received data</h2>
                </header>

                <?php if (isset($_GET['notif'])): ?>
                    <div class="p-4 mb-4 text-sm text-white rounded-lg <?= $_GET['notif'] === 'Disetujui' ? 'bg-green-500' : 'bg-red-500' ?>">
                        Permohonan berhasil <?= htmlspecialchars($_GET['notif']) ?>.
                    </div>
                <?php endif; ?>

                <!-- MOBILE CARD -->
                <div class="block md:hidden space-y-4">
                    <?php foreach ($cutiPage as $c): ?>
                        <div class="border rounded-xl p-4 bg-white dark:bg-gray-800">
                            <p class="text-sm font-semibold text-lime-700"><?= htmlspecialchars($c['username']) ?></p>
                            <p class="text-xs text-gray-500 mb-2">NIP: <?= htmlspecialchars($c['nip']) ?> • <?= htmlspecialchars($c['divisi']) ?></p>
                            <ul class="grid grid-cols-3 gap-x-3 gap-y-1 text-sm text-gray-700 dark:text-gray-200 m-0 p-0">
                                <li class="font-semibold col-span-1">Jabatan:</li>
                                <li class="col-span-2"><?= htmlspecialchars($c['jabatan']) ?></li>

                                <li class="font-semibold col-span-1">No HP:</li>
                                <li class="col-span-2"><?= htmlspecialchars($c['no_hp']) ?></li>

                                <li class="font-semibold col-span-1">Pengganti:</li>
                                <li class="col-span-2"><?= htmlspecialchars($c['pengganti']) ?></li>

                                <li class="font-semibold col-span-1">Jenis Cuti:</li>
                                <li class="col-span-2"><?= htmlspecialchars($c['jenis_cuti']) ?></li>

                                <li class="font-semibold col-span-1">Tanggal:</li>
                                <li class="col-span-2"><?= htmlspecialchars($c['tanggal_mulai']) ?> - <?= htmlspecialchars($c['tanggal_akhir']) ?></li>

                                <li class="font-semibold col-span-1">Catatan:</li>
                                <li class="col-span-2"><?= htmlspecialchars($c['catatan']) ?></li>

                                <?php
                                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                $dokumen = $c['dokumen'] ?? '';
                                $dokumen_path = '../../../uploads/' . urlencode($dokumen);
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
                                    <form method="post" action="/app/controller/proses_approval.php" class="flex gap-2 flex-wrap">
                                        <input type="hidden" name="cuti_id" value="<?= $c['id'] ?>">
                                        <button type="submit" name="aksi" value="setujui" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-full text-xs font-semibold">Setujui</button>
                                        <button type="submit" name="aksi" value="tolak" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs font-semibold">Tolak</button>
                                    </form>
                                </li>

                                <li class="font-semibold col-span-1">Tgl Pengajuan:</li>
                                <li class="col-span-1"><?= htmlspecialchars($c['created_at']) ?></li>

                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- DESKTOP TABLE -->
                <div class="hidden md:block overflow-x-auto max-h-[400px] overflow-y-auto">
                    <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="text-gray-900 text-xs uppercase font-semibold" style="background-color: #9AD914;">
                            <tr>
                                <th class="px-5 py-3">Name</th>
                                <th class="px-5 py-3">NIP/ID Karyawan</th>
                                <th class="px-5 py-3">Jabatan</th>
                                <th class="px-5 py-3">Divisi</th>
                                <th class="px-5 py-3">Nomor HP</th>
                                <th class="px-5 py-3">Tugas Dialihkan Kepada</th>
                                <th class="px-5 py-3">Jenis Cuti</th>
                                <th class="px-5 py-3">Tanggal Mulai</th>
                                <th class="px-5 py-3">Tanggal Akhir</th>
                                <th class="px-5 py-3">Catatan</th>
                                <th class="px-5 py-3">Dokumen</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Tanggal Permohonan Cuti</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (empty($cutiPage)): ?>
                                <tr>
                                    <td colspan="13" class="text-center py-4 text-gray-400">Belum ada data pengajuan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($cutiPage as $c): ?>
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
                                                    <button type="button" onclick="openModal('<?= $dokumen_path ?>')" class="text-blue-600 hover:underline">🖼️ Lihat</button>
                                                <?php else: ?>
                                                    <a href="<?= $dokumen_path ?>" target="_blank" class="text-blue-600 hover:underline">📄 Buka</a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <em>Tidak ada</em>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <form method="post" action="/app/controller/proses_approval.php" class="flex gap-2">
                                                <input type="hidden" name="cuti_id" value="<?= $c['id'] ?>">
                                                <button type="submit" name="aksi" value="setujui" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-full text-xs font-semibold">Setujui</button>
                                                <button type="submit" name="aksi" value="tolak" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs font-semibold">Tolak</button>
                                            </form>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['created_at']) ?></td>
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

        <!-- ketika user diam akan keluar -->
  <script>
    let idleTime = 0;
    const logoutTime = 600; // dalam detik

    // Reset waktu idle saat ada aktivitas
    function resetIdleTime() {
        idleTime = 0;
    }

    // Cek aktivitas user
    window.onload = resetIdleTime;
    document.onmousemove = resetIdleTime;
    document.onkeypress = resetIdleTime;
    document.onscroll = resetIdleTime;
    document.onclick = resetIdleTime;

    // Set timer setiap 1 detik
    setInterval(() => {
        idleTime++;
        if (idleTime >= logoutTime) {
            // Redirect ke logout atau halaman login
            window.location.href = "/logout.php";
        }
    }, 1000);
</script>

</body>

</html>