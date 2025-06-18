<?php
//session_start();

//if (!isset($_SESSION['user']) || !isset($_SESSION['domain'])) {
    //header("Location: index.php");
  //  exit();
//}

//$user = $_SESSION['user'];
//$domain = $_SESSION['domain'];
//?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="beranda.css">
  
  <title>iCuti</title>
  
</head>
<body>
  <div class="sidebar">
    <div>
      <div class="logo">iCuti</div>
      <div class="menu">
        <a href="#"><strong>Dashboard</strong><br></a>
        <a href="#"><strong>Receipt Log</strong><br></a>
        <a href="#"><strong>Groups</strong><br></a>
        <a href="#"><strong>Reports</strong></a>
        <a href="#"><strong>Settings</strong></a>
        <a href="#"><strong>Developer</strong></a>
      </div>
    </div>
      <div class="logout">
        <a href="logout.php" class="logout-link">
          <i class="bi bi-box-arrow-left"></i>
          Log out
        </a>
      </div>
  </div>
</body>
</html>
