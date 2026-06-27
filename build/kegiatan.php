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
$total_result = $conn->query("SELECT COUNT(*) AS total FROM kegiatan");
$total_row = $total_result->fetch_assoc();
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data karyawan sesuai halaman
$result_paginated = $conn->query("SELECT * FROM kegiatan LIMIT $start, $limit");
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


                <!-- cards row 2 -->
                <div class="w-full px-6 py-6 mx-auto">
                    <div class="w-full px-3 mb-6">
                        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl p-6">
                            <!-- Header Judul + Logo -->
                            <br>
                            <div class="flex items-center justify-center mb-2 relative">
                                <!-- Logo RS (posisi kiri) -->
                                <img src="assets/img/Logors.png" alt="Logo RS" class="h-12 w-12 absolute left-0">

                                <!-- Judul (posisi tengah) -->
                                <h1 class="text-2xl font-bold text-gray-800 text-center">
                                    Rekap Data Kegiatan Rumah Sakit Muhammadiyah
                                </h1>
                            </div>
                            <br>

                            <!-- Form Filter Rekap -->

                            <form method="GET" class="items-center">
                             <br>
                                <!-- Tombol Aksi (kanan atas) -->
                                <div class="flex justify-end mb-3 gap-2 -mt-2">
                                    <!-- Tombol Tambah Data -->
                                    <a href="inputkegiatan.php"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                        Tambah Data
                                    </a>

                                    <!-- Tombol Download Data -->
                                    <a href="export_exel_kegiatan.php"
                                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                        Download Data
                                    </a>
                                </div>

                                <!-- Tabel Daftar Kegiatan -->
                                <div class="overflow-x-auto mt-4">
                                    <table class="w-full border border-gray-200 rounded-lg text-sm text-center">
                                        <thead class="bg-blue-100 text-gray-700">
                                            <tr>
                                                <th class="px-2 py-2 border w-5">No</th>
                                                <th class="px-2 py-2 border w-10">Nama Kegiatan</th>
                                                <th class="px-2 py-2 border w-10">Jam Mulai</th>
                                                <th class="px-2 py-2 border w-10">Jam Selesai</th>
                                                <th class="px-2 py-2 border w-10">Status</th>
                                                <th class="px-2 py-2 border w-5">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result_paginated && $result_paginated->num_rows > 0) {
                                                $no = $start + 1;
                                                while ($row = $result_paginated->fetch_assoc()) {
                                                    $id     = $row['id_kegiatan'];
                                                    $nama   = htmlspecialchars($row['nama_kegiatan']);
                                                    $mulai  = $row['jam_mulai'];
                                                    $selesai = $row['jam_selesai'];
                                                    $status = $row['status'];

                                                    echo "<tr class='hover:bg-gray-50'>
                        <td class='border px-3 py-2'>$no</td>
                        <td class='border px-3 py-2'>$nama</td>
                        <td class='border px-3 py-2'>$mulai</td>
                        <td class='border px-3 py-2'>$selesai</td>
                        <td class='border px-3 py-2'>$status</td>
                        <td class='border px-3 py-2'>
                            <div class='flex justify-center space-x-2'>
                                <a href='updatekegiatan.php?id=$id' 
                                class='bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded'>
                                Update
                                </a>
                                <a href='hapuskegiatan.php?id=$id'  
                                class='bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded'>
                                Delete
                                </a>
                                <a href='ubahstatuskegiatan.php?id=$id'  
                                class='" . ($status == "Aktif"
                                                        ? "bg-yellow-500 hover:bg-yellow-600"
                                                        : "bg-green-500 hover:bg-green-600") . " text-white px-4 py-1 rounded'>
                                " . ($status == "Aktif" ? "Nonaktifkan" : "Aktifkan") . "
                                </a>
                            </div>
                        </td>
                    </tr>";
                                                    $no++;
                                                }
                                            } else {
                                                echo "<tr>
                <td colspan='6' class='text-center py-4 text-gray-500'>
                    Tidak ada data kegiatan
                </td>
              </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>


                                </div>

                                <!-- Pagination -->
                                <div class="mt-4 flex justify-center gap-2">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>"
                                            class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Previous</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>"
                                            class="px-3 py-1 <?php echo ($i == $page) ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 rounded'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>"
                                            class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</a>
                                    <?php endif; ?>
                                </div>
                            </form>

                        </div>
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
