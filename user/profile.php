<?php
include '../includes/check_user.php';
include '../includes/db.php'; // Pastikan koneksi database sudah dibuat

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Cek apakah user_id tersedia di session
if (!isset($user_id)) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna
$query = "SELECT name, email, phone, password FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email, $phone, $current_password);
$stmt->fetch();

// Proses update data jika ada perubahan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($old_password, $current_password)) {
        echo "Password lama yang Anda masukkan salah.";
    } else {
        if ($new_password === $confirm_password) {
            if (!empty($new_password)) {
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET name = ?, email = ?, password = ?, phone = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssi", $new_name, $new_email, $new_password_hashed, $new_phone, $user_id);
            } else {
                $query = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $user_id);
            }

            if ($stmt->execute()) {
                header("Location: profile.php?success=true");
                exit();
            } else {
                echo "Terjadi kesalahan dalam pengubahan data.";
            }
        } else {
            echo "Password baru dan konfirmasi password tidak cocok.";
        }
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user/profil.css">
</head>
<body>

<div class="profile-container">
    <h2>Update Profile</h2>
    <form method="POST" action="">
        <label for="name">New Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($name); ?>" required>

        <label for="email">New Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($email); ?>" required>

        <label for="old_password">Old Password:</label>
        <input type="password" name="old_password" id="old_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <label for="phone">New Phone:</label>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($phone); ?>" required>

        <button type="submit" class="btn btn-primary mt-3">Update</button>
        <button type="button" class="btn btn-secondary mt-3" onclick="window.history.back();">Back</button>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Berhasil</h5>
        
      </div>
      <div class="modal-body">
        Data berhasil diubah!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS + Modal Trigger -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
<script>
    window.addEventListener('load', function () {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
<?php endif; ?>

</body>
</html>
