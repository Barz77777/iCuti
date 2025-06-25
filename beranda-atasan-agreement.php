<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

// Ambil notifikasi untuk role 'admin'
$sqlNotif = "SELECT * FROM notifications WHERE penerima_role = 'admin' ORDER BY created_at DESC LIMIT 10";
$resNotif = $conn->query($sqlNotif);
$notifs = $resNotif->fetch_all(MYSQLI_ASSOC);

// Hitung jumlah notifikasi belum dibaca
$sqlJumlah = "SELECT COUNT(*) as total FROM notifications WHERE penerima_role = 'admin' AND status = 'unread'";
$resJumlah = $conn->query($sqlJumlah);
$jumlahNotifBaru = $resJumlah->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="asset/iC.png">
    <link rel="icon" href="asset/user-avatar.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/beranda-atasan-agreement.css" />
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
            <div class="icon-button top-icon profile-toggle" onclick="toggleProfileMenu()"><img src="asset/user-avatar.png">
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
                <button class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
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
        <div class="relative">
          <!-- Tombol lonceng -->
          <button id="notifBtn" aria-label="Notifications" class="bg-white relative p-2 rounded-full hover:bg-lime-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-lime-400">
            <i class="bi bi-bell text-2xl text-gray-600 dark:text-gray-300"></i>
            <?php if ($jumlahNotifBaru > 0): ?>
              <span class="absolute top-1 right-1 inline-block w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            <?php endif; ?>
          </button>

          <!-- Panel Dropdown Notifikasi -->
          <div id="notifPanel" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto animate-fade-slide">
            <div class="p-4 border-b font-semibold text-gray-700 dark:text-white">Notifications</div>
            <?php if (count($notifs) > 0): ?>
              <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($notifs as $notif): ?>
                  <li class="p-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <p class="text-sm font-medium"><?= htmlspecialchars($notif['judul']) ?></p>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($notif['pesan']) ?></p>
                    <p class="text-xs text-gray-400 italic"><?= date("d M Y H:i", strtotime($notif['created_at'])) ?></p>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="p-3 text-sm text-gray-500">No new notifications.</p>
            <?php endif; ?>
          </div>
        </div>

                
            </header>

            <!-- Tabel  -->
            <?php
            // ambil data dari database
            include 'db_connection.php'; // pastikan ini konek ke database

            $search = isset($_GET['q']) ? mysqli_real_escape_string($conn, $_GET['q']) : '';

            $query = "SELECT * FROM cuti WHERE status_pengajuan = 'Menunggu'";

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

            $result = mysqli_query($conn, $query);

            $cuti = [];
                while ($row = mysqli_fetch_assoc($result)) {
            $cuti[] = $row;
            }


            ?>

            <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md h-fit animate__animated animate__fadeIn" style="--animate-duration: 1.2s;">
                <header class="mb-4 flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Received data</h2>
                </header>

                <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                    <?php if (isset($_GET['notif'])): ?>
                <div class="p-4 mb-4 text-sm text-white rounded-lg 
                    <?= $_GET['notif'] === 'Disetujui' ? 'bg-green-500' : 'bg-red-500' ?>">
                    Permohonan berhasil <?= htmlspecialchars($_GET['notif']) ?>.
                </div>
            <?php endif; ?>

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
                            
                            <?php foreach ($cuti as $c): ?>
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
                                    <?php if (!empty($c['dokumen'])): ?>
                                    <?php $dokumen_path = 'uploads/' . urlencode($c['dokumen']); ?>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <a href="<?= $dokumen_path ?>" target="_blank">📄 Buka</a>
                                    </td>
                                    <?php else: ?>
                                        <td class="px-5 py-3 whitespace-nowrap"><em>Tidak ada</em></td>
                                    <?php endif; ?>
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <form method="post" action="proses_approval.php" class="flex gap-2">
                                                <input type="hidden" name="cuti_id" value="<?= $c['id'] ?>">
                                                <button type="submit" name="aksi" value="setujui" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-full text-xs font-semibold">Setujui</button>
                                                <button type="submit" name="aksi" value="tolak" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs font-semibold">Tolak</button>
                                            </form>
                                    </td>
                                    <td class="px-5 py-3 whitespace-nowrap"><?= htmlspecialchars($c['created_at']) ?></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

</body>

</html>