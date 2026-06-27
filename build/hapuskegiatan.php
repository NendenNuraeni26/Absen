<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

// Ambil ID kegiatan dari URL
$id_kegiatan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_kegiatan <= 0) {
    echo '<!DOCTYPE html>
    <html><head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head><body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops!",
            text: "ID kegiatan tidak valid.",
        }).then(() => {
            window.location.href = "kegiatan.php";
        });
    </script>
    </body></html>';
    exit();
}

// Cek apakah data kegiatan ada
$stmt = $conn->prepare("SELECT * FROM kegiatan WHERE id_kegiatan = ?");
$stmt->bind_param("i", $id_kegiatan);
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
            text: "Kegiatan dengan ID ' . $id_kegiatan . ' tidak ada.",
        }).then(() => {
            window.location.href = "kegiatan.php";
        });
    </script>
    </body></html>';
    exit();
}

$stmt->close();

// Jika konfirmasi hapus diterima
if (isset($_POST['hapus'])) {
    $stmt = $conn->prepare("DELETE FROM kegiatan WHERE id_kegiatan = ?");
    $stmt->bind_param("i", $id_kegiatan);

    if ($stmt->execute()) {
        echo '<!DOCTYPE html>
        <html><head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head><body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: "Data kegiatan berhasil dihapus!",
            }).then(() => {
                window.location.href = "kegiatan.php";
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
                window.location.href = "kegiatan.php";
            });
        </script>
        </body></html>';
        exit();
    }

    $stmt->close();
}

// Tampilkan halaman konfirmasi hapus
$kegiatan = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hapus Kegiatan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menghapus kegiatan: <?= htmlspecialchars($kegiatan['nama_kegiatan']) ?>",
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
                window.location.href = 'kegiatan.php';
            }
        });
    </script>
</body>

</html>