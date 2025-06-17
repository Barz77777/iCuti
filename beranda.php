<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['domain'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$domain = $_SESSION['domain'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berhasil Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-success text-white d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="text-center">
        <h1 class="mb-4">✅ Login Berhasil!</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($user) ?></strong></p>
        <p>Anda berhasil login ke domain: <strong><?= htmlspecialchars($domain) ?></strong></p>
        <a href="logout.php" class="btn btn-light mt-3">Logout</a>
    </div>
</body>
</html>
