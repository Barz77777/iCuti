<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}
$user = $_SESSION['user'];
$role = $_SESSION['role'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="beranda-user-agreement.css">
  
  <title>iCuti</title>
  
</head>

<body>

  <!-- sidebar -->
  <div class="sidebar">
    <div>
      <div class="logo">iCuti</div>
      <div class="menu">
        <a href="beranda-user-submission.php" class="<?= basename($_SERVER['PHP_SELF']) == 'beranda-user-submission.php' ? 'active' : '' ?>">
          <i class="bi bi-envelope-paper"></i>
          <strong>Submission</strong><br></a>
        <a href="beranda-user-agreement" class="<?= basename($_SERVER['PHP_SELF']) == 'beranda-user-agreement.php' ? 'active' : '' ?>">
          <i class="bi bi-envelope-paper-fill"></i>
          <strong>Agreement</strong><br></a>
      </div>
    </div>
      <div class="logout">
        <a href="logout.php" class="logout-link">
          <i class="bi bi-box-arrow-left"></i>
          Log out
        </a>
      </div>
  </div>



  <!-- Main Content -->
  <div class="main">
    <!-- Top Navbar -->
    <div class="topbar">
      <div class="title">Dashboard</div>
      <div class="user-info">
        <i class="bi bi-person-circle custom-icon"></i>
        <span class="username"><?= ($user) ?> (<?= ($role) ?>)</span>  
      </div>
    </div>

    <!-- Card Content -->
    <div class="content">
      <div class="card">
        <h2>Agreement</h2>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Types Of Leave</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  
  

      
  









  
</body>
</html>
