<?php
include 'koneksi.php';

// Ambil filter dari URL
$tgl_awal      = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir     = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';
$id_kegiatan   = isset($_GET['id_kegiatan']) ? $_GET['id_kegiatan'] : '';
$id_unit       = isset($_GET['id_unit']) ? $_GET['id_unit'] : '';   // 🔹 Tambahan
$nama_karyawan = isset($_GET['nama_karyawan']) ? trim($_GET['nama_karyawan']) : '';

$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$start = ($page - 1) * $limit;

// Query dasar + JOIN UNIT
$sql = "SELECT 
            k.nama_karyawan,
            u.nama_unit AS unit,          -- 🔹 Tambahkan di SELECT
            g.nama_kegiatan,
            a.tanggal,
            a.jam,
            a.keterangan
        FROM absensi a
        JOIN karyawan k ON a.id_karyawan = k.id_karyawan
        JOIN kegiatan g ON a.id_kegiatan = g.id_kegiatan
        LEFT JOIN unit u ON k.id_unit = u.id_unit   -- 🔹 Tambah JOIN UNIT
        WHERE k.status = 'Aktif'";

// Filter tanggal
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    $sql .= " AND a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
} elseif (!empty($tgl_awal)) {
    $sql .= " AND a.tanggal = '$tgl_awal'";
}

// Filter kegiatan
if (!empty($id_kegiatan)) {
    $sql .= " AND a.id_kegiatan = '$id_kegiatan'";
}

// Filter unit 🔹
if (!empty($id_unit)) {
    $sql .= " AND k.id_unit = '$id_unit'";
}

// Filter nama karyawan
if (!empty($nama_karyawan)) {
    $nama_karyawan_safe = $conn->real_escape_string($nama_karyawan);
    $sql .= " AND k.nama_karyawan LIKE '%$nama_karyawan_safe%'";
}

$sql .= " ORDER BY a.tanggal DESC, a.jam DESC";

$result = $conn->query($sql);

// Header untuk Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_absensi.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Cetak table
echo "<table border='1'>
        <tr>
            <th>No</th>
            <th>Nama Karyawan</th>
            <th>Unit</th>
            <th>Kegiatan</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Keterangan</th>
        </tr>";

if ($result && $result->num_rows > 0) {
    $no = $start + 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $no++ . "</td>
                <td>" . htmlspecialchars($row['nama_karyawan']) . "</td>
                <td>" . htmlspecialchars($row['unit']) . "</td>      <!-- 🔹 Tambah kolom Unit -->
                <td>" . htmlspecialchars($row['nama_kegiatan']) . "</td>
                <td>" . $row['tanggal'] . "</td>
                <td>" . $row['jam'] . "</td>
                <td>" . htmlspecialchars($row['keterangan']) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>Tidak ada data absensi</td></tr>";
}

echo "</table>";
?>

