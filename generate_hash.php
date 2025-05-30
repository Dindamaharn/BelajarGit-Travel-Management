<?php
// Ganti tulisan 'password_admin_123' dengan password admin yang kamu inginkan
$password_plain = '123';

// Generate hash dari password asli
$hash = password_hash($password_plain, PASSWORD_DEFAULT);

// Tampilkan password asli dan hasil hash-nya
echo "Password asli: " . $password_plain . "<br>";
echo "Hash untuk dimasukkan ke database:<br>";
echo $hash;
