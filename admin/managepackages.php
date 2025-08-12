<?php
session_start();

include '../includes/check_admin.php'; 

require '../includes/db.php';

// Update status kadaluarsa berdasarkan tanggal keberangkatan
// Tentukan tanggal hari ini (format YYYY-MM-DD)
$today = date('Y-m-d');

// Update paket yang sudah kadaluarsa (tanggal keberangkatan < hari ini) menjadi is_expired = 1
$updateExpiredQuery = "UPDATE travel_packages SET is_expired = 1 WHERE departure_date < ? AND is_expired = 0";
$stmtExpired = $conn->prepare($updateExpiredQuery);
$stmtExpired->bind_param("s", $today);
$stmtExpired->execute();
$stmtExpired->close();

// Update paket yang belum kadaluarsa (tanggal keberangkatan >= hari ini) menjadi is_expired = 0
$updateNotExpiredQuery = "UPDATE travel_packages SET is_expired = 0 WHERE departure_date >= ? AND is_expired = 1";
$stmtNotExpired = $conn->prepare($updateNotExpiredQuery);
$stmtNotExpired->bind_param("s", $today);
$stmtNotExpired->execute();
$stmtNotExpired->close();



$success = false; // Tambahkan ini
// Handle AJAX delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    header('Content-Type: application/json');

    $id = intval($_POST['delete_id']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM travel_packages WHERE id = ?");
        $stmt->bind_param("i", $id);

        try {
            if ($stmt->execute()) {
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Paket berhasil dihapus.'];
            }
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => 'Paket ini terhubung dengan data order. Tidak dapat dihapus.'
                ];
            } else {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'message' => 'Terjadi kesalahan saat menghapus paket: ' . $e->getMessage()
                ];
            }
        }

header("Location: managepackages.php");
exit();
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
    }
    $conn->close();
    exit();
}

$username = $_SESSION['user_name'];

// Tangkap parameter search dan filter tanggal
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Konfigurasi paginasi
$limit = 7;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Bangun WHERE clause dinamis
$where = [];
$params = [];
$types = "";

// Search kata kunci (bisa di nama paket, trip_type, lokasi, destinasi)
if ($search !== '') {
    $where[] = "(name LIKE ? OR trip_type LIKE ? OR departure_location LIKE ? OR destination LIKE ?)";
    for ($i = 0; $i < 4; $i++) {
        $params[] = "%$search%";
        $types .= "s";
    }
}

// Filter tanggal keberangkatan
if (!empty($start_date) && empty($end_date)) {
    $where[] = "departure_date = ?";
    $params[] = $start_date;
    $types .= "s";
} else {
    if (!empty($start_date)) {
        $where[] = "departure_date >= ?";
        $params[] = $start_date;
        $types .= "s";
    }
    if (!empty($end_date)) {
        $where[] = "departure_date <= ?";
        $params[] = $end_date;
        $types .= "s";
    }
}

$whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Hitung total data untuk pagination
$countQuery = "SELECT COUNT(*) as total FROM travel_packages $whereClause";
$countStmt = $conn->prepare($countQuery);

if ($types !== "") {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$countStmt->close();

// Pagination
$totalPages = ceil($totalRows / $limit);

// Query data dengan LIMIT dan OFFSET
$dataQuery = "SELECT * FROM travel_packages $whereClause ORDER BY id ASC LIMIT ? OFFSET ?";
$params_with_limit = $params;  // Copy dulu
$types_with_limit = $types;

$params_with_limit[] = $limit;
$params_with_limit[] = $offset;
$types_with_limit .= "ii";

$stmt = $conn->prepare($dataQuery);
$stmt->bind_param($types_with_limit, ...$params_with_limit);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Manage Packages Travel</title>
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
    + Tambah Paket Travel
  </a>
</div>


<div class="container">
  <div class="sidebar">
  <div class="logo-wrapper">
    <img src="../img/logoputih.png" alt="Logo Kiran" />
    <span class="logo-text"><strong>Kiran</strong> Tour & Travel</span>
  </div>
  <ul>
<li><a href="dashboard.php"><i class="fas fa-home"></i><span>Beranda</span></a></li>
    <li><a href="manageuser.php"><i class="fas fa-users"></i><span>Kelola Pengguna</span></a></li>
    <li><a href="managepackages.php"><i class="fas fa-suitcase"></i><span>Kelola Paket</span></a></li>
    <li><a href="transaction.php"><i class="fas fa-file-invoice"></i><span>Transaksi</span></a></li>
    <li class="logout-item"><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i><span>Keluar</span></a></li>
  </ul>
</div>

  <div class="main">
    <div class="content">
      <h2>Kelola Paket</h2>

  
  <!-- Flex container utama -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    
    <!-- Bagian kiri: Search + tombol search & refresh -->
    <form method="GET" action="managepackages.php" style="display: flex; gap: 10px; align-items: center;">
      <input 
        type="text" 
        name="search" 
        placeholder="Cari transaksi..." 
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
        style="padding: 6px 10px;"
      />
      <button type="submit" style="padding: 6px 12px; cursor: pointer;">
        <i class="fas fa-search"></i>
      </button>
      <button type="button" onclick="window.location.href='managepackages.php'" title="Refresh" style="cursor:pointer; padding: 6px 12px;">
        <i class="fa-solid fa-rotate-right"></i>
      </button>
    </form>

    <!-- Bagian kanan: Filter tanggal + tombol filter -->
    <form method="GET" action="managepackages.php" style="display: flex; gap: 10px; align-items: center;">
      <label for="start_date" style="font-weight: normal;">Dari:</label>
      <input 
        type="date" 
        name="start_date" 
        id="start_date" 
        value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>"
        style="padding: 4px 8px;"
      />

      <label for="end_date" style="font-weight: normal;">Sampai:</label>
      <input 
        type="date" 
        name="end_date" 
        id="end_date" 
        value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>"
        style="padding: 4px 8px;"
      />

      <button type="submit" style="padding: 6px 12px; cursor: pointer;">
        <i class="fas fa-filter"></i>
      </button>
    </form>
  </div>
</div>

      <?php if (isset($_SESSION['alert'])): ?>
        <div style="margin-bottom: 15px; padding: 10px; border-radius: 5px;
                    background-color: <?= $_SESSION['alert']['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>;
                    color: <?= $_SESSION['alert']['type'] === 'success' ? '#155724' : '#721c24' ?>;
                    border: 1px solid <?= $_SESSION['alert']['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;">
          <?= htmlspecialchars($_SESSION['alert']['message']) ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
      <?php endif; ?>



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

                  $tripType = str_replace('_', ' ', $row['trip_type']);
                  $tripType = ucwords($tripType);
                  echo "<td>" . htmlspecialchars($tripType) . "</td>";
                  
                  echo "<td>" . htmlspecialchars($row['departure_location']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
                  echo "<td>" . date('d-m-Y', strtotime($row['departure_date'])) . "</td>";
                  echo "<td>" . ($row['trip_type'] === 'pulang_pergi' ? htmlspecialchars($row['return_date']) : '-') . "</td>";
                  echo "<td>Rp " . number_format($row['price'], 2, ',', '.') . "</td>";

                  // Cek apakah tanggal keberangkatan sudah lewat
                  if ($row['is_expired'] == 1) {
                      echo "<td><span style='color: red; font-weight: bold;'>Kadaluarsa</span></td>";
                  } else {
                      echo "<td>" . htmlspecialchars($row['available_seats']) . "</td>";
                  }

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
          <a href="?<?php echo http_build_query([
            'page' => $page - 1,
            'search' => $search,
            'start_date' => $start_date,
            'end_date' => $end_date
          ]); ?>" class="pagination-link">&laquo; Kembali</a>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?php echo http_build_query([
              'page' => $i,
              'search' => $search,
              'start_date' => $start_date,
              'end_date' => $end_date
            ])
            ; ?>"
              class="pagination-link <?php echo $i === $page ? 'active' : ''; ?>"
              style="margin: 0 5px;">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>

          <a href="?<?php echo http_build_query([
            'page' => $page + 1,
            'search' => $search,
            'start_date' => $start_date,
            'end_date' => $end_date
          ]); ?>" class="pagination-link">Selanjutnya &raquo;</a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>
