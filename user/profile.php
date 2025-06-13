<?php
session_start();
include '../includes/db.php'; // Pastikan koneksi database sudah dibuat

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Cek apakah user_id tersedia di session
if (!isset($user_id)) {
    // Jika user_id tidak ditemukan di session, arahkan ke login page
    header("Location: login.php");
    exit();
}

// Ambil data pengguna yang akan diubah
$query = "SELECT name, email, phone, password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email, $phone, $current_password);
$stmt->fetch();

// Proses update data jika ada perubahan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data yang akan diubah dari POST
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi password lama
    if (!password_verify($old_password, $current_password)) {
        echo "Password lama yang Anda masukkan salah.";
    } else {
        // Jika password baru dan konfirmasi password baru cocok
        if ($new_password === $confirm_password) {
            // Jika password baru diisi, hash password dan simpan
            if (!empty($new_password)) {
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                // Query untuk mengupdate data pengguna termasuk password
                $query = "UPDATE users SET name = ?, email = ?, password = ?, phone = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssi", $new_name, $new_email, $new_password_hashed, $new_phone, $user_id);
            } else {
                // Jika password kosong, hanya update name, email, dan phone
                $query = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $user_id);
            }

            if ($stmt->execute()) {
                echo "Data berhasil diubah!";
            } else {
                echo "Terjadi kesalahan dalam pengubahan data.";
            }
        } else {
            echo "Password baru dan konfirmasi password tidak cocok.";
        }
    }
}

// Menutup statement dan koneksi
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
</head>
<body>

    <h2>Update Profile</h2>

    <!-- Tampilkan data pengguna yang ada sebagai value pada input fields -->
    <form action="" method="POST">
        <label for="name">New Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($name); ?>" required><br><br>

        <label for="email">New Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($email); ?>" required><br><br>

        <label for="old_password">Old Password:</label>
        <input type="password" name="old_password" id="old_password" required><br><br>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required><br><br>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>

        <label for="phone">New Phone:</label>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($phone); ?>" required><br><br>

        <button type="submit">Update</button>
    </form>

    <button onclick="window.history.back();">Back</button>

</body>
</html>
