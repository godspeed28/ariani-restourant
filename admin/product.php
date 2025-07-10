<?php
require '../functions.php';

session_start();
    
// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  // Jika bukan admin, arahkan ke login
  header('Location: ../post_logout.php');
  exit();
}

$result = $conn->query("SELECT * FROM menu");

// Tombol cari ditekan
if (isset($_POST["cari"])) {
    $result = cari($_POST["keyword"]);
}

// Query untuk mengambil data menu
$sql = "SELECT nama, stok FROM menu";
$hasil = $conn->query($sql);

$names = [];
$stocks = [];

if ($hasil->num_rows > 0) {
    // Ambil data setiap baris
    while($row = $hasil->fetch_assoc()) {
        $names[] = $row['nama'];
        $stocks[] = $row['stok'];
    }
} else {
    echo "0 results";
}

$conn->close();

// Mengonversi data PHP ke format JSON untuk digunakan oleh JavaScript
$names_json = json_encode($names);
$stocks_json = json_encode($stocks);

if (isset($_GET['mark_as_read'])) {
    $notif_id = intval($_GET['mark_as_read']);
    $update_sql = "UPDATE notifications SET status = 'read' WHERE id = $notif_id";
    $conn->query($update_sql);
    header("Location: product.php"); // Refresh halaman setelah perubahan
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Product - Aryani GO</title>
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

            .content {
                margin-left: 0;
            }

            .show-sidebar {
                display: block;
            }
        }
        #menuChart {
    max-width: 100%;  /* Agar grafik mengikuti lebar container */
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

.sidebar .dropdown-toggle:hover + .dropdown-menu,
.sidebar .dropdown-menu:hover {
    background-color: darkorange;
    display:contents;/* Tampilkan dropdown saat hover di toggle atau menu itu sendiri */
    
}
/* Menyesuaikan dropdown agar tidak tertutup di layar kecil */
@media (max-width: 768px) {
    .sidebar .dropdown-menu {
        position: relative; /* Agar dropdown mengikuti posisi sidebar */
        width: auto;
    }
}
/* Menyesuaikan dropdown agar tidak tertutup di layar kecil */
@media (max-width: 768px) {
    .sidebar .dropdown-menu {
        position: relative; /* Agar dropdown mengikuti posisi sidebar */
        width: auto;
    }
}
@media (max-width: 768px) {
    .navbar-nav .nav-item {
        margin-bottom: 10px;
    }

    .navbar-nav .btn {
        margin-right: auto; /* Agar lonceng berada di kiri */
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
   <a href="admin_dashboard.php
   " >Dashboard</a>
   <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="product.php"class="active">Product</a>
        <a href="#" class="dropdown-toggle">Reports</a>
    <div class="dropdown-menu">
        <a href="sales_report.php"style="color:white;">Sales Reports</a>
    </div>
    <a href="#" class="dropdown-toggle">Settings</a>
    <div class="dropdown-menu">
        <a href="add_ad.php"style="color:white;">Ads</a>
    </div>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" style="margin-top: -17px;
    margin-left: -17px;">
            <div class="container-fluid">
                <button class="btn btn-light d-lg-none" onclick="toggleSidebar()">☰ Menu</button>
                <a class="navbar-brand" href="#">Product</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                                            <a href="admin_dashboard.php?mark_as_read=<?= $row['id'] ?>" class="btn btn-sm btn-success mt-1">Tandai Sudah Dibaca</a>
                                        </li>
                                    <?php endwhile; ?>
                            <?php if ($result2->num_rows == 0): ?>
                                <li class="dropdown-item text-center text-muted">Tidak ada notifikasi baru.</li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li class="text-center"><a href="all_notifications.php" class="btn btn-sm btn-primary" id="markAllRead"><i class="fas fa-expand"></i> Lihat Semua</a></li>
                        </ul>
                    </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profil.php">Profile</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Pencarian -->
        <canvas id="menuChart"></canvas>

            <script>
           window.onload = function() {
    // Ambil data dari PHP
    const menuNames = <?php echo $names_json; ?>;
    const menuStocks = <?php echo $stocks_json; ?>;

    // Data untuk Bubble Chart
    const menuData = {
        datasets: menuNames.map((menu, index) => ({
            label: menu, // Nama menu sebagai label
            data: [{ x: index + 1, y: menuStocks[index], r: menuStocks[index] / 2 }], // Posisi dan ukuran bubble
            backgroundColor: `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.6)`,
            borderColor: 'rgba(0, 0, 0, 0.8)',
            borderWidth: 2,
            hoverBackgroundColor: 'rgba(0, 0, 0, 0.8)', // Warna saat hover
            hoverBorderColor: '#fff',  // Warna border saat hover
            hoverBorderWidth: 3  // Lebar border saat hover
        }))
    };

    // Konfigurasi grafik
    const config = {
        type: 'bubble',
        data: menuData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true, // Tampilkan legenda
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold',
                            family: 'Arial, sans-serif',
                        },
                        color: '#333',  // Warna teks legenda
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',  // Warna tooltip lebih gelap
                    titleColor: '#fff',  // Warna judul tooltip
                    bodyColor: '#fff',  // Warna teks tooltip
                    borderColor: '#fff',  // Border putih
                    borderWidth: 1,
                    callbacks: {
                        // Mengubah tooltip untuk menyembunyikan nilai 'r'
                        label: function(tooltipItem) {
                            return `Stok: ${tooltipItem.raw.y}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Menu Index',
                        font: {
                            size: 14,
                            weight: 'bold',
                            family: 'Arial, sans-serif'
                        },
                        color: '#333',
                    },
                    grid: {
                        color: '#ccc'  // Warna grid lebih lembut
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Jumlah Stok',
                        font: {
                            size: 14,
                            weight: 'bold',
                            family: 'Arial, sans-serif'
                        },
                        color: '#333',
                    },
                    beginAtZero: true,
                    grid: {
                        color: '#ccc',  // Warna grid lebih lembut
                    },
                    ticks: {
                        stepSize: 10, // Menentukan langkah pada sumbu Y
                    }
                }
            },
            animation: {
                duration: 1500,  // Durasi animasi lebih lama
                easing: 'easeInOutQuad',  // Efek animasi halus
                animateScale: true,  // Menambahkan efek scale pada animasi
                animateRotate: true,  // Menambahkan efek rotasi pada animasi
            },
            elements: {
                point: {
                    radius: 8,  // Ukuran titik lebih besar
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',  // Warna titik yang lebih jelas
                    borderColor: '#fff',  // Warna border titik putih
                    borderWidth: 2,  // Lebar border titik
                }
            }
        }
    };

    // Inisialisasi grafik
    const ctx = document.getElementById('menuChart').getContext('2d');
    const menuChart = new Chart(ctx, config);
}

            </script>
            <form action="" method="POST" class="mb-1">
                <input type="text" name="keyword" size="30" autofocus placeholder="Masukkan keyword pencarian" autocomplete="off">
                <button type="submit" name="cari" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>

            <!-- Tombol Tambah Menu -->
            <a href="add_product.php"> <div class="mb-3 text-start">
    <button class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Tambah Data
    </button>
  </div></a>

            <!-- Tabel -->
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th><i class="bi bi-image"></i> Gambar</th>
                            <th><i class="bi bi-card-text"></i> Menu</th>
                            <th><i class="bi bi-tag"></i> Kategori</th>
                            <th><i class="bi bi-currency-dollar"></i> Harga</th>
                            <th><i class="bi bi-box-seam"></i> Stok</th>
                            <th><i class="bi bi-gear"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1 ?>
                        <?php foreach ($result as $row) : ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><img src="../img/<?= $row['gambar'] ?>" style="width:50px;"></td>
                                <td><?= $row['nama'] ?></td>
                                <td><?php
                                    switch ($row['kategori']) {
                                        case 'Makanan':
                                            echo "🍔 Makanan";
                                            break;
                                        case 'Minuman':
                                            echo "🥤 Minuman";
                                            break;
                                        case 'Camilan':
                                            echo "🍪 Camilan";
                                            break;
                                        default:
                                            echo "<i>NULL</i>";
                                    }
                                    ?></td>
                                <td><?= $row['harga'] ?></td>
                                <td><?= $row['stok'] ?></td>
                                <td>
                                    <a href="update.php?id=<?= $row["id"]; ?>" class="btn btn-warning mb-3"> <i class="bi bi-pencil-square"></i> Edit</a>&nbsp;
                                    <a href="delete.php?id=<?= $row["id"]; ?>" onclick="return confirm('yakin?');" class="btn btn-danger mb-3"> <i class="bi bi-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php $i++ ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
         document.getElementById('markAllRead').addEventListener('click', function (event) {
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
         function toggleSidebar() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("show-sidebar");
    }
           
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
