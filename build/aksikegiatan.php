<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $jam_mulai     = trim($_POST['jam_mulai']);
    $jam_selesai   = trim($_POST['jam_selesai']);

    if ($nama_kegiatan != "" && $jam_mulai != "" && $jam_selesai != "") {
        $stmt = $conn->prepare("INSERT INTO kegiatan (nama_kegiatan, jam_mulai, jam_selesai, status) VALUES (?, ?, ?, 'Aktif')");

        $stmt->bind_param("sss", $nama_kegiatan, $jam_mulai, $jam_selesai);
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
                        text: 'Data kegiatan berhasil ditambahkan!'
                    }).then(() => {
                        window.location = 'kegiatan.php';
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
                        window.location = 'inputkegiatan.php';
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
                    window.location = 'inputkegiatan.php';
                });
            </script>
        </body>
        </html>";
    }
} else {
    header('Location: inputkegiatan.php');
    exit();
}
