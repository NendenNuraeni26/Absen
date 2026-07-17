<?php
$conn = new mysqli("192.168.x.x", "yawda", "yawda", "dbmu");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
