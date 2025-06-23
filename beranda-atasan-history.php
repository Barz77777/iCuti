<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

require 'db_connection.php';

// Ambil history cuti yang sudah disetujui atau ditolak
$sql = "SELECT username, nip, jabatan, divisi, no_hp, pengganti, jenis_cuti, tanggal_mulai, tanggal_akhir, catatan, dokumen, status_pengajuan
        FROM cuti 
        WHERE status_pengajuan = 'Ditolak' OR status_pengajuan = 'Disetujui'
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
$history = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $history[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/beranda-atasan-history.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>iCuti</title>
    <style>
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
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-atasan-agreement.php';">
                <i class="bi bi-envelope-paper"></i>
                <span class="text-icon">Agreement</span>
            </div>
            <div class="icon-button active sidebar-link" onclick="window.location.href='beranda-atasan-history.php';">
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
                    <input type="search" aria-label="Search anything here" placeholder="Search anything here" class="box-shadow w-full rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2 pl-10 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-lime-500" />
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>

                <!-- Icon notif -->
                <button aria-label="Notifications" class="border border-white bg-white dark:border-color: #334036 relative p-2 rounded-full hover:bg-lime-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-lime-400">
                    <i class="bi bi-bell text-2xl text-gray-600 dark:text-gray-300"></i>
                    <span class="absolute top-1 right-1 inline-block w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                </button>
            </header>


            <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md h-fit animate__animated animate__fadeIn" style="--animate-duration: 1.2s;">
                <header class="mb-4 flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">History</h2>
                </header>


                

                <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                    <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="text-gray-900 text-xs uppercase font-semibold" style="background-color: #9AD914;">
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
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php if (empty($history)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-gray-400">Belum ada data history.</td>
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
                                        <?php if (!empty($c['dokumen'])): ?>
                                        <?php $dokumen_path = 'uploads/' . urlencode($c['dokumen']); ?>
                                            <td class="px-5 py-3 whitespace-nowrap">
                                                <a href="<?= $dokumen_path ?>" target="_blank">📄 Buka</a>
                                            </td>
                                        <?php else: ?>
                                            <td class="px-5 py-3 whitespace-nowrap"><em>Tidak ada</em></td>
                                        <?php endif; ?>
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
                                                }

                                                echo "<span class='inline-block px-3 py-1 border $statusClass rounded-full text-xs font-semibold'>$statusText</span>";
                                            ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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

</body>

</html>