<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil status lama
    $cek = $conn->query("SELECT status FROM karyawan WHERE id_karyawan = $id");
    if ($cek && $cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        $status_lama = $row['status'];

        // Toggle status
        $status_baru = ($status_lama == 'Aktif') ? 'Non Aktif' : 'Aktif';

        $conn->query("UPDATE karyawan SET status='$status_baru' WHERE id_karyawan=$id");
    }
}

header("Location: karyawan.php");
exit();
