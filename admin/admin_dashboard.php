<?php
require '../functions.php';

session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Jika bukan admin, arahkan ke login
    header('Location: ../post_logout.php');
    exit();
}

// Ambil data jumlah pengguna berdasarkan role (Admin dan User)
$query_user = "SELECT role, COUNT(*) as count FROM users GROUP BY role";
$result_user = $conn->query($query_user);

// Siapkan array untuk data Chart.js
$user_roles = [];
$user_counts = [];
while ($row = $result_user->fetch_assoc()) {
    $user_roles[] = $row['role'];
    $user_counts[] = $row['count'];
}

// Ambil jumlah notifikasi yang belum terbaca
$count_sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE status = 'unread'";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$unread_count = $count_row['unread_count'];

// Ambil notifikasi yang belum terbaca
$notif_sql = "SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($notif_sql);

// Tandai notifikasi sebagai terbaca
if (isset($_GET['mark_as_read'])) {
    $notif_id = intval($_GET['mark_as_read']);
    $update_sql = "UPDATE notifications SET status = 'read' WHERE id = $notif_id";
    $conn->query($update_sql);
    header("Location: admin_dashboard.php"); // Refresh halaman setelah perubahan
    exit;
}
// Query untuk mendapatkan jumlah pengguna
$query_users = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$result_users = $conn->query($query_users);

if ($result_users) {
    $total_users = $result_users->fetch_assoc()['total_users']; // Mengambil nilai 'total_users' dari hasil query
} else {
    $total_users = 0; // Jika query gagal, set nilai default
}

// Query untuk mendapatkan pendapatan bulanan
$query_revenue = $conn->query("SELECT SUM(total_harga) AS total_revenue FROM sales_report WHERE MONTH(tanggal_transaksi) = MONTH(CURRENT_DATE) AND YEAR(tanggal_transaksi) = YEAR(CURRENT_DATE) AND stat != 'Dibatalkan' AND status_pembayaran NOT IN ('Belum bayar','Kedaluwarsa')");
$total_revenue = $query_revenue->fetch_assoc()['total_revenue']; // Menggunakan fetch_assoc untuk mendapatkan nilai

// Query untuk mendapatkan jumlah pesanan baru
$query_orders = $conn->query("SELECT COUNT(*) AS total_orders FROM sales_report WHERE DATE(tanggal_transaksi) = CURRENT_DATE");
$total_orders = $query_orders->fetch_assoc()['total_orders']; // Menggunakan fetch_assoc untuk mendapatkan nilai
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
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <title>Admin Dashboard - Aryani GO</title>
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

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .card-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #6e7dff, #3a49db);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar" style="border-radius: 5px;">
        <h5 style="padding-left:10px;"><img src="../img/garpu.jpg" alt="" style="width:50px; color:white; border-radius:100%;" class="gambar">&nbsp;&nbsp;Side Bar</h5>
        <hr style="width: 50%; margin: 0 auto; border: 1px solid ;">
        <br>
        <a href="#" class="active">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="users.php">Users</a>
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
                <a class="navbar-brand" href="#">Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-left">
                        <!-- Lonceng Notifikasi -->
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
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <li class="dropdown-item">
                                            <p class="mb-1 text-truncate text-break" style="max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars(substr($row['message'], 0, 50)) ?><?php if (strlen($row['message']) > 50): ?>...<?php endif; ?>
                                            </p>
                                            <small class="text-muted"><?= $row['created_at'] ?></small>
                                            <a href="admin_dashboard.php?mark_as_read=<?= $row['id'] ?>" class="btn btn-sm btn-success mt-1"><i class="fas fa-envelope-open"></i> Tandai Sudah Dibaca</a>
                                        </li>
                                    <?php endwhile; ?>

                                    <?php if ($result->num_rows == 0): ?>
                                        <li class="dropdown-item text-center text-muted">Tidak ada notifikasi baru.</li>
                                    <?php endif; ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li class="text-center"><a href="all_notifications.php" class="btn btn-sm btn-primary text-center" id="markAllRead"><i class="fas fa-expand"></i> Lihat Semua</a></li>
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

        <div class="container mt-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-gradient-primary text-white">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users fa-2x me-3"></i>
                                <h5 class="card-title mb-0">Statistik Pengguna</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2 id="totalUsers" class="text-center text-primary">0</h2>
                            <p class="text-center">Jumlah Pengguna Terdaftar</p>
                        </div>
                        <div class="card-footer text-center text-muted">
                            <small>Terakhir diperbarui 2 hari lalu</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-gradient-success text-white">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-dollar-sign fa-2x me-3"></i>
                                <h5 class="card-title mb-0">Pendapatan Bulanan</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2 id="totalRevenue" class="text-center text-success">0</h2>
                            <p class="text-center">Total Pendapatan Bulan Ini</p>
                        </div>
                        <div class="card-footer text-center text-muted">
                            <small>Terakhir diperbarui 1 hari lalu</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-gradient-warning text-white">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-box fa-2x me-3"></i>
                                <h5 class="card-title mb-0">Pesanan Baru</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2 id="totalOrders" class="text-center text-warning">0</h2>
                            <p class="text-center">Pesanan Baru Hari Ini</p>
                        </div>
                        <div class="card-footer text-center text-muted">
                            <small>Terakhir diperbarui 3 jam lalu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <canvas id="userChart"></canvas>
                </div>
                <div class="col-md-6 mb-4">
                    <canvas id="bubbleChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
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

        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show-sidebar");
        }

        // Animasi untuk grafik Chart.js
        const chartAnimation = {
            duration: 2000,
            easing: 'easeOutBounce',
        };

        // Radar Chart Animasi dengan Warna Baru
        const userChartCtx = document.getElementById('userChart').getContext('2d');
        new Chart(userChartCtx, {
            type: 'radar',
            data: {
                labels: <?php echo json_encode($user_roles); ?>,
                datasets: [{
                    label: 'Users Distribution',
                    data: <?php echo json_encode($user_counts); ?>,
                    backgroundColor: 'rgba(100, 181, 246, 0.2)', // Biru muda transparan
                    borderColor: 'rgba(33, 150, 243, 1)', // Biru lebih gelap
                    borderWidth: 3,
                    pointBackgroundColor: '#1976D2', // Biru tua untuk titik data
                    pointRadius: 6, // Ukuran titik data lebih besar
                    pointHoverRadius: 8, // Ukuran titik saat hover lebih besar
                    pointHoverBackgroundColor: '#0D47A1', // Biru gelap saat hover
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        grid: {
                            color: '#E3F2FD', // Grid berwarna biru muda
                            lineWidth: 1
                        },
                        angleLines: {
                            color: '#BBDEFB', // Garis sudut lebih terang
                        },
                        ticks: {
                            display: true,
                            backdropColor: 'rgba(255, 255, 255, 0.7)', // Latar belakang nilai sumbu lebih terang
                            color: '#0D47A1', // Warna angka pada sumbu lebih gelap
                            font: {
                                size: 14
                            }
                        },
                        pointLabels: {
                            color: '#1E88E5', // Warna label titik data
                            font: {
                                size: 16,
                                weight: 'bold',
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top', // Posisi legend di atas
                        labels: {
                            font: {
                                size: 16,
                                family: 'Arial, sans-serif'
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000, // Durasi animasi lebih lama untuk kesan halus
                    easing: 'easeInOutQuart' // Jenis easing untuk animasi lebih halus
                },
            }
        });

        // Bar Chart dengan Gradient, 3D, dan Animasi Dinamis
        fetch('get_sales_data.php')
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.nama_produk);
                const totalHarga = data.map(item => item.total_harga);

                const ctx = document.getElementById('bubbleChart').getContext('2d');

                // Membuat Gradient untuk Background
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(33, 150, 243, 0.8)'); // Biru Terang
                gradient.addColorStop(1, 'rgba(33, 150, 243, 0.3)'); // Biru Pudar

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Harga',
                            data: totalHarga,
                            backgroundColor: gradient, // Menggunakan Gradient
                            borderColor: 'rgba(33, 150, 243, 1)', // Biru Solid
                            borderWidth: 2,
                            hoverBackgroundColor: 'rgba(33, 150, 243, 0.9)', // Biru lebih gelap saat hover
                            hoverBorderColor: 'rgba(33, 150, 243, 1)', // Border lebih tebal saat hover
                            hoverBorderWidth: 3,
                            barPercentage: 0.8, // Lebar bar sedikit lebih ramping
                            borderRadius: 5, // Menambahkan rounded corners pada bar
                            shadowColor: 'rgba(0, 0, 0, 0.3)', // Efek shadow untuk kedalaman
                            shadowBlur: 10, // Efek blur pada shadow
                            shadowOffsetX: 5, // Posisi shadow horizontal
                            shadowOffsetY: 5, // Posisi shadow vertikal
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    color: '#E1F5FE', // Warna grid X lebih terang
                                },
                                ticks: {
                                    color: '#0288D1', // Warna label X lebih cerah
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#E1F5FE', // Warna grid Y lebih terang
                                },
                                ticks: {
                                    color: '#0288D1', // Warna label Y lebih cerah
                                    font: {
                                        size: 14,
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 16,
                                        family: 'Arial, sans-serif',
                                        weight: 'bold'
                                    },
                                    color: '#0288D1', // Warna legend lebih cerah
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.7)', // Latar belakang tooltip
                                titleColor: '#FFFFFF', // Warna judul tooltip
                                bodyColor: '#FFFFFF', // Warna isi tooltip
                                borderColor: '#0288D1', // Warna border tooltip
                                borderWidth: 2,
                                caretSize: 8, // Ukuran ekor tooltip
                                cornerRadius: 5, // Radius sudut tooltip
                                displayColors: false, // Menghilangkan warna di dalam tooltip
                            }
                        },
                        animation: {
                            duration: 2000, // Durasi animasi lebih panjang
                            easing: 'easeOutElastic', // Efek animasi lebih elastis
                            animateScale: true, // Efek animasi skala
                            animateRotate: true, // Efek rotasi saat animasi
                        },
                    }
                });
            });
             // Fungsi untuk animasi angka
    function animateNumber(element, start, end, duration) {
        let current = start;
        const range = end - start;
        const increment = range / (duration / 10); // Jumlah penambahan setiap interval
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 10); // Update setiap 10ms
    }

    // Mulai animasi setelah halaman selesai dimuat
    document.addEventListener("DOMContentLoaded", () => {
        const totalUsers = <?= $total_users; ?>;
        const totalRevenue = <?= $total_revenue; ?>;
        const totalOrders = <?= $total_orders; ?>;

        animateNumber(document.querySelector("#totalUsers"), 0, totalUsers, 2000);
        animateNumber(document.querySelector("#totalRevenue"), 0, totalRevenue, 2000);
        animateNumber(document.querySelector("#totalOrders"), 0, totalOrders, 2000);
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>