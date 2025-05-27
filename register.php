<?php include("db.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" name="register">Register</button>
    </form>
</div>

<?php
if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->query("SELECT * FROM user WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar');</script>";
    } else {
        $conn->query("INSERT INTO user(email, password) VALUES ('$email', '$password')");
        $user_id = $conn->insert_id;
        echo "<script>location.href='lengkapi.php?id=$user_id';</script>";
    }
}
?>
</body>
</html>
