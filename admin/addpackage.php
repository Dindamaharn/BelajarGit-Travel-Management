<?php
session_start();
include '../includes/check_admin.php'; 
$username = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Paket</title>
  <link rel="stylesheet" href="../css/admin/addpackage.css">
  <script>
    function toggleReturnDate() {
      const tripType = document.getElementById('trip_type').value;
      const returnDateField = document.getElementById('return_date_group');
      returnDateField.style.display = (tripType === 'pulang_pergi') ? 'block' : 'none';
    }

    function setMinDate() {
      const today = new Date().toISOString().split('T')[0];
      document.querySelector('input[name="departure_date"]').setAttribute('min', today);
      document.querySelector('input[name="return_date"]').setAttribute('min', today);
    }

    window.onload = function() {
      setMinDate();
      toggleReturnDate();
    };
  </script>

</head>
<body>
  <div class="form-container">
    <h2>Tambah Paket Wisata</h2>
    <form action="addpackage.php" method="POST">
      <div class="form-group">
        <label for="trip_type">Jenis Perjalanan</label>
        <select name="trip_type" id="trip_type" onchange="toggleReturnDate()" required>
          <option value="sekali_jalan">Sekali Jalan</option>
          <option value="pulang_pergi">Pulang Pergi</option>
        </select>
      </div>

      <div class="form-group">
        <label for="name">Nama Paket</label>
        <input type="text" name="name" required>
      </div>

      <div class="form-group">
        <label for="description">Deskripsi</label>
        <textarea name="description" rows="4"></textarea>
      </div>

      <div class="form-group">
        <label for="departure_location">Tempat Keberangkatan</label>
        <input type="text" name="departure_location" required>
      </div>

      <div class="form-group">
        <label for="destination">Destinasi</label>
        <input type="text" name="destination" required>
      </div>

      <div class="form-group">
        <label for="departure_date">Tanggal Keberangkatan</label>
        <input type="date" name="departure_date" required>
      </div>

      <div class="form-group" id="return_date_group" style="display: none;">
        <label for="return_date">Tanggal Kembali</label>
        <input type="date" name="return_date">
      </div>

      <div class="form-group">
        <label for="price">Harga (Rp)</label>
        <input type="number" step="0.01" name="price" required>
      </div>

      <div class="form-group">
        <label for="available_seats">Jumlah Kursi Tersedia</label>
        <input type="number" name="available_seats" required>
      </div>

      <div class="form-actions">
        <button type="submit" name="submit">Tambah Paket</button>
        <a href="managepackages.php" class="cancel-btn">Batal</a>
      </div>
    </form>
  </div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    require '../includes/db.php'; // koneksi ke database

    $trip_type = $_POST['trip_type'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $departure_location = $_POST['departure_location'];
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];
    $return_date = $_POST['return_date'] ?: null; // bisa null
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];
    $created_by = $_SESSION['admin_id']; // pastikan session ini ada

    $query = "INSERT INTO travel_packages 
        (trip_type, name, description, departure_location, destination, departure_date, return_date, price, available_seats, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssddi", $trip_type, $name, $description, $departure_location, $destination, $departure_date, $return_date, $price, $available_seats, $created_by);

    if ($stmt->execute()) {
        $_SESSION['alert'] = [
          'type' => 'success',
          'message' => 'Paket berhasil ditambahkan.'
        ];
        header("Location: managepackages.php?success=1");
        exit();
    } else {
        echo "<script>alert('Gagal menyimpan paket. Coba lagi.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>



