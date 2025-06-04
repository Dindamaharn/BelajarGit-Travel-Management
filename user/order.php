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

  // Simpan pesanan
  $stmt = $conn->prepare("INSERT INTO orders (user_id, package_id, total_people, total_price, metode_pembayaran, bukti_bayar, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiidsss", $user_id, $package_id, $total_people, $total_price, $metode, $file_name, $status);
  $stmt->execute();

  echo "<script>alert('Pesanan berhasil dibuat. Status: pending'); window.location='cekorder.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Order Paket</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    .form-container { max-width: 500px; }
    label { margin-top: 10px; display: block; }
    input, select { width: 100%; padding: 6px; margin-top: 5px; }
    .btn-pesan {
      padding: 10px 20px;
      background-color: #dd5c36;
      color: white;
      border: none;
      border-radius: 6px;
      margin-top: 15px;
      cursor: pointer;
    }
    .btn-pesan:hover { background-color: #b34727; }
  </style>
</head>
<body>

<h2>Detail Paket</h2>
<p><strong><?= htmlspecialchars($paket['name']) ?></strong></p>
<p>Tanggal: <?= htmlspecialchars($paket['departure_date']) ?></p>
<p>Jenis Trip: <?= htmlspecialchars($paket['trip_type']) ?></p>
<p>Harga per Orang: Rp<?= number_format($paket['price'], 0, ',', '.') ?></p>

<hr>

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

    <button type="submit" name="submit" class="btn-pesan">Pesan Sekarang</button>
  </form>

  <a href="index.php" style="display: inline-block; margin-top: 15px; text-decoration: none;">
  <button type="button" style="
    padding: 10px 20px;
    background-color: #888;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  ">← Kembali ke Beranda</button>
</a>

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
</script>

</body>
</html>
