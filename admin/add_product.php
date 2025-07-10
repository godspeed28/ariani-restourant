<?php

require '../functions.php';

session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Jika bukan admin, arahkan ke login
    header('Location: ../post_logout.php');
    exit();
}

if (isset($_POST["submit"])) {

    //cek apakah data berhasil ditambahkan atau tidak 
    if (tambah($_POST) > 0) {
        echo "
        <script>
        alert('Data berhasil ditambahkan!');
        document.location.href = 'product.php';
        </script>
        ";
    } else {
        echo "
        <script>
         alert('Data gagal ditambahkan!');
         document.location.href = 'product.php';
         </script>
        ";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <title>Manage Product - Aryani GO</title>
    <style>
        /* Custom Styling */
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            margin: 0;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
            z-index: 1000;
        }

        .sidebar a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #ccc;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }

        .sidebar .active {
            background-color: darkorange;
            color: white;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        /* Media Queries for Mobile */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                display: none;
            }

            .sidebar a {
                text-align: left;
            }

            .content {
                margin-left: 0;
            }

            .show-sidebar {
                display: block;
            }
        }

        .tombol {
            color: darkorange;
        }

        .sidebar .dropdown-menu {
            display: none;
            /* Tidak ditampilkan awalnya */
            padding-left: 10px;
            padding-right:10px;
            position: absolute;
            right: 1px;
            /* Pastikan dropdown tetap di posisi yang benar */
            background-color: white;
            /* Sama dengan sidebar */
            z-index: 1000;
        }

        .sidebar .dropdown-toggle:hover+.dropdown-menu,
        .sidebar .dropdown-menu:hover {
            background-color: darkorange;
            display:contents;
            /* Tampilkan dropdown saat hover di toggle atau menu itu sendiri */

        }

        /* Menyesuaikan dropdown agar tidak tertutup di layar kecil */
        @media (max-width: 768px) {
            .sidebar .dropdown-menu {
                position: relative;
                /* Agar dropdown mengikuti posisi sidebar */
                width: auto;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar" style="border-radius: 5px;">
        <h5 style="padding-left:10px;"><img src="../img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:100%;" class="gambar">&nbsp;&nbsp;Side Bar</h5>
        <hr style="width: 50%; margin: 0 auto; border: 1px solid ;">
        <br>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="product.php" class="active">Product</a>
        <a href="#" class="dropdown-toggle">Reports</a>
        <div class="dropdown-menu">
            <a href="sales_report.php" style="color:white;">Sales Reports</a>
        </div>
        <a href="#" class="dropdown-toggle">Settings</a>
        <div class="dropdown-menu">
            <a href="sales_report.php" style="color:white;">Ads</a>
        </div>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" style="margin-top: -17px;
    margin-left: -17px;">
            <div class="container-fluid">
                <button class="btn btn-light d-lg-none" onclick="toggleSidebar()">☰ Menu</button>
                <a class="navbar-brand" href="#">Manage Product</a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_notifications.php"></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4">
            <h4> <i class="bi bi-plus-circle"></i> Tambah Data</h4>
            <hr>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama"><i class="bi bi-card-text"></i> Menu</label>
                        <input type="text" class="form-control" name="nama" placeholder="Masukan nama menu" id="nama" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kategori"><i class="bi bi-tag"></i> Kategori</label>
                        <select class="form-control" name="kategori" id="kategori" required>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Camilan">Camilan</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga"><i class="bi bi-currency-dollar"></i>Harga</label>
                        <input type="number" class="form-control" placeholder="Masukan harga" name="harga" id="harga" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stok"><i class="bi bi-box-seam"></i> Stok</label>
                        <input type="number" class="form-control" placeholder="Masukan jumlah stok" name="stok" id="stok" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="gambar"><i class="bi bi-image"></i> Gambar</label>
                    <input type="file" class="form-control" name="gambar" id="gambar" required accept="image/*" onchange="previewImage(event)">
                    <img id="preview" src="#" alt="Preview Gambar" style="display:none; margin-top:10px; max-width:200px;">
                </div>
                <hr>
                <button type="submit" name="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                <hr>
            </form>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show-sidebar");
        }

        function previewImage(event) {
            const preview = document.getElementById('preview');
            const file = event.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            } else {
                preview.src = "#";
                preview.style.display = 'none';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN6jIeHz" crossorigin="anonymous"></script>
</body>

</html>