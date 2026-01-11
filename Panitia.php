<?php
session_start();
include "koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Akses ditolak! Halaman ini hanya untuk admin.');
        window.location='login.php';
    </script>";
    exit;
}

  /* TAMBAH KANDIDAT */
if (isset($_POST['tambah_kandidat'])) {
    $no_urut = $_POST['no_urut'];
    $nama    = $_POST['nama'];
    $visi    = $_POST['visi'];
    $misi    = $_POST['misi'];
    $foto_name = $_FILES['foto']['name'];
    $tmp_name  = $_FILES['foto']['tmp_name'];

    $ext = pathinfo($foto_name, PATHINFO_EXTENSION);
    $nama_foto_baru = uniqid() . "." . $ext;

    move_uploaded_file($tmp_name, "foto_kandidat/" . $nama_foto_baru);
    mysqli_query($koneksi, "
        INSERT INTO kandidat (no_urut, nama, visi, misi, foto, suara)
        VALUES ('$no_urut', '$nama', '$visi', '$misi', '$nama_foto_baru', 0)
    ");
header("Location: panitia.php#kandidat");
    exit;
}

/*HAPUS KANDIDAT */
if (isset($_GET['hapus_kandidat'])) {
    $id = $_GET['hapus_kandidat'];
    $foto = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT foto FROM kandidat WHERE id='$id'")
    );

    if ($foto && file_exists("foto_kandidat/" . $foto['foto'])) {
        unlink("foto_kandidat/" . $foto['foto']);
    }

    mysqli_query($koneksi, "DELETE FROM kandidat WHERE id='$id'");
    header("Location: panitia.php#kandidat");
    exit;
}

/* AMBIL DATA KANDIDAT */
$editKandidat = null;
if (isset($_GET['edit_kandidat'])) {
    $id = $_GET['edit_kandidat'];
    $editKandidat = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT * FROM kandidat WHERE id='$id'")
    );
}


/*UPDATE KANDIDAT */
if (isset($_POST['update_kandidat'])) {
    $id   = $_POST['id'];
    $no   = $_POST['no_urut'];
    $nama = $_POST['nama'];
    $visi = $_POST['visi'];
    $misi = $_POST['misi'];

    if (!empty($_FILES['foto']['name'])) {
        $foto_lama = $_POST['foto_lama'];
        if (file_exists("foto_kandidat/" . $foto_lama)) {
            unlink("foto_kandidat/" . $foto_lama);
        }

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_baru = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "foto_kandidat/" . $foto_baru);

        mysqli_query($koneksi, "
            UPDATE kandidat SET
            no_urut='$no', nama='$nama', visi='$visi', misi='$misi', foto='$foto_baru'
            WHERE id='$id'
        ");
    } else {
        mysqli_query($koneksi, "
            UPDATE kandidat SET
            no_urut='$no', nama='$nama', visi='$visi', misi='$misi'
            WHERE id='$id'
        ");
    }

    header("Location: panitia.php#kandidat");
    exit;
}

/* TAMBAH PERIODE */
if (isset($_POST['tambah_periode'])) {
    $nama  = $_POST['nama_periode'];
    $start = $_POST['start_datetime'];
    $end   = $_POST['end_datetime'];

    if (strtotime($end) <= strtotime($start)) {
        echo "<script>alert('Tanggal selesai harus setelah tanggal mulai');</script>";
    } else {
        mysqli_query($koneksi, "
            INSERT INTO periode_pemilu (nama_periode, start_datetime, end_datetime)
            VALUES ('$nama','$start','$end')
        ");
    }
}
/* AKTIFKAN PERIODE */
if (isset($_GET['aktifkan_periode'])) {
    $id = (int) $_GET['aktifkan_periode'];

    mysqli_query($koneksi, "UPDATE periode_pemilu SET status='nonaktif'");
    mysqli_query($koneksi, "UPDATE periode_pemilu SET status='aktif' WHERE id=$id");

    header("Location: panitia.php#periode");
    exit;
}
/* NONAKTIFKAN PERIODE */
if (isset($_GET['nonaktifkan_periode'])) {
    $id = (int) $_GET['nonaktifkan_periode'];

    mysqli_query($koneksi, "
        UPDATE periode_pemilu SET status='nonaktif' WHERE id=$id
    ");

    header("Location: panitia.php#periode");
    exit;
}
/* HAPUS PERIODE */
if (isset($_GET['hapus_periode'])) {
    $id = (int) $_GET['hapus_periode'];

    mysqli_query($koneksi, "DELETE FROM periode_pemilu WHERE id=$id");

    header("Location: panitia.php#periode");
    exit;
}


/* AMBIL DATA WARGA */
$editWarga = null;
if (isset($_GET['edit_warga'])) {
    $id = $_GET['edit_warga'];
    $editWarga = mysqli_fetch_assoc(
        mysqli_query($koneksi, "SELECT * FROM user WHERE id='$id' AND role='pemilih'")
    );
}

/* UPDATE WARGA */
if (isset($_POST['update_warga'])) {
    $id    = $_POST['id'];
    $nik   = $_POST['nik'];
    $nama  = $_POST['nama'];
    $email = $_POST['email'];

    mysqli_query(
        $koneksi,
        "UPDATE user SET
         nik='$nik',
         nama='$nama',
         email='$email'
         WHERE id='$id' AND role='pemilih'"
    );

    header("Location: panitia.php#warga");
    exit;
}


/* HAPUS WARGA */
if (isset($_GET['hapus_warga'])) {
    $id = $_GET['hapus_warga'];
    mysqli_query($koneksi, "DELETE FROM user WHERE id='$id' AND role='pemilih'");
    header("Location: panitia.php#warga");
    exit;
}

/* RESET VOTING */
if (isset($_POST['reset_voting'])) {
    mysqli_query($koneksi, "DELETE FROM voting");
    mysqli_query($koneksi, "UPDATE kandidat SET suara = 0");
    mysqli_query($koneksi, "UPDATE user SET sudah_memilih = 0");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Panitia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/PanitiaStyle.css">
</head>

<body>
<div class="admin-layout">

    <aside class="sidebar">
        <h3>PANEL ADMIN</h3>
        <a href="#periode">Manajemen Periode</a>
        <a href="#kandidat">Kelola Kandidat</a>
        <a href="#warga">Kelola Warga</a>
        <a href="#hasil">Hasil Voting</a>
        <a href="logout.php"
           onclick="return confirm('Apakah Anda Yakin ingin Keluar?')"
           style="color:#ffcccc">Logout</a>
    </aside>

    <main class="main-content">
        <h1>Dashboard Panitia Pemilihan</h1>

        <!-- PERIODE -->
        <section id="periode" class="card">
    <h2>Manajemen Periode Pemilu</h2>

    <button class="btn btn-success mb-3" onclick="togglePeriodeForm()">
    + Tambah Periode
</button>

<form method="POST"
      id="formPeriode"
      class="mb-4"
      style="display:none;">

    <div class="row">
        <div class="col-md-4">
            <label>Nama Periode</label>
            <input type="text" name="nama_periode" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Mulai</label>
            <input type="datetime-local" name="start_datetime" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Selesai</label>
            <input type="datetime-local" name="end_datetime" class="form-control" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100" name="tambah_periode">
                Simpan
            </button>
        </div>
    </div>

</form>


    <table class="table table-bordered">
    <thead>
    <tr>
        <th>Nama Periode</th>
        <th>Mulai</th>
        <th>Selesai</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $periode = mysqli_query($koneksi, "SELECT * FROM periode_pemilu ORDER BY id DESC");
    while ($p = mysqli_fetch_assoc($periode)) {
        echo "
        <tr>
            <td>{$p['nama_periode']}</td>
            <td>{$p['start_datetime']}</td>
            <td>{$p['end_datetime']}</td>
            <td>
                <span class='badge ".($p['status']=='aktif'?'bg-success':'bg-secondary')."'>
                    {$p['status']}
                </span>
            </td>
            <td>
        ";

        // tombol aktif / nonaktif
        if ($p['status'] == 'aktif') {
            echo "
                <a href='?nonaktifkan_periode={$p['id']}'
                   class='btn btn-danger btn-sm mb-1'>
                   Nonaktifkan
                </a>
            ";
        } else {
            echo "
                <a href='?aktifkan_periode={$p['id']}'
                   class='btn btn-primary btn-sm mb-1'>
                   Aktifkan
                </a>
            ";
        }

        // tombol hapus
if ($p['status'] != 'aktif') {
    echo "
        <a href='?hapus_periode={$p['id']}'
           class='btn btn-danger btn-sm'
           onclick=\"return confirm('Yakin ingin menghapus periode ini?')\">
           Hapus
        </a>
    ";
}

        echo "</td></tr>";
    }
    ?>
    </tbody>
</table>


        <!-- KANDIDAT -->
        <?php
        $jumlah_kandidat = mysqli_num_rows(
            mysqli_query($koneksi, "SELECT * FROM kandidat")
        );
        ?>

        <section id="kandidat" class="card">
            <h2>Kelola Data Kandidat</h2>

            <button class="btn btn-success mb-3" onclick="toggleForm()">
                + Tambah Kandidat
            </button>

            <form method="POST"
                  id="formKandidat"
                  style="display:none;"
                  class="mb-4"
                  enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-2">
                        <label>No Urut</label>
                        <input type="number" name="no_urut" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Nama Kandidat</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label>Visi</label>
                    <textarea name="visi" class="form-control" required></textarea>
                </div>

                <div class="mt-3">
                    <label>Misi</label>
                    <textarea name="misi" class="form-control" required></textarea>
                </div>

                <div class="mt-3">
                    <label>Foto Kandidat</label>
                    <input type="file" name="foto" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" name="tambah_kandidat" class="btn btn-primary mt-3">
                    Simpan Kandidat
                </button>
            </form>

<?php if ($editKandidat): ?>
<form method="POST" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="id" value="<?= $editKandidat['id'] ?>">
    <input type="hidden" name="foto_lama" value="<?= $editKandidat['foto'] ?>">

    <div class="row">
        <div class="col-md-2">
            <label>No Urut</label>
            <input type="number" name="no_urut" class="form-control"
                   value="<?= $editKandidat['no_urut'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control"
                   value="<?= $editKandidat['nama'] ?>" required>
        </div>
    </div>

    <div class="mt-3">
        <label>Visi</label>
        <textarea name="visi" class="form-control" required><?= $editKandidat['visi'] ?></textarea>
    </div>

    <div class="mt-3">
        <label>Misi</label>
        <textarea name="misi" class="form-control" required><?= $editKandidat['misi'] ?></textarea>
    </div>

    <div class="mt-3">
        <label>Ganti Foto (opsional)</label>
        <input type="file" name="foto" class="form-control">
    </div>

    <button class="btn btn-primary mt-3" name="update_kandidat">
        Update Kandidat
    </button>
    <a href="panitia.php#kandidat" class="btn btn-secondary mt-3">Batal</a>
</form>
<?php endif; ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>No Urut</th>
                    <th>Nama</th>
                    <th>Visi</th>
                    <th>Misi</th>
                    <th>Foto</th>
                    <th>Suara</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $kandidat = mysqli_query($koneksi, "SELECT * FROM kandidat ORDER BY no_urut");
                while ($k = mysqli_fetch_assoc($kandidat)) {
    echo "
    <tr>
        <td>{$k['no_urut']}</td>
        <td>{$k['nama']}</td>
        <td>{$k['visi']}</td>
        <td>{$k['misi']}</td>
        <td>
            <img src='foto_kandidat/{$k['foto']}' width='80'>
        </td>
        <td>{$k['suara']}</td>
        <td class='text-center'>
            <a href='panitia.php?edit_kandidat={$k['id']}#kandidat'
               class='btn btn-warning btn-sm'>Edit</a>

            <a href='panitia.php?hapus_kandidat={$k['id']}'
               class='btn btn-danger btn-sm'
               onclick=\"return confirm('Yakin ingin menghapus kandidat ini?')\">
               Hapus
            </a>
        </td>
    </tr>";
}
                ?>
                </tbody>
            </table>
        </section>


        <!-- WARGA -->
        <section id="warga" class="card">
            <h2>Data Pemilih</h2>
<?php if ($editWarga): ?>
<form method="POST" class="mb-4">
    <input type="hidden" name="id" value="<?= $editWarga['id'] ?>">

    <div class="row">
        <div class="col-md-4">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control"
                   value="<?= $editWarga['nik'] ?>" required>
        </div>

        <div class="col-md-4">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control"
                   value="<?= $editWarga['nama'] ?>" required>
        </div>

        <div class="col-md-4">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= $editWarga['email'] ?>" required>
        </div>
    </div>

    <button class="btn btn-primary mt-3" name="update_warga">
        Update Warga
    </button>
    <a href="panitia.php#warga" class="btn btn-secondary mt-3">
        Batal
    </a>
</form>
<?php endif; ?>

            <table id="tabelWarga" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Status Memilih</th>
                    <th>Token Unik</th>
                    <th>Aksi</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $warga = mysqli_query($koneksi, "SELECT * FROM user WHERE role='pemilih' ORDER BY nama ASC");
                while ($w = mysqli_fetch_assoc($warga)) {
                    echo "
                    <tr>
                        <td>{$w['id']}</td>
                        <td>{$w['nik']}</td>
                        <td>{$w['nama']}</td>
                        <td>{$w['email']}</td>
                        <td>" . ($w['sudah_memilih'] ? 'Sudah' : 'Belum') . "</td>
                        <td>{$w['kode_unik']}</td>
                        <td class='text-center'>
                   <a href='panitia.php?edit_warga={$w['id']}#warga'
                      class='btn btn-warning btn-sm'>Edit</a>

                   <a href='?hapus_warga={$w['id']}'
                      class='btn btn-danger btn-sm'
                     onclick=\"return confirm('Yakin ingin menghapus warga ini?')\"> Hapus</a>
                     </td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
        </section>

        <!-- HASIL -->
        <?php
        $jumlah_voting = mysqli_num_rows(
            mysqli_query($koneksi, "SELECT * FROM voting")
        );
        ?>

        <section id="hasil" class="card">
            <h2>Hasil Voting</h2>

            <table class="table table-bordered">
                <tr>
                    <th>No Urut</th>
                    <th>Nama</th>
                    <th>Suara</th>
                </tr>

                <?php
                $hasil = mysqli_query($koneksi, "SELECT * FROM kandidat ORDER BY suara DESC");
                while ($h = mysqli_fetch_assoc($hasil)) {
                    echo "
                    <tr>
                        <td>{$h['no_urut']}</td>
                        <td>{$h['nama']}</td>
                        <td>{$h['suara']}</td>
                    </tr>";
                }
                ?>
            </table>

            <?php if ($jumlah_voting > 0): ?>
                <form method="POST" class="mt-3">
                    <button class="btn btn-danger" name="reset_voting"
                            onclick="return confirm('Yakin ingin mereset seluruh data voting?')">
                        Reset Data Voting
                    </button>
                </form>
            <?php endif; ?>

            <p class="fw-bold">
                Total Warga:
                <?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM user WHERE role='pemilih'")); ?>
                |
                Total Suara Masuk:
                <?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM voting")); ?>
            </p>
        </section>

    </main>
</div>

<script>
function toggleForm() {
    const form = document.getElementById("formKandidat");
    form.style.display = form.style.display === "none" ? "block" : "none";
}
</script>

<script>
function togglePeriodeForm() {
    const form = document.getElementById("formPeriode");
    form.style.display = form.style.display === "none" ? "block" : "none";
}
</script>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelWarga').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json"
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        columnDefs: [
            { orderable: false, targets: 5 }
        ]
    });
});
</script>

</body>
</html>