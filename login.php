<?php
// session_start();

// // Konfigurasi LDAP
// $ldap_server = "ldap://172.10.10.70";
// $ldap_port = 389;
// $domain = "training.local";
// $base_dn = "DC=training,DC=local";

// require 'db_connection.php'; // pastikan file ini ada

// $message = "";
// $message_type = "";

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $username = trim($_POST['username'] ?? '');
//     $password = $_POST['password'] ?? '';

//     if (empty($username) || empty($password)) {
//         $message = "Username dan Password wajib diisi";
//         $message_type = "danger";
//     } else {
//         $ldap_conn = ldap_connect($ldap_server, $ldap_port);

//         if (!$ldap_conn) {
//             $message = "Gagal terhubung ke LDAP.";
//             $message_type = "danger";
//         } else {
//             ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
//             ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

//             $ldap_user = $username . '@' . $domain;
//             // error_log("LDAP user: $ldap_user");
//             error_log("Trying to bind: $ldap_user");

//             if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
//                 error_log("LDAP bind success: $username");
//             } else {
//                 error_log("LDAP bind failed: " . ldap_error($ldap_conn));
//             }
//             // Cek apakah bind berhasil
//             if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
//                 // Cek user di database lokal
//                 $username_clean = mysqli_real_escape_string($conn, $username);
//                 $query = "SELECT * FROM users WHERE username = '$username_clean'";
//                 $result = mysqli_query($conn, $query);

//                 if ($result && mysqli_num_rows($result) > 0) {
//                     $user_data = mysqli_fetch_assoc($result);

//                     $_SESSION['user'] = $username;
//                     $_SESSION['role'] = $user_data['role'];
//                     $_SESSION['user_id'] = $user_data['id']; // opsional untuk tracking user

//                     switch ($user_data['role']) {
//                         case 'admin':
//                             header("Location: beranda-admin.php");
//                             break;
//                         case 'atasan':
//                             header("Location: beranda-atasan.php");
//                             break;
//                         case 'user':
//                         default:
//                             header("Location: beranda-user-submission.php");
//                             break;
//                     }
//                     exit();
//                 } else {
//                     $message = "Akun Anda tidak terdaftar di sistem iCuti.";
//                     $message_type = "danger";
//                 }
//             } else {
//                 $message = "Login gagal: " . ldap_error($ldap_conn);
//                 $message_type = "danger";
//             }

//             ldap_unbind($ldap_conn);
//         }
//     }
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login iCuti</title>
    <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="set.css" />
</head>

<body>
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