<?php
session_start();
include "koneksi.php";

// CEGAT JIKA BELUM LOGIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilih') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['cek_kode'])) {
    $kode_unik = mysqli_real_escape_string($koneksi, $_POST['kode_unik']);

    $query = mysqli_query($koneksi, "
        SELECT id FROM user 
        WHERE id='$user_id' AND kode_unik='$kode_unik'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) > 0) {
        // KODE BENAR
        $_SESSION['kode_unik_valid'] = true;
        header("Location: voting.php");
        exit();
    } else {
        $error = "Kode unik tidak sesuai!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Kode Unik</title>
    <link rel="stylesheet" href="CSS/CodeStyle.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card single">

        <div class="login-left full">
            <h2>MASUKKAN KODE UNIK</h2>

            <form method="POST">
                <input type="text" name="kode_unik" maxlength="6" placeholder="XXXXXX" required>

                <button type="submit" name="cek_kode">
                    VERIFIKASI
                </button>
            </form>

            <?php if (isset($error)) : ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
        </div>

    </div>
</div>

</body>

</html>
