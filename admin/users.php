<?php

require '../functions.php';

session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Jika bukan admin, arahkan ke login
    header('Location: ../post_logout.php');
    exit();
}
function containsHtmlTags($input)
{
    return preg_match('/<.*?>/', $input);
}

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../post_logout.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if (containsHtmlTags($username) || containsHtmlTags($email) || containsHtmlTags($password)) {
        echo "
        <script>
            alert('Input tidak boleh mengandung elemen HTML. Silakan masukkan data yang valid.');
            window.location.href = 'users.php';
        </script>
        ";
        exit();
    }

    if (preg_match("/[^a-zA-Z0-9]/", $username)) {
        echo "<script>
            alert('Username tidak valid. Harap gunakan hanya huruf dan angka.');
            window.location.href = 'users.php';
        </script>";
        exit();
    }

    if (strlen($username) < 3 || strlen($username) > 15) {
        echo "<script>
            alert('Username harus memiliki panjang karakter 3-15 karakter.');
            window.location.href = 'users.php';
        </script>";
        exit();
    }

    $sql_check = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $row = $result_check->fetch_assoc();
        if ($row['username'] === $username) {
            echo "<script>alert('Username sudah terdaftar.');</script>";
        } elseif ($row['email'] === $email) {
            echo "<script>alert('Email sudah terdaftar.');</script>";
        }
    } else {
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        $conn->query($query);
    }
}

// Tandai notifikasi sebagai terbaca
if (isset($_GET['mark_as_read'])) {
    $notif_id = intval($_GET['mark_as_read']);
    $update_sql = "UPDATE notifications SET status = 'read' WHERE id = $notif_id";
    $conn->query($update_sql);
    header("Location: users.php"); // Refresh halaman setelah perubahan
    exit;
}
$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <title>Users - Aryani GO</title>
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
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar" style="border-radius: 5px;">
        <h5 style="padding-left:10px;"><img src="../img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:100%;" class="gambar">&nbsp;&nbsp;Side Bar</h5>
        <hr style="width: 50%; margin: 0 auto; border: 1px solid ;">
        <br>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="#" class="active">Users</a>
        <a href="product.php">Product</a>
        <a href="#" class="dropdown-toggle">Reports</a>
        <div class="dropdown-menu">
            <a href="sales_report.php" style="color:white;">Sales Reports</a>
        </div>
        <a href="#" class="dropdown-toggle">Settings</a>
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
                <a class="navbar-brand" href="#">Users</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <div class="dropdown">
                                <button class="btn btn-light position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-bell fa-1x"></i>
                                    <?php if ($unread_count > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <?= $unread_count ?>
                                        </span>
                                    <?php endif; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" class="position">
                                    <li class="dropdown-header">Notifikasi Baru</li>
                                   <?php while ($row = $result2->fetch_assoc()): ?>
                                        <li class="dropdown-item">
                                            <p class="mb-1 text-truncate text-break" style="max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars(substr($row['message'], 0, 50)) ?><?php if (strlen($row['message']) > 50): ?>...<?php endif; ?>
                                            </p>
                                            <small class="text-muted"><?= $row['created_at'] ?></small>
                                            <a href="admin_dashboard.php?mark_as_read=<?= $row['id'] ?>" class="btn btn-sm btn-success mt-1"><i class="fas fa-envelope-open"></i> Tandai Sudah Dibaca</a>
                                        </li>
                                    <?php endwhile; ?>
                                    <?php if ($result2->num_rows == 0): ?>
                                        <li class="dropdown-item text-center text-muted">Tidak ada notifikasi baru.</li>
                                    <?php endif; ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li class="text-center"><a href="all_notifications.php" class="btn btn-sm btn-primary" id="markAllRead"><i class="fas fa-expand"></i> Lihat Semua</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="profil.php">Profile</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label><i class="bi bi-person"></i> Nama</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukan nama" required>
                </div>
                <div class="mb-3">
                    <label><i class="bi bi-envelope"></i> Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Masukan email" required>
                </div>
                <div class="mb-3">
                    <label> <i class="bi-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <div class="mb-3">
                    <label><i class="bi bi-people"></i> Role</label>
                    <select name="role" class="form-control">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="mb-3 text-start">
    <button class="btn btn-primary" type="submit" data-bs-toggle="modal" data-bs-target="#addModal">
      <i class="bi bi-plus-circle"></i> Tambah Data
    </button>
  </div>
            </form>

            <table class="table table-bordered text-center table-responsive">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th><i class="bi bi-person"></i> Nama</th>
                        <th><i class="bi bi-envelope"></i> Email</th>
                        <th><i class="bi bi-people"></i> Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($result as $row) : ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['username'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['role'] ?></td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show-sidebar");
        }
        document.getElementById('markAllRead').addEventListener('click', function(event) {
            event.preventDefault(); // Hindari langsung berpindah halaman

            fetch('mark_all_read.php', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Tampilkan pesan atau update tampilan jika perlu
                        window.location.href = 'all_notifications.php'; // Arahkan ke halaman pesan
                    } else {
                        alert('Gagal menandai semua pesan sebagai terbaca: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>