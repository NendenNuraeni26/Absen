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

// Ambil data nama unit sesuai halaman
$result_paginated = $conn->query("SELECT * FROM unit LIMIT $start, $limit");
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



            <!-- row 2: Form Input Kegiatan -->
            <!-- Container besar -->
            <div class="w-full bg-white rounded-2xl shadow p-10 mt-6 relative">

                <!-- Header: Logo + Judul -->
                <div class="flex items-center justify-center mb-6 relative">
                    <!-- Logo kiri -->
                    <img src="assets/img/Logors.png" alt="Logo RS" class="h-16 w-16 absolute left-0">

                    <!-- Judul tengah -->
                    <h1 class="text-2xl font-bold text-gray-800 text-center">
                        Form Input Unit Rumah Sakit Muhammadiyah
                    </h1>
                </div>

                <!-- Grid 2 Kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Kiri: Form -->
                    <div class="bg-gray-50 shadow-xl rounded-xl p-5">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                            Input Unit
                        </h2>
                        <br>
                        <form action="aksiunit.php" method="POST" class="space-y-4">

                            <div class="mb-4">
                                <label for="nama_kegiatan" class="block text-base font-semibold text-gray-800 mb-2">
                                    Nama Unit
                                </label>
                                <input
                                    type="text"
                                    name="nama_unit"
                                    id="nama_unit"
                                    placeholder="Masukkan Nama Unit"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-base 
                                           shadow-md focus:border-green-500 focus:ring-2 focus:ring-green-400 focus:shadow-lg transition"
                                    required>
                            </div>

                            <div class="text-center">
                                <button type="submit" name="simpan"
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                                    Simpan
                                </button>
                            </div>

                            <p> <strong>Catatan</strong> :</p>
                            <p>
                                Masukkan nama unit dengan benar untuk memastikan semua aktivitas tercatat dengan tepat dan dapat dilaporkan
                                sebelum menekan tombol <span class="font-semibold">Simpan</span>.
                            </p>
                        </form>
                    </div>

                    <!-- Kanan: Keterangan -->

                    <!-- Kanan: Gambar -->
                    <div class="flex justify-center items-center">
                        <img src="img/gambar 3.jpg" alt="Ilustrasi Kegiatan" class="w-100">
                    </div>


                </div>
            </div>



            <!-- footer -->
            <footer class="pt-4">
                <div class="w-full px-6 mx-auto">
                    <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
                        <div class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2">
                            <div class="text-sm leading-normal text-center text-slate-500 lg:text-left">
                                © <script>
                                    document.write(new Date().getFullYear() + ",");
                                </script>
                                made with <i class="fa fa-heart"></i> by
                                <a href="https://www.creative-tim.com" class="font-semibold text-slate-700" target="_blank">RSM Tuban</a>
                                for a better web.
                                <span class="w-full"> Create By ❤️ NENDEN NURAENI </span>
                            </div>
                        </div>
                        <div class="w-full max-w-full px-3 mt-0 shrink-0 lg:w-1/2">
                            <ul class="flex flex-wrap justify-center pl-0 mb-0 list-none lg:justify-end">
                                <li><a href="#!" class="block px-4 pt-0 pb-1 text-sm font-normal text-slate-500">Rumah Sakit Muhammadiyah Tuban</a></li>
                                <li><a href="#!" class="block px-4 pt-0 pb-1 text-sm font-normal text-slate-500">BPJS Kesehatan</a></li>
                                <li><a href="#!" class="block px-4 pt-0 pb-1 text-sm font-normal text-slate-500">Satu Sehat</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
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
