<?php
require_once '../includes/session.php';
include '../includes/check_user.php'; 
include '../includes/db.php';


// --- Paginasi ---
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Hitung total data
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM travel_packages"));
$pages = ceil($total / $limit);

// Ambil data
$query = "SELECT * FROM travel_packages ORDER BY departure_date ASC LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Paket Travel</title>
  <link rel="stylesheet" href="../css/user/packages.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
      <meta charset="UTF-8" />
    <title>Kiran Travel & Tour</title>
</head>
<body>
  <script src="../js/script.js"></script>


<!-- Header Navbar -->
<header class="header">
  <div class="container">
    <div class="logo-wrapper">
      <img src="../img/logowarna.jpg" alt="Logo Kiran" />
      <span class="logo-text"><strong>Kiran</strong> Tour & Travel</span>
    </div>
    <nav class="navbar">
      <div class="nav-left">
        <a href="index.php">Beranda</a>
        <a href="packages.php">Paket</a>
        <a href="bantuan.php">Bantuan</a>
        <a href="carabayar.php">Cara Bayar</a>
      </div>
      
      <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-dropdown">
           <a href="cekorder.php">Cek Order</a>
          <button class="dropdown-btn">
            <?= !empty($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' ?> <i class="fa fa-caret-down"></i>
          </button>
          <div class="dropdown-content">
            <a href="profile.php"> Profil </a>
            <a href="../auth/logout.php">Keluar</a>
          </div>
        </div>
        <?php else: ?>
          <a href="../auth/login.php">Masuk</a>
          <a href="register.php" class="btn-daftar">Daftar</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

  <!-- Konten Paket Travel -->
  <div class="container" style="margin-top: 170px;">
    <h1>Daftar Paket Travel</h1>

    <div class="paket-container">
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <div class="card">
          <div class="card-content">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><strong>Tujuan:</strong> <?= htmlspecialchars($row['destination']) ?></p>
            <p><strong>Berangkat dari:</strong> <?= htmlspecialchars($row['departure_location']) ?></p>
            <p><strong>Jadwal:</strong> <?= date("d M Y", strtotime($row['departure_date'])) ?> 
              <?php if ($row['return_date']) echo " - " . date("d M Y", strtotime($row['return_date'])); ?>
            </p>
            <p><?= substr(strip_tags($row['description']), 0, 100) ?>...</p>
          </div>
          <div class="card-footer">
            <span>Rp<?= number_format($row['price'], 0, ',', '.') ?></span>
            <a href="order.php?id=<?= $row['id'] ?>" class="btn">Order</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <?php for ($i = 1; $i <= $pages; $i++) : ?>
        <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
  </div>
  <a href="https://api.whatsapp.com/send?phone=6281234567890&text=Halo%20Kiran%20Travel,%20saya%20ingin%20tanya%20soal%20pembayaran" class="chat-wa" target="_blank">
    <i class="fab fa-whatsapp"></i> Chat Sekarang
  </a>
  <footer class="footer">
  <div class="footer-content">
    <div class="footer-left">
      <img src="../img/logowarna.png" alt="Kiran Tour & Travel" class="footer-logo" />
      <p><strong>PT Trans Kiran Travel</strong></p>
      <p><i class="fas fa-envelope"></i> info@kiran.com</p>
      <p><i class="fas fa-phone"></i> 081234785009</p>
      <p><i class="fas fa-map-marker-alt"></i> Jl. Karimun Jawa IV No. 98B, Kec. Bodat Utara, Kota Surabaya, Jawa Timur 30881</p>
    </div>

    <div class="footer-right">
      <h4>Media Sosial</h4>
      <p><i class="fab fa-instagram"></i> kirantravel</p>
      <p><i class="fab fa-tiktok"></i> kirantravel</p>
      <p><i class="fab fa-facebook-f"></i> kirantravel</p>
    </div>
  </div>

  <div class="footer-bottom">
    <p>© 2025 PT Trans Kiran Travel. All Rights Reserved.</p>
  </div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const dropdownBtn = document.querySelector(".dropdown-btn");
  const dropdownContent = document.querySelector(".dropdown-content");

  if (dropdownBtn && dropdownContent) {
    dropdownBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      dropdownContent.classList.toggle("show");
    });

    document.addEventListener("click", function () {
      dropdownContent.classList.remove("show");
    });
  }
});
</script>

</body>
</html>