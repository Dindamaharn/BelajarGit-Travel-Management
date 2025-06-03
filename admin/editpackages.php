<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require '../includes/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: managepackages.php");
    exit();
}

$packageId = intval($_GET['id']);
$error = '';
$success = '';

$query = "SELECT * FROM travel_packages WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $packageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: managepackages.php");
    exit();
}

$package = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $trip_type = $_POST['trip_type'];
    $departure_location = trim($_POST['departure_location']);
    $destination = trim($_POST['destination']);
    $departure_date = $_POST['departure_date'];
    $return_date = $_POST['return_date'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];

    if (empty($name) || empty($trip_type) || empty($departure_location) || empty($destination) || empty($departure_date) || empty($price) || empty($available_seats)) {
        $error = "Semua field wajib diisi kecuali tanggal pulang untuk trip sekali jalan.";
    } else if ($trip_type === 'pulang_pergi' && empty($return_date)) {
        $error = "Tanggal pulang harus diisi untuk trip pulang pergi.";
    } else {
        $updateQuery = "UPDATE travel_packages SET name = ?, trip_type = ?, departure_location = ?, destination = ?, departure_date = ?, return_date = ?, price = ?, available_seats = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $return_date_db = ($trip_type === 'pulang_pergi') ? $return_date : NULL;
        $stmt->bind_param("ssssssdii", $name, $trip_type, $departure_location, $destination, $departure_date, $return_date_db, $price, $available_seats, $packageId);

        if ($stmt->execute()) {
            $success = "Paket berhasil diperbarui.";
            $package = [
                'name' => $name,
                'trip_type' => $trip_type,
                'departure_location' => $departure_location,
                'destination' => $destination,
                'departure_date' => $departure_date,
                'return_date' => $return_date_db,
                'price' => $price,
                'available_seats' => $available_seats,
            ];
        } else {
            $error = "Terjadi kesalahan saat memperbarui data paket.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Package</title>
  <link rel="stylesheet" href="../css/admin/editpackages.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Minimal tambahan khusus halaman ini */
    #return_date_field { display: none; }
  </style>
  <script>
    function toggleReturnDate() {
      const tripType = document.getElementById('trip_type').value;
      const returnDateField = document.getElementById('return_date_field');
      if (tripType === 'pulang_pergi') {
        returnDateField.style.display = 'block';
      } else {
        returnDateField.style.display = 'none';
      }
    }
    window.onload = function() {
      toggleReturnDate();
    }
  </script>
</head>
<body>
  <div class="container">

    <h2>Edit Package</h2>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="name">Nama Paket</label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($package['name']); ?>" />
      </div>

      <div class="form-group">
        <label for="trip_type">Jenis Trip</label>
        <select id="trip_type" name="trip_type" onchange="toggleReturnDate()" required>
          <option value="sekali_jalan" <?php echo ($package['trip_type'] === 'sekali_jalan') ? 'selected' : ''; ?>>Sekali Jalan</option>
          <option value="pulang_pergi" <?php echo ($package['trip_type'] === 'pulang_pergi') ? 'selected' : ''; ?>>Pulang Pergi</option>
        </select>
      </div>

      <div class="form-group">
        <label for="departure_location">Lokasi Keberangkatan</label>
        <input type="text" id="departure_location" name="departure_location" required value="<?php echo htmlspecialchars($package['departure_location']); ?>" />
      </div>

      <div class="form-group">
        <label for="destination">Destinasi</label>
        <input type="text" id="destination" name="destination" required value="<?php echo htmlspecialchars($package['destination']); ?>" />
      </div>

      <div class="form-group">
        <label for="departure_date">Tanggal Berangkat</label>
        <input type="date" id="departure_date" name="departure_date" required value="<?php echo htmlspecialchars($package['departure_date']); ?>" />
      </div>

      <div class="form-group" id="return_date_field">
        <label for="return_date">Tanggal Pulang</label>
        <input type="date" id="return_date" name="return_date" value="<?php echo htmlspecialchars($package['return_date']); ?>" />
      </div>

      <div class="form-group">
        <label for="price">Harga (Rp)</label>
        <input type="number" id="price" name="price" required step="0.01" min="0" value="<?php echo htmlspecialchars($package['price']); ?>" />
      </div>

      <div class="form-group">
        <label for="available_seats">Kursi Tersedia</label>
        <input type="number" id="available_seats" name="available_seats" required min="0" value="<?php echo htmlspecialchars($package['available_seats']); ?>" />
      </div>

      <div class="button-row">
        <button type="submit" class="btn-submit">Update Package</button>
        <a href="managepackages.php" class="btn-back">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
