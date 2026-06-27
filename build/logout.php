<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        Swal.fire({
            title: 'Apakah kamu ingin keluar?',
            text: "Sesi kamu akan diakhiri.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Panggil logout lewat fetch agar PHP session dihancurkan
                fetch('logout.php?do=1')
                    .then(() => {
                        window.location.href = 'index.php';
                    });
            } else {
                window.history.back();
            }
        });
    </script>
</body>

</html>

<?php
// Kalau ada parameter do=1 → destroy session
if (isset($_GET['do']) && $_GET['do'] == 1) {
    session_destroy();
    exit();
}
?>