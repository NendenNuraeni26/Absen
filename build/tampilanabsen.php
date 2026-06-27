<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* ================== GLOBAL STYLE ================== */
        body {
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* ================== CARD CONTAINER ================== */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            padding-bottom: 15px;
        }

        /* ================== CARD HEADER ================== */
        .card-header {
            background: linear-gradient(90deg, #4e54c8, #8f94fb);
            border-radius: 20px 20px 0 0;
            color: white;
            padding: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* ================== BUTTON STYLING ================== */
        .btn-gradient {
            background: linear-gradient(90deg, #4e54c8, #8f94fb);
            color: white;
            font-weight: bold;
            border-radius: 12px;
            padding: 12px;
            transition: 0.3s;
            width: 100%;
            border: none;
        }

        .btn-gradient:hover {
            transform: scale(1.03);
            opacity: 0.9;
        }

        /* ================== STATUS BOX ================== */
        .status-box {
            font-weight: bold;
            font-size: 0.95rem;
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            margin-top: 10px;
        }

        .status-valid {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-invalid {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* ================== KAMERA & PREVIEW FOTO ================== */
        #camera,
        #canvas,
        #previewArea img {
            width: 200px;
            height: 200px;
            border-radius: 10px;
            object-fit: cover;
            /* Foto selalu pas */
            border: 2px solid #ddd;
            background-color: #000;
        }

        /* ================== PREVIEW BOX DEFAULT ================== */
        .preview-box {
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px dashed #ccc;
            color: #888;
            font-size: 12px;
            width: 200px;
            height: 200px;
            border-radius: 10px;
            overflow: hidden;
            background-color: #f8f9fa;
            text-align: center;
        }

        /* ================== WRAPPER FOTO ================== */
        .ambil-foto-wrapper {
            display: flex;
            gap: 20px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        /* ================== RESPONSIVE DESIGN ================== */
        @media (max-width: 576px) {

            #camera,
            #canvas,
            #previewArea img,
            .preview-box {
                width: 140px;
                height: 140px;
            }

            .ambil-foto-wrapper {
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex justify-content-center align-items-center p-3" style="min-height: 100vh;">
        <div class="card p-4 w-100 shadow-sm" style="max-width: 600px; height: auto; overflow: hidden;">

            <!-- HEADER LOGO & NAMA RS -->
            <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                <img src="assets/img/Logors.png" alt="Logo RS Muhammadiyah" style="width: 50px; height: 50px; object-fit: contain; margin-right: 10px;">
                <div>
                    <h2 style="font-size: 16px; font-weight: bold; margin: 0; color:#333;">Rumah Sakit Muhammadiyah Tuban</h2>
                    <p style="font-size: 12px; margin: 0; color:#555;">Jl Diponegoro No 1 Kabupaten Tuban</p>
                </div>
            </div>

            <!-- Judul Absensi -->
            <div class="text-center">
                <h4 style="font-size: 20px; font-weight: bold; color:#222;">ABSEN SIRSMA</h4>
            </div>

            <form action="aksiabsen.php" method="POST">
                <!-- Nomor Karyawan -->
                <div class="mb-3">
                    <label class="form-label">Nomor Karyawan</label>
                    <input type="text" name="nomor_karyawan" id="nomor_karyawan" class="form-control"
                        placeholder="Ketik Nomor Karyawan" required>
                    <small id="status_karyawan" class="text-danger"></small>
                    <input type="hidden" name="id_karyawan" id="id_karyawan">
                </div>

                <!-- Nama -->
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" id="nama_karyawan" class="form-control" readonly>
                </div>

                <!-- Kegiatan -->
                <div class="mb-3">
                    <label class="form-label">Kegiatan</label>
                    <select name="id_kegiatan" class="form-select" required>
                        <option value="">-- Pilih Kegiatan --</option>
                        <?php
                        $keg = $conn->query("SELECT id_kegiatan, nama_kegiatan FROM kegiatan WHERE status = 'Aktif'");
                        while ($row = $keg->fetch_assoc()) {
                            echo "<option value='" . $row['id_kegiatan'] . "'>" . $row['nama_kegiatan'] . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Kehadiran -->
                <div class="mb-3">
                    <label class="form-label">Kehadiran</label>
                    <select name="keterangan" class="form-select" required>
                        <option value="Hadir">Hadir</option>
                    </select>
                </div>

                <!-- Lokasi -->
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <div id="lokasi_status" class="status-box bg-light">⏳ Menunggu deteksi lokasi...</div>

                <!-- Ambil Foto -->
                <div class="mb-3 text-center">
                    <label class="form-label d-block" style="font-weight: bold;">Ambil Foto</label>
                    <div class="ambil-foto-wrapper d-flex justify-content-center align-items-center gap-3 flex-nowrap">
                        <div>
                            <video id="camera" autoplay playsinline></video>
                        </div>
                        <div>
                            <canvas id="canvas" style="display: none;"></canvas>
                            <div id="previewArea" class="preview-box">
                                Belum ada foto
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="foto" id="foto">
                    <button type="button" id="takePhoto" class="btn btn-sm btn-primary mt-3 w-100">
                        Ambil Foto
                    </button>
                </div>

                <!-- Tombol Kirim -->
                <button type="submit" class="btn btn-gradient" id="submitBtn" disabled>🚀 Kirim Absensi</button>
            </form>
        </div>
    </div>

    <script>
        const video = document.getElementById('camera');
        const canvas = document.getElementById('canvas');
        const takePhoto = document.getElementById('takePhoto');
        const fotoInput = document.getElementById('foto');
        const previewArea = document.getElementById('previewArea');
        const submitBtn = document.getElementById('submitBtn');

        let lokasiValid = false;
        let karyawanValid = false;

        function updateSubmitStatus() {
            submitBtn.disabled = !(lokasiValid && karyawanValid);
        }

        // Kamera
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Bisa Diakses',
                    html: "Penyebab: " + err +
                        "<br><br>Solusi:<br>1. Gunakan HTTPS.<br>2. Izinkan akses kamera."
                });
                video.style.display = 'none';
                takePhoto.style.display = 'none';
            });

        takePhoto.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = canvas.toDataURL('image/png');
            fotoInput.value = imageData;

            // ✅ Tampilkan preview dengan ukuran fix
            previewArea.innerHTML = `<img src="${imageData}" alt="Foto Anda"/>`;
        });


        document.querySelector("form").addEventListener("submit", function(e) {
            if (fotoInput.value === "") {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Belum Ada',
                    text: 'Silakan ambil foto terlebih dahulu!',
                });
            }
        });

        document.querySelector("form").addEventListener("submit", function(e) {
            if (fotoInput.value === "") {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Belum Ada',
                    text: 'Silakan ambil foto terlebih dahulu!',
                });
            }
        });

        // Lokasi

//        const musholaLat = -6.88763;
//        const musholaLng = 112.05281;
//
        const musholaLat = -6.887051;
        const musholaLng = 112.052658;

        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (pos) => {
                    let lat = pos.coords.latitude;
                    let lng = pos.coords.longitude;

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;

                    let jarak = getDistanceFromLatLonInM(lat, lng, musholaLat, musholaLng);

                    const radiusValid = 80; // radius valid dalam meter

                    if (jarak <= radiusValid) {
                        document.getElementById("lokasi_status").innerHTML =
                            `✅ Lokasi valid (${jarak.toFixed(2)} m dari Mushola)`;
                        document.getElementById("lokasi_status").className = "status-box status-valid";
                        lokasiValid = true;
                    } else {
                        document.getElementById("lokasi_status").innerHTML =
                            `❌ Lokasi di luar area! (${jarak.toFixed(2)} m dari Mushola)`;
                        document.getElementById("lokasi_status").className = "status-box status-invalid";
                        lokasiValid = false;
                    }
                    updateSubmitStatus();

                },
                (err) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lokasi Tidak Terdeteksi',
                        text: 'Pastikan GPS aktif & izinkan akses lokasi!',
                    });
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 5000
                }
            );
        }



        // Cek Karyawan via AJAX
        $("#nomor_karyawan").on("keyup", function() {
            let nomor = $(this).val().trim();
            if (nomor !== "") {
                $.ajax({
                    url: "cek_karyawan.php",
                    type: "POST",
                    data: {
                        nomor_karyawan: nomor
                    },
                    success: function(response) {
                        let data = JSON.parse(response);
                        if (data.status === "ada") {
                            $("#status_karyawan").text("").removeClass("text-danger").addClass("text-success");
                            $("#id_karyawan").val(data.id_karyawan);
                            $("#nama_karyawan").val(data.nama_karyawan);
                            karyawanValid = true;
                        } else {
                            $("#status_karyawan").text("⚠ Nomor karyawan tidak ditemukan!")
                                .removeClass("text-success").addClass("text-danger");
                            $("#id_karyawan").val("");
                            $("#nama_karyawan").val("");
                            karyawanValid = false;
                        }
                        updateSubmitStatus();
                    }
                });
            } else {
                $("#status_karyawan").text("");
                $("#id_karyawan").val("");
                $("#nama_karyawan").val("");
                karyawanValid = false;
                updateSubmitStatus();
            }
        });
    </script>
</body>

</html>
