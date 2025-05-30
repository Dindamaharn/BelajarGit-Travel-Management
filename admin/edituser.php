<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "kiran_travel"); // Ganti dengan DB kamu
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID user dari parameter GET
if (!isset($_GET['id'])) {
    echo "ID user tidak ditemukan.";
    exit();
}

$id = intval($_GET['id']);

// Proses update ketika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Validasi sederhana (bisa ditingkatkan)
    if (!empty($name) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
        if ($stmt->execute()) {
            header("Location: manageuser.php");
            exit();
        } else {
            $error = "Gagal mengupdate data.";
        }
        $stmt->close();
    } else {
        $error = "Nama dan email tidak boleh kosong.";
    }
}

// Ambil data user untuk ditampilkan di form
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/admin/dashboard.css">
</head>
<body>
<div class="container">
    <div class="main">
        <h2>Edit User</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Nama:</label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

            <label>Telepon:</label><br>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"><br><br>

            <button type="submit">Simpan Perubahan</button>
            <a href="manageuser.php">Batal</a>
        </form>
    </div>
</div>
</body>
</html>
