<?php 
session_start();
include '../includes/check_user.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bantuan - Kiran Travel</title>
  <link rel="stylesheet" href="../css/user/bantuan.css">
  <!-- Font Awesome for WhatsApp icon -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

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

  <div class="help-container">
    <div class="help-box">
      <img src="../img/logowarna.png" alt="Logo Kiran" class="help-logo">
      <h1>Butuh Bantuan?</h1>
      <p>Tim kami siap membantu kamu kapan saja melalui WhatsApp.</p>
      <a href="https://api.whatsapp.com/send?phone=6281234567890&text=Halo%20Kiran%20Travel,%20saya%20butuh%20bantuan" class="btn-wa" target="_blank">
        <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
      </a>
    </div>
  </div>

</body>
</html>
