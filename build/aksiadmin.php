<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Cek ke database
$query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);

    // Set session
    $_SESSION['username'] = $data['username'];
    $_SESSION['status'] = "login";

    // Kalau login berhasil → tampilkan SweetAlert lalu redirect
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Login</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Selamat datang, " . $data['username'] . "',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'adminabsen.php';
            });
        </script>
    </body>
    </html>
    ";
} else {
    // Kalau gagal → tampilkan SweetAlert lalu redirect ke index
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Login</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: 'Username atau Password salah!',
                confirmButtonText: 'Coba Lagi'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>
    </body>
    </html>
    ";
}
