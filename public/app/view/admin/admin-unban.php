<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: /index.php");
    exit();
}

$user = $_SESSION['user'];
$role = $_SESSION['role'];

require '../../../config/db_connection.php';

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

    <style>
        /* Loader full screen */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .morphing-squares {
            display: flex;
            gap: 8px;
        }

        .morphing-squares div {
            width: 15px;
            height: 15px;
            background: linear-gradient(45deg, #9AD914, #8ABF17);
            border-radius: 2px;
            animation: morphSquares 1.5s ease-in-out infinite;
        }

        .morphing-squares div:nth-child(1) {
            animation-delay: 0s;
        }

        .morphing-squares div:nth-child(2) {
            animation-delay: 0.1s;
        }

        .morphing-squares div:nth-child(3) {
            animation-delay: 0.2s;
        }

        .morphing-squares div:nth-child(4) {
            animation-delay: 0.3s;
        }

        .morphing-squares div:nth-child(5) {
            animation-delay: 0.4s;
        }

        @keyframes morphSquares {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
                border-radius: 2px;
            }

            50% {
                transform: scale(1.5) rotate(180deg);
                border-radius: 50%;
            }
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div id="loader">
        <div class="morphing-squares">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- Alert Tidak Ada Aktivitas -->
    <div id="idleWarningModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 9999; justify-content: center; align-items: center; overflow: auto; padding: 20px;">
        <div style="background: #fff; padding: 40px 30px; border-radius: 25px; text-align: center; max-width: 400px; width: 100%; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);">

            <!-- Ikon Peringatan -->
            <img src="/asset/alert.svg" alt="Warning Icon" style="display: block; margin: 0 auto 25px; width: 120px; max-width: 100%; height: auto;">

            <!-- Judul -->
            <h2 style="font-size: 20px; color: #333; margin-bottom: 10px; font-weight: 700;">Tidak Ada Aktivitas!</h2>

            <!-- Subjudul -->
            <p style="font-size: 15px; color: #555; margin-bottom: 20px;">Anda akan logout dalam <span id="countdown" style="font-weight: bold; color: #e74c3c">30</span> detik.</p>

            <!-- Tombol Aksi -->
            <button onclick="stayLoggedIn()" style="padding: 10px 24px; background-color: #9AD914; border: none; color: white; font-weight: bold; font-size: 14px;border-radius: 8px; cursor: pointer; box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); transition: background 0.3s ease;">Saya Ada di Sini!</button>
        </div>
    </div>

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
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-atasan-history.php';">
                <i class="bi bi-clock-history"></i>
                <span class="text-icon">History</span>
            </div>
            <div class="icon-button active sidebar-link" onclick="window.location.href='admin-unban.php';">
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

                <!-- TABEL UNTUK DESKTOP -->
                <div class="hidden md:block overflow-x-auto max-h-[400px] overflow-y-auto">
                    <form method="post">
                        <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <thead class="text-gray-900 text-xs uppercase font-semibold" style="background-color: #9AD914; text-align:center">
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
                                            <td class="px-5 py-3"><?= htmlspecialchars($row['username']) ?></td>
                                            <td class="px-5 py-3"><?= htmlspecialchars($row['nip']) ?></td>
                                            <td class="px-5 py-3">
                                                <button type="submit" name="unban_user" value="<?= $row['username'] ?>" class="btn btn-success btn-sm">Unban</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted px-5 py-3">Tidak ada akun yang terban.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>

                <!-- CARD UNTUK MOBILE -->
                <div class="block md:hidden space-y-4">
                    <form method="post">
                        <?php if ($result->num_rows > 0): ?>
                            <?php
                            $result->data_seek(0); // reset pointer
                            while ($row = $result->fetch_assoc()): ?>
                                <div class="border-l-8 border-lime-500 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm relative">
                                    <div class="space-y-1">
                                        <p class="text-sm"><span class="font-semibold"><i class="bi bi-person-fill"></i> Username:</span> <?= htmlspecialchars($row['username']) ?></p>
                                        <p class="text-sm"><span class="font-semibold"><i class="bi bi-credit-card-2-front-fill"></i> NIP:</span> <?= htmlspecialchars($row['nip']) ?></p>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" name="unban_user" value="<?= $row['username'] ?>" class="bg-lime-500 hover:bg-lime-700 text-white text-sm px-4 py-1 rounded">
                                            Unban
                                        </button>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center text-gray-500 dark:text-gray-300">Tidak ada akun yang terban.</div>
                        <?php endif; ?>
                    </form>
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

        <!-- Loader JS -->
        <script>
            async function ambilData() {
                try {
                    const response = await fetch('https://jsonplaceholder.typicode.com/posts/1'); // simulasi API
                    const data = await response.json();

                    document.getElementById('dataOutput').innerText = JSON.stringify(data, null, 2);
                } catch (error) {
                    document.getElementById('dataOutput').innerText = "Gagal memuat data. Coba periksa koneksi Anda.";
                } finally {
                    // Sembunyikan loader setelah data selesai diambil (berhasil/gagal)
                    document.getElementById('loader').style.display = 'none';
                    document.getElementById('mainContent').style.display = 'block';
                }
            }

            window.addEventListener('load', ambilData);
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
        <!-- ketika user diam akan keluar -->
        <script>
            // ================================
            // KONFIGURASI & VARIABEL GLOBAL
            // ================================
            const CONFIG = {
                LOGOUT_TIME: 600, // 15 detik untuk testing (ganti ke 600 untuk produksi)
                WARNING_TIME: 600, // 10 detik untuk warning (ganti ke 570 untuk produksi)
                COUNTDOWN_DURATION: 30, // 5 detik countdown untuk testing (ganti ke 30 untuk produksi)
                GRACE_PERIOD: 2000, // 2 detik grace period setelah page load
                ACTIVITY_DELAY: 500, // 0.5 detik delay untuk mendeteksi aktivitas user
                LOADING_DELAY: 1000 // 1 detik delay untuk page loading
            };

            let state = {
                idleTime: 0,
                countdown: CONFIG.COUNTDOWN_DURATION,
                countdownInterval: null,
                idleInterval: null,
                warningShown: false,
                userStarted: false,
                isPageLoading: true,
                pageLoadTime: Date.now()
            };

            // ================================
            // FUNGSI UTILITAS
            // ================================
            function getTimeSinceLoad() {
                return Date.now() - state.pageLoadTime;
            }

            function isValidToShowModal() {
                return state.userStarted &&
                    !state.isPageLoading &&
                    !state.warningShown &&
                    getTimeSinceLoad() >= CONFIG.GRACE_PERIOD;
            }

            function isValidToStartTimer() {
                return state.userStarted &&
                    !state.isPageLoading &&
                    !state.idleInterval;
            }

            function clearAllIntervals() {
                if (state.idleInterval) {
                    clearInterval(state.idleInterval);
                    state.idleInterval = null;
                }
                if (state.countdownInterval) {
                    clearInterval(state.countdownInterval);
                    state.countdownInterval = null;
                }
            }

            // ================================
            // FUNGSI MODAL
            // ================================
            function showModal() {
                if (!isValidToShowModal()) return;

                const modal = document.getElementById("idleWarningModal");
                const countdownEl = document.getElementById("countdown");

                if (modal && countdownEl) {
                    modal.style.display = "flex";
                    countdownEl.innerText = state.countdown;
                    state.warningShown = true;

                    if (state.countdownInterval) {
                        clearInterval(state.countdownInterval);
                    }

                    state.countdownInterval = setInterval(() => {
                        state.countdown--;
                        countdownEl.innerText = state.countdown;

                        if (state.countdown <= 0) {
                            clearInterval(state.countdownInterval);
                            state.countdownInterval = null;
                            window.location.href = "/logout.php";
                        }
                    }, 1000);
                }
            }

            function hideModal() {
                const modal = document.getElementById("idleWarningModal");
                if (modal) {
                    modal.style.display = "none";
                }

                if (state.countdownInterval) {
                    clearInterval(state.countdownInterval);
                    state.countdownInterval = null;
                }

                state.countdown = CONFIG.COUNTDOWN_DURATION;
                state.warningShown = false;
            }

            // ================================
            // FUNGSI TIMER
            // ================================
            function resetIdle() {
                state.idleTime = 0;
                state.warningShown = false;
                hideModal();
            }

            function startIdleTimer() {
                if (!isValidToStartTimer()) return;

                console.log('Starting idle timer'); // Debug
                state.idleInterval = setInterval(() => {
                    state.idleTime++;
                    console.log(`Idle time: ${state.idleTime}s`); // Debug

                    if (state.idleTime >= CONFIG.WARNING_TIME && !state.warningShown &&
                        state.userStarted && !state.isPageLoading) {
                        console.log('Showing warning modal'); // Debug
                        showModal();
                    }

                    if (state.idleTime >= CONFIG.LOGOUT_TIME &&
                        state.userStarted && !state.isPageLoading) {
                        console.log('Auto logout triggered'); // Debug
                        window.location.href = "/logout.php";
                    }
                }, 1000);
            }

            function stayLoggedIn() {
                resetIdle();
                if (state.userStarted && !state.isPageLoading) {
                    startIdleTimer();
                }
            }

            // ================================
            // FUNGSI AKTIVITAS USER
            // ================================
            function handleUserActivity() {
                if (getTimeSinceLoad() < CONFIG.ACTIVITY_DELAY) return;

                if (!state.userStarted && !state.isPageLoading) {
                    console.log('User activity detected - starting timer'); // Debug
                    state.userStarted = true;
                    resetIdle();
                    startIdleTimer();
                } else if (state.userStarted && !state.isPageLoading) {
                    console.log('User activity - resetting idle timer'); // Debug
                    resetIdle();
                    if (!state.idleInterval) {
                        startIdleTimer();
                    }
                }
            }

            function cleanup() {
                clearAllIntervals();
                state.userStarted = false;
                state.isPageLoading = true;
                state.warningShown = false;
                hideModal();
            }

            // ================================
            // EVENT LISTENERS
            // ================================
            function initializeEventListeners() {
                // User activity events
                ["mousemove", "keydown", "click", "scroll", "touchstart"].forEach(evt => {
                    document.addEventListener(evt, handleUserActivity, {
                        passive: true
                    });
                });

                // Page lifecycle events
                window.addEventListener('DOMContentLoaded', () => {
                    state.pageLoadTime = Date.now();
                    state.idleTime = 0;
                    state.userStarted = false;
                    state.isPageLoading = true;
                    state.warningShown = false;
                    hideModal();

                    setTimeout(() => {
                        state.isPageLoading = false;
                        console.log('Page loading complete, user can now interact');

                        // FIX: Jika user tidak langsung gerak, paksa mulai idle
                        if (!state.userStarted && !state.idleInterval) {
                            console.log('No user interaction detected after load, starting idle timer');
                            state.userStarted = true; // anggap user sudah siap
                            startIdleTimer();
                        }
                    }, CONFIG.LOADING_DELAY);
                });

                window.addEventListener('beforeunload', cleanup);

                window.addEventListener('load', () => {
                    state.pageLoadTime = Date.now();
                    cleanup();
                });

                // Visibility change events
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        if (state.idleInterval) {
                            clearInterval(state.idleInterval);
                            state.idleInterval = null;
                        }
                    } else {
                        if (state.userStarted && !state.isPageLoading && !state.idleInterval) {
                            if (getTimeSinceLoad() > CONFIG.GRACE_PERIOD) {
                                startIdleTimer();
                            }
                        }
                    }
                });

                window.addEventListener('focus', () => {
                    if (getTimeSinceLoad() < CONFIG.GRACE_PERIOD) {
                        cleanup();
                    }
                });
            }

            // ================================
            // INISIALISASI
            // ================================
            initializeEventListeners();
        </script>
</body>

</html>