adminabsen.php

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

// 🔹 Ambil total absen hari ini
$sql_total_absen = "SELECT COUNT(*) as total_absen 
                    FROM absensi 
                    WHERE tanggal = '$tanggal'";
$result_total = $conn->query($sql_total_absen);
$total_absen = 0;
if ($result_total && $row = $result_total->fetch_assoc()) {
    $total_absen = $row['total_absen'];
}

// 🔹 Ambil filter dari URL
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$id_kegiatan = isset($_GET['id_kegiatan']) ? $_GET['id_kegiatan'] : '';
$nama_karyawan = isset($_GET['nama_karyawan']) ? trim($_GET['nama_karyawan']) : '';

// 🔹 Query utama rekap absensi
$sql = "SELECT a.id_absensi, k.nama_karyawan, g.nama_kegiatan, a.tanggal, a.jam, a.keterangan, a.foto
        FROM absensi a
        JOIN karyawan k ON a.id_karyawan = k.id_karyawan
        JOIN kegiatan g ON a.id_kegiatan = g.id_kegiatan
        WHERE MONTH(a.tanggal) = '$bulan' AND YEAR(a.tanggal) = '$tahun'";

// Filter berdasarkan kegiatan jika dipilih
if (!empty($id_kegiatan)) {
    $sql .= " AND a.id_kegiatan = '$id_kegiatan'";
}

// Filter berdasarkan nama karyawan
if (!empty($nama_karyawan)) {
    $nama_karyawan_safe = $conn->real_escape_string($nama_karyawan);
    $sql .= " AND k.nama_karyawan LIKE '%$nama_karyawan_safe%'";
}

$sql .= " ORDER BY a.tanggal DESC, a.jam DESC";
$result = $conn->query($sql);

// 🔹 Ambil daftar kegiatan untuk filter dropdown
$kegiatanList = $conn->query("SELECT * FROM kegiatan ORDER BY nama_kegiatan ASC");


//Menampilkan 20 baris di dalam tabel 
// Jumlah data per halaman
$limit = 10;

// Ambil nomor halaman dari URL, default 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$total_result = $conn->query($sql);
$total_rows = $total_result->num_rows;
$total_pages = ceil($total_rows / $limit);

// Query data dengan LIMIT
$sql_paginated = $sql . " LIMIT $start, $limit";
$result_paginated = $conn->query($sql_paginated);
?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        rel="apple-touch-icon"
        sizes="76x76"
        href="./assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="./assets/img/favicon.png" />
    <title>Admin Absen</title>
    <!--     Fonts and icons     -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
        rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script
        src="https://kit.fontawesome.com/42d5adcbca.js"
        crossorigin="anonymous"></script>
    <!-- Nucleo Icons -->
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Popper -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <!-- Main Styling -->
    <link
        href="./assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5"
        rel="stylesheet" />
    <!-- Nepcha Analytics (nepcha.com) -->
    <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
    <script
        defer
        data-site="YOUR_DOMAIN_HERE"
        src="https://api.nepcha.com/js/nepcha-analytics.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body
    class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    <!-- sidenav  -->
    <?php
    include 'komponen/asidenav.php';
    ?>

    <!-- end sidenav -->

    <main
        class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
        <!-- Navbar -->
        <nav class="flex items-center justify-between px-6 py-3 bg-white shadow-md rounded-2xl mx-6 mt-4">
            <!-- Bagian Kiri: Judul & Breadcrumb -->
            <div class="flex flex-col">
                <h2 class="text-lg font-semibold text-gray-800">
                    Selamat Datang, <?php echo $_SESSION['username']; ?>!
                </h2>
                <p class="text-sm text-gray-600">Anda berhasil login sebagai admin.</p>

                <!-- Breadcrumb -->
                <ol class="flex items-center space-x-2 text-sm mt-1">
                    <li>
                        <a href="tampilanabsen.php" class="text-blue-600 hover:underline">Absen</a>
                    </li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700 font-medium">Rekap Absensi</li>
                </ol>
            </div>

            <!-- Bagian Kanan: Tombol Logout -->
            <div>
                <a href="logout.php"
                    class="px-4 py-2 text-white bg-red-500 rounded-lg shadow hover:bg-red-600 transition duration-200 ease-in-out">
                    Logout
                </a>
            </div>
        </nav>


        <!-- end Navbar -->

        <!-- cards -->
        <div class="w-full px-6 py-6 mx-auto">
            <!-- row 1 -->
            <div class="flex flex-wrap -mx-3">
                <!-- card1 -->
                <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="flex-auto p-4">
                            <div class="flex flex-row -mx-3">
                                <div class="flex-none w-2/3 max-w-full px-3">
                                    <div>
                                        <p class="mb-0 font-sans text-sm font-semibold leading-normal">
                                            Tanggal Hari Ini
                                        </p>

                                        <h5 class="mb-0 font-bold">
                                            <?php echo date("d M Y"); ?>
                                        </h5>
                                    </div>
                                </div>
                                <div class="px-3 text-right basis-1/3">
                                    <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500">
                                        <i class="ni leading-none ni-calendar-grid-58 text-lg relative top-3.5 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- card2 -->
                <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
                    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="flex-auto p-4">
                            <div class="flex flex-row -mx-3">
                                <div class="flex-none w-2/3 max-w-full px-3">
                                    <div>
                                        <p class="mb-0 font-sans text-sm font-semibold leading-normal">
                                            Total Absen Hari Ini
                                        </p>
                                        <h5 class="mb-0 font-bold">
                                            <?php echo $total_absen; ?> Presensi
                                        </h5>
                                    </div>
                                </div>
                                <div class="px-3 text-right basis-1/3">
                                    <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-green-600 to-teal-400">
                                        <i class="ni leading-none ni-check-bold text-lg relative top-3.5 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- cards row 1 -->
                <div class="w-full px-6 py-6 mx-auto">
                    <div class="w-full px-3 mb-6">
                        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl p-6">
                            <br>
                            <!-- Header Judul + Logo -->
                            <div class="flex items-center justify-center mb-4 relative">
                                <!-- Logo RS (posisi kiri) -->
                                <img src="assets/img/Logors.png" alt="Logo RS" class="h-20 w-20 absolute left-0">

                                <!-- Judul (posisi tengah) -->
                                <h1 class="text-2xl font-bold text-gray-800 text-center">
                                    Rekap Absen Keagamaan Rumah Sakit Muhammadiyah Tuban
                                </h1>
                            </div>
                            <br>

                            <!-- Form Filter Rekap -->
                            <form method="GET" class=" items-center mb-5">
                                <select name="bulan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $selected = ($i == $bulan) ? 'selected' : '';
                                        echo "<option value='$i' $selected>" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
                                    }
                                    ?>
                                </select>

                                <select name="tahun" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <?php
                                    $yearNow = date("Y");
                                    // Loop dari 3 tahun ke belakang sampai 5 tahun ke depan
                                    for ($y = $yearNow - 3; $y <= $yearNow + 5; $y++) {
                                        $selected = (isset($tahun) && $y == $tahun) ? 'selected' : '';
                                        echo "<option value='$y' $selected>$y</option>";
                                    }
                                    ?>
                                </select>


                                <select name="id_kegiatan" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="">-- Semua Kegiatan --</option>
                                    <?php
                                    if ($kegiatanList && $kegiatanList->num_rows > 0) {
                                        while ($kg = $kegiatanList->fetch_assoc()) {
                                            $selected = ($id_kegiatan == $kg['id_kegiatan']) ? 'selected' : '';
                                            echo "<option value='" . $kg['id_kegiatan'] . "' $selected>" . $kg['nama_kegiatan'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>

                                <input type="text" name="nama_karyawan" placeholder="Cari nama karyawan..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars($nama_karyawan); ?>">

                                <!-- Tombol Filter -->
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                    Filter
                                </button>

                                <!-- Tombol Download Data -->
                                <a href="export_exel.php?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&id_kegiatan=<?php echo $id_kegiatan; ?>&nama_karyawan=<?php echo urlencode($nama_karyawan); ?> "
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                    Download Data
                                </a>


                                <!-- Tabel Rekap Absensi -->
                                <div class="overflow-x-auto mt-4">
                                    <table class="w-full border border-gray-200 rounded-lg text-sm text-center">
                                        <thead class="bg-blue-100 text-gray-700">
                                            <tr>
                                                <th class="px-4 py-2 border">No</th>
                                                <th class="px-4 py-2 border">Nama Karyawan</th>
                                                <th class="px-4 py-2 border">Kegiatan</th>
                                                <th class="px-4 py-2 border">Tanggal</th>
                                                <th class="px-4 py-2 border">Jam</th>
                                                <th class="px-4 py-2 border">Keterangan</th>
                                                <th class="px-4 py-2 border">Foto</th>
                                                <th class="px-4 py-2 border">Aksi</th> <!-- Tambahkan kolom aksi -->
                                            </tr>
                                        </thead>

                                        <tbody class="text-center">
                                            <?php
                                            if ($result_paginated && $result_paginated->num_rows > 0) {
                                                $no = $start + 1;
                                                while ($row = $result_paginated->fetch_assoc()) {
                                            ?>
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="border px-3 py-2"><?php echo $no++; ?></td>
                                                        <td class="border px-3 py-2"><?php echo htmlspecialchars($row['nama_karyawan']); ?></td>
                                                        <td class="border px-3 py-2"><?php echo htmlspecialchars($row['nama_kegiatan']); ?></td>
                                                        <td class="border px-3 py-2"><?php echo $row['tanggal']; ?></td>
                                                        <td class="border px-3 py-2"><?php echo date("H:i:s", strtotime($row['jam'])); ?></td>
                                                        <td class="border px-3 py-2"><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                                        <td class="border px-3 py-2">
                                                            <?php if (!empty($row['foto'])): ?>
                                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>" alt="Foto Absen" class="w-12 h-12 object-cover rounded-lg mx-auto">
                                                            <?php else: ?>
                                                                -
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="border px-3 py-2">
                                                            <button type="button" onclick="hapusData(<?php echo $row['id_absensi']; ?>)"
                                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow">
                                                                Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                            <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='8' class='text-center py-4 text-gray-500'>Tidak ada data absensi</td></tr>";
                                            }
                                            ?>
                                        </tbody>


                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4 flex justify-center gap-2">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Previous</a>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>" class="px-3 py-1 <?php echo ($i == $page) ? 'bg-blue-500 text-white rounded' : 'bg-gray-200 rounded'; ?>"><?php echo $i; ?></a>
                                    <?php endfor; ?>

                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="pt-4">
                <div class="w-full px-6 mx-auto">
                    <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
                        <div
                            class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
                            <div
                                class="text-sm leading-normal text-center text-slate-500 lg:text-left">
                                ©
                                <script>
                                    document.write(new Date().getFullYear() + ",");
                                </script>
                                made with <i class="fa fa-heart"></i> by
                                <a
                                    href="https://www.creative-tim.com"
                                    class="font-semibold text-slate-700"
                                    target="_blank">RSM Tuban</a>

                                for a better web.
                                <span class="w-full"> Create By ❤️ NENDEN NURAENI </span>
                            </div>
                        </div>
                        <div
                            class="w-full max-w-full px-3 mt-0 shrink-0 lg:w-1/2 lg:flex-none">
                            <ul
                                class="flex flex-wrap justify-center pl-0 mb-0 list-none lg:justify-end">
                                <li class="nav-item">
                                    <a
                                        href="#!"
                                        class="block px-4 pt-0 pb-1 text-sm font-normal transition-colors ease-soft-in-out text-slate-500">Rumah Sakit Muhammadiyah Tuban</a>
                                </li>
                                <li class="nav-item">
                                    <a
                                        href="#!"
                                        class="block px-4 pt-0 pb-1 text-sm font-normal transition-colors ease-soft-in-out text-slate-500">BPJS Kesehatan</a>
                                </li>
                                <li class="nav-item">
                                    <a
                                        href="#!"
                                        class="block px-4 pt-0 pb-1 text-sm font-normal transition-colors ease-soft-in-out text-slate-500">Satu Sehat</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end cards -->
    </main>

    <!-- Hapus Data Absen -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusData(id) {
            Swal.fire({
                title: 'Yakin Hapus Data?',
                text: "Data absensi akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('hapusabsen.php?id=' + id)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data absensi berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error!', 'Terjadi kesalahan koneksi.', 'error');
                        });
                }
            });
        }
    </script>






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




sidenav

<aside
    class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 ml-4 block w-full -translate-x-full flex-wrap items-center justify-between rounded-2xl border-0 bg-white p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0 xl:bg-transparent"
    style="height:100vh; overflow:hidden;">

    <div class="h-19.5">
        <i
            class="absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer fas fa-times text-slate-400 xl:hidden"
            sidenav-close></i>
        <a
            class="block px-8 py-6 m-0 text-sm whitespace-nowrap text-slate-700"
            href="javascript:;"
            target="_blank">
            <img
                src="./assets/img/Logors.png"
                class="inline h-full max-w-full transition-all duration-200 ease-nav-brand max-h-8"
                alt="main_logo" />
            <span
                class="ml-1 font-semibold transition-all duration-200 ease-nav-brand">Admin Absen</span>
        </a>
    </div>

    <hr
        class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />

    <div class="items-center block w-auto h-sidenav grow basis-full">

        <ul class="space-y-2">

            <!-- Rekap Absen -->
            <li>
                <a href="adminabsen.php"
                    class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                        <!-- Calendar Check Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14M9 15l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="ml-3 font-medium text-gray-700">Rekap Absen</span>
                </a>
            </li>

            <!-- Absen -->
            <li>
                <a href="tampilanabsen.php"
                    class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                        <!-- Clipboard Check Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m1-4H7a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V8z" />
                        </svg>
                    </div>
                    <span class="ml-3 font-medium text-gray-700">Absen</span>
                </a>
            </li>

            <!-- Lihat Data -->
            <li>
                <div class="flex items-center p-3 rounded-lg bg-gray-50">
                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                        <!-- Eye Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <span class="ml-3 font-medium text-gray-700">Lihat Data</span>
                </div>
                <ul class="ml-12 mt-1 space-y-1">
                    <li>
                        <a href="kegiatan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                            <div class="h-6 w-6 flex items-center justify-center rounded-md bg-gray-200 text-gray-600">
                                📂
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Data Kegiatan</span>
                        </a>
                    </li>
                    <li>
                        <a href="karyawan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                            <div class="h-6 w-6 flex items-center justify-center rounded-md bg-gray-200 text-gray-600">
                                👥
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Data Karyawan</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Input Data -->
            <li>
                <div class="flex items-center p-3 rounded-lg bg-gray-50">
                    <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                        <!-- Plus Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="ml-3 font-medium text-gray-700">Input Data</span>
                </div>
                <ul class="ml-12 mt-1 space-y-1">
                    <li>
                        <a href="inputkegiatan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                            <div class="h-6 w-6 flex items-center justify-center rounded-md bg-gray-200 text-gray-600">
                                📝
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Input Kegiatan</span>
                        </a>
                    </li>
                    <li>
                        <a href="inputkaryawan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100">
                            <div class="h-6 w-6 flex items-center justify-center rounded-md bg-gray-200 text-gray-600">
                                ➕
                            </div>
                            <span class="ml-2 text-sm text-gray-600">Input Karyawan</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>

    </div>
</aside>