<?php
session_start();

if(!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];
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
    <title>iCuti</title>
    <style>
    </style>
</head>

<body>
    <div class="sidebar">
        <!-- Logo -->
        <div class="icon-button top-icon profile-toggle" onclick="toggleProfileMenu()"><img src="asset/user-avatar.png" alt="User Avatar">
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
        <div class="toggle-container">
            <div id="lightBtn" class="icon-btn active"><i class="bi bi-brightness-high"></i></div>
            <div id="darkBtn" class="icon-btn"><i class="bi bi-moon"></i></div>
        </div>
    </div>

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

</body>

</html>