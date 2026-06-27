<?php
include 'koneksi.php';

if (isset($_POST['nomor_karyawan'])) {
    $nomor = trim($_POST['nomor_karyawan']);
    $sql = $conn->prepare("SELECT id_karyawan, nama_karyawan FROM karyawan WHERE nomor_karyawan = ?");
    $sql->bind_param("s", $nomor);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "status" => "ada",
            "id_karyawan" => $row['id_karyawan'],
            "nama_karyawan" => $row['nama_karyawan']
        ]);
    } else {
        echo json_encode(["status" => "tidak"]);
    }
}
