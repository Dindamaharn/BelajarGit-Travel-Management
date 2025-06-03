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
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
<link rel="stylesheet" href="../css/admin/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> <!-- ikon -->
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
    <div class="topbar">
  <div class="greeting">
    <p>Selamat datang kembali, <strong><?php echo htmlspecialchars($username); ?></strong>!</p>
    <h3>Semoga harimu menyenangkan 😊</h3>
  </div>

    </div>

    <div class="content">
      <!-- Konten dashboard kamu bisa tambah di sini -->
      <h2>Dashboard Admin</h2>

<!-- Row 1: 3 Cards -->
<div class="row cards-top">
  <div class="card">
    <h3>Total User</h3>
    <p>150</p> <!-- nanti diganti dinamis -->
  </div>
  <div class="card">
    <h3>Total Paket</h3>
    <p>35</p>
  </div>
  <div class="card">
    <h3>Total Order</h3>
    <p>128</p>
  </div>
</div>

<!-- Row 2: 2 Cards Laba dan 1 Card Recently Order -->
<div class="row cards-bottom">
  <div class="card laba-card">
    <h3>Total Pendapatan</h3>
    <p>Rp 50.000.000</p>
  </div>
  <div class="card laba-card">
    <h3>Laba</h3>
    <p>Rp 600.000.000</p>
  </div>

  <div class="card recent-order">
    <h3>Recently Order</h3>
    <ul>
   <ul>
  <li>Order #001 - Paket A - Rp 1.500.000 <span class="badge completed">Completed</span></li>
  <li>Order #002 - Paket B - Rp 2.000.000 <span class="badge pending">Pending</span></li>
  <li>Order #003 - Paket C - Rp 1.000.000 <span class="badge cancelled">Cancelled</span></li>

      <!-- isi bisa dinamis -->
    </ul>
  </div>
</div>

    </div>
  </div>
</div>

</body>
</html>
