<?php

session_start();
if (isset($_SESSION['status']) && $_SESSION['status'] === "login") {
  header("Location: adminabsen.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script
    src="https://kit.fontawesome.com/64d58efce2.js"
    crossorigin="anonymous"></script>

  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Tailwind CDN (atau CSS kamu) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Optional styling untuk ikon sosial media */
    .social-media {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }

    .social-icon i {
      font-size: 24px;
      color: #333;
      transition: color 0.3s;
    }

    .social-icon:hover i {
      color: #1d4ed8;
      /* biru saat hover */
    }

    .social-text {
      text-align: center;
      margin-top: 15px;
      font-weight: 500;
    }
  </style>

  <link rel="stylesheet" href="style.css" />
  <title>Sign in Admin Absen</title>
</head>


<body>
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="aksiadmin.php" method="post" class="sign-in-form">
          <h2 class="title text-center text-2xl font-bold mb-4">Login Admin</h2>

          <div class="input-field flex items-center border rounded px-2 py-1 mb-3">
            <i class="fas fa-user mr-2"></i>
            <input type="text" name="username" placeholder="Username" class="w-full outline-none" required />
          </div>

          <div class="input-field flex items-center border rounded px-2 py-1 mb-3">
            <i class="fas fa-lock mr-2"></i>
            <input type="password" name="password" placeholder="Password" class="w-full outline-none" required />
          </div>

          <input type="submit" name="login" class="btn" value="Login" />

          <p class="social-text">Follow social platforms</p>
          <div class="social-media">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fas fa-globe"></i></a>

          </div>
        </form>


      </div>
    </div>

    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content">
          <h3>Selamat Datang </h3>
          <br><br>
          <p>
            Selamat datang di <strong>Aplikasi Monitoring Kegiatan Keagamaan</strong>
            <br>Rumah Sakit Muhammadiyah Tuban.
            <br><br>
            Aplikasi ini digunakan untuk memantau dan mengelola kegiatan keagamaan
            yang berlangsung di lingkungan rumah sakit.
            <br>Hanya <strong>admin</strong> yang memiliki akses untuk menggunakan sistem ini.
          </p>
        </div>
        <img src="img/log.svg" class="image" alt="Login Illustration" />
      </div>
    </div>

  </div>

  <script src="app.js"></script>
</body>

</html>