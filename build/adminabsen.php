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
$total_absen = ($result_total && $row = $result_total->fetch_assoc()) ? $row['total_absen'] : 0;

// 🔹 Hitung total karyawan Aktif
$sql_total_karyawan = "SELECT COUNT(*) AS total_karyawan FROM karyawan WHERE status = 'Aktif'";
$result_karyawan = $conn->query($sql_total_karyawan);
$total_karyawan = ($result_karyawan && $row = $result_karyawan->fetch_assoc()) ? (int) $row['total_karyawan'] : 0;

// 🔹 Total Kegiatan
$sql_total_kegiatan = "SELECT COUNT(*) as total_kegiatan FROM kegiatan WHERE status ='Aktif'";
$result_kegiatan = $conn->query($sql_total_kegiatan);
$total_kegiatan = ($result_kegiatan && $row = $result_kegiatan->fetch_assoc()) ? (int) $row['total_kegiatan'] : 0;

// 🔹 Ambil filter dari URL
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$id_kegiatan = isset($_GET['id_kegiatan']) ? $_GET['id_kegiatan'] : '';
$nama_karyawan = isset($_GET['nama_karyawan']) ? trim($_GET['nama_karyawan']) : '';

// FILTER UNTUK UNIT 
$id_unit = isset($_GET['id_unit']) ? $_GET['id_unit'] : '';


// 🔹 Query utama rekap absensi
$sql = "SELECT a.id_absensi, k.nama_karyawan, u.nama_unit, 
       g.nama_kegiatan, a.tanggal, a.jam, a.keterangan, a.foto
       FROM absensi a
       JOIN karyawan k ON a.id_karyawan = k.id_karyawan
       JOIN kegiatan g ON a.id_kegiatan = g.id_kegiatan
       LEFT JOIN unit u ON k.id_unit = u.id_unit
       WHERE k.status = 'Aktif' ";


// 🔹 Tambahkan filter tanggal jika dipilih
if (!empty($_GET['tgl_awal']) && !empty($_GET['tgl_akhir'])) {
  $tgl_awal  = $conn->real_escape_string($_GET['tgl_awal']);
  $tgl_akhir = $conn->real_escape_string($_GET['tgl_akhir']);
  $sql .= " AND a.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'";
} elseif (!empty($_GET['tgl_awal'])) {
  $tgl_awal = $conn->real_escape_string($_GET['tgl_awal']);
  $sql .= " AND a.tanggal = '$tgl_awal'";
} else {
  $sql .= " AND a.tanggal = '$tanggal'";
}


// 🔹 Filter kegiatan
if (!empty($id_kegiatan)) {
  $sql .= " AND a.id_kegiatan = '$id_kegiatan'";
}

// Filter berdasarkan nama karyawan
if (!empty($nama_karyawan)) {
  $nama_karyawan_safe = $conn->real_escape_string($nama_karyawan);
  $sql .= " AND k.nama_karyawan LIKE '%$nama_karyawan_safe%'";
}

// FILTER UNTUK UNIT 
if (!empty($id_unit)) {
  $sql .= " AND k.id_unit = '$id_unit'";
}


// 🔹 Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'a.tanggal';
$order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Validasi kolom yang boleh diurutkan
$valid_columns = ['k.nama_karyawan', 'g.nama_kegiatan', 'a.tanggal', 'a.jam'];
if (!in_array($sort, $valid_columns)) {
  $sort = 'a.tanggal';
}

// Validasi order
$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

// Default tampilkan yang terakhir masuk (tanggal terbaru + jam terbaru)
if (!isset($_GET['sort'])) {
  $sql .= " ORDER BY a.tanggal DESC, a.jam DESC";
} else {
  $sql .= " ORDER BY $sort $order";
}


// 🔹 Ambil daftar kegiatan untuk filter dropdown
$kegiatanList = $conn->query("SELECT * FROM kegiatan ORDER BY nama_kegiatan ASC");
//🔹 Ambil daftar UNIT untuk filter dropdown 
$unitList = $conn->query("SELECT * FROM unit ORDER BY nama_unit ASC");



// 🔹 Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data (hasil filter)
$total_result = $conn->query($sql);
$total_rows = $total_result->num_rows;
$total_pages = ceil($total_rows / $limit);

// Query data dengan LIMIT
$sql_paginated = $sql . " LIMIT $start, $limit";
$result_paginated = $conn->query($sql_paginated);

// 🔹 Hitung total semua data absensi (tanpa filter)
$sql_all = "SELECT COUNT(*) as total_all 
            FROM absensi a
            JOIN karyawan k ON a.id_karyawan = k.id_karyawan
            WHERE k.status='Aktif'";
$res_all = $conn->query($sql_all);
$total_all = ($res_all && $row = $res_all->fetch_assoc()) ? $row['total_all'] : 0;

// 🔹 Biar queryString tersedia untuk sorting & pagination
$queryString = "bulan=$bulan&tahun=$tahun&id_kegiatan=$id_kegiatan&id_unit=$id_unit&nama_karyawan=" . urlencode($nama_karyawan);


if (!empty($_GET['tgl_awal'])) {
  $queryString .= "&tgl_awal=" . urlencode($_GET['tgl_awal']);
}
if (!empty($_GET['tgl_akhir'])) {
  $queryString .= "&tgl_akhir=" . urlencode($_GET['tgl_akhir']);
}



?>

<!DOCTYPE html>
<html>
<?php include 'komponen/header.php'; ?>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
  <div id="sidenavWrapper">
    <?php include 'komponen/asidenav.php'; ?>
  </div>

  <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
    <?php include 'komponen/navbar.php'; ?>

    <div class="w-full px-6 py-6 mx-auto">
      <div class="flex flex-wrap -mx-3">
        <?php include 'komponen/card.php'; ?>

        <div class="w-full px-6 py-6 mx-auto">
          <div class="w-full px-3 mb-6">
            <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl p-6">
              <br>
              <div class="flex items-center justify-center mb-4 relative">
                <img src="assets/img/Logors.png" alt="Logo RS" class="h-12 w-12 absolute left-0">
                <h1 class="text-2xl font-bold text-gray-800 text-center">
                  Rekap Absen Keagamaan Rumah Sakit Muhammadiyah Tuban
                </h1>
              </div>
              <br>

              <!-- Form Filter Rekap -->

              <form method="GET" class="flex flex-wrap items-center gap-2 mb-5">
                <label class="whitespace-nowrap">Dari:</label>
                <input type="date" name="tgl_awal"
                  value="<?= isset($_GET['tgl_awal']) && $_GET['tgl_awal'] !== '' ? $_GET['tgl_awal'] : date('Y-m-d') ?>"
                  class="w-28 border border-gray-300 rounded-lg px-1 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">

                <label class="whitespace-nowrap">Sampai:</label>
                <input type="date" name="tgl_akhir"
                  value="<?= isset($_GET['tgl_akhir']) && $_GET['tgl_akhir'] !== '' ? $_GET['tgl_akhir'] : date('Y-m-d') ?>"
                  class="w-28 border border-gray-300 rounded-lg px-1 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">


                <select name="id_kegiatan" class="w-33 border border-gray-300 rounded-lg px-1 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
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


                 <select name="id_unit" class="w-30 border border-gray-300 rounded-lg px-1 py-1 text-sm">
                    <option value="">- Semua Unit -</option>
                    <?php while ($u = $unitList->fetch_assoc()): ?>
                        <option value="<?= $u['id_unit']; ?>" 
                            <?= ($id_unit == $u['id_unit']) ? 'selected' : '' ?>>
                            <?= $u['nama_unit']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                    
                  
                <input type="text" name="nama_karyawan" placeholder="Cari nama karyawan..."
                  class="w-30 border border-gray-300 rounded-lg px-1 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                  value="<?php echo htmlspecialchars($nama_karyawan); ?>">


                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-1 py-1  rounded">
                  Filter
                </button>

                <a href="export_exel.php?<?php echo $queryString; ?>"
                  class="bg-green-500 hover:bg-green-600 text-white px-1 py-1 rounded">
                  Download
                </a>
              </form>


              <!-- Tabel Rekap Absensi -->
              <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg text-sm text-center">
                  <thead class="bg-blue-100 text-gray-700">
                    <tr>
                      <th class="border px-3 py-2">No</th>

                      <!-- 🔹 Sort Nama Karyawan -->
                      <th class="border px-3 py-2">
                        <a href="?<?php echo $queryString; ?>&sort=k.nama_karyawan&order=<?php echo ($sort == 'k.nama_karyawan' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                          Nama Karyawan
                          <?php if ($sort == 'k.nama_karyawan'): ?>
                            <?php echo ($order == 'ASC') ? '▲' : '▼'; ?>
                          <?php endif; ?>
                        </a>
                      </th>


                      <th class="border px-3 py-2">Unit</th>

                      <!-- 🔹 Sort Kegiatan -->
                      <th class="border px-3 py-2">
                        <a href="?<?php echo $queryString; ?>&sort=g.nama_kegiatan&order=<?php echo ($sort == 'g.nama_kegiatan' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                          Kegiatan
                          <?php if ($sort == 'g.nama_kegiatan'): ?>
                            <?php echo ($order == 'ASC') ? '▲' : '▼'; ?>
                          <?php endif; ?>
                        </a>
                      </th>


                      <!-- 🔹 Sort Tanggal -->
                      <th class="border px-3 py-2">
                        <a href="?<?php echo $queryString; ?>&sort=a.tanggal&order=<?php echo ($sort == 'a.tanggal' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>">
                          Tanggal
                          <?php if ($sort == 'a.tanggal'): ?>
                            <?php echo ($order == 'ASC') ? '▲' : '▼'; ?>
                          <?php endif; ?>
                        </a>
                      </th>

                      <th class="border px-3 py-2">Jam</th>
                      <th class="border px-3 py-2">Keterangan</th>
                      <th class="border px-3 py-2">Foto</th>
                      <th class="border px-3 py-2">Aksi</th>
                    </tr>
                  </thead>


                  <tbody class="text-center">
                    <?php
                    if ($result_paginated && $result_paginated->num_rows > 0) {
                      $no = $start + 1;
                      while ($row = $result_paginated->fetch_assoc()) {
                        echo "<tr class='hover:bg-gray-50'>
                                <td class='border px-3 py-2'>{$no}</td>
                                <td class='border px-3 py-2'>" . htmlspecialchars($row['nama_karyawan']) . "</td>
                                <td class='border px-3 py-2'>" . htmlspecialchars($row['nama_unit']) . "</td>

                                <td class='border px-3 py-2'>" . htmlspecialchars($row['nama_kegiatan']) . "</td>
                                <td class='border px-3 py-2'>{$row['tanggal']}</td>
                                <td class='border px-3 py-2'>" . date("H:i:s", strtotime($row['jam'])) . "</td>
                                <td class='border px-3 py-2'>" . htmlspecialchars($row['keterangan']) . "</td>
                                <td class='border px-3 py-2'>";
                        if (!empty($row['foto'])) {
                          echo "<img src='data:image/jpeg;base64," . base64_encode($row['foto']) . "' 
                                    class='w-12 h-12 object-cover rounded-lg mx-auto cursor-pointer hover:scale-110 transition-transform duration-200'
                                    onclick='showImageModal(this.src)'>";
                        } else {
                          echo "-";
                        }
                        echo "</td>
                              <td class='border px-3 py-2'>
                                <a href='hapusabsen.php?id={$row['id_absensi']}'
                                   class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow'>Hapus</a>
                              </td>
                            </tr>";
                        $no++;
                      }
                    } else {
                      echo "<tr><td colspan='8' class='text-center py-4 text-gray-500'>Tidak ada data absensi</td></tr>";
                    }
                    ?>
                  </tbody>

                </table>

                <!-- 🔹 Info jumlah data hasil filter -->
                <p class="text-gray-600 text-sm mb-3">
                  Menampilkan <b><?php echo $total_rows; ?></b> data hasil pencarian dari total <b><?php echo $total_all; ?></b> data absensi.
                </p>
              </div>


             <!-- Pagination Compact Kanan -->
                <div class="mt-4 flex justify-end gap-2">

                    <!-- Previous -->
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&<?php echo $queryString; ?>"
                           class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Previous</a>
                    <?php endif; ?>

                    <?php
                    // Jumlah halaman awal/akhir yang ditampilkan
                    $visible_pages = 2;

                    // Tampilkan halaman pertama jika jauh dari current page
                    if ($page > 1 + $visible_pages) {
                        echo '<a href="?page=1&' . $queryString . '" class="px-3 py-1 bg-gray-200 rounded">1</a>';
                        echo '<span class="px-2">...</span>';
                    }

                    // Halaman di sekitar current (before & after)
                    for ($i = max(1, $page - 1); $i <= min($total_pages, $page + 1); $i++) {
                        if ($i == $page) {
                            echo '<a class="px-3 py-1 bg-blue-500 text-white rounded">' . $i . '</a>';
                        } else {
                            echo '<a href="?page=' . $i . '&' . $queryString . '" 
                                    class="px-3 py-1 bg-gray-200 rounded">' . $i . '</a>';
                        }
                    }

                    // Tampilkan halaman terakhir jika jauh dari current page
                    if ($page < $total_pages - $visible_pages) {
                        echo '<span class="px-2">...</span>';
                        echo '<a href="?page=' . $total_pages . '&' . $queryString . '" 
                                class="px-3 py-1 bg-gray-200 rounded">' . $total_pages . '</a>';
                    }
                    ?>

                    <!-- Next -->
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&<?php echo $queryString; ?>"
                           class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Next</a>
                    <?php endif; ?>

                </div>


            </div>
          </div>
        </div>
      </div>

      <?php include 'komponen/footer.php'; ?>
    </div>
  </main>



  <!-- Modal Tampilan Foto -->
  <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="relative">
      <!-- Foto Besar -->
      <img id="modalImage" src="" alt="Foto Absensi" class="max-w-full max-h-screen rounded-lg shadow-lg">

      <!-- Tombol Close -->
      <button onclick="closeImageModal()"
        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full shadow">
        ✕
      </button>
    </div>
  </div>

  <script>
    // Fungsi untuk menampilkan modal foto
    function showImageModal(src) {
      document.getElementById('modalImage').src = src;
      document.getElementById('imageModal').classList.remove('hidden');
    }

    // Fungsi untuk menutup modal foto
    function closeImageModal() {
      document.getElementById('imageModal').classList.add('hidden');
    }

    // Tutup modal jika klik area luar foto
    document.getElementById('imageModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeImageModal();
      }
    });
  </script>

</body>

</html>
