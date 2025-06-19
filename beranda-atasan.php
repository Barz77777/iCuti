<?php
session_start();
require 'db_connection.php';

// Role yang diperbolehkan mengakses halaman ini
$allowed_role = 'atasan';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $allowed_role) {
  header("Location: login.php");
  exit();
}

// Ambil data dari session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user']; // <- bukan 'username'
$role = $_SESSION['role'];

// Ambil detail user dari database
$query_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = mysqli_query($conn, $query_user);
$user = mysqli_fetch_assoc($result_user);

// Ambil data cuti dari user-user yang diawasinya (berdasarkan atasan_id)
$query = "
    SELECT c.*, u.nama AS nama_user, j.nama AS jenis_cuti
    FROM cuti c
    JOIN users u ON c.user_id = u.id
    JOIN jenis_cuti j ON c.jenis_cuti_id = j.id
    WHERE c.status = 'Menunggu Persetujuan'
      AND u.atasan_id = $user_id
";

$result_cuti = mysqli_query($conn, $query);
$cuti_data = [];

if ($result_cuti && mysqli_num_rows($result_cuti) > 0) {
  while ($row = mysqli_fetch_assoc($result_cuti)) {
    $cuti_data[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="beranda-atasan.css">
  <link rel="stylesheet" href="beranda-atasan.js">
  <title>iCuti</title>
</head>

<body>

  <!-- Checkbox kontrol popup -->
  <input type="checkbox" id="popup-toggle" checked />

  <!-- Popup -->
  <label for="popup-toggle" class="popup-overlay">
    <div class="popup-box" onclick="event.stopPropagation()">
      <a href="#" class="close-btn" onclick="document.getElementById('popup-toggle').checked = false"><i class="bi bi-x"></i></a>
      <h2>Selamat Datang<img src="asset/tangan.png">!</h2>
      <h4><?= htmlspecialchars($user['nama']) ?></h4>
    </div>
  </label>
  <!-- sidebar -->
  <div class="sidebar">
    <div>
      <div class="logo">iCuti</div>
      <div class="menu">
        <a href="#">
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
        <span class="username"><?php echo htmlspecialchars($user['nama']); ?> (<?= htmlspecialchars($role) ?>)</span>
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
              <th>Action</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cuti_data as $cuti): ?>
              <tr>
                <td><?= htmlspecialchars($cuti['nama']) ?></td>
                <td><?= htmlspecialchars($cuti['jenis_cuti']) ?></td>
                <td><?= $cuti['tanggal_mulai'] ?></td>
                <td><?= $cuti['tanggal_akhir'] ?></td>
                <td>
                  <?php
                  $status = $cuti['status'];
                  if ($status == 'Disetujui') {
                    echo '<span class="badge bg-success">Approved</span>';
                  } elseif ($status == 'Ditolak') {
                    echo '<span class="badge bg-danger">Rejected</span>';
                  } elseif ($status == 'Selesai') {
                    echo '<span class="badge bg-primary">Finished</span>';
                  } else {
                    echo '<span class="badge bg-warning text-dark">Waiting For Approval</span>';
                  }
                  ?>
                </td>
                <td>
                  <?php if ($cuti['status'] == 'Menunggu Persetujuan'): ?>
                    <form method="post" action="approval_action.php" style="display:inline-block">
                      <input type="hidden" name="cuti_id" value="<?= $cuti['id'] ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form method="post" action="approval_action.php" style="display:inline-block">
                      <input type="hidden" name="cuti_id" value="<?= $cuti['id'] ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                    </form>
                  <?php else: ?>
                    <em>-</em>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<!-- untuk menghilangkan popup -->
  <script>
  document.addEventListener("DOMContentLoaded", function () {
    // Cek apakah popup sudah pernah ditampilkan di localStorage
    if (localStorage.getItem("popupShown") === "true") {
      // Jika sudah, popup tidak akan muncul
      document.getElementById("popup-toggle").checked = false;
    } else {
      // Jika belum pernah tampil, tampilkan popup dan tandai sudah muncul
      document.getElementById("popup-toggle").checked = true;
      localStorage.setItem("popupShown", "true");
    }
  });
</script>
</body>

</html>