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

// Konfigurasi paginasi
$limit = 7; // 7 data per halaman
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;


// Siapkan query dengan search jika ada
if ($search !== '') {

   $likeSearch = '%' . $search . '%'; // <<< Tambahkan ini sebelum bind_param

    $likeSearch = '%' . $search . '%';
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM travel_packages WHERE 
        name LIKE ? OR 
        trip_type LIKE ? OR 
        departure_location LIKE ? OR 
        destination LIKE ?");
    $countStmt->bind_param("ssss", $likeSearch, $likeSearch, $likeSearch, $likeSearch);


        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalRows = $countResult->fetch_assoc()['total'];
        $countStmt->close();

    // Ambil data dengan paginasi

    $stmt = $conn->prepare("SELECT * FROM travel_packages WHERE 
        name LIKE ? OR 
        trip_type LIKE ? OR 
        departure_location LIKE ? OR 
        destination LIKE ?
        ORDER BY id ASC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssssii", $likeSearch, $likeSearch, $likeSearch, $likeSearch, $limit, $offset);


    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $countResult = $conn->query("SELECT COUNT(*) as total FROM travel_packages");
    $totalRows = $countResult->fetch_assoc()['total'];

    $query = "SELECT * FROM travel_packages ORDER BY id ASC LIMIT $limit OFFSET $offset";
    $result = $conn->query($query);
}

// Hitung total halaman
$totalPages = ceil($totalRows / $limit); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Manage Packages</title>
  <link rel="stylesheet" href="../css/admin/managepackages.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    .pagination-link {
      display: inline-block;
      padding: 8px 12px;
      margin: 2px;
      background-color: #f0f0f0;
      color: #000;
      text-decoration: none;
      border-radius: 5px;
    }
    .pagination-link.active {
      background-color: #007bff;
      color: white;
      font-weight: bold;
    }
  </style>

</head>
<body>
<div class="btn-add-package-wrapper" style="position: relative;">
  <a href="addpackage.php" class="btn-add-package" style="position: absolute; top: 20px; right: 20px;">
    + Add Package
  </a>
</div>


<div class="container">
  <div class="sidebar">
  <div class="logo-wrapper">
    <img src="../img/logoputih.png" alt="Logo Kiran" />
    <span class="logo-text"><strong>Kiran</strong> Tour & Travel</span>
  </div>
  <ul>
    <li><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
    <li><a href="manageuser.php"><i class="fas fa-users"></i><span>Manage Users</span></a></li>
    <li><a href="managepackages.php"><i class="fas fa-suitcase"></i><span>Manage Packages</span></a></li>
    <li><a href="transaction.php"><i class="fas fa-file-invoice"></i><span>Transaction</span></a></li>
    <li class="logout-item"><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
  </ul>
</div>

  <div class="main">
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
          <button type="submit" class="btn-search"><i class="fas fa-search"></i>Cari</button>
          <button type="button" onclick="window.location.href='managepackages.php'" title="Refresh" class="btn-refresh">
          <i class="fa-solid fa-rotate-right"></i>
          </button>

        </form>

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
                        <a href='editpackages.php?id=" . $row['id'] . "' style='color: #007bff; text-decoration: none;'>
                          <i class='fas fa-edit'></i>
                        </a>
                        <form method='POST' action='' style='display:inline;' onsubmit=\"return confirm('Yakin ingin menghapus paket ini?');\">
                            <input type='hidden' name='delete_id' value='" . $row['id'] . "' />
                            <button type='submit' style='background:none; border:none; color:red; cursor:pointer; padding:0; margin:0;'>
                            <i class='fas fa-trash'></i>
                            </button>
                        </form>
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

      <?php if ($totalPages > 1): ?>
        <div class="pagination" style="margin-top: 20px; text-align:center;">
          <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(['page' => $page - 1, 'search' => $search]); ?>" class="pagination-link">&laquo; Prev</a>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?php echo http_build_query(['page' => $i, 'search' => $search]); ?>"
              class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"
              style="margin: 0 5px;">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <a href="?<?php echo http_build_query(['page' => $page + 1, 'search' => $search]); ?>" class="pagination-link">Next &raquo;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

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
