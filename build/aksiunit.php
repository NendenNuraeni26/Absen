<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_unit = trim($_POST['nama_unit']);

    if ($nama_unit != "") {
        $stmt = $conn->prepare("INSERT INTO unit (nama_unit) VALUES (?)");
        $stmt->bind_param("s", $nama_unit);

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
                        text: 'Data Unit berhasil ditambahkan!'
                    }).then(() => {
                        window.location = 'unit.php';
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
                        window.location = 'inputunit.php';
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
                    window.location = 'inputunit.php';
                });
            </script>
        </body>
        </html>";
    }
} else {
    header('Location: inputunit.php');
    exit();
}
