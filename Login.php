<?php
session_start();
include "koneksi.php";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "
        SELECT * FROM user WHERE email='$email' LIMIT 1
    ");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        if (password_verify($password, $data['password'])) {

            // SESSION 
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['email']   = $data['email'];
            $_SESSION['role']    = $data['role'];

            // REDIRECT
            if ($data['role'] === 'pemilih') {
                $_SESSION['verifikasi_kode'] = true;
                header("Location: kode_unik.php");
                exit();
            } elseif ($data['role'] === 'admin') {
                header("Location: panitia.php");
                exit();
            } else {
                echo "<script>alert('Role tidak dikenali');</script>";
            }

        } else {
            echo "<script>alert('Password salah');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Masuk</title>
    <link rel="stylesheet" href="CSS/LoginStyle.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <!-- KIRI -->
        <div class="login-left">
            <h2>Masuk</h2>

            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <div class="password-box">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" name="login">Masuk</button>
            </form>

            <div class="register-link">
                Belum Punya Akun?
                <a href="register.php">Daftar</a>
            </div>
        </div>

        <!-- KANAN -->
        <div class="login-right">
            <img src="images/web.jpg" alt="Login Image">
        </div>

    </div>
</div>

</body>
</html>
