<?php
// Setelah semua proses berhasil
session_start();
$_SESSION['sukses'] = "Your leave request has been submitted!";
header("Location: beranda-user-submission.php");
exit;