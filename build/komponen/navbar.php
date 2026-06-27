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