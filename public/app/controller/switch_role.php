<?php
session_start();

//switch role
if ($_SESSION['active_role'] === 'admin') {
    $_SESSION['active_role'] = 'user';
    header("Location: /app/view/user/beranda-user-overview.php");
} else {
    $_SESSION['active_role'] = 'admin';
    header("Location: /app/view/admin/beranda-atasan-overview.php");
}
exit;