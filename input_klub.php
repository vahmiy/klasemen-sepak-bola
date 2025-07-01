<?php include 'koneksi.php'; ?>
<?php include 'header.php'; ?>

<h2 class="mb-4 text-primary text-center">Input Data Klub</h2>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $kota = trim($_POST['kota']);

    if ($nama === '' || $kota === '') {
        echo "<div class='alert alert-warning'>⚠️ Semua field harus diisi.</div>";
    } else {
        $cek = $conn->prepare("SELECT * FROM klub WHERE nama = ?");
        $cek->bind_param("s", $nama);
        $cek->execute();
        $result = $cek->get_result();

        if ($result->num_rows > 0) {
            echo "<div class='alert alert-danger'>🚫 Klub dengan nama tersebut sudah ada.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO klub (nama, kota) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama, $kota);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>✅ Data klub berhasil disimpan.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Gagal menyimpan data.</div>";
            }
        }
    }
}
?>

<form method="post">
    <div class="mb-3">
        <label for="nama" class="form-label">Nama Klub</label>
        <input type="text" class="form-control" name="nama" id="nama" required>
    </div>
    <div class="mb-3">
        <label for="kota" class="form-label">Kota Klub</label>
        <input type="text" class="form-control" name="kota" id="kota" required>
    </div>
    <div class="d-grid d-md-block">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
    </div>
</form>

<?php include 'footer.php'; ?>
