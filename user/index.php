<?php
session_start();

// Include koneksi database
include '../includes/db.php';

// Ambil data unik untuk dropdown asal dan tujuan dari tabel travel_packages
$asal_result = $conn->query("SELECT DISTINCT departure_location FROM travel_packages WHERE departure_location IS NOT NULL");
$tujuan_result = $conn->query("SELECT DISTINCT destination FROM travel_packages WHERE destination IS NOT NULL");

// Inisialisasi variabel hasil dan pilihan asal/tujuan
$hasil = null;
$asal = "";
$tujuan = "";

// Proses pencarian paket ketika form dikirim dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $asal = $_POST["asal"];
    $tujuan = $_POST["tujuan"];

    // Query dengan prepared statement untuk keamanan dari SQL Injection
    $query = "SELECT * FROM travel_packages 
              WHERE departure_location = ? 
              AND destination = ? 
              AND available_seats > 0 
              AND departure_date >= CURDATE()";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $asal, $tujuan);
    $stmt->execute();
    $hasil = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Kiran Travel & Tour</title>
    <link rel="stylesheet" href="../css/user/index.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Flatpickr untuk datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin.js"></script>
</head>

<body>
<script src="../js/script.js"></script>

<!-- Header Navbar -->
<header class="header">
  <div class="container">
    <div class="logo-wrapper">
      <img src="../img/logoputih.png" alt="Logo Kiran" />
      <span class="logo-text"><strong>Kiran</strong> Tour & Travel</span>
    </div>
    <nav class="navbar">
      <div class="nav-left">
        <a href="#">Beranda</a>
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
            <a href="../auth/logout.php">Logout</a>
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

<!-- Hero Section dengan pilihan trip -->
<section class="hero">
  <div class="hero-overlay">
    <h1 class="hero-title">
      Cari Tiket Shuttle dan Bus Murah Hari ini
      <div class="trip-options">
        <button class="trip-btn active" id="oneway-btn">Sekali Jalan</button>
        <button class="trip-btn" id="roundtrip-btn">Pulang Pergi</button>
      </div>
    </h1>

    <!-- Form Sekali Jalan -->
    <form method="POST" action="" class="search-box" id="form-oneway" style="display: flex;">
      <div class="search-group">
         
        <label>Berangkat Dari</label>
        <select name="asal" required>
          <option value="">-- Pilih Asal --</option>
          <?php
          // Reset pointer data asal_result untuk fetch ulang jika diperlukan
          $asal_result->data_seek(0);
          while ($row = $asal_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['departure_location']) ?>" <?= $asal == $row['departure_location'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['departure_location']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>


      <button type="button" class="swap-btn" title="Tukar Asal & Tujuan" id="swap-oneway">
        <i class="fa fa-right-left"></i>
      </button>

      <div class="search-group">
        <label>Untuk Tujuan</label>
         <div class="input-wrapper">
        <select name="tujuan" required>
          <option value="">-- Pilih Tujuan --</option>
          <?php
          // Reset pointer data tujuan_result
          $tujuan_result->data_seek(0);
          while ($row = $tujuan_result->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['destination']) ?>" <?= $tujuan == $row['destination'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['destination']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      </div>


      <div class="search-group">
        <label>Tanggal Berangkat</label>
      <div class="input-wrapper">
        <input type="date" id="departure-oneway" name="departure_date" required 
      value="<?= htmlspecialchars($departure_date ?? '') ?>" />
      </div>
      </div>

      <button type="submit" class="btn-cari"><i class="fa fa-search"></i> Cari</button>
    </form>

    <!-- Form Pulang Pergi -->
    <form method="POST" action="" class="search-box" id="form-roundtrip" style="display: none;">
      <div class="search-group">
        <label>Berangkat Dari</label>
        <div class="input-wrapper">
          <i class="fa fa-location-dot"></i>
          <input type="text" value="Bandung" readonly />
        </div>
      </div>

      <button type="button" class="swap-btn" title="Tukar Asal & Tujuan" id="swap-roundtrip">
        <i class="fa fa-right-left"></i>
      </button>

      <div class="search-group">
        <label>Untuk Tujuan</label>
        <div class="input-wrapper">
          <i class="fa fa-location-dot"></i>
          <input type="text" value="Jakarta" readonly />
        </div>
      </div>

      <div class="search-group">
        <label>Tanggal Pulang Pergi</label>
        <div class="input-wrapper">
          <i class="fa fa-calendar-days"></i>
          <input type="text" id="display-range" readonly placeholder="Pilih tanggal" />
          <input type="hidden" id="departure-date" name="departure_date" />
          <input type="hidden" id="return-date" name="return_date" />
        </div>
      </div>

      <button type="submit" class="btn-cari"><i class="fa fa-search"></i> Cari</button>
    </form>
  </div>
</section>

<!-- Tampilkan hasil pencarian -->
<?php if ($hasil !== null): ?>
<section class="search-results container" style="padding: 20px;">
  <h3>Hasil Pencarian:</h3>
  <?php if ($hasil->num_rows > 0): ?>
    <ul>
      <?php while ($row = $hasil->fetch_assoc()): ?>
        <li style="margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
          <strong><?= htmlspecialchars($row['name']) ?></strong><br>
          Tanggal Berangkat: <?= htmlspecialchars($row['departure_date']) ?><br>
          Jenis Trip: <?= htmlspecialchars($row['trip_type']) ?><br>
          Harga: Rp<?= number_format($row['price'], 2, ',', '.') ?><br>
          Kursi Tersedia: <?= htmlspecialchars($row['available_seats']) ?><br>
          <a href="order.php?id=<?= urlencode($row['id']) ?>" class="btn-pesan">Pesan</a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>Maaf, tidak ada paket perjalanan yang cocok ditemukan.</p>
  <?php endif; ?>
</section>
<?php endif; ?>

<!-- Info section -->
<section class="info-section">
  <div class="info-box">
    <h2>Beragam Pilihan Shuttle/Travel Terbaik</h2>
    <p>Ada banyak pilihan shuttle/travel terbaik dengan rute lengkap untuk teman perjalanan kamu, kemanapun kamu inginkan. Pesan tiketnya sekarang!</p>
    <a href="lihatmitra.php" class="btn-mitra">Lihat Mitra</a>
  </div>
  <div class="info-img">
    <img src="../img/section1.png" alt="Shuttle" />
  </div>
</section>

<!-- Logo Slider -->
<section class="logo-slider">
  <!-- Baris 1 -->
  <div class="slider-wrapper">
    <div class="slider track1">
      <div class="slide-track">
        <img src="../img/logo1.png" alt="Logo 1" />
        <img src="../img/logo2.png" alt="Logo 2" />
        <img src="../img/logo3.png" alt="Logo 3" />
        <img src="../img/logo4.png" alt="Logo 4" />
        <img src="../img/logo5.png" alt="Logo 5" />
        <!-- Duplikat -->
        <img src="../img/logo1.png" alt="Logo 1" />
        <img src="../img/logo2.png" alt="Logo 2" />
        <img src="../img/logo3.png" alt="Logo 3" />
        <img src="../img/logo4.png" alt="Logo 4" />
        <img src="../img/logo5.png" alt="Logo 5" />
        <img src="../img/logo1.png" alt="Logo 1" />
        <img src="../img/logo2.png" alt="Logo 2" />
        <img src="../img/logo3.png" alt="Logo 3" />
        <img src="../img/logo4.png" alt="Logo 4" />
        <img src="../img/logo5.png" alt="Logo 5" />
        <img src="../img/logo1.png" alt="Logo 1" />
        <img src="../img/logo2.png" alt="Logo 2" />
        <img src="../img/logo3.png" alt="Logo 3" />
        <img src="../img/logo4.png" alt="Logo 4" />
        <img src="../img/logo5.png" alt="Logo 5" />
      </div>
    </div>
  </div>

  <!-- Baris 2 -->
  <div class="slider-wrapper">
    <div class="slider track2">
      <div class="slide-track">
        <img src="../img/logo6.png" alt="Logo 6" />
        <img src="../img/logo7.png" alt="Logo 7" />
        <img src="../img/logo8.png" alt="Logo 8" />
        <img src="../img/logo9.png" alt="Logo 9" />
        <img src="../img/logo10.png" alt="Logo 10" />
        <!-- Duplikat -->
        <img src="../img/logo6.png" alt="Logo 6" />
        <img src="../img/logo7.png" alt="Logo 7" />
        <img src="../img/logo8.png" alt="Logo 8" />
        <img src="../img/logo9.png" alt="Logo 9" />
        <img src="../img/logo10.png" alt="Logo 10" />
        <img src="../img/logo6.png" alt="Logo 6" />
        <img src="../img/logo7.png" alt="Logo 7" />
        <img src="../img/logo8.png" alt="Logo 8" />
        <img src="../img/logo9.png" alt="Logo 9" />
        <img src="../img/logo10.png" alt="Logo 10" />
        <img src="../img/logo6.png" alt="Logo 6" />
        <img src="../img/logo7.png" alt="Logo 7" />
        <img src="../img/logo8.png" alt="Logo 8" />
        <img src="../img/logo9.png" alt="Logo 9" />
        <img src="../img/logo10.png" alt="Logo 10" />
      </div>
    </div>
  </div>
</section>

<section class="info-section alt">
  <div class="info-img">
    <img src="../img/payment.png" alt="Pembayaran" />
  </div>
  <div class="info-box">
    <h2>Beragam Pilihan Pembayaran</h2>
    <p>Pembayaran jadi lebih mudah dan fleksibel. Kamu bisa pilih pembayaran sesuai keinginan mulai dari OVO, GoPay, BCA Virtual Account, Gerai Retail (Alfamart/Indomaret) dan berbagai pilihan lainnya.</p>
    <a href="carabayar.php" class="lihat-btn">Lihat Metode Pembayaran</a>
  </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
  <div class="faq-container">
    <!-- Left Illustration -->
    <div class="faq-image-wrapper">
      <div class="faq-background-shape"></div>
      <img src="../img/faq.png" alt="Ilustrasi FAQ" class="faq-image" />
    </div>

    <!-- Right Content -->
    <div class="faq-content">
      <h2 class="faq-heading">
        Punya Pertanyaan? Tenang, <br />
        Tiketux Punya Jawabannya!
      </h2>
      <p class="faq-subtext">
        Kita udah rangkumin tiga pertanyaan yang paling sering ditanyain. Kalau masih kurang jelas, cek FAQ lengkapnya, ya!
      </p>

      <!-- Accordion -->
      <div class="faq-accordion">
        <details class="faq-item highlighted">
          <summary>Dapatkah pemesanan tiket dilakukan tanpa mengisi alamat email dan nomor telepon atau hanya mengisi salah satunya?</summary>
          <p>
            Tidak bisa, pemesanan tiket secara online di Tiketux.com perlu mengisi email dan nomor kontak aktif (disarankan terhubung Whatsapp)
            karena e-tiket akan dikirim melalui email yang terdaftar.
          </p>
        </details>

        <details class="faq-item">
          <summary>Platform apa saja yang dapat digunakan untuk melakukan pemesanan tiket bus dan shuttle / travel di Tiketux?</summary>
          <p>
            Tidak bisa, pemesanan tiket secara online di Tiketux.com perlu mengisi email dan nomor kontak aktif (disarankan terhubung Whatsapp)
            karena e-tiket akan dikirim melalui email yang terdaftar.
          </p>
        </details>

        <details class="faq-item">
          <summary>Apakah semua pengguna bisa mendapatkan voucher refund?</summary>
          <p>
            Tidak bisa, pemesanan tiket secara online di Tiketux.com perlu mengisi email dan nomor kontak aktif (disarankan terhubung Whatsapp)
            karena e-tiket akan dikirim melalui email yang terdaftar.
          </p>
        </details>
      </div>

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
      <p><i class="fas fa-map-marker-alt"></i> Jl. Karimun Jawa
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
  // Toggle antara form sekali jalan dan pulang pergi
  const onewayBtn = document.getElementById('oneway-btn');
  const roundtripBtn = document.getElementById('roundtrip-btn');
  const formOneway = document.getElementById('form-oneway');
  const formRoundtrip = document.getElementById('form-roundtrip');

  onewayBtn.addEventListener('click', () => {
    onewayBtn.classList.add('active');
    roundtripBtn.classList.remove('active');
    formOneway.style.display = 'flex';
    formRoundtrip.style.display = 'none';
  });

  roundtripBtn.addEventListener('click', () => {
    roundtripBtn.classList.add('active');
    onewayBtn.classList.remove('active');
    formRoundtrip.style.display = 'flex';
    formOneway.style.display = 'none';
  });

  // Inisialisasi flatpickr untuk input tanggal range (pulang pergi)
  flatpickr("#display-range", {
    mode: "range",
    dateFormat: "d-m-Y",
    minDate: "today",
    onChange: function(selectedDates, dateStr, instance) {
      if (selectedDates.length === 2) {
        // Isi input hidden tanggal berangkat dan pulang
        document.getElementById('departure-date').value = selectedDates[0].toISOString().slice(0,10);
        document.getElementById('return-date').value = selectedDates[1].toISOString().slice(0,10);
      }
    }
  });

  // Tombol tukar asal dan tujuan
  const swapButtons = document.querySelectorAll('.swap-btn');
  swapButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Cari select asal dan tujuan yang berada di satu form yang sama
      const form = button.closest('form');
      const selectAsal = form.querySelector('select[name="asal"]');
      const selectTujuan = form.querySelector('select[name="tujuan"]');

      if (selectAsal && selectTujuan) {
        const temp = selectAsal.value;
        selectAsal.value = selectTujuan.value;
        selectTujuan.value = temp;
      }
    });
  });
</script>

</body>
</html>

