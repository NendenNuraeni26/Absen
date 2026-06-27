<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}

include 'koneksi.php';

// Ambil ID absensi dari URL
$id_absensi = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_absensi <= 0) {
    echo '<!DOCTYPE html>
    <html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops!",
            text: "ID absensi tidak valid.",
        }).then(() => {
            window.location.href = "adminabsen.php";
        });
    </script>
    </body></html>';
    exit();
}

// Cek apakah data absensi ada
$stmt = $conn->prepare("SELECT a.id_absensi, k.nama_karyawan, g.nama_kegiatan, a.tanggal 
                        FROM absensi a
                        JOIN karyawan k ON a.id_karyawan = k.id_karyawan
                        JOIN kegiatan g ON a.id_kegiatan = g.id_kegiatan
                        WHERE a.id_absensi = ?");
$stmt->bind_param("i", $id_absensi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<!DOCTYPE html>
    <html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Data tidak ditemukan",
            text: "Absensi dengan ID ' . $id_absensi . ' tidak ada.",
        }).then(() => {
            window.location.href = "adminabsen.php";
        });
    </script>
    </body></html>';
    exit();
}

$data = $result->fetch_assoc();
$stmt->close();

// Jika konfirmasi hapus diterima
if (isset($_POST['hapus'])) {
    $stmt = $conn->prepare("DELETE FROM absensi WHERE id_absensi = ?");
    $stmt->bind_param("i", $id_absensi);

    if ($stmt->execute()) {
        echo '<!DOCTYPE html>
        <html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body>
        <script>
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: "Data absensi berhasil dihapus!",
            }).then(() => {
                window.location.href = "adminabsen.php";
            });
        </script>
        </body></html>';
    } else {
        echo '<!DOCTYPE html>
        <html><head><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body>
        <script>
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: "Terjadi kesalahan saat menghapus data."
            }).then(() => {
                window.location.href = "adminabsen.php";
            });
        </script>
        </body></html>';
    }
    $stmt->close();
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hapus Absensi</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Hapus absensi milik <?= htmlspecialchars($data['nama_karyawan']) ?> pada kegiatan <?= htmlspecialchars($data['nama_kegiatan']) ?> (<?= $data['tanggal'] ?>)?",
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
                window.location.href = 'adminabsen.php';
            }
        });
    </script>
</body>

</html>