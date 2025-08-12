<?php
session_start();
include '../includes/check_admin.php'; 

$username = $_SESSION['user_name'];

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "kiran_travel"); // Ganti nama_database sesuai database kamu
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Konfigurasi paginasi
$limit = 7; // jumlah data per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'User berhasil dihapus.'
        ];
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Hapus data gagal karena terhubung dengan data lain.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus user.'
            ];
        }
    }

    header("Location: manageuser.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Manage Users</title>
  <link rel="stylesheet" href="../css/admin/manageuser.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    .pagination{
      text-align: center;
    }

    .page-link {
      margin: 0 5px;
      padding: 6px 12px;
      background-color: #f1f1f1;
      color: #333;
      text-decoration: none;
      border-radius: 4px;
    }
    .page-link:hover {
      background-color: #ddd;
    }
    .page-link.active {
      background-color: #007BFF;
      color: white;
      font-weight: bold;
    }

  </style>
</head>
<body>

<div class="container">
  <!-- Sidebar -->
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

      <!-- Main content -->
      <div class="main">
      <div class="content">
      <h2>Kelola Pengguna</h2>

      <?php if (isset($_SESSION['alert'])): ?>
        <div style="margin-bottom: 15px; padding: 10px; border-radius: 5px;
                    background-color: <?= $_SESSION['alert']['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>;
                    color: <?= $_SESSION['alert']['type'] === 'success' ? '#155724' : '#721c24' ?>;
                    border: 1px solid <?= $_SESSION['alert']['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;">
          <?= htmlspecialchars($_SESSION['alert']['message']) ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
      <?php endif; ?>

      <form method="GET" action="manageuser.php" style="margin-bottom: 20px;">
      <input type="text" name="search" placeholder="Cari pengguna..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
      <button type="submit"><i class="fas fa-search"></i></button>
      <button type="button" onclick="window.location.href='manageuser.php'" title="Refresh" style="cursor:pointer;">
      <i class="fa-solid fa-rotate-right"></i>
      </button>
      </form>

      <table border="1" cellpadding="10" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $search = isset($_GET['search']) ? trim($_GET['search']) : '';
          if ($search !== '') {
              $stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?  LIMIT ? OFFSET ?");
              $like = "%" . $search . "%";
              $stmt->bind_param("sssii", $like, $like, $like, $limit, $offset);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->close();

              // Hitung total hasil pencarian
              $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?");
              $countStmt->bind_param("sss", $like, $like, $like);
              $countStmt->execute();
              $countResult = $countStmt->get_result();
              $totalData = $countResult->fetch_assoc()['total'];
              $countStmt->close();
          } else {
              $stmt = $conn->prepare("SELECT id, name, email, phone FROM users LIMIT ? OFFSET ?");
              $stmt->bind_param("ii", $limit, $offset);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->close();

              // Hitung total data user
              $count = $conn->query("SELECT COUNT(*) as total FROM users");
              $totalData = $count->fetch_assoc()['total'];
          }

          $totalPages = ceil($totalData / $limit);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                  echo "<td>
                          <form method='POST' action='' style='display:inline;' onsubmit=\"return confirm('Yakin ingin menghapus user ini?');\">
                            <input type='hidden' name='delete_id' value='" . $row['id'] . "' />
                            <button type='submit' style='background:none;border:none;color:red;cursor:pointer;'>
                              <i class='fas fa-trash'></i>
                            </button>
                          </form>
                        </td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='5'>Tidak ada data pengguna.</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="pagination" style="margin-top: 20px;">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-link">&laquo; Kembali</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-link">Selanjutnya &raquo;</a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

</body>
</html>
