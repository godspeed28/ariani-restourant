<?php
require '../logic/add_ad_logic.php';

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = handleAdSubmission($conn);
}

$ads = fetchAllAds($conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Ads - Aryani GO</title>
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

        #salesChart {
            max-width: 100%;
            height: 900px;
            ;
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
        <a href="product.php">Product</a>
        <a href="#" class="dropdown-toggle">Reports</a>
        <div class="dropdown-menu">
            <a href="sales_report.php" style="color:white;">Sales Reports</a>
        </div>
        <a href="#" class="dropdown-toggle active">Settings</a>
        <div class="dropdown-menu">
            <a href="#" style="color:white;">Ads</a>
        </div>
        <a href="../logout.php">Logout</a>
    </div>
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" style="margin-top: -17px;
    margin-left: -17px;">
            <div class="container-fluid">
                <button class="btn btn-light d-lg-none" onclick="toggleSidebar()">☰ Menu</button>
                <a class="navbar-brand" href="#">Ads</a>
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
                                            <a href="admin_dashboard.php?mark_as_read=<?= $row['id'] ?>" class="btn btn-sm btn-success mt-1">  <i class="fas fa-envelope-open"></i> Tandai Sudah Dibaca</a>
                    

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
        <div class="container my-4">
            <form action="" method="post" enctype="multipart/form-data" class="p-4 border rounded shadow-sm bg-light">
                <div class="mb-3">
                    <label for="title" class="form-label"><i class="fas fa-bullhorn"></i> Judul Iklan</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul iklan" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label"><i class="fas fa-file-alt"></i> Konten Iklan</label>
                    <textarea name="content" id="content" class="form-control" rows="4" placeholder="Masukkan konten iklan" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label"><i class="fas fa-image"></i> Gambar</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="end_date" class="form-label"><i class="fas fa-hourglass-half"></i> <!-- Font Awesome -->
                    Tanggal dan Waktu Berakhir</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-control" required>
                </div>
                <div class="text-start">
                      <button type="submit" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Tambah Iklan
    </button>
                </div>
            </form>
        </div>
        <div class="container mt-5">
            <?php
            // Query untuk mengambil data iklan
            $query = "SELECT id, title, content, image_url, is_active, end_date FROM ads";
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                echo '<div class="row g-4">';
                while ($row = $result->fetch_assoc()) {
            ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <?php if (!empty($row['image_url'])) { ?>
                                <img src="../img/<?= htmlspecialchars($row['image_url']); ?>"
                                    class="card-img-top"
                                    alt="Gambar Iklan"
                                    style="width:100%; height: 350px;">
                            <?php } ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($row['content']); ?></p>
                                <p>
                                    <span class="badge bg-<?= $row['is_active'] ? 'success' : 'danger'; ?>">
                                        <?= $row['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                                    </span>
                                </p>
                                <p class="mt-2">
                                    <strong>Waktu Berakhir: </strong>
                                    <span id="countdown-<?= $row['id']; ?>" class="text-danger"></span>
                                </p>
                                <div class="mt-auto">
                                    <a href="toggle_ad.php?id=<?= $row['id']; ?>&status=<?= $row['is_active'] ? 0 : 1; ?>"
                                        class="btn btn-sm btn-<?= $row['is_active'] ? 'danger' : 'success'; ?> w-100 mb-2">
                                        <?= $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>
                                    </a>
                                    <div class="text-start">
                                        <a href="edit_ad.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                                        <a href="delete_ad.php?id=<?= $row['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus iklan ini?');"><i class="bi bi-trash"></i> Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        var countdownTimers = {}; // Objek untuk menyimpan semua timer berdasarkan ID iklan

                        function initializeCountdown(endDateString, elementId) {
                            var endDate = new Date(endDateString).getTime();
                            var countdownElement = document.getElementById(elementId);

                            if (countdownTimers[elementId]) {
                                clearInterval(countdownTimers[elementId]); // Hapus timer lama jika ada
                            }

                            countdownTimers[elementId] = setInterval(function() {
                                var now = new Date().getTime();
                                var distance = endDate - now;

                                if (distance < 0) {
                                    clearInterval(countdownTimers[elementId]);
                                    countdownElement.innerHTML = "Iklan berakhir!";
                                } else {
                                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                                    countdownElement.innerHTML = days + " hari " + hours + " jam " + minutes + " menit " + seconds + " detik";
                                }
                            }, 1000);
                        }
                    </script>
                    <script>
                        initializeCountdown("<?= $row['end_date']; ?>", "countdown-<?= $row['id']; ?>");
                    </script>
            <?php
                }
                echo '</div>';
            } else {
                echo '<p class="text-center text-muted">Tidak ada iklan untuk ditampilkan.</p>';
            }
            ?>
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