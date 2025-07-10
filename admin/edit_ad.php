<?php
require '../functions.php'; // Pastikan koneksi database tersedia

session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Jika bukan admin, arahkan ke login
    header('Location: ../post_logout.php');
    exit();
}
// Ambil ID iklan yang akan diedit dari URL
if (isset($_GET['id'])) {
    $ad_id = $_GET['id'];

    // Query untuk mengambil data iklan berdasarkan ID
    $query = "SELECT * FROM ads WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ad = $result->fetch_assoc();

    if (!$ad) {
        echo "Iklan tidak ditemukan!";
        exit;
    }
}

// Jika form disubmit, lakukan update
if (isset($_POST['update'])) {
    // Ambil data dari form
    $title = !empty($_POST['title']) ? htmlspecialchars($_POST['title']) : $ad['title'];
    $content = !empty($_POST['content']) ? htmlspecialchars($_POST['content']) : $ad['content'];
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $ad['end_date'];

    // Proses unggah gambar baru
    $imageUrl = $ad['image_url']; // Default gambar lama
    if (!empty($_FILES['image_url']['name'])) {
        $targetDir = "../img/";
        $fileExtension = strtolower(pathinfo($_FILES['image_url']['name'], PATHINFO_EXTENSION));
        $uniqueFileName = uniqid() . '.' . $fileExtension; // Nama file unik
        $targetFile = $targetDir . $uniqueFileName;

        // Validasi file gambar
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($fileExtension, $allowedTypes)) {
            echo "<script>alert('Jenis file tidak didukung!');</script>";
            exit;
        }

        // Pindahkan file ke folder tujuan
        if (move_uploaded_file($_FILES['image_url']['tmp_name'], $targetFile)) {
            $imageUrl = $uniqueFileName; // Update URL gambar
        } else {
            echo "<script>alert('Gagal mengunggah gambar!');</script>";
            exit;
        }
    }

    // Query untuk update data iklan
    $query = "UPDATE ads SET title = ?, content = ?, image_url = ?, end_date = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $title, $content, $imageUrl, $end_date, $ad_id);

    if ($stmt->execute()) {
        echo "<script>alert('Iklan berhasil diperbarui!');window.location.href = 'add_ad.php';</script>";
    } else {
        echo "Gagal memperbarui iklan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <title>Manage Ads - Aryani GO</title>
    <style>
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

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                display: none;
            }

            .content {
                margin-left: 0;
            }

            .show-sidebar {
                display: block;
            }
        }

        .sidebar .dropdown-menu {
            display: none;
            /* Tidak ditampilkan awalnya */
            padding-left: 10px;
            padding-right: 10px;
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
            display: contents;
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

        @media (max-width: 768px) {
            .navbar-nav .nav-item {
                margin-bottom: 10px;
            }

            .navbar-nav .btn {
                margin-right: auto;
                /* Agar lonceng berada di kiri */
                margin-top: 10px;
            }
        }

        /* Chart Animation */
        canvas {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar" style="border-radius:5px;">
        <h5 style="padding-left:10px;"><img src="../img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:100%;" class="gambar">&nbsp;&nbsp;Side Bar</h5>
        <hr style="width: 50%; margin: 0 auto; border: 1px solid ;">
        <br>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="product.php">Product</a>
        <a href="#" class="dropdown-toggle">Reports</a>
        <div class="dropdown-menu">
            <a href="sales_report.php" style="color:white;">Sales Reports</a>
        </div>
        <a href="#" class="dropdown-toggle active">Settings</a>
        <div class="dropdown-menu">
            <a href="add_ad.php" style="color:white;">Ads</a>
        </div>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" style="margin-top: -17px;
    margin-left: -17px;">
            <div class="container-fluid">
                <button class="btn btn-light d-lg-none" onclick="toggleSidebar()">☰ Menu</button>
                <a class="navbar-brand" href="#">Manage Ads</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
        <form method="POST" action="" enctype="multipart/form-data" class="p-3 border rounded shadow-sm bg-light" style="margin-top: 15px;">
            <div class="mb-3">
                <label for="title" class="form-label"><i class="fas fa-bullhorn"></i> Judul Iklan</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($ad['title']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label"><i class="fas fa-file-alt"></i> Konten Iklan</label>
                <textarea name="content" id="content" rows="5" class="form-control" required><?php echo htmlspecialchars($ad['content']); ?></textarea>
            </div>

            <div class="mb-1">
                <label for="image_url" class="form-label"><i class="fas fa-image"></i> Gambar</label>
                <input type="file" name="image_url" id="image_url" class="form-control">
            </div>

            <div class="mb-3">
                <label for="current_image" class="form-label"></label><br>
                <img src="../img/<?php echo htmlspecialchars($ad['image_url']); ?>" alt="Gambar Lama" class="img-thumbnail" style="max-width: 200px;">
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label"><i class="fas fa-hourglass-half"></i> Tanggal Berakhir</label>
                <input type="datetime-local" name="end_date" id="end_date" value="<?php echo htmlspecialchars($ad['end_date']); ?>" class="form-control" required>
            </div>
            <div class="d-flex justify-content-start">
                <button class="btn btn-primary" type="submit" name="update">
                    <i class="bi bi-save" style="font-style:normal;"> Simpan</i>
                </button>
            </div>
        </form>


    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show-sidebar");
        }
    </script>


</body>

</html>