<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

// Ambil ID karyawan dari URL
$id_karyawan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_karyawan <= 0) {
    echo '<!DOCTYPE html>
    <html><head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head><body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops!",
            text: "ID karyawan tidak valid.",
        }).then(() => {
            window.location.href = "karyawan.php";
        });
    </script>
    </body></html>';
    exit();
}

// Cek apakah data karyawan ada
$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id_karyawan = ?");
$stmt->bind_param("i", $id_karyawan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<!DOCTYPE html>
    <html><head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head><body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Data tidak ditemukan",
            text: "Karyawan dengan ID ' . $id_karyawan . ' tidak ada.",
        }).then(() => {
            window.location.href = "karyawan.php";
        });
    </script>
    </body></html>';
    exit();
}

$stmt->close();

// Jika konfirmasi hapus diterima
if (isset($_POST['hapus'])) {
    $stmt = $conn->prepare("DELETE FROM karyawan WHERE id_karyawan = ?");
    $stmt->bind_param("i", $id_karyawan);

    if ($stmt->execute()) {
        echo '<!DOCTYPE html>
        <html><head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head><body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: "Data karyawan berhasil dihapus!",
            }).then(() => {
                window.location.href = "karyawan.php";
            });
        </script>
        </body></html>';
        exit();
    } else {
        echo '<!DOCTYPE html>
        <html><head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head><body>
        <script>
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: "Terjadi kesalahan saat menghapus data."
            }).then(() => {
                window.location.href = "karyawan.php";
            });
        </script>
        </body></html>';
        exit();
    }

    $stmt->close();
}

// Tampilkan halaman konfirmasi hapus
$karyawan = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hapus Karyawan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menghapus karyawan: <?= htmlspecialchars($karyawan['nama_karyawan']) ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat form dan submit otomatis
                let form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = '<input type="hidden" name="hapus" value="1">';
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = 'karyawan.php';
            }
        });
    </script>
</body>

</html>