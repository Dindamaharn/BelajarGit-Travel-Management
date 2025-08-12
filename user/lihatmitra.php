<?php 
session_start();
require_once '../includes/db.php';

// Cek jika sudah login tapi bukan user
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] !== 'user') {
    // Jika admin diarahkan ke dashboard admin
    header("Location: ../admin/dashboard.php");
    exit();
}


?>

<!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="UTF-8">
      <title>Kiran Travel & Tour</title>
      <link rel="stylesheet" href="../css/user/lihatmitra.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  </head>
  <body>

  <!-- Header -->
  <header class="header">
    <div class="container">
      <div class="logo-wrapper">
        <img src="../img/logowarna.jpg" alt="Logo Kiran">
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
      <a href="cekorder.php">Cek Order</a>
      <a href="login.php">Masuk</a>
      <a href="register.php" class="btn-daftar">Daftar</a>
    </div>
  </nav>

    </div>
  </header>

  <section class="mitra-section">
  <div class="container">
 

    <h2 class="mitra-title">Mitra Bus & Travel Kami</h2>
    <p class="mitra-desc">
      Jelajahi berbagai operator bus kami untuk menemukan pilihan perjalanan terbaik. <br>
      Daftar lengkap direktori operator bus kami menanti Anda. Temukan mitra perjalanan
      ideal untuk pengalaman yang nyaman dan aman
    </p>

    <p class="mitra-count">Menampilkan 55 mitra</p>

    <div class="mitra-grid">
      <ul>
        <li>ADIBUZZ</li>
        <li>Aragon Transport</li>
        <li>Banyumili</li>
        <li>Bina Sarana</li>
        <li>CGTrans</li>
        <!-- Tambah lagi sesuai kebutuhan -->
      </ul>
      <ul>
        <li>ANS</li>
        <li>Areon Trans</li>
        <li>Baraya Travel</li>
        <li>BTM Travel</li>
        <li>Cititrans</li>
      </ul>
      <ul>
        <li>Antar Lintas Sumatra</li>
        <li>Arnes Shuttle</li>
        <li>Bhinneka Sangkuriang</li>
        <li>Cahaya Shuttle</li>
        <li>City Trans Utama</li>
      </ul>
    </div>
  </div>
</section>

  
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