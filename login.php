<?php
session_start();  // Memulai session untuk login
require 'functions.php';

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

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mencari user
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = 'user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User ditemukan, cek password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login sukses, simpan informasi ke session
            $_SESSION['user_id'] = $row['id']; // Simpan user_id dari database ke session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';

            // Setelah berhasil login, arahkan ke halaman dashboard
            header('Location: index.php'); // Redirect ke halaman dashboard.php
            exit(); // Pastikan untuk berhenti setelah pengalihan
        } else {
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="img/garpu.jpg" />
    <link rel="stylesheet" href="css/style_2.css">
    <title>Login - Aryani GO</title>
</head>

<body>
    <img src="img/bg.png" alt="" class="img-fluid background">
    <div class="overlay">
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="row">
                <div class="col-12 text-light">
                    <h2>Login</h2>
                    <p>Silahkan masukan username dan password!</p>
                    <form method="POST" action="login.php" class="mb-3" autocomplete="off">
                        Username <input type="text" name="username" class="form-control" placeholder="masukan username" required><br>
                        Password <input type="password" class="form-control" name="password" placeholder="masukan password" required><br>
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>