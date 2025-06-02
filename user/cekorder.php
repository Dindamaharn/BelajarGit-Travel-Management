<?php
session_start();
include '../includes/db.php';

$showResult = false;
$error = '';
$order = null;

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['orderId']) && isset($_GET['contact'])) {
    $orderId = $conn->real_escape_string($_GET['orderId']);
    $contact = $conn->real_escape_string($_GET['contact']);

    $sql = "SELECT package_id, nama_paket, jumlah_kursi, status 
            FROM orders 
            WHERE package_id = '$orderId' AND (email = '$contact' OR no_telp = '$contact')";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $showResult = true;
    } else {
        $error = "Data pesanan tidak ditemukan. Pastikan ID dan kontak sudah benar.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Pesanan - Kiran Travel</title>
    <link rel="stylesheet" href="../css/user/cekorder.css">
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
                <a href="bantuan.php">Bantuan</a>
                <a href="carabayar.php">Cara Bayar</a>
            </div>
            <div class="nav-right">
                <a href="cekorder.php">Cek Order</a>
                <a href="login.php">Masuk</a>
                <a href="register.php" class="btn-daftar">Daftar</a>
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
            <p>Masukkan email atau no. telepon dan ID pemesanan di form cek pesanan</p>
        </div>

        <!-- Kanan: Form -->
        <div class="cek-form">
            <h2>Cek Pesanan</h2>
            <form action="cekorder.php" method="GET">
                <label for="orderId">ID Pemesanan</label>
                <input type="text" id="orderId" name="orderId" placeholder="Masukkan ID Pemesanan" required />

                <label for="contact">No Telepon / Email Pemesan</label>
                <input type="text" id="contact" name="contact" placeholder="Masukkan no telepon / email" required />

                <button type="submit" class="btn-cek">Cek Pesanan</button>
            </form>

            <?php if ($error): ?>
                <p style="color:red; margin-top:10px;"><?= $error ?></p>
            <?php endif; ?>

            <?php if ($showResult && $order): ?>
                <div class="hasil-pesanan" style="margin-top:20px;">
                    <h3>Detail Pesanan:</h3>
                    <p><strong>ID Pemesanan:</strong> <?= htmlspecialchars($order['packages_id']) ?></p>
                    <p><strong>Nama Paket:</strong> <?= htmlspecialchars($order['nama_paket']) ?></p>
                    <p><strong>Jumlah Kursi:</strong> <?= htmlspecialchars($order['jumlah_kursi']) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

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
</body>
</html>
