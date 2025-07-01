<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'db_klasemen';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

function cek_duplikat_klub($nama, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM klub WHERE nama = ?");
    $stmt->bind_param("s", $nama);
    $stmt->execute();
    $stmt->bind_result($jumlah);
    $stmt->fetch();
    $stmt->close();
    return $jumlah > 0;
}

function cek_duplikat_pertandingan($klub1, $klub2, $conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM pertandingan WHERE (klub1_id = ? AND klub2_id = ?) OR (klub1_id = ? AND klub2_id = ?)");
    $stmt->bind_param("iiii", $klub1, $klub2, $klub2, $klub1);
    $stmt->execute();
    $stmt->bind_result($jumlah);
    $stmt->fetch();
    $stmt->close();
    return $jumlah > 0;
}
?>
