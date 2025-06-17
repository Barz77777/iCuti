<?php
session_start();

//konfigurasi LDAP
$ldap_server = "ldap://172.10.10.70"; // Alama LDAP Kalian
$ldap_port = 389;
$domain = "training.local";

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)){
        $message = "Username Dan Password Wajib Diisi";
        $message_type = "danger";
    } else {
        //koneksi LDAP
        $ldap_conn = ldap_connect($ldap_server, $ldap_port);
        if (!$ldap_conn) {
            $message = "Gagal Terhubung Ke LDAP.";
            $message_type = "danger";
        } else {
            //set opsi LDAP
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);


            //format UPN (userPrincipalName)
            $ldap_user = $username . '@' . $domain ;
            
            //coba login
            if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
                $_SESSION['user'] = $username;
                $_SESSION['domain'] = $domain;


                //jika berhasil login redirect ke halaman beranda
                header("location: beranda.php");
                exit();
            } else {
                $message = "Login Gagal: " . ldap_error($ldap_conn);
                $message_type = "danger";
            }

            ldap_unbind($ldap_conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login GoCuti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow" style="width: 400px;">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Login LDAP</h3>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>