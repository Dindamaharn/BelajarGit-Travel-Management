<?php
include '../includes/check_user.php';

require '../includes/db.php';

$orderId = '';
$resultMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil input dari form
    $orderId = $_POST['orderId'];

    // Query untuk mencari order berdasarkan order_unique_id
    $sql = "SELECT * FROM orders WHERE order_unique_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $orderId); // Mengikat parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Pesanan ditemukan, ambil detail pesanan
        $order = $result->fetch_assoc();

        // Menampilkan semua kolom dari tabel orders
        $resultMessage = "
<div class='ticket-card hasil-pesanan'>

    <h3>Detail Pesanan</h3>
    <p><strong>ID Pemesanan:</strong> {$order['order_unique_id']}</p>
    <p><strong>User ID:</strong> {$order['user_id']}</p>
    <p><strong>Package ID:</strong> {$order['package_id']}</p>
    <p><strong>Order Date:</strong> {$order['order_date']}</p>
    <p><strong>Total People:</strong> {$order['total_people']}</p>
    <p><strong>Total Price:</strong> Rp " . number_format($order['total_price'], 0, ',', '.') . "</p>
    <p><strong>Status:</strong> {$order['status']}</p>
    <p><strong>Payment Method:</strong> {$order['metode_pembayaran']}</p>
    <p><strong>Payment Proof:</strong> {$order['bukti_bayar']}</p>
</div>";

    } else {
        // Jika tidak ada pesanan yang ditemukan
        $resultMessage = "<p>Pesanan tidak ditemukan. Pastikan ID pemesanan yang Anda masukkan benar.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Pesanan - Kiran Travel</title>
    <link rel="stylesheet" href="../css/user/cekorder.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .ticket-card {
    max-width: 500px;
    margin: 220px auto;
    padding: 24px;
    background: #fffaf0;
    border: 2px dashed #d2691e;
    border-radius: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
    position: relative;
}


.ticket-card::before {
    left: -10px;
}

.ticket-card::after {
    right: -10px;
}

.ticket-card h3 {
    margin-bottom: 16px;
    color: #d2691e;
    text-align: center;
}

.ticket-card p {
    margin: 6px 0;
    font-size: 15px;
    color: #333;
}

.ticket-card strong {
    color: #000;
}
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
            font-size: 14px;
        }

        .status-paid {
            background-color: #28a745; /* Hijau */
        }

        .status-pending {
            background-color: #ffc107; /* Kuning */
            color: #000;
        }

        .status-cancelled {
            background-color: #dc3545; /* Merah */
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .close {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
        transform: translateX(-400px);
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    
<header class="header">
    <div class="container">
        <div class="logo-wrapper">
            <img src="../img/logowarna.jpg" alt="Logo Kiran">
            <span class="logo-text"><strong>Kiran</strong> Tour & Travel</span>
        </div>
        <nav class="navbar">
            <div class="nav-left">
                <a href="index.php">Beranda</a>
                <a href="packages.php">Paket</a>
                <a href="bantuan.php">Bantuan</a>
                <a href="carabayar.php">Cara Bayar</a>
            </div>
          <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-dropdown">
           <a href="cekorder.php">Cek Order</a>
          <button class="dropdown-btn">
            <?= !empty($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' ?> <i class="fa fa-caret-down"></i>
          </button>
          <div class="dropdown-content">
                        <a href="profile.php"> Profil </a>
            <a href="../auth/logout.php">Keluar</a>
          </div>
        </div>
        <?php else: ?>
          <a href="../auth/login.php">Masuk</a>
          <a href="register.php" class="btn-daftar">Daftar</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<section class="cek-order-section">
    <div class="cek-order-container">
        <!-- Kiri: Ilustrasi + Keterangan -->
        <div class="cek-illustration">
            <img src="../img/cekorder.png" alt="Cek Pesanan" />
            <h2>Cek Pesanan Kamu</h2>
            <p>Kode Pemesanan Kamu</p>
        </div>

        <!-- Kanan: Form -->
        <div class="cek-form">
            <h2>Cek Order</h2>
            <form id="cekForm" method="POST" action="cekorder.php">
                <label for="orderId">ID Pemesanan</label>
                <input type="text" id="orderId" name="orderId" placeholder="Masukkan ID Pemesanan" required />
                <button type="submit" class="btn-cek">Cek Pesanan</button>
            </form>
        </div>
    </div>
</section>

<!-- Modal for result -->
<div class="modal" id="resultModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <?php echo $resultMessage; ?>
    </div>
</div>

<a href="https://api.whatsapp.com/send?phone=6281234567890&text=Halo%20Kiran%20Travel,%20saya%20ingin%20tanya%20soal%20pembayaran" class="chat-wa" target="_blank">
    <i class="fab fa-whatsapp"></i> Chat Sekarang
</a>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-left">
            <img src="../img/logowarna.png" alt="Kiran Tour & Travel" class="footer-logo" />
            <p><strong>PT Trans Kiran Travel</strong></p>
            <p><i class="fas fa-envelope"></i> info@kiran.com</p>
            <p><i class="fas fa-phone"></i> 081234785009</p>
            <p><i class="fas fa-map-marker-alt"></i> Jl. Karimun Jawa IV No. 98B, Kec. Bodat Utara, Kota Surabaya, Jawa Timur 30881</p>
        </div>
        <div class="footer-right">
            <h4>Media Sosial</h4>
            <p><i class="fab fa-instagram"></i> kirantravel</p>
            <p><i class="fab fa-tiktok"></i> kirantravel</p>
            <p><i class="fab fa-facebook-f"></i> kirantravel</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2025 PT Trans Kiran Travel. All Rights Reserved.</p>
    </div>
</footer>

<script>
    // Get the modal
    var modal = document.getElementById("resultModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Show the modal when the page loads if there's a result
    window.onload = function() {
        if (document.querySelector('.hasil-pesanan')) {
            modal.style.display = "block";
        }
    };

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

document.addEventListener("DOMContentLoaded", function () {
  const dropdownBtn = document.querySelector(".dropdown-btn");
  const dropdownContent = document.querySelector(".dropdown-content");

  if (dropdownBtn && dropdownContent) {
    dropdownBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      dropdownContent.classList.toggle("show");
    });

    document.addEventListener("click", function () {
      dropdownContent.classList.remove("show");
    });
  }
});
</script>



</body>
</html>
