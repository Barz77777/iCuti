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
