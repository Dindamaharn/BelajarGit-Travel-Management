  <!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="UTF-8">
      <title>Kiran Travel & Tour</title>
      <link rel="stylesheet" href="css/index.css">
  </head>
  <body>

  <!-- Header -->
  <header class="header">
    <div class="container">
      <div class="logo-wrapper">
        <img src="img/logoputih.png" alt="Logo Kiran">
        <span class="logo-text"><strong>Kiran</strong> Tour & Travel</span>
      </div>
      <nav class="navbar">
    <div class="nav-left">
      <a href="#">Beranda</a>
      <a href="#">Bantuan</a>
      <a href="#">Cara Bayar</a>
    </div>
    <div class="nav-right">
      <a href="">Cek Order</a>
      <a href="login.php">Masuk</a>
      <a href="register.php" class="btn-daftar">Daftar</a>
    </div>
  </nav>

    </div>
  </header>


  
<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay">
    <h1 class="hero-title">
      Cari Tiket Shuttle dan Bus Murah Hari ini
      <div class="trip-options">
        <button class="trip-btn active" id="oneway-btn">Sekali Jalan</button>
        <button class="trip-btn" id="roundtrip-btn">Pulang Pergi</button>
      </div>
    </h1>

    <!-- Form untuk Sekali Jalan -->
    <div class="search-box" id="form-oneway">
      <div class="search-group">
        <label>Berangkat Dari</label>
        <input type="text" value="Bandung">
      </div>
      <div class="swap-btn">
        <img src="img/swap.svg" alt="Swap">
      </div>
      <div class="search-group">
        <label>Untuk Tujuan</label>
        <input type="text" value="Jakarta">
      </div>
      <div class="search-group">
        <label>Tanggal Berangkat</label>
        <input type="text" value="Sen, 27 Mei">
      </div>
      <button class="btn-cari"><i class="fa fa-search"></i> Cari</button>
    </div>

    <!-- Form untuk Pulang Pergi -->
    <div class="search-box" id="form-roundtrip" style="display: none;">
      <div class="search-group">
        <label>Berangkat Dari</label>
        <input type="text" value="Bandung">
      </div>
      <div class="swap-btn">
        <img src="img/swap.svg" alt="Swap">
      </div>
      <div class="search-group">
        <label>Untuk Tujuan</label>
        <input type="text" value="Jakarta">
      </div>
      <div class="search-group">
        <label>Tanggal Berangkat</label>
        <input type="text" value="Sen, 27 Mei">
      </div>
      <div class="search-group">
        <label>Tanggal Pulang</label>
        <input type="text" value="Rab, 29 Mei">
      </div>
      <button class="btn-cari"><i class="fa fa-search"></i> Cari</button>
    </div>

  </div>
</section>

  <!-- Informasi -->
  <section class="info-section">
      <div class="info-box">
          <h2>Beragam Pilihan Shuttle/Travel Terbaik</h2>
          <p>Ada banyak pilihan shuttle/travel terbaik dengan rute lengkap untuk teman perjalanan kamu, kemanapun kamu inginkan. Pesan tiketnya sekarang!</p>
      </div>
      <div class="info-img">
          <img src="img/shuttle.png" alt="Shuttle">
      </div>
  </section>


  <section class="info-section alt">
      <div class="info-img">
          <img src="img/payment.png" alt="Pembayaran">
      </div>
      <div class="info-box">
          <h2>Beragam Pilihan Pembayaran</h2>
          <p>Pembayaran jadi lebih mudah dan fleksibel. Kamu bisa pilih pembayaran sesuai keinginan mulai dari OVO, GoPay, BCA Virtual Account, Gerai Retail (Alfamart/Indomaret) dan berbagai pilihan lainnya.</p>
          <a href="#" class="lihat-btn">Lihat Metode Pembayaran</a>
      </div>
  </section>
<script src="js/script.js"></script>
  </body>
  </html>
