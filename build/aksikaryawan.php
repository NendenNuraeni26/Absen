<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomor_karyawan = trim($_POST['nomor_karyawan']);
    $nama_karyawan  = trim($_POST['nama_karyawan']);

    if ($nomor_karyawan != "" && $nama_karyawan != "") {
       $id_unit = intval($_POST['id_unit']);
    $stmt = $conn->prepare("INSERT INTO karyawan (nomor_karyawan, nama_karyawan, id_unit) 
                            VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nomor_karyawan, $nama_karyawan, $id_unit);



        if ($stmt->execute()) {
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data karyawan berhasil ditambahkan!'
                    }).then(() => {
                        window.location = 'karyawan.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            echo "
            <!DOCTYPE html>
            <html>
            <head>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menambahkan data!'
                    }).then(() => {
                        window.location = 'inputkaryawan.php';
                    });
                </script>
            </body>
            </html>";
        }
        $stmt->close();
    } else {
        echo "
        <!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Mohon isi semua field!'
                }).then(() => {
                    window.location = 'inputkaryawan.php';
                });
            </script>
        </body>
        </html>";
    }
} else {
    header('Location: inputkaryawan.php');
    exit();
}
