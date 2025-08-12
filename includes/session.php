<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Belum login atau role tidak ada
    header("Location: ../auth/login.php");
    exit();
}

// Cek apakah yang login adalah user (bukan admin)
if ($_SESSION['role'] !== 'user') {
    echo "<script>alert('Hanya pengguna (user) yang dapat mengakses halaman ini.'); window.location='../auth/login.php';</script>";
    exit();
}
?>
