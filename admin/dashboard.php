<?php
session_start();
include '../includes/check_admin.php'; 
include '../includes/db.php'; // pastikan file ini berisi koneksi ke $conn

$username = $_SESSION['user_name'];


$userResult = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users");
$userData = mysqli_fetch_assoc($userResult);
$totalUsers = $userData['total_users'];

$revenueResult = mysqli_query($conn, "SELECT SUM(total_price) AS total_revenue FROM orders WHERE status = 'confirmed'");
$revenueData = mysqli_fetch_assoc($revenueResult);
$totalRevenue = $revenueData['total_revenue'] ?? 0;

$totalProfit = $totalRevenue * 0.15;

// Jumlah paket
$packageResult = mysqli_query($conn, "SELECT COUNT(*) AS total_packages FROM travel_packages");
$packageData = mysqli_fetch_assoc($packageResult);
$totalPackages = $packageData['total_packages'];

// Jumlah transaksi
$transactionResult = mysqli_query($conn, "SELECT COUNT(*) AS total_transactions FROM orders");
$transactionData = mysqli_fetch_assoc($transactionResult);
$totalTransactions = $transactionData['total_transactions'];


$recentOrdersQuery = mysqli_query($conn, "
  SELECT o.id, o.total_price, o.status, p.name AS package_name
  FROM orders o
  JOIN travel_packages p ON o.package_id = p.id
  ORDER BY o.order_date DESC
  LIMIT 3
");


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
    <li><a href="dashboard.php"><i class="fas fa-home"></i><span>Beranda</span></a></li>
    <li><a href="manageuser.php"><i class="fas fa-users"></i><span>Kelola Pengguna</span></a></li>
    <li><a href="managepackages.php"><i class="fas fa-suitcase"></i><span>Kelola Paket</span></a></li>
    <li><a href="transaction.php"><i class="fas fa-file-invoice"></i><span>Transaksi</span></a></li>
    <li class="logout-item"><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i><span>Keluar</span></a></li>
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
      <h2>Dasbor Admin</h2>

<!-- Row 1: 3 Cards -->
<div class="row cards-top">
  <div class="card">
    <h3>Total Pengguna</h3>
    <p><?php echo $totalUsers; ?></p>
  </div>
  <div class="card">
    <h3>Total Paket</h3>
    <p><?php echo $totalPackages; ?></p>
  </div>
  <div class="card">
    <h3>Total Order</h3>
    <p><?php echo $totalTransactions; ?></p>
  </div>
</div>

<!-- Row 2: 2 Cards Laba dan 1 Card Recently Order -->
<div class="row cards-bottom">
  <div class="card laba-card">
    <h3>Total Pendapatan</h3>
    <p>Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></p>
  </div>
  <div class="card laba-card">
    <h3>Laba</h3>
    <p>Rp <?php echo number_format($totalProfit, 0, ',', '.'); ?></p>
  </div>

  <div class="card recent-order">
    <h3>Order Terbaru</h3>
    <ul>
   <ul>
      <?php while ($order = mysqli_fetch_assoc($recentOrdersQuery)): ?>
        <li>
          Order #<?php echo str_pad($order['id'], 3, '0', STR_PAD_LEFT); ?> - 
          <?php echo htmlspecialchars($order['package_name']); ?> - 
          Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?>
          <span class="badge <?php echo strtolower($order['status']); ?>">
            <?php echo ucfirst($order['status']); ?>
          </span>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</div>

    </div>
  </div>
</div>

</body>
</html>
