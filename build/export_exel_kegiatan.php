<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

// Nama file Excel
$filename = "data_kegiatan_" . date("Ymd_His") . ".xls";

// Header supaya browser mengunduh file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Ambil data kegiatan dari database
$sql = "SELECT id_kegiatan, nama_kegiatan FROM kegiatan ORDER BY id_kegiatan ASC";
$result = $conn->query($sql);

// Mulai tabel Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nama Kegiatan</th>
      </tr>";

if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama_kegiatan']}</td>
              </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='2'>Tidak ada data kegiatan</td></tr>";
}

echo "</table>";
exit();
