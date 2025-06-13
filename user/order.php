<?php include '../includes/check_user.php'; ?>

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


if (!isset($_GET['id'])) {
  echo "ID paket tidak ditemukan.";
  exit;
}
$package_id = $_GET['id'];

// Ambil detail paket
$result = $conn->query("SELECT * FROM travel_packages WHERE id = '$package_id'");
if ($result->num_rows === 0) {
  echo "Paket tidak ditemukan.";
  exit;
}
$paket = $result->fetch_assoc();

if (isset($_POST['submit'])) {
  $user_id = $_SESSION['user_id'];
  $total_people = intval($_POST['jumlah_kursi']);
  $total_price = $total_people * $paket['price'];
  $metode = $_POST['metode_pembayaran'];
  $status = 'pending';

  // Upload bukti
  $file = $_FILES['bukti_bayar'];
  $file_name = time() . '_' . basename($file['name']);
  $target_path = '../img/bukti_bayar/' . $file_name;
  move_uploaded_file($file['tmp_name'], $target_path);


  function generateUniqueId($length = 8) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $uniqueId = '';
    for ($i = 0; $i < $length; $i++) {
        $uniqueId .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $uniqueId;
}

$order_unique_id = generateUniqueId();  // Generate ID unik

// Simpan pesanan
$stmt = $conn->prepare("INSERT INTO orders (user_id, package_id, total_people, total_price, metode_pembayaran, bukti_bayar, status, order_unique_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiidssss", $user_id, $package_id, $total_people, $total_price, $metode, $file_name, $status, $order_unique_id);
$stmt->execute();


  $order_id = $conn->insert_id;

  echo "<script>alert('Pesanan berhasil dibuat. Status: pending'); window.location='cetaktiket.php?id=$order_id';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order Paket</title>
  <link rel="stylesheet" href="../css/user/order.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

<div class="container">
  <!-- Kartu detail paket -->
<!-- Kartu detail paket -->
<div class="detail-paket" style="max-width: 600px; margin: 0 auto; padding: 20px;">
  <h2 style="text-align: center;">Detail Paket</h2>
  <p class="nama-paket" style="font-size: 20px; font-weight: bold; text-align: center;">
    <?= htmlspecialchars($paket['name']) ?>
  </p>

  <div class="row-info">
    <span><i class="fa-solid fa-suitcase-rolling"></i> <strong>Jenis trip</strong></span>
    <span>: <?= $paket['trip_type'] === 'sekali_jalan' ? 'Sekali Jalan' : 'Pulang Pergi'; ?></span>
  </div>

  <div class="row-info">
    <span><i class="fa-solid fa-calendar-days"></i> <strong>Tanggal keberangkatan</strong></span>
    <span>: <?= date('d-m-Y', strtotime($paket['departure_date'])); ?></span>
  </div>

  <div class="row-info">
    <span><i class="fa-solid fa-calendar-days"></i> <strong>Tanggal kembali</strong></span>
    <span>: <?= $paket['return_date'] ? date('d-m-Y', strtotime($paket['return_date'])) : '-'; ?></span>
  </div>

  <div class="row-info">
    <span><i class="fa-solid fa-location-dot"></i> <strong>Lokasi keberangkatan</strong></span>
    <span>: <?= htmlspecialchars($paket['departure_location']); ?></span>
  </div>

  <div class="row-info">
    <span><i class="fa-solid fa-map-marker-alt"></i> <strong>Lokasi tujuan</strong></span>
    <span>: <?= htmlspecialchars($paket['destination']); ?></span>
  </div>

  <div class="row-info">
    <span><i class="fa-solid fa-money-bill-wave"></i> <strong>Harga per orang</strong></span>
    <span>: Rp. <?= number_format($paket['price'], 2, ',', '.'); ?></span>
  </div>
</div>


  <!-- Formulir pemesanan -->
  <div class="form-container">
    <h3>Form Pemesanan</h3>
    <form method="POST" enctype="multipart/form-data">
      <label>Jumlah Kursi:</label>
      <input type="number" name="jumlah_kursi" id="jumlah_kursi" min="1" max="<?= $paket['available_seats'] ?>" value="1" required>

      <label>Total Harga:</label>
      <input type="text" id="total_harga" readonly>

      <label>Metode Pembayaran:</label>
      <select name="metode_pembayaran" required>
        <option value="">-- Pilih --</option>
        <option value="OVO">OVO</option>
        <option value="DANA">DANA</option>
        <option value="Shopeepay">Shopeepay</option>
        <option value="BCA">BCA</option>
        <option value="Indomaret">Indomaret</option>
        <option value="Alfamart">Alfamart</option>
      </select>

      <label>Upload Bukti Pembayaran:</label>
      <input type="file" name="bukti_bayar" accept="image/*" required>

      <div class="button-group">
      <button type="submit" name="submit" class="btn-pesan">Pesan Sekarang</button>
      <a href="index.php"><button type="button" class="btn-kembali">Kembali</button></a>
</div>

  </div>
</div>

<script>
  const harga = <?= $paket['price'] ?>;
  const inputJumlah = document.getElementById('jumlah_kursi');
  const inputTotal = document.getElementById('total_harga');

  function updateHarga() {
    const jumlah = parseInt(inputJumlah.value) || 0;
    inputTotal.value = 'Rp' + (jumlah * harga).toLocaleString('id-ID');
  }

  inputJumlah.addEventListener('input', updateHarga);
  updateHarga();
  

   document.querySelector('.plus-btn').addEventListener('click', () => {
    const input = document.getElementById('jumlah_kursi');
    if (parseInt(input.value) < parseInt(input.max)) {
      input.value = parseInt(input.value) + 1;
    }
  });

  document.querySelector('.minus-btn').addEventListener('click', () => {
    const input = document.getElementById('jumlah_kursi');
    if (parseInt(input.value) > parseInt(input.min)) {
      input.value = parseInt(input.value) - 1;
    }
  });
</script>



</body>
</html>
