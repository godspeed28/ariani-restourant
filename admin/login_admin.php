<?php

require '../functions.php';
session_start();
// Cegah pengguna yang sudah login mengakses halaman ini
if (isset($_SESSION['role']) && !empty($_SESSION['role'])) {
    header('Location: admin_dashboard.php');
    exit();
}

// Cegah caching halaman login
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");
header("Pragma: no-cache");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
   
    // Query untuk mencari admin
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Admin ditemukan, cek password
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login sukses sebagai admin, simpan username dan role ke session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'admin';
            $_SESSION['id'] = $id['id']; // Menyimpan ID pengguna setelah login
            // Arahkan ke halaman admin
            header('Location: admin_dashboard.php'); // Ganti dengan halaman dashboard admin yang sesuai
            exit();
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Admin tidak ditemukan!";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style_2.css">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <title>Login Admin - Aryani GO</title>
</head>

<body>
    <img src="../img/bg.png" alt="" class=" img-fluid background">
    <div class="overlay">
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="row">
                <div class="col-12 text-light">
                    <h2>Login as Admin</h2>
                    <p>Masukkan username dan password admin!</p>
                    <form method="POST" action="login_admin.php" class="mb-3" autocomplete="on">
                        Username <input type="text" name="username" class="form-control" placeholder="masukan username" required><br>
                        Email <input type="email" name="email" class="form-control" placeholder="masukan email" required><br>
                        Password <input type="password" class="form-control" name="password" placeholder="masukan password" required><br>
                        <button type="submit" class="btn btn-warning btn-lg">Login as <img src="../img/manager.png" alt="" style="width:20px;"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>