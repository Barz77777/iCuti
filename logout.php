<?php
session_start();
session_unset();
session_destroy();
?>
<script>
  localStorage.removeItem('popupShown');
  window.location.href = 'login.php';
</script>
