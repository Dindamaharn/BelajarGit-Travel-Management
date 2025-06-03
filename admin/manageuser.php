<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
$username = $_SESSION['user_name'];

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "kiran_travel"); // Ganti nama_database sesuai database kamu
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Manage Users</title>
  <link rel="stylesheet" href="../css/admin/manageuser.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
    <li><a href="dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
    <li><a href="manageuser.php"><i class="fas fa-users"></i><span>Manage Users</span></a></li>
    <li><a href="managepackages.php"><i class="fas fa-suitcase"></i><span>Manage Packages</span></a></li>
    <li><a href="transaction.php"><i class="fas fa-file-invoice"></i><span>Transaction</span></a></li>
    <li class="logout-item"><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
  </ul>
</div>

  <!-- Main content -->
  <div class="main">
  <div class="content">
  <h2>Manage Users</h2>

  <form method="GET" action="manageuser.php" style="margin-bottom: 20px;">
  <input type="text" name="search" placeholder="Cari pengguna..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
  <button type="submit"><i class="fas fa-search"></i> Cari</button>
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
    $stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query("SELECT id, name, email, phone FROM users");
}

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['id']) . "</td>";
              echo "<td>" . htmlspecialchars($row['name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['email']) . "</td>";
              echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
              echo "<td>
                      <a href='edituser.php?id=" . $row['id'] . "' style='color: #007bff;'>
                      <i class='fas fa-edit'></i>
                      </a>
                      &nbsp;
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
</div>


  </div>
</div>

</body>
</html>
