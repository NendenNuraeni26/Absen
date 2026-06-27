<?php
$conn = new mysqli("192.168.10.13", "teknis", "rsmtuban", "absen");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
