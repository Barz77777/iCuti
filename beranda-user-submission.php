<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="asset/user-avatar.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/beranda-user-submission.css" />
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
    </style>
</head>

<body>
    <div class="layout">
        <div class="sidebar">
            <!-- Logo -->
            <div class="icon-button top-icon profile-toggle" onclick="toggleProfileMenu()"><img src="asset/user-avatar.png">
                <span class="text-icon">Profile</span>
            </div>

            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-content">
                    <div class="user-info">
                        <p class="user-name">Muhammad Akbar</p>
                        <p class="user-role">Employee</p>
                    </div>
                </div>
                <button class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
            </div>

            <!-- Menu Icons -->
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-user-overview.php';">
                <i class="bi bi-grid-fill"></i>
                <span class="text-icon">Overview</span>
            </div>
            <div class="icon-button active sidebar-link" onclick="window.location.href='beranda-user-submission.php';">
                <i class="bi bi-envelope-paper"></i>
                <span class="text-icon">Submission</span>
            </div>
            <div class="icon-button sidebar-link" onclick="window.location.href='beranda-user-history.php';">
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

            <!-- Tabel  -->

            <article class="bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-md h-fit animate__animated animate__fadeIn" style="--animate-duration: 1.2s;">
                <header class="mb-4 flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-100">Received data</h2>
                    <button
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#submissionModal"
                        class="shadow-xl flex items-center gap-2 px-2 py-1 text-white rounded-md shadow transition-colors duration-200"
                        style="background-color: #2D5938;"
                        onmouseover="this.style.backgroundColor='#24482C';"
                        onmouseout="this.style.backgroundColor='#2D5938';">
                        <i class="bi bi-plus text-lg"></i>
                        <span>Add Submission</span>
                    </button>
                </header>

                <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                    <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead class="text-gray-900 text-xs uppercase font-semibold" style="background-color: #9AD914;">
                            <tr>
                                <th class="px-5 py-3">Name</th>
                                <th class="px-5 py-3">NIP/ID Karyawan</th>
                                <th class="px-5 py-3">Jabatan</th>
                                <th class="px-5 py-3">Divisi</th>
                                <th class="px-5 py-3">Jenis Cuti</th>
                                <th class="px-5 py-3">Tanggal Mulai</th>
                                <th class="px-5 py-3">Tanggal Akhir</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td class="px-5 py-3 whitespace-nowrap">Muhammad Akbar</td>
                                <td class="px-5 py-3 whitespace-nowrap">123456</td>
                                <td class="px-5 py-3 whitespace-nowrap">Staff</td>
                                <td class="px-5 py-3 whitespace-nowrap">IT</td>
                                <td class="px-5 py-3 whitespace-nowrap">Cuti Tahunan</td>
                                <td class="px-5 py-3 whitespace-nowrap">2024-06-01</td>
                                <td class="px-5 py-3 whitespace-nowrap">2024-06-05</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <span class="inline-block px-3 py-1 border border-green-400 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full text-xs font-semibold">
                                        Disetujui
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </article>
            <!-- Animate.css CDN -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
            <script>
                // Optional: re-trigger animation if needed
                document.addEventListener('DOMContentLoaded', function() {
                    const article = document.querySelector('article.animate__animated');
                    if(article) {
                        article.classList.remove('animate__fadeIn');
                        void article.offsetWidth; // trigger reflow
                        article.classList.add('animate__fadeIn');
                    }
                });
            </script>

            <!-- Modal Add Submission -->
            <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="proses_submission.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="submissionModalLabel">Add Leave Submission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Type Of Leave</label>
                                    <select class="form-select" name="jenis_cuti" required>
                                        <option value="">-- Select Leave Type --</option>
                                        <option value="Annual Leave">Annual Leave</option>
                                        <option value="Sick Leave">Sick Leave</option>
                                        <option value="Maternity Leave">Maternity Leave</option>
                                        <option value="Unpaid Leave">Unpaid Leave</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" class="form-control" name="tanggal_mulai" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" class="form-control" name="tanggal_akhir" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="catatan" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Upload Dokumen (PDF/JPG/PNG)</label>
                                    <input type="file" class="form-control" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Template CSV (Opsional)</label><br>
                                    <a href="template-cuti.csv" class="btn btn-sm btn-outline-primary" download>
                                        <i class="bi bi-download"></i> Download Template CSV
                                    </a>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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