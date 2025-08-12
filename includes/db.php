<?php
$host = "localhost";       // Nama host
$user = "root";            // Username MySQL (default: root untuk XAMPP)
$pass = "";                // Password MySQL (default: kosong untuk XAMPP)
$dbname = "kiran_travel";  // Nama database

// Membuat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>
