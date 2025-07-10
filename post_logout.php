<?php
require 'functions.php';
session_start();

// cek cookie
if (isset($_COOKIE['id']) && isset($_COOKIE['key'])){
    $id = $_COOKIE['id'];
    $key = $_COOKIE['key'];

    // ambil username berdasarkan id
    $result = mysqli_query($conn, "SELECT username FROM users WHERE id = $id");

    $row = mysqli_fetch_assoc($result);

    // cek cookie dan username
    if($key === hash('sha256', $row['username'])){
        $_SESSION['role'] = true;
    }
}


// Cegah pengguna yang sudah login mengakses halaman ini
if (isset($_SESSION['role']) && !empty($_SESSION['role'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
        header('Location: admin/admin_dashboard.php');
        exit();
    } else {
        header('Location: index.php');
        exit();
    }
}

// Cegah caching halaman login
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
header("Pragma: no-cache");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="img/garpu.jpg" />
    <link rel="stylesheet" href="css/style_2.css">
    <title>Aryani GO</title>
</head>

<body>
    <img src="img/bg.png" alt="" class="img-fluid background">
    <div class="overlay">
        <div class="container d-flex flex-column-reverse justify-content-center align-items-center" style="height: 100vh;">
            <div class="text-center text-light">
                <h2>Selamat Datang!</h2>
                <p>Silakan pilih salah satu opsi di bawah ini</p>
                <!-- Grup Tombol -->
                <div class="">
                    <!-- Tombol untuk Login -->
                    <form method="GET" action="login.php" style="padding-bottom:10px;">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </form>
                    <!-- Tombol untuk Login sebagai Admin -->
                    <form method="GET" action="admin/login_admin.php">
                        <button type="submit" class="btn btn-warning btn-lg">
                            Login as
                            <img src="img/manager.png" alt="Admin Icon" style="width:20px;">
                        </button>
                    </form>
                    <!-- Tombol untuk Sign Up -->
                    <form method="GET" action="signup.php" style="padding-top:20px;">
                        Belum punya akun?
                        <br>
                        <a href="signup.php">Daftar Sekarang</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</body>

</html>