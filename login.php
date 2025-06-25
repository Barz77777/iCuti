<?php
 session_start();

// Konfigurasi LDAP
$ldap_server = "ldap://172.10.10.70";
$ldap_port = 389;
$domain = "training.local";
$base_dn = "DC=training,DC=local";

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Username dan Password wajib diisi";
        $message_type = "danger";
    } else {
        $ldap_conn = ldap_connect($ldap_server, $ldap_port);

        if (!$ldap_conn) {
            $message = "Gagal terhubung ke LDAP.";
            $message_type = "danger";
        } else {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

            $ldap_user = $username . '@' . $domain;

            // Autentikasi LDAP
            if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
                // Cari informasi user untuk mendapatkan atribut grup
                $filter = "(sAMAccountName=$username)";
                $attributes = ['memberOf'];
                $result = ldap_search($ldap_conn, $base_dn, $filter, $attributes);

                if ($result && ldap_count_entries($ldap_conn, $result) > 0) {
                    $entries = ldap_get_entries($ldap_conn, $result);
                    $groups = $entries[0]['memberof'] ?? [];

                    $is_admin = false;

                    // Periksa apakah user ada dalam grup PAM_ADMIN
                    foreach ($groups as $group_dn) {
                        if (stripos($group_dn, "CN=PAM_ADMIN") !== false) {
                            $is_admin = true;
                            break;
                        }
                    }

                    $_SESSION['user'] = $username;
                    $_SESSION['role'] = $is_admin ? 'admin' : 'user';

                    if ($is_admin) {
                        header("Location: beranda-atasan-overview.php");
                    } else {
                        header("Location: beranda-user-overview.php");
                    }
                    exit();
                } else {
                    $message = "Tidak dapat menemukan informasi grup pengguna.";
                    $message_type = "danger";
                }
            } else {
                $message = "Login gagal: " . ldap_error($ldap_conn);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="asset/iC.png">
    <title>Login iCuti</title>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="style/set.css" />
</head>

<body>
    <p>VistaTraining1@2025</p>
    <main>
        <div class="login-container">
            <div class="image-side">
                <img src="asset/Chill-Time.png" alt="Login Illustration" />
            </div>
            <div class="form-side">
                <h1>Welcome back to <span class="highlight">iCuti</span></h1>
                <p class="subtitle">Please enter your details to login to your account!</p>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-group">
                            <span class="material-icons">person</span>
                            <input type="text" id="username" name="username" required placeholder="Username" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <span class="material-icons">lock</span>
                            <input type="password" id="password" name="password" required placeholder="Password" />
                        </div>
                    </div>      
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </main>

    

</body>

</html>