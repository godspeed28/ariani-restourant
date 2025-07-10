<?php
require 'functions.php';
session_start();

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

// Fungsi untuk mendeteksi elemen HTML dalam input
function containsHtmlTags($input)
{
    return preg_match('/<.*?>/', $input);
}

// Proses sign up
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah input mengandung elemen HTML
    if (containsHtmlTags($username) || containsHtmlTags($email) || containsHtmlTags($password)) {
        echo "
        <script>
            alert('Input tidak boleh mengandung elemen HTML. Silakan masukkan data yang valid.');
            window.location.href = 'signup.php'; // Kembali ke halaman sign up
        </script>
        ";
        exit(); // Hentikan eksekusi script
    }
    // Cek apakah username mengandung karakter khusus
    if (preg_match("/[^a-zA-Z0-9]/", $username)) {
        // Jika ada karakter khusus
        echo "<script>alert('Username tidak valid. Harap gunakan hanya huruf dan angka.');
     window.location.href = 'signup.php';</script> ";
        exit();
    }
    if (strlen($username) < 3 || strlen($username) > 15) {
        echo "<script>alert('Username harus memiliki panjang karakter 3-15 karakter');
     window.location.href = 'signup.php';</script> ";
        exit();
    }

    // Hash password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah username atau email sudah ada di database
    $sql_check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Cek apakah username atau email sudah digunakan
        $row = $result_check->fetch_assoc();
        if ($row['username'] === $username) {
            echo "
            <script>
                alert('Username sudah terdaftar. Silakan pilih username lain.');
            </script>
            ";
        } elseif ($row['email'] === $email) {
            echo "
            <script>
                alert('Email sudah terdaftar. Silakan gunakan email lain.');
            </script>
            ";
        }
    } else {
        // Query untuk menyimpan data
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            // Setelah berhasil sign up, arahkan ke halaman login
            header('Location: login.php'); // Redirect ke halaman login.php
            exit(); // Pastikan untuk berhenti setelah pengalihan
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
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
    <title>Sign Up - Aryani GO</title>
</head>

<body>
    <img src="img/bg.png" alt="" class="img-fluid background">
    <div class="overlay">
        <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="row">
                <div class="col-12 text-light">
                    <h2>Sign Up</h2>
                    <p>Daftar sekarang dan nikmati sajian kami!</p>
                    <form method="POST" action="signup.php" class="mt-4" autocomplete="off">
                        Username <input type="text" name="username" class="form-control" placeholder="masukan username" required><br>
                        Email <input type="email" name="email" class="form-control" placeholder="masukan alamat email" required><br>
                        Password <input type="password" class="form-control" name="password" placeholder="masukan password" required><br>
                        <button type="submit" class="btn btn-secondary btn-lg">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>