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
  <link rel="stylesheet" href="beranda-user-submission.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  
  <title>iCuti</title>
  
</head>

<body>



<!-- Checkbox kontrol popup -->
<input type="checkbox" id="popup-toggle" />

<!-- Popup -->
<label for="popup-toggle" class="popup-overlay">
  <div class="popup-box" onclick="event.stopPropagation()">
    <a href="#" class="close-btn" onclick="document.getElementById('popup-toggle').checked = false"><i class="bi bi-x"></i></a>
    <h2>Selamat Datang<img src="asset/tangan.png">!</h2>
    <h4><?= ($user) ?></h4>
  </div>
</label>

<!-- sidebar -->
  <div class="sidebar">
    <div>
      <div class="logo">iCuti</div>
      <div class="menu">
        <a href="beranda-user-submission.php" class="<?= basename($_SERVER['PHP_SELF']) == 'beranda-user-submission.php' ? 'active' : '' ?>">
          <i class="bi bi-envelope-paper"></i>
          <strong>Submission</strong><br></a>
          
        <a href="beranda-user-agreement.php" class="<?= basename($_SERVER['PHP_SELF']) == 'beranda-user-agreement.php' ? 'active' : ''  ?>">
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
        <h2>Submission</h2>
        <button class="btn-add btn-warning text-white" data-bs-toggle="modal" data-bs-target="#submissionModal">+ Add Submission</button>
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
            <tr>
              <td>Farzaliano</td>
              <td>Maternity Leave</td>
              <td>2025-06-08</td>
              <td>2025-06-12</td>
              <td><span class="badge waiting">Waiting For Approval</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  
  
<!-- Modal Add Submission -->
<div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="proses_submission.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="submissionModalLabel">Add Leave Submission</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Type Of Leave</label>
            <select class="form-select" name="jenis_cuti" required>
              <option value="">-- Select Leave Type --</option>
              <option value="Annual Leave">Annual Leave</option>
              <option value="Sick Leave">Sick Leave</option>
              <option value="Maternity Leave">Maternity Leave</option>
              <option value="Unpaid Leave">Unpaid Leave</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" class="form-control" name="tanggal_mulai" required>
          </div>

          <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" class="form-control" name="tanggal_akhir" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea class="form-control" name="catatan" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Upload Dokumen (PDF/JPG/PNG)</label>
            <input type="file" class="form-control" name="dokumen" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Template CSV (Opsional)</label><br>
            <a href="template-cuti.csv" class="btn btn-sm btn-outline-primary" download>
              <i class="bi bi-download"></i> Download Template CSV
            </a>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>
      
  
<?php if (isset($_SESSION['sukses'])): ?>
<!-- Modal notifikasi sukses -->
<div class="modal fade" id="suksesModal" tabindex="-1" aria-labelledby="suksesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-center">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title w-100" id="suksesModalLabel">Done</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 48px;"></i>
        <p class="mt-3 fs-5"><?= $_SESSION['sukses'] ?></p>
      </div>
    </div>
  </div>
</div>

<script>
  // Tampilkan modal sukses setelah halaman dimuat
  const suksesModal = new bootstrap.Modal(document.getElementById('suksesModal'));
  suksesModal.show();
</script>
<?php unset($_SESSION['sukses']); endif; ?>


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
