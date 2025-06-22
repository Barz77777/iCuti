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
          <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
          </svg>
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
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave Balance</h2>
            <select aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option>Monthly</option>
              <option>Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="leaveBalanceChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold text-lime-600">12 days</p>
        </article>
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Upcoming Leave Schedule</h2>
            <select aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option>Weekly</option>
              <option>Monthly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="upcomingLeaveChart"></canvas>
          </div>
          <p class="mt-3 text-right text-3xl font-bold text-lime-600">4 leaves</p>
        </article>
        <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md flex flex-col justify-between">
          <header class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Pending Leave Requests</h2>
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
            <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave Type Usage</h2>
            <select aria-label="Select period" class="text-sm bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 focus:outline-none focus:ring-2 focus:ring-lime-500">
              <option>Monthly</option>
              <option>Yearly</option>
            </select>
          </header>
          <div class="relative h-36 w-full">
            <canvas id="leaveTypeChart"></canvas>
          </div>
          <p class="mt-3 text-right text-lime-600">Annual: 8 days</p>
          <p class="text-right text-lime-500">Sick: 4 days</p>
        </article>
      </section>
      <section class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md max-w-7xl">
        <header class="flex justify-between items-center mb-4">
          <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Leave Requests Management</h2>
          <button aria-label="Add new leave request" class="bg-lime-600 hover:bg-lime-700 text-white font-semibold px-4 py-2 rounded-full shadow transition focus:outline-none focus:ring-2 focus:ring-lime-400">
            +
          </button>
        </header>
      </section>
    </main>
  </div>

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