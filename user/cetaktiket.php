<?php
require_once '../includes/session.php'; // atau path relatifnya
require '../includes/db.php';
include '../includes/check_user.php';

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

// Ambil order_unique_id dari tabel orders
$order_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
$order = $order_query->fetch_assoc();
$order_unique_id = $order['order_unique_id'];
$penumpang = $order['total_people'];
$harga = $order['total_price'];

$data_user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$data_user = $data_user_query->fetch_assoc();
$nama = $data_user['name'];

$package_id = $order['package_id'];
$package_query = $conn->query("SELECT * FROM travel_packages WHERE id = $package_id");
$package_data= $package_query->fetch_assoc();
$nama_package = $package_data['name'];
$jenis = $package_data['trip_type'];
$berangkat = $package_data['departure_location'];
$tujuan = $package_data['destination'];
$tanggal_tujuan = $package_data['departure_date'];
$tanggal_kembali= $package_data['return_date'];


function formatTanggal($tanggal) {
    return date('d-m-Y', strtotime($tanggal));
}

function formatHarga($harga) {
    return 'Rp. ' . number_format($harga, 2, ',', '.');
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
    <div class="kode-unik">Kode Tiket: <?php echo $order_unique_id; ?></div>

    <div class="tiket-header">
      <h2><i class="fa-solid fa-ticket"></i> Tiket Perjalanan</h2>
      <p>Travel Aman & Nyaman Bersama Kami</p>
    </div>

    <div class="tiket-info"><span class="label">Nama Pemesan</span><span class="colon">:</span><span class="value"><?= htmlspecialchars($nama) ?></span></div>
    <div class="tiket-info"><span class="label">Nama Paket</span><span class="colon">:</span><span class="value"><?= htmlspecialchars($nama_package) ?></span></div>
    <div class="tiket-info"><span class="label">Jenis Paket</span><span class="colon">:</span><span class="value"><?= ucwords(str_replace('_', ' ', $jenis)) ?></span></div>
    <div class="tiket-info"><span class="label">Tanggal Berangkat</span><span class="colon">:</span><span class="value"><?= formatTanggal($tanggal_tujuan) ?></span></div>

    <?php if ($tanggal_kembali !== null): ?>
      <div class="tiket-info"><span class="label">Tanggal Kembali</span><span class="colon">:</span><span class="value"><?= formatTanggal($tanggal_kembali) ?></span></div>
    <?php endif; ?>

    <div class="tiket-info"><span class="label">Lokasi Keberangkatan</span><span class="colon">:</span><span class="value"><?= htmlspecialchars($berangkat) ?></span></div>
    <div class="tiket-info"><span class="label">Lokasi Tujuan</span><span class="colon">:</span><span class="value"><?= htmlspecialchars($tujuan) ?></span></div>
    <div class="tiket-info"><span class="label">Jumlah Kursi</span><span class="colon">:</span><span class="value"><?= htmlspecialchars($penumpang) ?></span></div>
    <div class="tiket-info"><span class="label">Total Harga</span><span class="colon">:</span><span class="value"><?= formatHarga($harga) ?></span></div>


    <div class="warning">
      Harap screenshot tiket ini sebagai bukti saat keberangkatan dan pengecekan status pesanan!
    </div>


</div>

</body>
</html>
