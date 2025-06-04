<?php
require_once '../includes/session.php'; // atau path relatifnya
require '../includes/db.php';


if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('Silakan login terlebih dahulu.'); window.location='../auth/login.php';</script>";
  exit;
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Cek apakah user_id benar-benar ada di tabel users
$cekUser = $conn->query("SELECT id FROM users WHERE id = $user_id");
if ($cekUser->num_rows === 0) {
    echo "<script>alert('User tidak valid. Silakan login sebagai pengguna.'); window.location='../auth/login.php';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Order Paket</title>
  <link rel="stylesheet" href="../css/user/cetaktiket.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
<div class="tiket-container">
        <a href="cekorder.php" class="back-button">X</a>
    <div class="kode-unik">Kode Tiket: TKT-928374</div>

    <div class="tiket-header">
      <h2><i class="fa-solid fa-ticket"></i> Tiket Perjalanan</h2>
      <p>Travel Aman & Nyaman Bersama Kami</p>
    </div>

    <div class="tiket-info"><strong>Nama Pemesan:</strong> Dinda Nur Azizah</div>
    <div class="tiket-info"><strong>Nama Paket:</strong> Explore Bromo Sunrise</div>
    <div class="tiket-info"><strong>Jenis Paket:</strong> Sekali Jalan/Pulang Pergi</div>
    <div class="tiket-info"><strong>Tanggal Berangkat:</strong> 12 Juli 2025</div>
    <div class="tiket-info"><strong>Tanggal Kembalit:</strong> 12 Juli 2025</div>
    <div class="tiket-info"><strong>Lokasi Keberangkatan:</strong> Surabaya</div>
    <div class="tiket-info"><strong>Lokasi Tujuan:</strong> Gunung Bromo</div>
    <div class="tiket-info"><strong>Jumlah Kursi:</strong> 2 Orang</div>
    <div class="tiket-info"><strong>Total Harga:</strong> Rp1.200.000</div>

    <div class="warning">
      Harap screenshot tiket ini sebagai bukti saat keberangkatan dan pengecekan status pesanan!
    </div>
  </div>

</body>
</body>
</html>