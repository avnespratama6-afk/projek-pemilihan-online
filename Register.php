<?php
include "koneksi.php";

/* function kode unik */
function generateKodeUnik($koneksi) {
    do {
        $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $kode = '';
        for ($i = 0; $i < 6; $i++) {
            $kode .= $karakter[rand(0, strlen($karakter) - 1)];
        }
        $cek = mysqli_query($koneksi, "SELECT id FROM user WHERE kode_unik='$kode'");
    } while (mysqli_num_rows($cek) > 0);

    return $kode;
}

if (isset($_POST['register'])) {
    $nik      = mysqli_real_escape_string($koneksi, $_POST['nik']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    /* CEK EMAIL & NIK SUDAH TERDAFTAR */
    $cek = mysqli_query($koneksi, "
        SELECT id FROM user 
        WHERE email='$email' OR nik='$nik'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
            alert('Registrasi gagal! Email atau NIK sudah terdaftar.');
            window.history.back();
        </script>";
        exit;
    }

    // KODE UNIK HANYA UNTUK PEMILIH
    if ($role === 'pemilih') {
        $kode_unik = generateKodeUnik($koneksi);
    } else {
        $kode_unik = NULL;
    }

    // INSERT DATA
    $query = mysqli_query($koneksi, "
        INSERT INTO user (nik, nama, email, password, role, kode_unik)
        VALUES (
            '$nik',
            '$nama',
            '$email',
            '$password',
            '$role',
            " . ($kode_unik === NULL ? "NULL" : "'$kode_unik'") . "
        )
    ");

    if ($query) {
        if ($role === 'pemilih') {
            echo "<script>
                alert('Registrasi berhasil! Kode Unik Anda: $kode_unik');
                window.location='login.php';
            </script>";
        } else {
            echo "<script>
                alert('Registrasi berhasil!');
                window.location='login.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Registrasi gagal!');
        </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- CSS Custom -->
    <link rel="stylesheet" href="CSS/RegisterStyle.css">
</head>
<body>

<div class="register-container">
    <h2>Daftar</h2>

    <!-- FORM TUNGGAL (BENAR) -->
    <form action="" method="POST">

        <label></label>
        <input type="text" name="nik" class="form-control" maxlength="16" placeholder="NIK" required>

        <label></label>
        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>

        <label></label>
        <input type="email" name="email" class="form-control" placeholder="Email" required>

        <label></label>
        <input type="password" name="password" class="form-control" placeholder="Password" required>

        <label></label>
        <select name="role" class="form-control">
            <option value="role" disabled selected>Role</option>
            <option value="admin">Admin</option>
            <option value="pemilih">Pemilih</option>
        </select><br>

        <button type="submit" name="register" class="btn btn-primary w-100 mt-3">
            DAFTAR
        </button>
    </form>

    <p class="text-center mt-3">
        <a href="login.php">Sudah punya akun? Login</a>
    </p>
</div>

</body>
</html>
