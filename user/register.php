<?php include ("../includes/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/user/style.css">
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Nama Lengkap" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="phone" placeholder="Nomor Telepon" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="register">Register</button>
    </form>
</div>

<?php
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar');</script>";
    } else {
        // Insert data baru
        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $password);
        if ($stmt->execute()) {
            echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar. Silakan coba lagi.');</script>";
        }
    }
}
?>
</body>
</html>
