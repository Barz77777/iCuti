<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/beranda-atasan-new.css" />
    <title>iCuti</title>
    <style>
    </style>
</head>

<body>
    <div class="sidebar">
        <!-- Logo -->
        <div class="icon-button top-icon"><img src="asset/user-avatar.png">
            <span class="text-icon">Profile</span>
        </div>

        <!-- Menu Icons -->
        <div id="sidebarToggle" class="icon-button active" onclick="window.location.href='';">
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
        const sidebarToggle = document.getElementById("sidebarToggle");

        // === 1. THEME MODE SWITCH ===
        // Cek preferensi tersimpan
        const savedTheme = localStorage.getItem("theme");

        if (savedTheme === "dark") {
            body.classList.add("dark-mode");
            darkBtn.classList.add("active");
        } else {
            body.classList.add("light-mode");
            lightBtn.classList.add("active");
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

        // === 2. SIDEBAR TOGGLE ===
        // Simpan status sidebar
        const savedSidebar = localStorage.getItem("sidebar-expanded");

        if (savedSidebar === "true") {
            sidebar.classList.add("expanded");
        }

        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("expanded");

            // Ubah arah ikon toggle
            const icon = sidebarToggle.querySelector("i");
            icon.classList.toggle("bi-chevron-double-right");
            icon.classList.toggle("bi-chevron-double-left");

            // Simpan status
            localStorage.setItem("sidebar-expanded", sidebar.classList.contains("expanded"));
        });
    </script>

</body>

</html>