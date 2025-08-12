<?php
session_start();
require_once '../includes/db.php';

// Cek jika sudah login tapi bukan user
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] !== 'user') {
    // Jika admin diarahkan ke dashboard admin
    header("Location: ../admin/dashboard.php");
    exit();
}


// Ambil data dropdown
$asal_result = $conn->query("SELECT DISTINCT departure_location FROM travel_packages WHERE departure_location IS NOT NULL AND is_expired = 0");
$tujuan_result = $conn->query("SELECT DISTINCT destination FROM travel_packages WHERE destination IS NOT NULL AND is_expired = 0");

// Inisialisasi variabel
$hasil = null;
$asal = $_POST['asal'] ?? '';
$tujuan = $_POST['tujuan'] ?? '';
$departure_date = $_POST['departure_date'] ?? '';
$return_date = $_POST['return_date'] ?? '';
$form_type = $_POST['form_type'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (!empty($asal) && !empty($tujuan) && !empty($departure_date)) {

        if ($form_type === 'roundtrip') {
            // ✅ LOGIKA PULANG PERGI
            if (!empty($return_date)) {
              if ($form_type === 'roundtrip') {
  $asal = $_POST['asal'] ?? '';
  $tujuan = $_POST['tujuan'] ?? '';
$departure_date = $_POST['departure_date'] ?? '';
$return_date = $_POST['return_date'] ?? '';

// Tambah 1 hari (atau bisa ubah menjadi -1 day jika ingin mundur)
if (!empty($departure_date)) {
    $depDateObj = new DateTime($departure_date);
    $depDateObj->modify('+1 day'); // atau '-1 day' untuk mundur
    $departure_date = $depDateObj->format('Y-m-d');
}

if (!empty($return_date)) {
    $retDateObj = new DateTime($return_date);
    $retDateObj->modify('+1 day'); // atau '-1 day' untuk mundur
    $return_date = $retDateObj->format('Y-m-d');
}

}

                $query = "SELECT * FROM travel_packages 
                          WHERE departure_location = ? 
                          AND destination = ? 
                          AND departure_date = ? 
                          AND return_date = ? 
                          AND available_seats > 0
                          AND is_expired = 0";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $asal, $tujuan, $departure_date, $return_date);
                $stmt->execute();
                $hasil = $stmt->get_result();

            }

        } else {
            // ✅ LOGIKA SEKALI JALANddd
            $query = "SELECT * FROM travel_packages 
                      WHERE departure_location = ? 
                      AND destination = ? 
                      AND departure_date = ? 
                      AND available_seats > 0
                      AND trip_type = 'sekali_jalan'
                      ";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $asal, $tujuan, $departure_date);
            $stmt->execute();
            $hasil = $stmt->get_result();
        }

    }
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


    <!-- Tsabit imanana -->

    <!-- Form Sekali Jalan -->
    <form method="POST" action="" class="search-box" id="form-oneway" style="display: flex;">
      <div class="search-group">
         <input type="hidden" name="form_type" value="oneway">

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

 <!-- ✅ Form Pulang Pergi -->
<form method="POST" action="" class="search-box" id="form-roundtrip" style="display: none;">
  <input type="hidden" name="form_type" value="roundtrip">

  <div class="search-group">
    <label>Berangkat Dari</label>
    <select name="asal" required>
      <option value="">-- Pilih Asal --</option>
      <?php
      $asal_result->data_seek(0);
      while ($row = $asal_result->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['departure_location']) ?>" <?= $asal == $row['departure_location'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['departure_location']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <button type="button" class="swap-btn" title="Tukar Asal & Tujuan" id="swap-roundtrip">
    <i class="fa fa-right-left"></i>
  </button>

  <div class="search-group">
    <label>Untuk Tujuan</label>
    <select name="tujuan" required>
      <option value="">-- Pilih Tujuan --</option>
      <option value="">-- Pilih Tujuan --</option>
      <option value="">-- Pilih Tujuan --</option>
      <option value="">-- Pilih Tujuan --</option>
      <option value="">-- Pilih Tujuan --</option>
      <option value="">-- Pilih Tujuan --</option>
      <?php
      $tujuan_result->data_seek(0);
      while ($row = $tujuan_result->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['destination']) ?>" <?= $tujuan == $row['destination'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($row['destination']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="search-group">
    <label>Tanggal Pulang Pergi</label>
    <div class="input-wrapper">
      <i class="fa fa-calendar-days"></i>
      <input type="text" id="display-range" readonly placeholder="Pilih tanggal" />
      <input type="hidden" id="departure-date" name="departure_date" value="<?= htmlspecialchars($departure_date ?? '') ?>" />
      <input type="hidden" id="return-date" name="return_date" value="<?= htmlspecialchars($return_date ?? '') ?>" />
    </div>
  </div>
  <div class="search-group">
    <label>Tanggal Pulang Pergi</label>
    <div class="input-wrapper">
      <i class="fa fa-calendar-days"></i>
      <input type="text" id="display-range" readonly placeholder="Pilih tanggal" />
      <input type="hidden" id="departure-date" name="departure_date" value="<?= htmlspecialchars($departure_date ?? '') ?>" />
      <input type="hidden" id="return-date" name="return_date" value="<?= htmlspecialchars($return_date ?? '') ?>" />
    </div>
  </div>
  <div class="search-group">
    <label>Tanggal Pulang Pergi</label>
    <div class="input-wrapper">
      <i class="fa fa-calendar-days"></i>
      <input type="text" id="display-range" readonly placeholder="Pilih tanggal" />
      <input type="hidden" id="departure-date" name="departure_date" value="<?= htmlspecialchars($departure_date ?? '') ?>" />
      <input type="hidden" id="return-date" name="return_date" value="<?= htmlspecialchars($return_date ?? '') ?>" />
    </div>
  </div>
  <div class="search-group">
    <label>Tanggal Pulang Pergi</label>
    <div class="input-wrapper">
      <i class="fa fa-calendar-days"></i>
      <input type="text" id="display-range" readonly placeholder="Pilih tanggal" />
      <input type="hidden" id="departure-date" name="departure_date" value="<?= htmlspecialchars($departure_date ?? '') ?>" />
      <input type="hidden" id="return-date" name="return_date" value="<?= htmlspecialchars($return_date ?? '') ?>" />
    </div>
  </div>

  <button type="submit" class="btn-cari"><i class="fa fa-search"></i> Cari</button>
</form>
   


  </div>
</section>

<!-- Tampilkan hasil pencarian -->
<?php if ($hasil !== null): ?>
<section class="search-results container" style="padding: 20px;">
  <h3 style="margin-bottom: 10px; font-family: Arial, sans-serif; color: #2c3e50;">Hasil Pencarian:</h3>
  <?php if ($hasil->num_rows > 0): ?>
    <ul style="padding: 0; margin: 0;">
      <?php while ($row = $hasil->fetch_assoc()): ?>
      <li style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 16px 24px;
        background-color: #fdfdfd;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        list-style: none;
        font-family: Arial, sans-serif;
      ">
        <div style="flex: 1;">
          <h4 style="
            margin: 0 0 14px 0;
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            text-decoration: underline;
          ">
            <?= htmlspecialchars($row['name']) ?>
          </h4>

          <?php
          if (!function_exists('labelValueWithIcon')) {
          function labelValueWithIcon($label, $value, $iconSvg) {
            return '
              <div style="display: flex; align-items: center; margin: 6px 0; font-size: 16px; font-family: Arial, sans-serif; color: #444;">
                <span style="margin-right: 10px;">' . $iconSvg . '</span>
                  <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <span style="min-width: 140px; display: inline-block; color: #333; font-weight: 500;">' . $label . '</span>
                    <span style="font-weight: 500;">:</span>
                    <span>' . $value . '</span>
                  </div>
              </div>
            ';
            }
          }


          // SVG icons diperbesar
          $iconStyle = 'width="24" height="24" fill="#3498db" style="vertical-align: middle;"';
          $iconCalendar = '<svg xmlns="http://www.w3.org/2000/svg" '.$iconStyle.' viewBox="0 0 24 24"><path d="M7 11h5v5H7zm7-6h3a2 2 0 0 1 2 2v3H3V7a2 2 0 0 1 2-2h3V2h2v3h4V2h2v3zM3 10h18v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V10z"/></svg>';
          $iconReturnCalendar = '<svg xmlns="http://www.w3.org/2000/svg" '.$iconStyle.' viewBox="0 0 24 24"><path d="M12 8v4l3 3 1.5-1.5-2-2V8z"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/></svg>';
          $iconTrip = '<svg xmlns="http://www.w3.org/2000/svg" '.$iconStyle.' viewBox="0 0 24 24"><path d="M12 2l-5.5 9h11L12 2zm0 14a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>';
          $iconPrice = '<svg xmlns="http://www.w3.org/2000/svg" '.$iconStyle.' viewBox="0 0 24 24"><path d="M21 12.3L12.3 3.6a1.25 1.25 0 0 0-.9-.4H4a1 1 0 0 0-1 1v7.4c0 .3.1.6.4.9l8.7 8.7c.5.5 1.3.5 1.8 0l7.1-7.1c.5-.5.5-1.3 0-1.8zM6.5 7.5A1.5 1.5 0 1 1 9 6a1.5 1.5 0 0 1-2.5 1.5z"/></svg>';
          $iconSeat = '<svg xmlns="http://www.w3.org/2000/svg" '.$iconStyle.' viewBox="0 0 24 24"><path d="M4 12h16v6H4zM5 18v2h14v-2H5z"/></svg>';
          ?>

          <?= labelValueWithIcon('Tanggal Berangkat', date('d-m-Y', strtotime($row['departure_date'])), $iconCalendar) ?>
            <?php if (!empty($row['return_date'])): ?>
          <?= labelValueWithIcon('Tanggal Kembali', date('d-m-Y', strtotime($row['return_date'])), $iconReturnCalendar) ?>
            <?php endif; ?>
          <?= labelValueWithIcon('Jenis Trip', ucwords(str_replace('_', ' ', $row['trip_type'])), $iconTrip) ?>
          <?= labelValueWithIcon('Harga', 'Rp. ' . number_format($row['price'], 2, ',', '.'), $iconPrice) ?>

        </div>

        <div style="display: flex; align-items: center;">
          <a href="order.php?id=<?= urlencode($row['id']) ?>"
             style="
               padding: 12px 20px;
               background-color: #3498db;
               color: white;
               text-decoration: none;
               border-radius: 6px;
               font-weight: 700;
               font-family: Arial, sans-serif;
               box-shadow: 0 4px 8px rgba(52,152,219,0.3);
               transition: background-color 0.25s ease, box-shadow 0.25s ease;
               text-align: center;
             "
             onmouseover="this.style.backgroundColor='#2980b9'; this.style.boxShadow='0 6px 12px rgba(41,128,185,0.4)';"
             onmouseout="this.style.backgroundColor='#3498db'; this.style.boxShadow='0 4px 8px rgba(52,152,219,0.3)';"
          >Pesan</a>
        </div>
      </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p style="color: #d10d0d; font-weight: 700; font-family: Arial, sans-serif; font-size: 18px;">
      Maaf, tidak ada paket perjalanan yang cocok ditemukan.
    </p>
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
function formatToYMD(date) {
  const year = date.getFullYear();
  const month = ('0' + (date.getMonth() + 1)).slice(-2);
  const day = ('0' + date.getDate()).slice(-2);
  return `${year}-${month}-${day}`;
}

flatpickr("#display-range", {
  mode: "range",
  dateFormat: "d-m-Y",
  minDate: "today",
  onChange: function(selectedDates) {
    if (selectedDates.length === 2) {
      document.getElementById('departure-date').value = formatToYMD(selectedDates[0]);
      document.getElementById('return-date').value = formatToYMD(selectedDates[1]);
    }
  },
  onClose: function(selectedDates) {
    if (selectedDates.length === 2) {
      document.getElementById('departure-date').value = formatToYMD(selectedDates[0]);
      document.getElementById('return-date').value = formatToYMD(selectedDates[1]);
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

