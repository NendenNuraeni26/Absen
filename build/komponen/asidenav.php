<aside class="w-64 fixed inset-y-0 left-0 bg-white shadow-lg rounded-r-2xl overflow-y-auto" style="height:100vh;">

    <!-- Header -->
    <div class="flex items-center px-6 py-5 border-b">
        <img src="./assets/img/Logors.png" class="h-8 w-auto" alt="Logo">
        <span class="ml-3 font-semibold text-lg text-gray-700">Admin Absen</span>
    </div>

    <!-- Nav -->
    <nav class="p-4 space-y-2">


        <!-- Rekap Absen -->
        <a href="adminabsen.php"
            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
            <div
                class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                <!-- Chart Bar Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3v18h18M9 17V9m4 8V5m4 12v-6" />
                </svg>
            </div>
            <span class="ml-3 font-medium text-gray-700">Rekap Absen</span>
        </a>


        <!-- Absen -->
        <a href="tampilanabsen.php"
            class="flex items-center p-3 rounded-lg hover:bg-gray-100 transition">
            <div
                class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                <!-- Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m1-4H7a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V8z" />
                </svg>
            </div>
            <span class="ml-3 font-medium text-gray-700">Absen</span>
        </a>


        <!-- Lihat Data -->
        <div>
            <button onclick="toggleMenu('lihatDataMenu')"
                class="flex items-center w-full p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <span class="ml-3 font-semibold text-gray-700 flex-1 text-left">Lihat Data</span>
                <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="lihatDataMenu" class="hidden mt-1 space-y-1">
                <a href="kegiatan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-lg">👥</span>
                    <span class="ml-2 text-sm text-gray-600">Data Kegiatan</span>
                </a>
                <a href="karyawan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-lg">👥</span>
                    <span class="ml-2 text-sm text-gray-600">Data Karyawan</span>
                </a>
                <a href="unit.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-lg">👥</span>
                    <span class="ml-2 text-sm text-gray-600">Data Unit</span>
                </a>
            </div>
        </div>

        <!-- Input Data -->
        <div>
            <button onclick="toggleMenu('inputDataMenu')"
                class="flex items-center w-full p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="ml-3 font-semibold text-gray-700 flex-1 text-left">Input Data</span>
                <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="inputDataMenu" class="hidden mt-1 space-y-1">
                <a href="inputkegiatan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-lg">📝</span>
                    <span class="ml-2 text-sm text-gray-600">Input Kegiatan</span>
                </a>
                <a href="inputkaryawan.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-lg">📝</span>
                    <span class="ml-2 text-sm text-gray-600">Input Karyawan</span>
                </a>
                <a href="inputunit.php" class="flex items-center p-2 rounded-lg hover:bg-gray-100 transition">
                    <span class="text-lg">📝</span>
                    <span class="ml-2 text-sm text-gray-600">Input Unit</span>
                </a>
            </div>
        </div>

    </nav>
</aside>

<script>
    function toggleMenu(id) {
        document.getElementById(id).classList.toggle("hidden");
    }
</script>
