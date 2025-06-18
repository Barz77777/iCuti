<?php
session_start();

//konfigurasi LDAP
$ldap_server = "ldap://172.10.10.70"; // Alama LDAP Kalian
$ldap_port = 389;
$domain = "training.local";

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
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

            $ldap_user = $username . '@' . $domain;


            //format UPN (userPrincipalName)
            $ldap_user = $username . '@' . $domain;

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
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>

    <!-- Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="set.css" />

    <meta charset="UTF-8">
    <title>Login GoCuti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <main>
        <!-- text welcome -->
        <section class="login-card" role="main" aria-labelledby="login-heading">
            <h1 id="login-heading">
                Welcome back to <span class="highlight">iCuti</span>
            </h1>
            <p class="subtitle">please enter your details to login in your account!</p>
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <!-- form login -->
            <form method="post" action="" novalidate>
                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <span class="material-icons">person</span>
                        <input id="username" name="username" type="text" placeholder="Username" />
                    </div>
                </div>
                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="material-icons">lock</span>
                        <input id="password" name="password" type="password" placeholder="Password" />
                    </div>
                </div>
                <!-- Tombol Login -->
                <button type="submit" aria-label="Login to your account">Login</button>
            </form>
        </section>

        <section class="image-panel" aria-hidden="true">
            <img src="asset/Desktop - 2.png" alt="sunset" />
        </section>
    </main>
</body>

</html>