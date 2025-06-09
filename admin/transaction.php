<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
$username = $_SESSION['user_name'];

include '../includes/db.php'; // Koneksi database

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$whereClause = '';
if ($search !== '') {
    $whereClause = "WHERE 
        orders.order_unique_id LIKE '%$search%' OR
        users.name LIKE '%$search%' OR 
        travel_packages.name LIKE '%$search%' OR 
        orders.metode_pembayaran LIKE '%$search%' OR
        orders.status LIKE '%$search%'
        ";
        
}


$limit = 7; // jumlah data per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Hitung total data dengan pencarian
$total_result = mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM orders 
    JOIN travel_packages ON orders.package_id = travel_packages.id
    JOIN users ON orders.user_id = users.id
    $whereClause
");
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);


// Update status jika ada permintaan konfirmasi atau cancel
// Gunakan isset untuk menangani tombol form GET submit
if (isset($_GET['confirm']) && is_numeric($_GET['confirm'])) {
    $order_id = intval($_GET['confirm']);

    // Ambil total orang dan package_id dari order
    $order_result = mysqli_query($conn, "SELECT total_people, package_id FROM orders WHERE id = $order_id");
    if ($order_result && mysqli_num_rows($order_result) > 0) {
        $order = mysqli_fetch_assoc($order_result);
        $total_people = $order['total_people'];
        $package_id = $order['package_id'];

        // Ambil jumlah kursi tersedia saat ini
        $package_result = mysqli_query($conn, "SELECT available_seats FROM travel_packages WHERE id = $package_id");
        if ($package_result && mysqli_num_rows($package_result) > 0) {
            $package = mysqli_fetch_assoc($package_result);
            $available_seats = $package['available_seats'];

            // Pastikan kursi cukup
            if ($available_seats >= $total_people) {
                // Kurangi kursi tersedia
                $new_seats = $available_seats - $total_people;
                $update_seats = mysqli_query($conn, "UPDATE travel_packages SET available_seats = $new_seats WHERE id = $package_id");

                if ($update_seats) {
                    // Update status order ke confirmed
                    $update_status = mysqli_query($conn, "UPDATE orders SET status = 'confirmed' WHERE id = $order_id");
                    if ($update_status) {
                        $_SESSION['alert'] = [
                          'type' => 'success',
                          'message' => 'Pesanan berhasil dikonfirmasi.'
                        ];
                        header("Location: transaction.php");
                        exit();
                    } else {
                        echo "Gagal update status: " . mysqli_error($conn);
                        exit();
                    }
                } else {
                    echo "Gagal mengurangi kursi: " . mysqli_error($conn);
                    exit();
                }
            } else {
                echo "Kursi tersedia tidak mencukupi untuk konfirmasi.";
                exit();
            }
        }
    } else {
        echo "Pesanan tidak ditemukan.";
        exit();
    }
}


if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $order_id = intval($_GET['cancel']);
    $update = mysqli_query($conn, "UPDATE orders SET status = 'cancelled' WHERE id = $order_id");
    if ($update) {
        $_SESSION['alert'] = [
          'type' => 'success',
          'message' => 'Pesanan berhasil dibatalkan.'
        ];
        header("Location: transaction.php");
        exit();
    } else {
        echo "Gagal update status cancel: " . mysqli_error($conn);
        exit();
    }
}

// Reset status ke pending jika ada permintaan reset
if (isset($_GET['reset']) && is_numeric($_GET['reset'])) {
    $order_id = intval($_GET['reset']);

    // Ambil data order dulu: status sekarang, total_people, package_id
    $order_result = mysqli_query($conn, "SELECT status, total_people, package_id FROM orders WHERE id = $order_id");
    if ($order_result && mysqli_num_rows($order_result) > 0) {
        $order = mysqli_fetch_assoc($order_result);
        $current_status = $order['status'];
        $total_people = $order['total_people'];
        $package_id = $order['package_id'];

        // Kalau status sebelumnya confirmed, kursi dikembalikan
        if ($current_status === 'confirmed') {
            $package_result = mysqli_query($conn, "SELECT available_seats FROM travel_packages WHERE id = $package_id");
            if ($package_result && mysqli_num_rows($package_result) > 0) {
                $package = mysqli_fetch_assoc($package_result);
                $available_seats = $package['available_seats'];
                $new_seats = $available_seats + $total_people;

                $update_seats = mysqli_query($conn, "UPDATE travel_packages SET available_seats = $new_seats WHERE id = $package_id");
                if (!$update_seats) {
                    echo "Gagal mengembalikan kursi saat reset: " . mysqli_error($conn);
                    exit();
                }
            }
        }

        // Update status order jadi pending
        $update = mysqli_query($conn, "UPDATE orders SET status = 'pending' WHERE id = $order_id");
        if ($update) {
            $_SESSION['alert'] = [
              'type' => 'success',
              'message' => 'Status pesanan berhasil di-reset ke pending.'
            ];
            header("Location: transaction.php");
            exit();
        } else {
            echo "Gagal update status reset: " . mysqli_error($conn);
            exit();
        }
    } else {
        echo "Pesanan tidak ditemukan.";
        exit();
    }
}



// Query data dengan pencarian
$query = "
SELECT 
    orders.id AS order_id,
    orders.order_unique_id,
    users.name AS user_name,
    travel_packages.name AS package_name,
    travel_packages.price AS seat_price,
    orders.total_people,
    orders.total_price,
    orders.metode_pembayaran,
    orders.bukti_bayar,
    orders.status
FROM orders
JOIN travel_packages ON orders.package_id = travel_packages.id
JOIN users ON orders.user_id = users.id
$whereClause
ORDER BY orders.id DESC
LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Transaksi</title>
  <link rel="stylesheet" href="../css/admin/transaction.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .pagination {
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
      <h2>Transaksi</h2>

      <?php if (isset($_SESSION['alert'])): ?>
        <div style="margin-bottom: 15px; padding: 10px; border-radius: 5px;
                    background-color: <?= $_SESSION['alert']['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>;
                    color: <?= $_SESSION['alert']['type'] === 'success' ? '#155724' : '#721c24' ?>;
                    border: 1px solid <?= $_SESSION['alert']['type'] === 'success' ? '#c3e6cb' : '#f5c6cb' ?>;">
          <?= htmlspecialchars($_SESSION['alert']['message']) ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
      <?php endif; ?>

      <!-- Flex container untuk baris -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    
    <!-- Kolom kiri: Pencarian -->
    <form method="GET" action="transaction.php" style="display: flex; gap: 10px;">
      <input type="text" name="search" placeholder="Cari transaksi..." 
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
      <button type="submit"><i class="fas fa-search"></i></button>
      <button type="button" onclick="window.location.href='transaction.php'" title="Refresh" style="cursor:pointer;">
        <i class="fa-solid fa-rotate-right"></i>
      </button>
    </form>

    
    </div>
    </form>
    
      <table>
        <thead>
          <tr>
            <th>ID Pesanan</th>
            <th>Nama User</th>
            <th>Nama Paket</th>
            <th>Harga Kursi</th>
            <th>Total Orang</th>
            <th>Total Harga</th>
            <th>Metode Pembayaran</th>
            <th>Bukti Bayar</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <tr>
            <td><?= htmlspecialchars($row['order_unique_id']); ?></td>
            <td><?= $row['user_name']; ?></td>
            <td><?= htmlspecialchars($row['package_name']); ?></td>
            <td>Rp<?= number_format($row['seat_price'], 0, ',', '.'); ?></td>
            <td><?= $row['total_people']; ?></td>
            <td>Rp<?= number_format($row['total_price'], 0, ',', '.'); ?></td>
            <td><?= htmlspecialchars($row['metode_pembayaran']); ?></td>
            <td>
              <?php if ($row['bukti_bayar']) : ?>
                <a href="../img/bukti_bayar/<?= rawurlencode($row['bukti_bayar']); ?>" target="_blank">Lihat</a><br />
                <img src="../img/bukti_bayar/<?= rawurlencode($row['bukti_bayar']); ?>" alt="Bukti Bayar" style="max-width:100px; max-height:100px; margin-top:5px;" />
              <?php else : ?>
                Tidak ada
              <?php endif; ?>
            </td>
            <td>
              <?php
                $status = $row['status'];
                if ($status === 'confirmed') {
                  echo '<span class="status-confirmed">Sukses</span>';
                } elseif ($status === 'cancelled') {
                  echo '<span class="status-cancel">Dibatalkan</span>';
                } else {
                  echo '<span class="status-pending">Tertunda</span>';
                }
              ?>
            </td>
            <td>
              <?php 
                if ($status === 'pending') : ?>
                  <form method="GET" action="transaction.php" style="display:inline;">
                    <button type="submit" name="confirm" value="<?= $row['order_id']; ?>" onclick="return confirm('Yakin konfirmasi pesanan ini?')">Konfirmasi</button>
                  </form>
                  <form method="GET" action="transaction.php" style="display:inline;">
                    <button type="submit" name="cancel" value="<?= $row['order_id']; ?>" onclick="return confirm('Yakin batalkan pesanan ini?')">Cancel</button>
                  </form>
              <?php elseif ($status === 'confirmed' || $status === 'cancelled') : ?>
                  <form method="GET" action="transaction.php" style="display:inline;">
                    <button type="submit" name="reset" value="<?= $row['order_id']; ?>" title="Reset ke Pending" onclick="return confirm('Yakin reset status ke pending?')">×</button>
                  </form>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="pagination" style="margin-top: 20px;">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>" class="page-link">&laquo; Kembali</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
            <?= $i; ?>
          </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>" class="page-link">Selanjutnya &raquo;</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
