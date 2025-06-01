
<?php
include "../includes/db.php";

session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    // Cek dulu di tabel admin
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultAdmin = $stmt->get_result();

    if ($resultAdmin && $resultAdmin->num_rows > 0) {
        $data = $resultAdmin->fetch_assoc();

        if (password_verify($pass, $data['password'])) {
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_name'] = $data['name']; // Sesuaikan nama kolom di tabel admin
            $_SESSION['role'] = 'admin';

            header("Location: ../admin/dashboard.php"); // Halaman khusus admin
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        // Jika tidak ketemu admin, cek di tabel users
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultUser = $stmt->get_result();

        if ($resultUser && $resultUser->num_rows > 0) {
            $data = $resultUser->fetch_assoc();

            if (password_verify($pass, $data['password'])) {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['user_name'] = $data['name']; // Sesuaikan nama kolom di tabel users
                $_SESSION['role'] = 'user';

                header("Location: ../user/index.php"); // Halaman utama user
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email tidak terdaftar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login / Daftar – Kiran Travel</title>
  <link rel="stylesheet" href="../css/user/login.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="auth-wrapper">
    <!-- Form Card -->
    <div class="auth-box">
      <img src="../img/logowarna.png" alt="Logo Kiran" class="auth-logo">
      <h2>Login / Masuk</h2>

      <?php if($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php" novalidate>
        <label for="email">Email</label>
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
          Lihat semua transaksi pesananmu lebih mudah
        </li>
      </ul>

    </div>
  </div>
</body>
</html>
