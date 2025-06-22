<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style/beranda-user.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <title>iCuti</title>
  <style>

  </style>
</head>

<body>
  <?php
  // Contoh data dari PHP (bisa diganti dengan query database)
  $receivedData = [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'data' => [2, 3, 1, 4, 2, 0]
  ];
  $rejectedData = [
    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    'data' => [0, 1, 0, 2, 1, 0]
  ];
  $leaveTypeData = [
    'labels' => ['Annual', 'Sick'],
    'data' => [8, 4]
  ];
  ?>

  <div class="layout">
    <div class="sidebar">
      <!-- Logo -->
      <div class="icon-button top-icon"><img src="asset/user-avatar.png">
        <span class="text-icon">Profile</span>
      </div>

      <!-- Menu Icons -->
      <div id="sidebarToggle" class="icon-button">
        <i class="bi bi-grid-fill"></i>
        <span class="text-icon">Overview</span>
      </div>
      <div id="sidebarToggle" class="icon-button">
        <i class="bi bi-envelope-paper"></i>
        <span class="text-icon">Submission</span>
      </div>
      <div id="sidebarToggle" class="icon-button">
        <i class="bi bi-clock-history"></i>
        <span class="text-icon">History</span>
      </div>

      <!-- Bottom Icon -->
      <div class="toggle-container" style="margin-top:auto;">
        <div id="lightBtn" class="icon-btn active"><i class="bi bi-brightness-high"></i></div>
        <div id="darkBtn" class="icon-btn"><i class="bi bi-moon"></i></div>
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
        <button aria-label="Notifications" class="relative p-2 rounded-full hover:bg-lime-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-lime-400">
          <i class="bi bi-bell text-2xl text-gray-600 dark:text-gray-300"></i>
          <span class="absolute top-1 right-1 inline-block w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
        </button>
      </header>
      <section class="rounded-3xl p-6 shadow-md text-white max-w-4xl" style="background: linear-gradient(135deg, #2D5938 0%, #334036 100%);">
        <h1 class="text-3xl font-bold mb-2">Hello, Muhammad Akbar!! <span class="inline-block animate-wave">👋</span></h1>
        <p class="text-lg font-light">How are you feeling about your leave today?</p>
      </section>
      <section class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl">
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Received data</h2>
            <select id="receivedPeriod" aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option value="monthly">Monthly</option>
              <option value="yearly">Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="leaveBalanceChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold text-lime-600">12 days</p>
        </article>
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Rejected data</h2>
            <select id="rejectedPeriod" aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option value="weekly">Weekly</option>
              <option value="monthly">Monthly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="upcomingLeaveChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold text-lime-600">4 leaves</p>
        </article>
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave data awaiting confirmation</h2>
            <select aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option>Daily</option>
              <option>Weekly</option>
            </select>
          </header>
          <div class="relative h-36 w-full flex items-center justify-center">
            <span class="text-5xl font-extrabold text-red-500">3</span>
          </div>
          <p class="mt-3 text-right text-gray-500 dark:text-gray-300 italic">Requires approval</p>
        </article>
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave limit</h2>
            <select aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option>Monthly</option>
              <option>Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="leaveTypeChart"></canvas>
          </div>
          <?php foreach ($leaveTypeData['labels'] as $idx => $type): ?>
            <p class="mt-1 text-right text-lime-600">
              <?php echo htmlspecialchars($type); ?>: <?php echo htmlspecialchars($leaveTypeData['data'][$idx]); ?> days
            </p>
          <?php endforeach; ?>
        </article>
      </section>
    </main>
  </div>

  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Data dari PHP ke JS
    const receivedData = <?php echo json_encode($receivedData); ?>;
    const rejectedData = <?php echo json_encode($rejectedData); ?>;
    const leaveTypeData = <?php echo json_encode($leaveTypeData); ?>;

    // Chart: Received Data
    const ctxReceived = document.getElementById('leaveBalanceChart').getContext('2d');
    let receivedChart = new Chart(ctxReceived, {
      type: 'line',
      data: {
        labels: receivedData.labels,
        datasets: [{
          label: 'Received',
          data: receivedData.data,
          borderColor: '#9AD914',
          backgroundColor: 'rgba(132,204,22,0.2)',
          fill: true,
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
        interaction: {
          mode: 'index',
          intersect: false
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Chart: Rejected Data
    const ctxRejected = document.getElementById('upcomingLeaveChart').getContext('2d');
    let rejectedChart = new Chart(ctxRejected, {
      type: 'bar',
      data: {
        labels: rejectedData.labels,
        datasets: [{
          label: 'Rejected',
          data: rejectedData.data,
          backgroundColor: '#f87171'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        interaction: {
          mode: 'index',
          intersect: false
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Chart: Leave Type
    const ctxType = document.getElementById('leaveTypeChart').getContext('2d');
    let leaveTypeChart = new Chart(ctxType, {
      type: 'doughnut',
      data: {
        labels: leaveTypeData.labels,
        datasets: [{
          data: leaveTypeData.data,
          backgroundColor: ['#84cc16', '#facc15']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    // Contoh interaktif: Ganti data chart berdasarkan select period
    document.getElementById('receivedPeriod').addEventListener('change', function() {
      // AJAX fetch data sesuai period, contoh statis:
      if (this.value === 'yearly') {
        receivedChart.data.labels = ['2021', '2022', '2023', '2024'];
        receivedChart.data.datasets[0].data = [12, 14, 10, 16];
      } else {
        receivedChart.data.labels = receivedData.labels;
        receivedChart.data.datasets[0].data = receivedData.data;
      }
      receivedChart.update();
    });

    document.getElementById('rejectedPeriod').addEventListener('change', function() {
      if (this.value === 'monthly') {
        rejectedChart.data.labels = rejectedData.labels;
        rejectedChart.data.datasets[0].data = rejectedData.data;
      } else {
        rejectedChart.data.labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
        rejectedChart.data.datasets[0].data = [0, 1, 2, 1];
      }
      rejectedChart.update();
    });

    // Untuk realtime, polling AJAX bisa ditambahkan di sini
    // setInterval(() => { ...fetch data baru dan update chart... }, 5000);
  </script>

  <script>
    const body = document.body;
    const lightBtn = document.getElementById("lightBtn");
    const darkBtn = document.getElementById("darkBtn");

    lightBtn.addEventListener("click", () => {
      body.classList.remove("dark-mode");
      body.classList.add("ligh-mode");
      lightBtn.classList.add("active");
      darkBtn.classList.remove("active");
    });

    darkBtn.addEventListener("click", () => {
      body.classList.remove("ligh-mode");
      body.classList.add("dark-mode");
      lightBtn.classList.remove("active");
      darkBtn.classList.add("active");
    });
  </script>

</body>

</html>