<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
$username = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Transaction</title>
  <link rel="stylesheet" href="../css/admin/dashboard.css" />
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
      <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
    </ul>
  </div>

  <!-- Main content -->
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
      <h2>Transaction</h2>
      <!-- Isi konten khusus manage user di sini -->
    </div>
  </div>
</div>

</body>
</html>
