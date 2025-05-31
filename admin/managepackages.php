<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    // Jika ajax request kirim json unauthorized
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    } else {
        header("Location: ../auth/login.php");
        exit();
    }
}

require '../includes/db.php';

// Handle AJAX delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    header('Content-Type: application/json');

    $id = intval($_POST['delete_id']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM travel_packages WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    }
    $conn->close();
    exit();
}

$username = $_SESSION['user_name'];

// Tangkap parameter search jika ada
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Siapkan query dengan search jika ada
if ($search !== '') {
    // Prepared statement untuk pencarian nama paket dengan LIKE
    $stmt = $conn->prepare("SELECT * FROM travel_packages WHERE name LIKE ? ORDER BY id ASC");
    $likeSearch = '%' . $search . '%';
    $stmt->bind_param("s", $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT * FROM travel_packages ORDER BY id ASC";
    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Manage Packages</title>
  <link rel="stylesheet" href="../css/admin/managepackages.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Tambahan style untuk search bar dan refresh button */
    .search-container {
      margin-bottom: 15px;
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .search-input {
      padding: 6px 10px;
      font-size: 16px;
      width: 300px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .btn-refresh {
      padding: 7px 12px;
      font-size: 16px;
      cursor: pointer;
      background-color: #ddd;
      border: 1px solid #aaa;
      border-radius: 4px;
      color: #333;
      transition: background-color 0.3s ease;
    }
    .btn-refresh:hover {
      background-color: #ccc;
    }
  </style>
</head>
<body>
<a href="addpackage.php" class="btn-add-package">+ Add Package</a>

<div class="container">
  <div class="sidebar">
    <div class="logo">Kiran Travel</div>
    <ul>
      <li><a href="dashboard.php"><i class="fas fa-home"></i></a></li>
      <li><a href="manageuser.php"><i class="fas fa-users"></i></a></li>
      <li><a href="managepackages.php"><i class="fas fa-suitcase"></i></a></li>
      <li><a href="transaction.php"><i class="fas fa-file-invoice"></i></a></li>
      <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i></a></li>
    </ul>
  </div>

  <div class="main">
    <div class="topbar">
      <div class="greeting">
        <p>Hello</p>
        <h3><?php echo htmlspecialchars($username); ?></h3>
      </div>
      <div class="user-icon">
        <img src="https://via.placeholder.com/40" alt="User" class="avatar" />
      </div>
    </div>

    <div class="content">
      <h2>Manage Packages</h2>

      <div class="search-container">
        <form method="GET" action="" style="display:flex; gap: 10px; align-items: center;">
          <input 
            type="text" 
            name="search" 
            class="search-input" 
            placeholder="Cari nama paket..." 
            value="<?php echo htmlspecialchars($search); ?>"
            autocomplete="off"
          />
          <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
        </form>
        <button class="btn-refresh" onclick="window.location.href='managepackages.php'">
          <i class="fas fa-sync-alt"></i> Refresh
        </button>
      </div>

      <table class="packages-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Paket</th>
            <th>Jenis</th>
            <th>Lokasi Keberangkatan</th>
            <th>Destinasi</th>
            <th>Tanggal Berangkat</th>
            <th>Tanggal Pulang</th>
            <th>Harga</th>
            <th>Kursi Tersedia</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['trip_type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['departure_location']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['departure_date']) . "</td>";
                  echo "<td>" . ($row['trip_type'] === 'pulang_pergi' ? htmlspecialchars($row['return_date']) : '-') . "</td>";
                  echo "<td>Rp " . number_format($row['price'], 2, ',', '.') . "</td>";
                  echo "<td>" . htmlspecialchars($row['available_seats']) . "</td>";
                  echo "<td>
                          <a href='editpackages.php?id=" . $row['id'] . "' class='btn-edit'><i class='fas fa-edit'></i></a>
                          <button class='btn-delete' data-id='" . $row['id'] . "'><i class='fas fa-trash'></i></button>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='10'>Belum ada data paket.</td></tr>";
          }

          if ($search !== '') {
              $stmt->close();
          }
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
      const packageId = this.getAttribute('data-id');
      if (confirm('Apakah kamu yakin ingin menghapus paket ini?')) {
        fetch('', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: 'delete_id=' + encodeURIComponent(packageId)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Paket berhasil dihapus.');
            const row = this.closest('tr');
            if (row) row.remove();
          } else {
            alert('Gagal menghapus paket: ' + data.message);
          }
        })
        .catch(() => {
          alert('Terjadi kesalahan saat menghapus paket.');
        });
      }
    });
  });
});
</script>
</body>
</html>
