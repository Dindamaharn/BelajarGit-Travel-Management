<?php
include "db.php";

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $result = $conn->query("SELECT * FROM user WHERE email='$email'");
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($pass, $data['password'])) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak terdaftar.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login / Daftar – Kiran Travel</title>
  <link rel="stylesheet" href="css/login.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="auth-wrapper">
    <!-- Form Card -->
    <div class="auth-box">
      <img src="img/logowarna.png" alt="Logo Kiran" class="auth-logo">
      <h2>Login / Daftar</h2>

      <?php if($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <label for="email">Nomor Telepon / Email</label>
        <input
          type="text"
          id="email"
          name="email"
          placeholder="Masukan nomor telepon atau email"
          value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
          required
        >

        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Masukan password"
          required
        >

        <button type="submit" name="login" class="btn-primary">Lanjutkan</button>
      </form>

      <div class="or"><span>atau</span></div>

      <a href="#" class="btn-google">
        <i class="fab fa-google"></i> Gunakan Akun Google
      </a>
    </div>

    <!-- Info Card -->
    <div class="info-box">
      <h3>Gabung Bersama Kiran Travel</h3>
      <ul class="benefits">
        <li>
          <strong>Benefit Pengguna</strong>
          Dapatkan keuntungan voucher sebagai pengguna
        </li>
        <li>
          <strong>Otomatis Isi Pemesan</strong>
          Lengkapi detail penumpang lebih cepat
        </li>
        <li>
          <strong>Detail Transaksi</strong>
          Lihat semua transaksi pesan-anmu lebih mudah
        </li>
      </ul>
      <img src="img/illustration-guest.png" alt="Ilustrasi" class="info-illu">
    </div>
  </div>
</body>
</html>
