<?php
include "koneksi.php";

/* TOTAL SUARA */
$totalSuara = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM voting")
)['total'];

/* TOTAL PEMILIH */
$totalUser = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pemilih'")
)['total'];


$partisipasi = ($totalUser > 0) ? round(($totalSuara / $totalUser) * 100) : 0;

/* DATA KANDIDAT */
$kandidat = mysqli_query($koneksi, "SELECT * FROM kandidat ORDER BY no_urut");

$hasil = [];
$labels = [];
$values = [];

while ($k = mysqli_fetch_assoc($kandidat)) {
    $persen = ($totalSuara > 0)
        ? round(($k['suara'] / $totalSuara) * 100)
        : 0;

    $hasil[] = [
        'nama' => $k['nama'],
        'suara' => $k['suara'],
        'persen' => $persen
    ];

    $labels[] = $k['nama'];
    $values[] = $k['suara'];
}

/* AMBIL PERIODE PEMILIHAN AKTIF */
$periode = mysqli_fetch_assoc(
    mysqli_query($koneksi, "
        SELECT start_datetime, end_datetime
        FROM periode_pemilu
        WHERE status = 'aktif'
        LIMIT 1
    ")
);

if ($periode) {
    $tanggal_mulai   = $periode['start_datetime'];
    $tanggal_selesai = $periode['end_datetime'];
} else {
    $tanggal_mulai   = null;
    $tanggal_selesai = null;
}


/* HITUNG TOTAL SUARA KANDIDAT */
$totalSuara = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT SUM(suara) AS total FROM kandidat")
)['total'] ?? 0;

/* AMBIL DATA KANDIDAT */
$dataKandidat = mysqli_query($koneksi, "SELECT id, suara FROM kandidat");


/* INSERT HASIL PEMILIHAN */
$cekHasil = mysqli_fetch_assoc(
    mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM hasil_pemilihan")
);

if ($cekHasil['total'] == 0 && $tanggal_mulai && $tanggal_selesai) {

    $dataKandidat = mysqli_query($koneksi, "
        SELECT id, suara FROM kandidat
    ");

    while ($k = mysqli_fetch_assoc($dataKandidat)) {

        $id_kandidat = $k['id'];
        $suara       = $k['suara'];

        mysqli_query($koneksi, "
            INSERT INTO hasil_pemilihan (
                id_kandidat,
                tanggal_mulai,
                tanggal_selesai,
                perolehan_suara_kandidat,
                total_suara
            ) VALUES (
                '$id_kandidat',
                '$tanggal_mulai',
                '$tanggal_selesai',
                '$suara',
                '$totalSuara'
            )
        ");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pemilihan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/ResultStyle.css">
</head>

<body>

<div class="container main-container">

    <!-- JUDUL -->
    <h1 class="page-title">HASIL PEMILIHAN</h1>

    <!-- SUMMARY -->
    <div class="row summary-row">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-value"><?= $totalSuara ?></div>
                <div class="summary-label">Total Suara</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-value"><?= $totalUser ?></div>
                <div class="summary-label">DPT / Pemilih</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card highlight">
                <div class="summary-value"><?= $partisipasi ?>%</div>
                <div class="summary-label">Partisipasi</div>
            </div>
        </div>
    </div>

    <!-- KONTEN -->
    <div class="row content-row">

        <!-- KIRI -->
        <div class="col-md-7">
            <div class="card-box">

                <?php
                $warna = ['blue', 'cyan', 'orange'];
                foreach ($hasil as $i => $h):
                ?>
                <div class="candidate-item">
                    <div class="candidate-top">
                        <div>
                            <div class="candidate-name"><?= $h['nama'] ?></div>
                            <div class="candidate-id">
                                ID : <?= $i + 1 ?> - <?= $h['suara'] ?> suara
                            </div>
                        </div>
                        <div class="candidate-value">
                            <?= $h['suara'] ?> (<?= $h['persen'] ?>%)
                        </div>
                    </div>

                    <div class="progress-custom">
                        <div class="progress-fill <?= $warna[$i] ?>"
                             style="width: <?= $h['persen'] ?>%">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- RINCIAN HASIL -->
        <div class="col-md-5">
            <div class="card-box">

                <h5 class="section-title">Rincian Hasil</h5>
                
                <table class="table custom-table mt-3">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kandidat</th>
                            <th>Suara</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($hasil as $i => $h): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $h['nama'] ?></td>
                            <td><?= $h['suara'] ?></td>
                            <td><?= $h['persen'] ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="rekap-box">Data terkini direkap otomatis</div>

                <div class="btn-row">
                    <button onclick="window.print()" class="btn btn-success">
                        Cetak Hasil
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- GRAFIK -->
    <div class="card-box chart-box">
        <h5 class="section-title">Grafik Hasil Pemilihan</h5>
        <canvas id="voteChart"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('voteChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($values) ?>,
            backgroundColor: ['#5b64e6', '#4db6e5', '#f0b15b'],
            borderRadius: 6
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>

</html>
