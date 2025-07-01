<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>
    <title>Input Klub</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">
    <h2>Input Data Klub</h2>
    <form method="post">
        <input type="text" name="nama" placeholder="Nama Klub" class="form-control mb-2" required>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama']);
        if (!cek_duplikat_klub($nama, $conn)) {
            $stmt = $conn->prepare("INSERT INTO klub (nama) VALUES (?)");
            $stmt->bind_param("s", $nama);
            $stmt->execute();
            echo "<div class='alert alert-success mt-2'>Data berhasil disimpan!</div>";
        } else {
            echo "<div class='alert alert-danger mt-2'>Nama klub sudah ada!</div>";
        }
    }
    ?>
<?php include 'footer.php'; ?>
