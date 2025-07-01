<?php
include 'koneksi.php';

$klasemen = [];

$klub_query = $conn->query("SELECT * FROM klub");
while ($klub = $klub_query->fetch_assoc()) {
    $klasemen[$klub['id']] = [
        'nama' => $klub['nama'],
        'main' => 0,
        'menang' => 0,
        'seri' => 0,
        'kalah' => 0,
        'gm' => 0,
        'gk' => 0,
        'poin' => 0
    ];
}

$match_query = $conn->query("SELECT * FROM pertandingan");
while ($m = $match_query->fetch_assoc()) {
    $id1 = $m['klub1_id'];
    $id2 = $m['klub2_id'];
    $s1 = $m['skor_klub1'];
    $s2 = $m['skor_klub2'];

    // Tambah jumlah main
    $klasemen[$id1]['main']++;
    $klasemen[$id2]['main']++;

    // Tambah GM dan GK
    $klasemen[$id1]['gm'] += $s1;
    $klasemen[$id1]['gk'] += $s2;

    $klasemen[$id2]['gm'] += $s2;
    $klasemen[$id2]['gk'] += $s1;

    if ($s1 > $s2) {
        $klasemen[$id1]['menang']++;
        $klasemen[$id1]['poin'] += 3;
        $klasemen[$id2]['kalah']++;
    } elseif ($s1 < $s2) {
        $klasemen[$id2]['menang']++;
        $klasemen[$id2]['poin'] += 3;
        $klasemen[$id1]['kalah']++;
    } else {
        $klasemen[$id1]['seri']++;
        $klasemen[$id2]['seri']++;
        $klasemen[$id1]['poin']++;
        $klasemen[$id2]['poin']++;
    }
}

// Urutkan berdasarkan poin, lalu selisih gol jika sama
usort($klasemen, function($a, $b) {
    if ($b['poin'] === $a['poin']) {
        $selisihA = $a['gm'] - $a['gk'];
        $selisihB = $b['gm'] - $b['gk'];
        return $selisihB <=> $selisihA;
    }
    return $b['poin'] <=> $a['poin'];
});
?>
<?php include 'header.php'; ?>
    <title>Klasemen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2 class="text-center mb-4">Tampilan Klasemen</h2>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>No</th><th>Klub</th><th>Ma</th><th>Me</th><th>S</th><th>K</th><th>GM</th><th>GK</th><th>Point</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($klasemen as $data): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($data['nama']) ?></td>
                <td><?= $data['main'] ?></td>
                <td><?= $data['menang'] ?></td>
                <td><?= $data['seri'] ?></td>
                <td><?= $data['kalah'] ?></td>
                <td><?= $data['gm'] ?></td>
                <td><?= $data['gk'] ?></td>
                <td><?= $data['poin'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php include 'footer.php'; ?>
