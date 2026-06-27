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

// 🔹 Total Karyawan
$sql_total_karyawan = "SELECT COUNT(*) as total_karyawan FROM karyawan";
$result_karyawan = $conn->query($sql_total_karyawan);
$total_karyawan = 0;
if ($result_karyawan && $row = $result_karyawan->fetch_assoc()) {
    $total_karyawan = $row['total_karyawan'];
}

// 🔹 Total Kegiatan
$sql_total_kegiatan = "SELECT COUNT(*) as total_kegiatan FROM kegiatan";
$result_kegiatan = $conn->query($sql_total_kegiatan);
$total_kegiatan = 0;
if ($result_kegiatan && $row = $result_kegiatan->fetch_assoc()) {
    $total_kegiatan = $row['total_kegiatan'];
}

// === Pagination + Pencarian ===
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Kata kunci pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : "";


// ==== SORTING ====
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'nomor_karyawan';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validasi nama kolom yang boleh diurutkan
$valid_columns = ['nomor_karyawan', 'nama_karyawan'];
if (!in_array($sort, $valid_columns)) {
    $sort = 'nomor_karyawan';
}

// Validasi arah urutan ASC/DESC
$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';


// Query data sesuai pencarian
$sql = "SELECT karyawan.*, unit.nama_unit
        FROM karyawan
        LEFT JOIN unit ON karyawan.id_unit = unit.id_unit
        WHERE karyawan.nomor_karyawan LIKE '%$search%' 
        OR karyawan.nama_karyawan LIKE '%$search%'
        ORDER BY $sort $order
        LIMIT $start, $limit";



$result_paginated = $conn->query($sql);

// Hitung total data sesuai filter (untuk pagination)
$sql_count = "SELECT COUNT(*) as total FROM karyawan 
              WHERE nomor_karyawan LIKE '%$search%' 
              OR nama_karyawan LIKE '%$search%'";
$count_result = $conn->query($sql_count)->fetch_assoc();
$total_filtered = $count_result['total'];   // total data sesuai pencarian
$total_pages = ceil($total_filtered / $limit);

// === Total semua karyawan (untuk card) ===
$sql_total_all = "SELECT COUNT(*) AS total FROM karyawan";
$total_all_result = $conn->query($sql_total_all)->fetch_assoc();
$total_karyawan_all = $total_all_result['total'];

?>


<!DOCTYPE html>
<html>

<?php include 'komponen/header.php'; ?>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    <!-- sidenav -->
    <?php include 'komponen/asidenav.php'; ?>

    <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
        <!-- Navbar -->
        <?php include 'komponen/navbar.php'; ?>
        <!-- end Navbar -->

        <!-- cards -->
        <div class="w-full px-6 py-6 mx-auto">
            <!-- row 1: Tanggal, Total Absen, Total Karyawan, Total Kegiatan -->
            <div class="flex flex-wrap -mx-3">
                <?php include 'komponen/card.php'; ?>

                <!-- cards row 2 -->
                <div class="w-full px-6 py-6 mx-auto">
                    <div class="w-full px-3 mb-6">
                        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl p-6">
                            <br>
                            <!-- Header Judul + Logo -->
                            <div class="flex items-center justify-center mb-2 relative">
                                <img src="assets/img/Logors.png" alt="Logo RS" class="h-12 w-12 absolute left-0">
                                <h1 class="text-2xl font-bold text-gray-800 text-center">
                                    Rekap Data Karyawan Rumah Sakit Muhammadiyah
                                </h1>
                            </div>
                          

                            <!-- Form Filter Rekap -->
                            <form method="GET" class="items-center mb-5">
                                  <br>
                                <!-- Botton Cari dan Download -->
                                <div class="flex justify-end mb-3 gap-2">
                                    <a href="inputkaryawan.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                        Tambah Data
                                    </a>
                                    <a href="export_excel_karyawan.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                        Download Data
                                    </a>

                                    <!-- Input Cari -->
                                    <div class="flex gap-2">
                                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                                            placeholder="Cari Nomor / Nama Karyawan..." class="border rounded px-3 py-2 w-64">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                                            Cari
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabel Daftar Karyawan -->
                                <div class="overflow-x-auto mt-4">

                                <table class="w-full border border-gray-200 rounded-lg text-sm text-center">
                                    <thead class="bg-blue-100 text-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 border w-5">No</th>
                                            <th class="px-4 py-2 border w-5">
                                                Nomor Karyawan
                                            </th>
                                            <th class="px-4 py-2 border w-10">
                                                Nama Karyawan
                                            </th>
                                            <th class="px-4 py-2 border w-10">Unit</th>
                                            <th class="px-4 py-2 border w-5">Status</th>
                                            <th class="px-2 py-2 border w-5">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        if ($result_paginated && $result_paginated->num_rows > 0) {
                                            $no = $start + 1;
                                            while ($row = $result_paginated->fetch_assoc()) {

                                                $id_karyawan = $row['id_karyawan'];
                                                $nomor = htmlspecialchars($row['nomor_karyawan']);
                                                $nama = htmlspecialchars($row['nama_karyawan']);
                                                $status = $row['status'];
                                                $unit = $row['nama_unit'] ?: '-';

                                                echo "<tr class='hover:bg-gray-50'>
                                                    <td class='border px-3 py-2'>" . $no++ . "</td>
                                                    <td class='border px-3 py-2'>$nomor</td>
                                                    <td class='border px-3 py-2'>$nama</td>
                                                    <td class='border px-3 py-2'>$unit</td>
                                                    <td class='border px-3 py-2'>$status</td>

                                                    <td class='border px-3 py-2'>
                                                        <div class='flex justify-center space-x-2'>
                                                            <a href='updatekaryawan.php?id=$id_karyawan' 
                                                                class='bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded'>
                                                                Update
                                                            </a>

                                                            <a href='hapuskaryawan.php?id=$id_karyawan' 
                                                                class='bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded'>
                                                                Delete
                                                            </a>
                                                            
                                                            <a href='ubahstatuskaryawan.php?id=$id_karyawan'  
                                                                class='" . ($status == "Aktif"
                                                                ? "bg-yellow-500 hover:bg-yellow-600"
                                                                : "bg-green-500 hover:bg-green-600") . " text-white px-4 py-1 rounded'>
                                                                " . ($status == "Aktif" ? "Nonaktifkan" : "Aktifkan") . "
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center py-4 text-gray-500'>Tidak ada data karyawan</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                    </div>
                                    
                                     
                                    <!-- Info jumlah data -->
                                    <div class="mb-3 text-gray-700 text-sm">
                                        <?php
                                        if (!empty($search)) {
                                            if ($total_filtered > 0) {
                                                echo "Menampilkan <span class='font-bold'>$total_filtered</span> data hasil pencarian 
                                                dari total <span class='font-bold'>$total_karyawan_all</span> data karyawan.";
                                            } else {
                                                echo "Tidak ada hasil pencarian dari total <span class='font-bold'>$total_karyawan_all</span> data karyawan.";
                                            }
                                        } else {
                                            echo "Menampilkan <span class='font-bold'>$total_karyawan_all</span> data 
                                            dari total <span class='font-bold'>$total_karyawan_all</span> data karyawan.";
                                        }
                                        ?>
                                    </div>




                                    <!-- Pagination -->
                                 
                               <div class="mt-4 flex justify-end gap-2">

                                    <!-- Tombol Previous -->
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"
                                            class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Previous</a>
                                    <?php endif; ?>

                                    <?php
                                    // ==== Pagination Compact ====
                                    $visible_pages = 3; // jumlah halaman awal & akhir yang ingin ditampilkan

                                    // Halaman pertama
                                    if ($page > 1 + $visible_pages) {
                                        echo '<a href="?page=1&search=' . urlencode($search) . '" class="px-3 py-1 bg-gray-200 rounded">1</a>';
                                        echo '<span class="px-2">...</span>';
                                    }

                                    // Halaman tengah (di sekitar current page)
                                    for ($i = max(1, $page - 1); $i <= min($total_pages, $page + 1); $i++) {
                                        if ($i == $page) {
                                            echo '<a class="px-3 py-1 bg-blue-500 text-white rounded">' . $i . '</a>';
                                        } else {
                                            echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '" 
                                                    class="px-3 py-1 bg-gray-200 rounded">' . $i . '</a>';
                                        }
                                    }

                                    // Halaman terakhir
                                    if ($page < $total_pages - $visible_pages) {
                                        echo '<span class="px-2">...</span>';
                                        echo '<a href="?page=' . $total_pages . '&search=' . urlencode($search) . '" 
                                                class="px-3 py-1 bg-gray-200 rounded">' . $total_pages . '</a>';
                                    }
                                    ?>

                                    <!-- Tombol Next -->
                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"
                                            class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</a>
                                    <?php endif; ?>

                                </div>

</div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- footer -->
            <?php include 'komponen/footer.php'; ?>

        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const headers = document.querySelectorAll(".sort-header");

            headers.forEach(header => {
                header.addEventListener("click", function() {
                    const table = header.closest("table");
                    const tbody = table.querySelector("tbody");
                    const rows = Array.from(tbody.querySelectorAll("tr"));

                    const column = parseInt(header.dataset.column);
                    let order = header.dataset.order;

                    // Urutkan data
                    rows.sort((a, b) => {
                        const valA = a.children[column].innerText.trim().toLowerCase();
                        const valB = b.children[column].innerText.trim().toLowerCase();

                        if (!isNaN(valA) && !isNaN(valB)) {
                            return order === "asc" ? valA - valB : valB - valA;
                        } else {
                            return order === "asc" ? valA.localeCompare(valB) : valB.localeCompare(valA);
                        }
                    });

                    // Masukkan kembali urutan baru ke tabel
                    rows.forEach(row => tbody.appendChild(row));

                    // Toggle urutan
                    header.dataset.order = order === "asc" ? "desc" : "asc";

                    // Ganti ikon ▲▼
                    document.querySelectorAll(".sort-icon").forEach(icon => icon.textContent = "▲▼");
                    header.querySelector(".sort-icon").textContent = order === "asc" ? "▼" : "▲";
                });
            });
        });
    </script>

</body>

<script src="./assets/js/plugins/chartjs.min.js" async></script>
<script src="./assets/js/plugins/perfect-scrollbar.min.js" async></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="./assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5" async></script>

</html>
