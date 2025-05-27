<?php include("db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="login">Login</button>
    </form>
</div>

<?php
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $result = $conn->query("SELECT * FROM user WHERE email='$email'");
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        if (password_verify($pass, $data['password'])) {
            echo "<script>alert('Login berhasil'); location.href='index.php';</script>";
        } else {
            echo "<script>alert('Password salah');</script>";
        }
    } else {
        echo "<script>alert('Email tidak terdaftar');</script>";
    }
}
?>
</body>
</html>
