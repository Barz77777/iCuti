<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
  header("Location: index.php");
  exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

include 'db_connection.php';

// 1. Received & Rejected monthly
$labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$received = array_fill(0, 12, 0);
$rejected = array_fill(0, 12, 0);

// Replace 'status' with the actual status column name, e.g., 'approval_status'
$query = "SELECT MONTH(tanggal_mulai) as bulan, status_pengajuan, COUNT(*) as jumlah
FROM cuti
WHERE YEAR(tanggal_mulai) = YEAR(CURDATE())
GROUP BY bulan, status_pengajuan";
$res = $conn->query($query);
while ($row = $res->fetch_assoc()) {
  $i = $row['bulan'] - 1;
  if ($row['status_pengajuan'] === 'Disetujui') $received[$i] = $row['jumlah'];
  elseif ($row['status_pengajuan'] === 'Ditolak') $rejected[$i] = $row['jumlah'];
}

// 2. Awaiting Confirmation
$qWaiting = "SELECT COUNT(*) as total FROM cuti WHERE status_pengajuan = 'Menunggu'";
$waitingTotal = $conn->query($qWaiting)->fetch_assoc()['total'] ?? 0;

// 3. Leave Type
$leaveTypeData = ['labels' => [], 'data' => []];
$qLeaveType = "SELECT jenis_cuti, COUNT(*) as total
FROM cuti
GROUP BY jenis_cuti";

$resType = $conn->query($qLeaveType);
while ($row = $resType->fetch_assoc()) {
  $leaveTypeData['labels'][] = $row['jenis_cuti'];
  $leaveTypeData['data'][] = $row['total'];
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
  <link rel="stylesheet" href="style/beranda-atasan-overview.css" />
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
  <!-- Profile Card -->
  <div class="profile-dropdown" id="profileDropdown">
    <div class="profile-content">
      <div class="user-info">
        <p class="user-name"><?= ($user) ?></p>
        <p class="user-role"><?= ($role) ?></p>
      </div>
    </div>
    <button class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
  </div>

  <div class="layout">
    <div class="sidebar sticky top-10">
      <!-- Logo -->
      <div class="icon-button top-icon profile-toggle" onclick="toggleProfileMenu()"><img src="asset/user-avatar.png" alt="User Avatar">
        <span class="text-icon">Profile</span>
        <i class="menu bi bi-list"></i>
      </div>



      <!-- Menu Icons -->
      <div class="icon-button active sidebar-link" onclick="window.location.href='beranda-atasan-overview.php';">
        <i class="bi bi-grid-fill"></i>
        <span class="text-icon">Overview</span>
      </div>
      <div class="icon-button sidebar-link" onclick="window.location.href='beranda-user-submission.php';">
        <i class="bi bi-envelope-paper"></i>
        <span class="text-icon">Submission</span>
      </div>
      <div class="icon-button" onclick="window.location.href='beranda-user-history.php';">
        <i class="bi bi-clock-history"></i>
        <span class="text-icon">History</span>
      </div>

      <!-- Bottom Icon -->
      <div class="toggle-container">
        <div id="lightBtn" class="icon-btn active sidebar-link"><i class="bi bi-brightness-high"></i></div>
        <div id="darkBtn" class="icon-btn sidebar-link"><i class="bi bi-moon"></i></div>
      </div>
    </div>

    <main class="main-content flex-grow max-w-7xl mx-auto flex flex-col gap-8">
      <header class="flex items-center justify-between space-x-4">
        <div class="flex-grow relative max-w-lg">
          <input type="search" aria-label="Search anything here" placeholder="Search anything here" class="w-full rounded-full border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 pl-10 text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-lime-500" />
          <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="7" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
          </svg>
        </div>
        <button aria-label="Notifications" class="bg-white relative p-2 rounded-full hover:bg-lime-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-lime-400">
          <i class="bi bi-bell text-2xl text-gray-600 dark:text-gray-300"></i>
          <span class="absolute top-1 right-1 inline-block w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
        </button>
      </header>
      <section class="rounded-3xl p-6 shadow-md text-white max-w-4xl" style="background: linear-gradient(135deg, #2D5938 0%, #334036 100%);">
        <h1 class="text-3xl font-bold mb-2 animate-text delay-1">Hello, <?= ($user) ?> <span class="inline-block animate-wave"></span></h1>
        <p class="text-lg font-light animate-text delay-2">How are you feeling about your leave today?</p>
      </section>
      <section class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl">
        <!-- Received -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between initial-hidden">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Received data</h2>
            <select id="receivedPeriod" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border px-2 py-1 focus:ring-lime-500">
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="receivedChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold text-lime-600">
            <?= array_sum($received) ?> data
          </p>
        </article>

        <!-- Rejected -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between initial-hidden">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Rejected data</h2>
            <select id="rejectedPeriod" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border px-2 py-1 focus:ring-lime-500">
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="rejectedChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold" style="color: #ff4040;">
            <?= array_sum($rejected) ?> data
          </p>
        </article>

        <!-- Awaiting Confirmation -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between initial-hidden">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave data awaiting confirmation</h2>
          </header>
          <div class="relative h-36 w-full flex items-center justify-center">
            <span class="text-5xl font-extrabold text-red-500"><?= $waitingTotal ?></span>
          </div>
          <p class="mt-3 text-right text-gray-500 dark:text-gray-300 italic">Requires approval</p>
        </article>

        <!-- Leave Type -->
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between initial-hidden">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave Data</h2>
            <select class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border px-2 py-1 focus:ring-lime-500">
              <option>Monthly</option>
              <option>Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="leaveTypeChart"></canvas>
          </div>
          <?php foreach ($leaveTypeData['labels'] as $i => $type): ?>
            <p class="mt-1 text-right text-lime-600 text-sm italic">
              <?= htmlspecialchars($type) ?>: <?= htmlspecialchars($leaveTypeData['data'][$i]) ?> Data
            </p>
          <?php endforeach; ?>
        </article>
      </section>
    </main>
  </div>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const receivedChart = new Chart(document.getElementById('receivedChart'), {
      type: 'line',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Received',
          data: <?= json_encode($received) ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: '#4ade80',
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

    const rejectedChart = new Chart(document.getElementById('rejectedChart'), {
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

    const leaveTypeChart = new Chart(document.getElementById('leaveTypeChart'), {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($leaveTypeData['labels']) ?>,
        datasets: [{
          data: <?= json_encode($leaveTypeData['data']) ?>,
          backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'right'
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
    window.addEventListener('DOMContentLoaded', () => {
      const boxes = document.querySelectorAll('main .grid article');
      boxes.forEach((box, index) => {
        setTimeout(() => {
          box.classList.remove('initial-hidden');
          box.classList.add('animate-box');
        }, index * 150); // delay antar box untuk efek berurutan
      });
    });
  </script>


</body>

</html>