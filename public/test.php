<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: projects/iCuti/index.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

require 'config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unban_user'])) {
    $username = $_POST['unban_user'];
    $stmt = $conn->prepare("UPDATE users SET is_banned = 0, login_attempts = 0 WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
}

// Ambil semua user yang diblokir
$result = $conn->query("SELECT * FROM users WHERE is_banned = 1");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/asset/iC.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style/beranda-atasan-history.css" />
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

        
   <main class="main-content flex-grow max-w-7xl mx-auto flex flex-col gap-8 mt-20">
<article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md h-fit animate__animated animate__fadeIn" style="--animate-duration: 1.2s;">
        <header class="mb-4 flex justify-between items-center">
          <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Account</h2>
        </header>
<div class="hidden md:block overflow-x-auto max-h-[400px] overflow-y-auto">
          <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg">
            <thead class="text-gray-900 text-xs uppercase font-semibold" style="background-color: #9AD914;">
              <tr>
                <th class="px-5 py-3">Username</th>
                <th class="px-5 py-3">NIP</th>
                <th class="px-5 py-3">Aksi</th>
              </tr>
            </thead>
           <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['nip']) ?></td>
            <td>
                <button type="submit" name="unban_user" value="<?= $row['username'] ?>" class="btn btn-success btn-sm">Unban</button>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="3" class="text-center text-muted">Tidak ada akun yang terban.</td>
        </tr>
    <?php endif; ?>
</tbody>

          </table>
        </div>
          </article>
   </main>

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