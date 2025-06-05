<?php
// Tidak perlu koneksi database karena data ditampilkan statis
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Pesanan - Kiran Travel</title>
    <link rel="stylesheet" href="../css/user/cekorder.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
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
                <a href="cekorder.php">Cek Order</a>
                <a href="../auth/login.php">Masuk</a>
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
            <h2>Cek Order</h2>
            <form id="cekForm">
                <label for="orderId">ID Pemesanan</label>
                <input type="text" id="orderId" name="orderId" placeholder="Masukkan ID Pemesanan" required />

                <label for="contact">No Telepon / Email Pemesan</label>
                <input type="text" id="contact" name="contact" placeholder="Masukkan no telepon / email" required />

                <button type="submit" class="btn-cek">Cek Pesanan</button>
            </form>

            <div id="result" style="margin-top:20px;"></div>
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

<script>
    document.getElementById('cekForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const orderId = document.getElementById('orderId').value;
        const contact = document.getElementById('contact').value;
        const resultDiv = document.getElementById('result');

        // Simulasi data statis
        const dummyData = {
            id: orderId,
            contact: contact,
            total_people: 3,
            status: 'Menunggu Pembayaran' // Ganti ke 'Sudah Dibayar' atau 'Dibatalkan' untuk uji coba
        };

        let statusClass = '';
        if (dummyData.status === 'Sudah Dibayar') {
            statusClass = 'status-badge status-paid';
        } else if (dummyData.status === 'Menunggu Pembayaran') {
            statusClass = 'status-badge status-pending';
        } else if (dummyData.status === 'Dibatalkan') {
            statusClass = 'status-badge status-cancelled';
        }

        resultDiv.innerHTML = `
            <div class="hasil-pesanan">
                <h3>Detail Pesanan:</h3>
                <p><strong>ID Pemesanan:</strong> ${dummyData.id}</p>
                <p><strong>Jumlah Kursi:</strong> ${dummyData.total_people}</p>
                <p><strong>Status:</strong> <span class="${statusClass}">${dummyData.status}</span></p>
            </div>
        `;
    });
</script>
</body>
</html>
