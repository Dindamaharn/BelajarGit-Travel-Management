<?php include("db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Lengkapi Informasi</title>
    <link rel="stylesheet" href="../css/user/style.css">
</head>
<body>
<?php $id = $_GET['id']; ?>
<div class="container">
    <h2>Lengkapi Informasi Diri</h2>
    <form action="" method="POST">
        <input type="text" name="nama_depan" placeholder="Nama Depan" required />
        <input type="text" name="nama_belakang" placeholder="Nama Belakang" required />
        <input type="text" name="no_telepon" placeholder="No Telepon" />
        <button type="submit" name="lengkapi">Simpan</button>
    </form>
</div>

<?php
if (isset($_POST['lengkapi'])) {
    $nama_depan = $_POST['nama_depan'];
    $nama_belakang = $_POST['nama_belakang'];
    $no_telepon = $_POST['no_telepon'];

    $conn->query("UPDATE user SET nama_depan='$nama_depan', nama_belakang='$nama_belakang', no_telepon='$no_telepon' WHERE id_user=$id");
    echo "<script>alert('Data berhasil disimpan'); location.href='index.php';</script>";
}
?>
</body>
</html>
