<?php

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Hanya admin yang dapat mengakses halaman ini.'); window.location='../auth/login.php';</script>";
    exit();
}
?>
