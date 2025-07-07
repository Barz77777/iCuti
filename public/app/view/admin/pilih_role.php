<?php
session_start();

// Jika sudah memilih, langsung arahkan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choose_role'])) {
    $_SESSION['active_role'] = $_POST['choose_role'];
    
    if ($_POST['choose_role'] === 'admin') {
        header("Location: /app/view/admin/beranda-atasan-overview.php");
    } else {
        header("Location: /app/view/user/beranda-user-overview.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih Peran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(to top, #d0dfd3, #ffffff);
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .modal-content {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            background-color: #2d6a4f;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .modal-footer {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .btn-primary {
            background-color: #2d6a4f;
            border: none;
        }

        .btn-secondary {
            background-color: #334036;
            color: white;
            border: none;
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            opacity: 0.9;
        }

        .btn-primary:hover {
            background-color: #334036;
        }

        .modal-body p {
            font-size: 16px;
        }

    </style>
</head>
<body>
    <!-- Modal -->
    <div class="modal show d-block" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Selamat Datang di iCuti</h5>
                    </div>
                    <div class="modal-body text-center">
                        <p>Halo <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>, kamu login sebagai <strong>Admin</strong>.</p>
                        <p class="mb-0">Silakan pilih tampilan yang ingin kamu gunakan:</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" name="choose_role" value="admin" class="btn btn-primary px-4">Sebagai Admin</button>
                        <button type="submit" name="choose_role" value="user" class="btn btn-secondary px-4">Sebagai User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
