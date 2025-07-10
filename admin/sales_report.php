<?php
require '../functions.php';

session_start();
    
// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  // Jika bukan admin, arahkan ke login
  header('Location: ../post_logout.php');
  exit();
}
$sql = "SELECT * FROM sales_report 
        WHERE stat != 'Dibatalkan' 
          AND status_pembayaran NOT IN ('Belum bayar', 'Kedaluwarsa') 
        ORDER BY tanggal_transaksi DESC";

$result = $conn->query($sql);

// Ambil data untuk grafik
$produk = [];
$kuantitas = [];
$total_harga = [];

foreach ($result as $row) {
    $produk[] = $row['nama_produk'];
    $kuantitas[] = $row['kuantitas'];
    $total_harga[] = $row['total_harga'];
}

$conn->close();

// Tandai notifikasi sebagai terbaca
if (isset($_GET['mark_as_read'])) {
    $notif_id = intval($_GET['mark_as_read']);
    $update_sql = "UPDATE notifications SET status = 'read' WHERE id = $notif_id";
    $conn->query($update_sql);
    header("Location: sales_report.php"); // Refresh halaman setelah perubahan
    exit;
}
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Sales Report - Aryani GO</title>
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

.sidebar .dropdown-toggle:hover + .dropdown-menu,
.sidebar .dropdown-menu:hover {
    background-color: darkorange;
    display:contents; /* Tampilkan dropdown saat hover di toggle atau menu itu sendiri */
    
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
#salesChart {
                max-width: 100%;
                height: 900px;;
            }

    </style>
</head>

<body>
    <div class="sidebar" id="sidebar" style="border-radius: 5px;">
   <h5 style="padding-left:10px;"><img src="../img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:100%;" class="gambar">&nbsp;&nbsp;Side Bar</h5>
   <hr style="width: 50%; margin: 0 auto; border: 1px solid ;">
   <br>
        <a href="admin_dashboard.php" >Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
        <a href="product.php">Product</a>
        <a href="#" class="dropdown-toggle active">Reports</a>
    <div class="dropdown-menu">
        <a href="#"style="color:white;">Sales Reports</a>
    </div>
    <a href="#" class="dropdown-toggle">Settings</a>
    <div class="dropdown-menu">
        <a href="add_ad.php"style="color:white;">Ads</a>
    </div>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" style="margin-top: -17px;
    margin-left: -17px;">
            <div class="container-fluid">
                <button class="btn btn-light d-lg-none" onclick="toggleSidebar()">☰ Menu</button>
                <a class="navbar-brand" href="#">Sales Reports</a>
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
                                            <a href="sales_report.php?mark_as_read=<?= $row['id'] ?>" class="btn btn-sm btn-success mt-1">   <i class="fas fa-envelope-open"></i> Tandai Sudah Dibaca</a>
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
                        <li class="nav-item"><a class="nav-link" href="profil.php">Profile</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <canvas id="salesChart" width="500" height="200"></canvas>
        <div class="container">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th><i class="bi bi-person"></i> Penerima</th>
                        <th> <i class="bi-geo-alt"></i> Alamat</th>
                        <th><i class="bi bi-card-text"></i> Menu</th>
                        <th><i class="bi bi-basket"></i> Kuantitas</th>
                        <th> <i class="bi-cash-stack"></i> Total Harga</th>
                        <th> <i class="bi-clock"></i> Tanggal Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($result as $row) : ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $row['nama_penerima'] ?></td>
                            <td><?= $row['alamat'] ?></td>
                            <td><?= $row['nama_produk'] ?></td>
                            <td><?= $row['kuantitas'] ?></td>
                            <td><?= $row['total_harga'] ?></td>
                            <td><?= $row['tanggal_transaksi'] ?></td>
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

    const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($produk) ?>,  // Nama Produk
        datasets: [
            {
                label: 'Kuantitas Produk',
                data: <?= json_encode($kuantitas) ?>,  // Kuantitas
                backgroundColor: 'rgba(54, 162, 235, 0.2)',  // Warna background
                borderColor: 'rgba(54, 162, 235, 1)',  // Warna border garis
                borderWidth: 3,  // Lebar garis lebih tebal
                fill: true,  // Mengisi area di bawah garis
                tension: 0.4,  // Memberikan kelengkungan halus pada garis
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',  // Warna titik
                pointBorderColor: '#fff',  // Border titik putih
                pointBorderWidth: 2,  // Lebar border titik
                pointRadius: 5,  // Ukuran titik lebih besar
                borderCapStyle: 'round',  // Sudut garis lebih halus
                borderJoinStyle: 'round',  // Menghubungkan garis dengan sudut lebih halus
            },
            {
                label: 'Total Harga',
                data: <?= json_encode($total_harga) ?>,  // Total Harga
                backgroundColor: 'rgba(255, 99, 132, 0.2)',  // Warna background
                borderColor: 'rgba(255, 99, 132, 1)',  // Warna border garis
                borderWidth: 3,  // Lebar garis lebih tebal
                fill: true,  // Mengisi area di bawah garis
                tension: 0.4,  // Memberikan kelengkungan halus pada garis
                pointBackgroundColor: 'rgba(255, 99, 132, 1)',  // Warna titik
                pointBorderColor: '#fff',  // Border titik putih
                pointBorderWidth: 2,  // Lebar border titik
                pointRadius: 5,  // Ukuran titik lebih besar
                borderCapStyle: 'round',  // Sudut garis lebih halus
                borderJoinStyle: 'round',  // Menghubungkan garis dengan sudut lebih halus
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        size: 14,
                        family: 'Arial, sans-serif',
                        weight: 'bold'
                    },
                    color: '#333',  // Warna teks legenda lebih gelap
                }
            },
            tooltip: {
                enabled: true,
                backgroundColor: 'rgba(0, 0, 0, 0.7)',  // Latar belakang tooltip
                titleColor: '#fff',  // Warna judul tooltip
                bodyColor: '#fff',  // Warna isi tooltip
                borderColor: '#fff',  // Border tooltip putih
                borderWidth: 1,  // Lebar border tooltip
                displayColors: false,  // Menyembunyikan warna titik pada tooltip
                caretSize: 8,  // Ukuran ekor tooltip
                cornerRadius: 5,  // Radius sudut tooltip
            }
        },
        animation: {
            duration: 1500,  // Durasi animasi lebih lama
            easing: 'easeInOutQuad',  // Efek animasi yang lebih halus
            animateScale: true,  // Menambahkan efek scale pada animasi
            animateRotate: true,  // Menambahkan efek rotasi pada animasi
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 10,
                    max: Math.max(...<?= json_encode($total_harga) ?>) + 50,  // Mengatur maksimal Y agar pas
                    min: 0,  // Minimal Y tetap di 0
                    color: '#333',  // Warna label Y
                    font: {
                        size: 12,  // Ukuran font label Y
                        weight: 'bold'
                    }
                },
                grid: {
                    color: '#ccc',  // Warna grid yang lebih lembut
                    borderColor: '#bbb',  // Border sumbu lebih lembut
                }
            },
            x: {
                grid: {
                    color: '#ccc',  // Warna grid X lebih lembut
                },
                ticks: {
                    color: '#333',  // Warna label X lebih gelap
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            }
        }
    }
});

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


</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
