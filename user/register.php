<?php 
require_once '../includes/session.php';
include '../includes/check_user.php';
include ("../includes/db.php");
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lengkapi Informasi Diri</title>
  <link rel="stylesheet" href="../css/user/register.css">

</head>
<body>

<div class="container">
  <img src="../img/logowarna.png" alt="Logo" style="height: 50px;">
  <h2>Lengkapi Informasi Diri</h2>
  <form action="register.php" method="POST">
    <label for="name">Nama Lengkap</label>
    <input type="text" name="name" id="name" placeholder="Masukan nama lengkap" required />

    <label for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="Masukan email" required />

    <label for="phone">Nomor Telepon</label>
    <input type="text" name="phone" id="phone" placeholder="Masukan nomor telepon" required />

    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Masukan password" required />

    <button type="submit" name="register">Lanjutkan</button>
  </form>
</div>

<?php
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $password);
        if ($stmt->execute()) {
            echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar. Silakan coba lagi.');</script>";
        }
    }
}
?>

</body>
</html>
