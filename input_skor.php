<?php
include 'koneksi.php';

// Ambil daftar klub untuk dropdown
$klub_result = $conn->query("SELECT * FROM klub ORDER BY nama");
$klub_options = "";
while ($klub = $klub_result->fetch_assoc()) {
    $klub_options .= "<option value='{$klub['id']}'>{$klub['nama']}</option>";
}
?>

<?php include 'header.php'; ?>

<h2 class="mb-4 text-center text-primary">Input Skor Pertandingan</h2>
<form method="post" onsubmit="return validateForm()">
    <!-- Label judul kolom -->
    <div class="row fw-bold mb-2 g-2 text-center d-none d-md-flex">
        <div class="col-md-3">Nama Klub 1</div>
        <div class="col-md-1">Skor</div>
        <div class="col-md-1"></div>
        <div class="col-md-3">Nama Klub 2</div>
        <div class="col-md-1">Skor</div>
        <div class="col-md-2">Aksi</div>
    </div>

    <div id="form-container"></div>
    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
        <button type="button" class="btn btn-outline-primary btn-custom w-100 w-md-auto" onclick="addRow()">
            <i class="fa fa-plus"></i> Tambah Pertandingan
        </button>
        <button type="submit" class="btn btn-success btn-custom w-100 w-md-auto">
            <i class="fa fa-save"></i> Simpan Semua
        </button>
    </div>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $klub1 = $_POST['klub1'] ?? [];
    $skor1 = $_POST['skor1'] ?? [];
    $klub2 = $_POST['klub2'] ?? [];
    $skor2 = $_POST['skor2'] ?? [];
    $berhasil = 0;
    $duplikat = 0;
    $sama = 0;

    for ($i = 0; $i < count($klub1); $i++) {
        $id1 = (int) $klub1[$i];
        $id2 = (int) $klub2[$i];
        $s1 = (int) $skor1[$i];
        $s2 = (int) $skor2[$i];

        if ($id1 === $id2) {
            $sama++;
            continue;
        }
        if (cek_duplikat_pertandingan($id1, $id2, $conn)) {
            $duplikat++;
            continue;
        }
        $stmt = $conn->prepare("INSERT INTO pertandingan (klub1_id, klub2_id, skor_klub1, skor_klub2) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $id1, $id2, $s1, $s2);
        $stmt->execute();
        $stmt->close();
        $berhasil++;
    }

    echo "<div class='mt-3'>
            <div class='alert alert-success'>✅ Berhasil simpan $berhasil pertandingan</div>";
    if ($sama > 0) echo "<div class='alert alert-warning'>⚠️ $sama data tidak disimpan karena klub sama</div>";
    if ($duplikat > 0) echo "<div class='alert alert-danger'>🚫 $duplikat data tidak disimpan karena duplikat</div>";
    echo "</div>";
}
?>

<script>
function addRow() {
    const container = document.getElementById('form-container');
    const row = document.createElement('div');
    row.classList.add('row', 'align-items-center', 'mb-3', 'g-2');
    row.innerHTML = `
        <div class="col-12 col-md-3">
            <label class="form-label d-md-none">Nama Klub 1</label>
            <select name="klub1[]" class="form-select klub1" required>
                <option value="">Pilih Klub</option>
                <?php echo $klub_options; ?>
            </select>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label d-md-none">Skor Klub 1</label>
            <input type="number" name="skor1[]" class="form-control" required min="0">
        </div>
        <div class="col-12 col-md-1 text-center fw-bold d-none d-md-block">vs</div>
        <div class="col-12 col-md-3">
            <label class="form-label d-md-none">Nama Klub 2</label>
            <select name="klub2[]" class="form-select klub2" required>
                <option value="">Pilih Klub</option>
                <?php echo $klub_options; ?>
            </select>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label d-md-none">Skor Klub 2</label>
            <input type="number" name="skor2[]" class="form-control" required min="0">
        </div>
        <div class="col-12 col-md-2 text-end">
            <label class="form-label d-md-none">Aksi</label>
            <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.parentElement.parentElement.remove()">
                <i class='fa fa-trash'></i> Hapus
            </button>
        </div>
    `;
    container.appendChild(row);
}

function validateForm() {
    const klub1s = document.querySelectorAll('.klub1');
    const klub2s = document.querySelectorAll('.klub2');
    let isValid = true;
    let duplikatSet = new Set();

    for (let i = 0; i < klub1s.length; i++) {
        const val1 = klub1s[i].value;
        const val2 = klub2s[i].value;

        if (val1 === "" || val2 === "") {
            alert("Semua klub harus dipilih.");
            isValid = false;
            break;
        }
        if (val1 === val2) {
            alert("Klub tidak boleh sama pada baris ke-" + (i+1));
            isValid = false;
            break;
        }
        const key1 = `${val1}-${val2}`;
        const key2 = `${val2}-${val1}`;
        if (duplikatSet.has(key1) || duplikatSet.has(key2)) {
            alert("Pertandingan duplikat terdeteksi pada baris ke-" + (i+1));
            isValid = false;
            break;
        }
        duplikatSet.add(key1);
    }

    return isValid;
}

addRow();
</script>

<?php include 'footer.php'; ?>
