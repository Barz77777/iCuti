<?php
session_start();
require 'config/db_connection.php';

// konfigurasi LDAPS
$ldap_server = "ldap://172.10.10.61";
$ldap_port   = 389;
$domain      = "nuc.local";
$base_dn     = "DC=nuc,DC=local";

// Ambil input dari form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
// $responseKey = $_POST['g-recaptcha-response'] ?? '';

// // Verifikasi reCAPTCHA
// $secretKey = "6Lcsm3srAAAAANDzZl4WShWkb7fGTE4iGW8fD4mD";
// $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey");
// $captcha_response = json_decode($verify);

// // Jika reCAPTCHA gagal
// if (!$captcha_response->success) {
//     $message = "Silahkan Centang reCAPTCHA'.";
//     $message_type = "danger";
// }
// Jika username/password kosong
if (empty($username) || empty($password)) {
    $message = "Username dan Password wajib diisi.";
    $message_type = "danger";
}
// Lanjut proses login
else {
    // Cek apakah user diblokir
    $stmt = $conn->prepare("SELECT login_attempts, is_banned FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_row = $result->fetch_assoc();

    if ($user_row && $user_row['is_banned']) {
        $message = "Akun Anda telah diblokir karena terlalu banyak percobaan gagal login.";
        $message_type = "danger";
    } else {
        // Proses login ke LDAP
        $ldap_conn = ldap_connect($ldap_server, $ldap_port);
        if (!$ldap_conn) {
            $message = "Gagal terhubung ke server LDAP.";
            $message_type = "danger";
        } else {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

            $ldap_user = $username . '@' . $domain;

            if (@ldap_bind($ldap_conn, $ldap_user, $password)) {
                // Login sukses, ambil grup
                $filter = "(sAMAccountName=$username)";
                $attributes = ['memberOf'];
                $result = ldap_search($ldap_conn, $base_dn, $filter, $attributes);

                if ($result && ldap_count_entries($ldap_conn, $result) > 0) {
                    $entries = ldap_get_entries($ldap_conn, $result);
                    $groups = $entries[0]['memberof'] ?? [];

                    $is_admin = false;
                    foreach ($groups as $group_dn) {
                        if (stripos($group_dn, "CN=PAM_ADMIN") !== false) {
                            $is_admin = true;
                            break;
                        }
                    }

                    // Simpan session
                    $_SESSION['user'] = $username;
                    $_SESSION['role'] = $is_admin ? 'admin' : 'user';
                    $_SESSION['active_role'] = $is_admin ? 'admin' : 'user';

                    // Reset login attempts
                    $conn->query("UPDATE users SET login_attempts = 0 WHERE username = '$username'");

                    ldap_unbind($ldap_conn);

                    // Redirect
                    if ($is_admin) {
                        header("Location: app/view/admin/pilih_role.php");
                    } else {
                        header("Location: app/view/user/beranda-user-overview.php");
                    }
                    exit();
                } else {
                    $message = "Tidak dapat menemukan informasi grup pengguna.";
                    $message_type = "danger";
                }
            } else {
                // Gagal login → tambah attempt
                $attempts = ($user_row['login_attempts'] ?? 0) + 1;
                $is_banned = ($attempts >= 5) ? 1 : 0;

                $update = $conn->prepare("UPDATE users SET login_attempts = ?, is_banned = ? WHERE username = ?");
                $update->bind_param("iis", $attempts, $is_banned, $username);
                $update->execute();

                if ($is_banned) {
                    $message = "Akun Anda telah diblokir karena terlalu banyak percobaan login gagal.";
                } else {
                    $message = "Login gagal: Username atau password salah. Percobaan ke-$attempts.";
                }

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style/set.css" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<style>
    .badge-danger {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background-color: #fef2f2;
        /* bg-red-50 */
        padding: 4px 8px;
        /* py-1 px-2 */
        font-size: 0.75rem;
        /* text-xs */
        font-weight: 500;
        /* font-medium */
        color: #b91c1c;
        /* text-red-700 */
        border: 1px inset rgba(220, 38, 38, 0.1);
        /* ring-1 ring-inset ring-red-600/10 */
        text-align: center;
        /* ⬅️ Pusatkan isi teks multiline */
        width: 100%;
        /* Optional: agar lebar penuh */
        box-sizing: border-box;
        /* Pastikan padding tidak meluap */
    }
</style>


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
                    <?php if (isset($message)): ?>
                        <div class="badge-danger"><?= $message ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-group">
                            <span class="material-icons">person</span>
                            <input type="text" id="username" name="username" required placeholder="Username" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group" style="position: relative;">
                            <span class="material-icons">lock</span>
                            <input type="password" id="password" name="password" required placeholder="Password" />
                            <i class="bi bi-eye-slash" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                        </div>
                    </div>
                    <!-- <div class="g-recaptcha" data-sitekey="6Lcsm3srAAAAAMlHTtTBxEt_zrAfKRYX7ZddqYhI"></div>     -->
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </main>

    

    <!-- Script untuk toggle password -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;

            togglePassword.classList.toggle('bi-eye');
            togglePassword.classList.toggle('bi-eye-slash');
        });
    </script>

    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
</body>

</html>