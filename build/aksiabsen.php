<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_karyawan = $_POST['id_karyawan'];
    $id_kegiatan = $_POST['id_kegiatan'];
    $keterangan  = $_POST['keterangan'];
    $foto        = $_POST['foto'];

    $latitude    = $_POST['latitude'];
    $longitude   = $_POST['longitude'];

    $tanggal = date("Y-m-d");
    $jam     = date("H:i:s");

    // === CEK JAM ABSEN SESUAI KEGIATAN ===
    $cekJam = $conn->prepare("SELECT jam_mulai, jam_selesai FROM kegiatan WHERE id_kegiatan = ? AND status='Aktif'");
    $cekJam->bind_param("i", $id_kegiatan);
    $cekJam->execute();
    $hasilJam = $cekJam->get_result();

    if ($hasilJam->num_rows > 0) {
        $rowJam = $hasilJam->fetch_assoc();

        $jamMulai   = strtotime($rowJam['jam_mulai']);
        $jamSelesai = strtotime($rowJam['jam_selesai']);
        $jamSekarang = strtotime($jam);

        if ($jamSekarang < $jamMulai || $jamSekarang > $jamSelesai) {
            echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Absen!',
                    text: 'Absensi hanya bisa dilakukan antara {$rowJam['jam_mulai']} - {$rowJam['jam_selesai']}',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='tampilanabsen.php';
                });
            });
        </script>";
            exit;
        }
    }

    $cekJam->close();

    // === CEK JARAK DENGAN KOORDINAT MUSHOLA ===
    $musholaLat = -6.887051;
    $musholaLng = 112.052658;

//    $musholaLat = -6.88763;
//    $musholaLng = 112.05281;
    $batasMeter = 80; // radius dalam meter




    function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000; // radius bumi (meter)
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    $jarak = haversine($latitude, $longitude, $musholaLat, $musholaLng);

    if ($jarak > $batasMeter) {
        // ❌ Jika di luar radius, jangan simpan absensi
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Absen!',
                    text: 'Silahkan Refresh Kembali Halaman (" . round($jarak, 2) . " m)',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='tampilanabsen.php';
                });
            });
        </script>";
        exit;
    }

    // === CEK APAKAH SUDAH ABSEN ===
    $cek = $conn->prepare("SELECT id_absensi FROM absensi 
                           WHERE id_karyawan = ? 
                           AND id_kegiatan = ? 
                           AND tanggal = ?");
    $cek->bind_param("iis", $id_karyawan, $id_kegiatan, $tanggal);
    $cek->execute();
    $hasil = $cek->get_result();

    if ($hasil->num_rows > 0) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sudah Absen!',
                    text: 'Anda sudah melakukan absensi untuk kegiatan ini pada tanggal tersebut!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='tampilanabsen.php';
                });
            });
        </script>";
        exit;
    }
    $cek->close();

    // === PROSES KONVERSI FOTO ===
    $foto = str_replace('data:image/png;base64,', '', $foto);
    $foto = str_replace(' ', '+', $foto);
    $fotoData = base64_decode($foto);

    // === SIMPAN ABSENSI ===
    $sql = "INSERT INTO absensi 
            (id_karyawan, id_kegiatan, tanggal, jam, keterangan, foto, latitude, longitude) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Query Error: " . $conn->error);
    }

    $stmt->bind_param(
        "iisssbss",
        $id_karyawan,
        $id_kegiatan,
        $tanggal,
        $jam,
        $keterangan,
        $fotoData,
        $latitude,
        $longitude
    );
    $stmt->send_long_data(5, $fotoData);

    if ($stmt->execute()) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Absensi berhasil disimpan!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='tampilanabsen.php';
                });
            });
        </script>";
    } else {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menyimpan absensi: " . addslashes($stmt->error) . "',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href='tampilanabsen.php';
                });
            });
        </script>";
    }

    $stmt->close();
}
$conn->close();
