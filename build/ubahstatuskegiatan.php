<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ambil status lama
    $cek = $conn->query("SELECT status FROM kegiatan WHERE id_kegiatan = $id");
    if ($cek && $cek->num_rows > 0) {
        $row = $cek->fetch_assoc();
        $status_lama = $row['status'];

        // toggle status
        $status_baru = ($status_lama == 'Aktif') ? 'Non Aktif' : 'Aktif';

        $conn->query("UPDATE kegiatan SET status='$status_baru' WHERE id_kegiatan=$id");
    }
}

header("Location: kegiatan.php");
exit();
