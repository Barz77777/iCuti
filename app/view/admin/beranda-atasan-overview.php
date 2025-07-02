<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
  header("Location: /projects/iCuti/public/index.php");
  exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

include '../../../config/db_connection.php';

// Tombol "Tandai semua dibaca"
if (isset($_GET['read_all'])) {
  $conn->query("UPDATE notifications SET status = 'dibaca' WHERE penerima_role = 'admin'");
  header("Location: /projects/iCuti/app/view/admin/beranda-atasan-overview.php");
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

// === 2. RECEIVED & REJECTED BULANAN ===
$labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$received = array_fill(0, 12, 0);
$rejected = array_fill(0, 12, 0);

$query = "SELECT MONTH(tanggal_disetujui) AS bulan, status_pengajuan, COUNT(*) AS jumlah 
          FROM cuti 
          WHERE YEAR(tanggal_disetujui) = YEAR(CURDATE()) 
          GROUP BY bulan, status_pengajuan";

$res = $conn->query($query);
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $i = $row['bulan'] - 1;
    if ($row['status_pengajuan'] === 'Disetujui') {
      $received[$i] = $row['jumlah'];
    } elseif ($row['status_pengajuan'] === 'Ditolak') {
      $rejected[$i] = $row['jumlah'];
    }
  }
}

// === 3. YANG MASIH MENUNGGU PERSETUJUAN ===
$qWaiting = "SELECT COUNT(*) as total FROM cuti WHERE status_pengajuan = 'Menunggu'";
$resWaiting = $conn->query($qWaiting);
if (!$resWaiting) {
  die("Error executing query: " . $conn->error);
}
$waitingTotal = $resWaiting->fetch_assoc()['total'] ?? 0;

// === 4. DATA JENIS CUTI ===
$leaveTypeData = ['labels' => [], 'data' => []];
$qLeaveType = "SELECT jenis_cuti, COUNT(*) as total FROM cuti GROUP BY jenis_cuti";
$resType = $conn->query($qLeaveType);
if (!$resType) {
  die("Error executing query: " . $conn->error);
}
while ($row = $resType->fetch_assoc()) {
  $leaveTypeData['labels'][] = $row['jenis_cuti'];
  $leaveTypeData['data'][] = $row['total'];
}

// Sekarang variabel $received, $rejected, $waitingTotal, dan $leaveTypeData bisa digunakan di HTML/chart
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="/projects/iCuti/public/asset/iC.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/projects/iCuti/public/style/beranda-atasan-overview.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>iCuti</title>

  <!-- animasi box table -->
  <style>
    @keyframes fadeScaleIn {
      0% {
        opacity: 0;
        transform: scale(0.95);
      }

      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    .animate-box {
      animation: fadeScaleIn 0.5s ease-out forwards;
      animation-delay: 0ms;
      /* Default delay, overridden by inline styles */
    }

    .initial-hidden {
      opacity: 0;
      transform: scale(0.95);
    }
  </style>
  <!-- animasi text nama username -->
  <style>
    @keyframes fadeSlideUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-text {
      opacity: 0;
      animation: fadeSlideUp 0.8s ease-out forwards;
    }

    .animate-text.delay-1 {
      animation-delay: 0.3s;
    }

    .animate-text.delay-2 {
      animation-delay: 0.6s;
    }
  </style>
</head>

<body>
  <div class="profile-dropdown" id="profileDropdown">
    <div class="profile-content">
      <div class="user-info">
        <p class="user-name"><?= ($user) ?></p>
        <p class="user-role"><?= ($role) ?></p>
      </div>
    </div>
    <button class="logout-btn" onclick="window.location.href='/projects/iCuti/public/logout.php';">Logout</button>
  </div>


  <div class="layout">
    <div class="sidebar sticky top-10">
      <!-- Logo -->
      <div class="icon-button top-icon profile-toggle" onclick="toggleProfileMenu()"><img src="/projects/iCuti/public/asset/default-avatar.png" alt="User Avatar">
        <span class="text-icon">Profile</span>
        <i class="menu bi bi-list"></i>
      </div>

      <!-- Menu Icons -->
      <div class="icon-button active sidebar-link" onclick="window.location.href='beranda-atasan-overview.php';">
        <i class="bi bi-grid-fill"></i>
        <span class="text-icon">Overview</span>
      </div>
      <div class="icon-button sidebar-link" onclick="window.location.href='beranda-atasan-agreement.php';">
        <i class="bi bi-envelope-paper"></i>
        <span class="text-icon">Agreement</span>
      </div>
      <div class="icon-button" onclick="window.location.href='beranda-atasan-history.php';">
        <i class="bi bi-clock-history"></i>
        <span class="text-icon">History</span>
      </div>

      <!-- Bottom Icon -->
      <div class="toggle-container">
        <div id="lightBtn" class="icon-btn active sidebar-link"><i class="bi bi-brightness-high"></i></div>
        <div id="darkBtn" class="icon-btn sidebar-link"><i class="bi bi-moon"></i></div>
      </div>
    </div>

    <!-- tabel, search, dan icon notif -->
    <main class="main-content flex-grow max-w-7xl mx-auto flex flex-col gap-8">
      <!-- search -->
      <header class="flex items-center justify-between space-x-4">
        <div class="flex-grow relative max-w-lg">
          <input type="search" aria-label="Search anything here" placeholder="Search anything here" class="w-full rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 pl-10 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-lime-500" />
          <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>
        <!-- notif -->
        <div class="relative">
          <button id="notifBtn" aria-label="Notifications" class="bg-white relative p-2 rounded-full hover:bg-lime-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-lime-400">
            <i class="bi bi-bell text-2xl text-gray-600 dark:text-gray-300"></i>
            <?php if ($notifResult->num_rows > 0): ?>
              <span id="notifDot" class="absolute top-2 right-2 inline-block w-3 h-3 bg-red-500 rounded-full"></span>
            <?php endif; ?>
          </button>
          <div id="notifDropdown" class="notifikasi bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md absolute right-0 mt-2 w-96 z-50" style="display:none;">
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

      <section class="rounded-3xl p-6 shadow-md text-white max-w-4xl" style="background: linear-gradient(135deg, #2D5938 0%, #334036 100%);">
        <h1 class="text-3xl font-bold mb-2 animate-text delay-1">Hello, <?= ($user) ?>! <span class="inline-block animate-wave"></span></h1>
        <p class="text-lg font-light animate-text delay-2">How are you feeling about your leave today?</p>
      </section>
      <section class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl">

        <!-- Received Data -->

        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md initial-hidden" data-title="Received Data">
          <header class="flex justify-between items-center mb-4">
          <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Received Data</h2>
          </header>
          <div class="relative h-40 w-full">
            <canvas id="leaveBalanceChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold text-lime-600">
            <?= array_sum($received) ?> data
          </p>
        </article>

        <!-- Rejected Data -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md initial-hidden" data-title="Rejected Data">
          <header class="flex justify-between items-center mb-4">
          <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Rejected Data</h2>
          </header>
          <div class="relative h-40 w-full">
            <canvas id="upcomingLeaveChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold" style="color: #ff4040;">
            <?= array_sum($rejected) ?> data
          </p>
        </article>

        <!-- Awaiting Confirmation -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md initial-hidden relative" data-title="Leave Data Awaiting Confitmation">
          <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Leave Data Awaiting Confirmation</h2>

          <div class="flex justify-center items-center h-48">
            <p class="text-7xl font-extrabold text-red-500"><?= htmlspecialchars($waitingTotal) ?></p>
          </div>

          <div class="absolute right-6 bottom-6 text-gray-500 dark:text-gray-300 italic text-sm">
            Requires approval
          </div>
        </article>


        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md initial-hidden" data-title="Leave Data by Type">
          <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100 mb-4">Leave Data by Type</h2>

          <!-- Centered and sized canvas -->
          <div class="flex justify-center items-center">
            <div class="w-[220px] h-[220px] relative">
              <canvas id="leaveTypeChart"></canvas>
            </div>
          </div>
        </article>


      </section>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        // Pass PHP leaveTypeData to JS
        const leaveTypeData = <?= json_encode($leaveTypeData) ?>;

        const receivedChart = new Chart(document.getElementById('leaveBalanceChart'), {
          type: 'line',
          data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
              label: 'Received',
              data: <?= json_encode($received) ?>,
              backgroundColor: 'rgba(104, 192, 75, 0.2)',
              borderColor: '#9AD914',
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });

        const rejectedChart = new Chart(document.getElementById('upcomingLeaveChart'), {
          type: 'line',
          data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
              label: 'Rejected',
              data: <?= json_encode($rejected) ?>,
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              borderColor: '#ef4444',
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });


        const ctx = document.getElementById('leaveTypeChart').getContext('2d');
        const leaveChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: <?= json_encode($leaveTypeData['labels']) ?>,
            datasets: [{
              data: <?= json_encode($leaveTypeData['data']) ?>,
              backgroundColor: ['#7CB342', '#FFEB3B', '#29B6F6', '#EF5350'],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  boxWidth: 14,
                  padding: 12,
                  usePointStyle: true,
                  pointStyle: 'circle',
                  font: {
                    size: 13
                  }
                }
              },
              title: {
                display: false
              }
            }
          }
        });
      </script>

      <script>
        const body = document.body;
        const lightBtn = document.getElementById("lightBtn");
        const darkBtn = document.getElementById("darkBtn");
        const sidebar = document.querySelector(".sidebar");
        const toggleContainer = document.querySelector(".toggle-container");
        const savedSidebar = localStorage.getItem("sidebar-expanded");




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
          window.location.href = '/projects/iCuti/public/logout.php';
        }

        document.addEventListener('click', function(event) {
          const profile = document.querySelector('.profile-toggle');
          const dropdown = document.getElementById('profileDropdown');
          if (!profile.contains(event.target)) {
            dropdown.style.display = 'none';
          }
        });
      </script>

      <!-- search js -->
      <script>
        const searchInput = document.querySelector('input[type="search"]');
        const articles = document.querySelectorAll('main .grid article');

        searchInput.addEventListener('input', function() {
          const keyword = this.value.toLowerCase();

          articles.forEach(article => {
            const title = article.getAttribute('data-title').toLowerCase();
            if (title.includes(keyword)) {
              article.style.display = 'block';
            } else {
              article.style.display = 'none';
            }
          });
        });
      </script>

      <script>
        const ANIMATION_DELAY = 150; // Define delay as a constant for configurability

        window.addEventListener('DOMContentLoaded', () => {
          const boxes = document.querySelectorAll('main .grid article');
          boxes.forEach((box, index) => {
            box.style.animationDelay = `${index * ANIMATION_DELAY}ms`; // Use the constant for delay
            box.classList.remove('initial-hidden');
            box.classList.add('animate-box');
          });
        });
      </script>
</body>

</html>