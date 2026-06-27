<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== "login") {
    header("Location: index.php");
    exit();
}


date_default_timezone_set('Asia/Jakarta');

// 🔹 Koneksi dulu
include 'koneksi.php';

// 🔹 Tanggal hari ini
$tanggal = date("Y-m-d");
$jam = date("H:i:s");

// 🔹 Ambil total absen hari ini (hanya karyawan aktif)
$sql_total_absen = "SELECT COUNT(*) as total_absen 
                    FROM absensi a
                    JOIN karyawan k ON a.id_karyawan = k.id_karyawan
                    WHERE k.status = 'Aktif'
                      AND a.tanggal = '$tanggal'";
$result_total = $conn->query($sql_total_absen);
$total_absen = 0;
if ($result_total && $row = $result_total->fetch_assoc()) {
    $total_absen = $row['total_absen'];
}


// default aman
$total_karyawan = 0;

// Hitung total karyawan Aktif
$sql_total_karyawan = "SELECT COUNT(*) AS total_karyawan FROM karyawan WHERE status = 'Aktif'";
$result_karyawan = $conn->query($sql_total_karyawan);
if ($result_karyawan && $row = $result_karyawan->fetch_assoc()) {
    $total_karyawan = (int) $row['total_karyawan'];
}

// 🔹 Total Kegiatan
$sql_total_kegiatan = "SELECT COUNT(*) as total_kegiatan FROM kegiatan WHERE status ='Aktif'";
$result_kegiatan = $conn->query($sql_total_kegiatan);
if ($result_kegiatan && $row = $result_kegiatan->fetch_assoc()) {
    $total_kegiatan = (int) $row['total_kegiatan'];
}



// Pagination
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$total_result = $conn->query("SELECT COUNT(*) AS total FROM karyawan");
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);

// Ambil ID karyawan dari request
$id_unit = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data karyawan dari database
$stmt = $conn->prepare("SELECT * FROM unit WHERE id_unit = ?");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<!DOCTYPE html>
    <html><head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head><body>
    <script>
        Swal.fire({
            icon: "error",
            title: "Data tidak ditemukan!",
            text: "Unit tidak ada.",
        }).then(() => {
            window.location.href = "unit.php";
        });
    </script>
    </body></html>';
    exit();
}

$unit = $result->fetch_assoc();
$stmt->close();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_unit = trim($_POST['nama_unit']);

    if ($nama_unit != "") {

        $stmt = $conn->prepare("UPDATE unit SET nama_unit = ? WHERE id_unit = ?");
        $stmt->bind_param("si", $nama_unit, $id_unit);

        if ($stmt->execute()) {
            echo '<!DOCTYPE html>
            <html><head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head><body>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: "Data unit berhasil diupdate!",
                }).then(() => {
                    window.location.href = "unit.php";
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
                    text: "Terjadi kesalahan saat mengupdate data."
                });
            </script>
            </body></html>';
        }

        $stmt->close();

    } else {
        echo '<!DOCTYPE html>
        <html><head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head><body>
        <script>
            Swal.fire({
                icon: "warning",
                title: "Oops!",
                text: "Mohon isi semua field!"
            });
        </script>
        </body></html>';
    }
}

?>





<!DOCTYPE html>
<html>

<?php
include 'komponen/header.php';
?>

<body
    class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    <!-- sidenav : Menu Rekab Absen,  Absen ,Lihat Data dan Input Data  -->
    <?php
    include 'komponen/asidenav.php';
    ?>
    <!-- end sidenav -->


    <main
        class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">

        <!-- Navbar : Selamat Datang dan Logout -->
        <?php
        include 'komponen/navbar.php';
        ?>
        <!-- end Navbar -->



        <!-- cards -->
        <div class="w-full px-6 py-6 mx-auto">
            <!-- row 1: Tanggal, Total Absen, Total Karyawan, Total Kegiatan -->
            <div class="flex flex-wrap -mx-3">
                <?php
                include 'komponen/card.php';
                ?>
            </div> <!-- end row 1 -->



            <!-- row 2: Form Input Unit -->
            <!-- Container besar -->
            <div class="w-full bg-white rounded-2xl shadow p-10 mt-6 relative">

                <!-- 🔹 Tombol Kembali -->
                <a href="unit.php"
                    class="absolute top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-gray-600 transition">
                    Kembali
                </a>


                <!-- Header: Logo + Judul -->
                <div class="flex items-center justify-center mb-6 relative">
                    <!-- Logo kiri -->
                    <img src="assets/img/Logors.png" alt="Logo RS" class="h-16 w-16 absolute left-0">

                    <!-- Judul tengah -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center">
                        Form Update Data Unit Rumah Sakit Muhammadiyah
                    </h1>
                </div>

                <!-- Grid 2 Kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Kiri: Form -->
                    <div class="bg-gray-50 shadow-xl rounded-xl p-5">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                            Update Unit
                        </h2>

                        <br>
                        <form method="POST" class="space-y-4">
                            <div>
                                <label for="nama_unit" class="block font-semibold mb-2">Nama Unit</label>
                                <input type="text" name="nama_unit" id="nama_unit"
                                    value="<?= htmlspecialchars($unit['nama_unit']) ?>"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base shadow-md focus:border-green-500 focus:ring-2 focus:ring-green-400 focus:shadow-lg transition"
                                    required>
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Kanan: Gambar -->
                    <div class="flex justify-center items-center">
                        <img src="img/Gambar 4.png" alt="Ilustrasi Karyawan" class="w-100">
                    </div>

                </div>
            </div>




            <!-- footer -->
            <?php
            include 'komponen/footer.php';
            ?>

        </div>

        <!-- end cards -->
    </main>

</body>
<!-- plugin for charts  -->
<script src="./assets/js/plugins/chartjs.min.js" async></script>
<!-- plugin for scrollbar  -->
<script src="./assets/js/plugins/perfect-scrollbar.min.js" async></script>
<!-- github button -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- main script file  -->
<script
    src="./assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5"
    async></script>

</html>
