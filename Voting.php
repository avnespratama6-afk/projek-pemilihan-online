<?php
session_start();
include "koneksi.php";

/* CEK PERIODE PEMILIHAN AKTIF */
$periodeAktif = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT * FROM periode_pemilu
        WHERE status='aktif'
        AND NOW() BETWEEN start_datetime AND end_datetime
        LIMIT 1
    ")
);

if (!$periodeAktif) {
    echo "<script>
        alert('Voting belum dibuka atau sudah ditutup!');
        window.location='login.php';
    </script>";
    exit;
}


/* CEK LOGIN */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilih') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk pemilih.');
        window.location='login.php';
    </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

/* CEK STATUS MEMILIH USER */
$user_id = (int) $_SESSION['user_id'];

$cekUser = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT sudah_memilih 
        FROM user 
        WHERE id = $user_id AND role = 'pemilih'
        LIMIT 1
    ")
);

/* AMBIL NAMA USER UNTUK PROFILE */
$userData = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT nama 
        FROM user 
        WHERE id = $user_id AND role = 'pemilih'
        LIMIT 1
    ")
);


/* PROSES VOTING */
if (isset($_POST['vote']) && $cekUser['sudah_memilih'] == 0) {
    $kandidat_id = $_POST['kandidat_id'];

    // simpan ke tabel voting
    mysqli_query($koneksi, "
        INSERT INTO voting (id_pemilih, id_kandidat, waktu_vote)
        VALUES ('$user_id', '$kandidat_id', NOW())
    ");

    // tambah suara kandidat
    mysqli_query($koneksi, "
        UPDATE kandidat 
        SET suara = suara + 1 
        WHERE id='$kandidat_id'
    ");

    // update status memilih user
    mysqli_query($koneksi, "
        UPDATE user 
        SET sudah_memilih = 1 
        WHERE id='$user_id'
    ");

    header("Location: voting.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Halaman Voting RT 001</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="CSS/VotingStyle.css">
<style>
.btn-vote-custom,
.btn-result-custom {
  background-color: #5f6cf0 !important;
  border-color: #5f6cf0 !important;
  color: #fff !important;
}

.btn-vote-custom:hover,
.btn-result-custom:hover {
  background-color: #4b57d6 !important;
  border-color: #4b57d6 !important;
}
</style>

</head>

<body>

<header class="header d-flex justify-content-between align-items-center px-4">

  <!-- PROFIL USER -->
  <div class="d-flex align-items-center gap-2">
      <i class="bi bi-person-circle" style="font-size:40px; color:#5f6cf0;"></i>
      <span style="font-weight:600;">
      <?= htmlspecialchars($userData['nama']) ?>
      </span>
    </div>

  <!-- LOGOUT -->
  <a href="logout.php" 
     class="header-btn"
     onclick="return confirm('Apakah Anda Yakin ingin Keluar?')">
     Logout
  </a>

</header>


<main class="content">
<h1><?= strtoupper($periodeAktif['nama_periode']) ?></h1>

<h2>
Pilih kandidat yang sesuai dengan harapan Anda untuk masa depan
lingkungan yang lebih baik
</h2>

<!-- STATUS VOTING -->
<div class="text-center my-4">
<?php if ($cekUser['sudah_memilih'] == 1): ?>
<div class="alert alert-success">
    Anda sudah menggunakan hak suara Anda.
</div>
<?php else: ?>
<div class="alert alert-warning">
    Anda belum melakukan voting.
</div>
<?php endif; ?>
</div>

<!-- DAFTAR KANDIDAT -->
 <?php if ($cekUser['sudah_memilih'] == 1): ?>
<a href="result.php" class="btn btn-result-custom btn-lg px-5">
  Lihat Hasil Pemilihan
 </a>
<?php endif; ?>

<div class="candidate-grid">
<?php
$data = mysqli_query($koneksi, "SELECT * FROM kandidat ORDER BY no_urut");
while ($k = mysqli_fetch_assoc($data)) {
?>
  <div class="card p-3 text-center">
    <h3>No <?= $k['no_urut']; ?></h3>
    <h4><?= $k['nama']; ?></h4>
    <img 
  src="foto_kandidat/<?= $k['foto']; ?>" 
  class="img-fluid rounded mb-3"
  style="max-height:200px; object-fit:cover;"
>
    <p><strong>Visi:</strong><br><?= $k['visi']; ?></p>
    <p><strong>Misi:</strong><br><?= $k['misi']; ?></p>

    <?php if ($cekUser['sudah_memilih'] == 0): ?>
    <form method="POST">
        <input type="hidden" name="kandidat_id" value="<?= $k['id']; ?>">
        <button type="submit" name="vote" class="btn btn-vote-custom w-100">
            Pilih Kandidat
        </button>
    </form>
    <?php else: ?>
    <button class="btn btn-secondary w-100" disabled>
        Anda Sudah Voting
    </button>
    <?php endif; ?>
  </div>
<?php } ?>
</div>

</main>

</body>
</html>
