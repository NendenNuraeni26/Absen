<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

$filename = "data_karyawan_" . date("Ymd_His") . ".xls";

// Header supaya browser mengunduh file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Ambil data karyawan
$sql = "SELECT nomor_karyawan, nama_karyawan FROM karyawan ORDER BY id_karyawan ASC";
$result = $conn->query($sql);

// Tabel Excel
echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Nomor Karyawan</th>
        <th>Nama Karyawan</th>
      </tr>";

$no = 1; // Variabel counter untuk nomor urut
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['nomor_karyawan']}</td>
                <td>{$row['nama_karyawan']}</td>
              </tr>";
        $no++; // naikkan nomor urut
    }
} else {
    echo "<tr><td colspan='3'>Tidak ada data karyawan</td></tr>";
}

echo "</table>";
exit();
