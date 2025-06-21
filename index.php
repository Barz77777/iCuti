<?php
// session_start();
// //jika user suda login
// if (isset($_SESSION['user'])) {
//     header("Location: beranda-atasan.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WARNING</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <link rel="stylesheet" href="style.css">
  
</head>

<body>
<main>
  <section class="login-card" aria-labelledby="login-title" role="form">
    <h1 class="judul">PERINGATAN!</h1>


    <p class="peringatan">Silahkan login terlebih dahulu untuk melanjutkan ke aplikasi</p>
    
   
    <button type="submit" class="login-btn" onclick="window.location.href='login.php'">Login</button>
    
  </section>
</main>

<footer>
  &copy; 2025 iCuti
</footer>

<script>
  const menuToggleBtn = document.querySelector('.mobile-menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');

  menuToggleBtn.addEventListener('click', () => {
    const expanded = menuToggleBtn.getAttribute('aria-expanded') === 'true' || false;
    menuToggleBtn.setAttribute('aria-expanded', !expanded);
    mobileMenu.classList.toggle('show');
  });

  // Close mobile menu on window resize if desktop layout
  window.addEventListener('resize', () => {
    if(window.innerWidth >= 768) {
      mobileMenu.classList.remove('show');
      menuToggleBtn.setAttribute('aria-expanded', false);
    }
  });
</script>
</body>
</html>

