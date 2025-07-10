<?php
require '../functions.php';

session_start();

$stmt = $conn->prepare("SELECT * FROM sales_report ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
$all_pesanan = $result->fetch_all(MYSQLI_ASSOC);

if (isset($_POST['update_status'])) {
    $status_baru = $_POST['status'];
    $id_pesanan = $_POST['id_pesanan'];

    $stmt_update = $conn->prepare("UPDATE sales_report SET stat = ? WHERE id = ?");
    $stmt_update->bind_param('si', $status_baru, $id_pesanan);

    if ($stmt_update->execute()) {
        echo "<script>alert('Status berhasil diperbarui.'); window.location.href = 'orders.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status: {$stmt_update->error}');</script>";
    }
}
if (isset($_POST['update_status_pembayaran'])) {
    $status_baru = $_POST['status_pembayaran'];
    $id_pesanan = $_POST['id_pesanan'];

    $stmt_update = $conn->prepare("UPDATE sales_report SET status_pembayaran = ? WHERE id = ?");
    $stmt_update->bind_param('si', $status_baru, $id_pesanan);

    if ($stmt_update->execute()) {
        echo "<script>alert('Status berhasil diperbarui.'); window.location.href = 'orders.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status: {$stmt_update->error}');</script>";
    }
}
$revenue_by_date = [];
$revenue_by_date = [];

foreach ($all_pesanan as $pesanan) {
    // Filter data yang tidak berstatus 'Dibatalkan' atau 'Kadaluwarsa'
    if ($pesanan['stat'] === 'Dibatalkan' || $pesanan['status_pembayaran'] === 'Belum bayar' || $pesanan['status_pembayaran'] === 'Kedaluwarsa') {
        continue; // Lewati data ini
    }
    if ($pesanan['status_pembayaran'] === 'Kadaluwarsa' || $pesanan['stat'] === 'Dibatalkan') {
        continue; // Lewati data ini
    }

    $date = date('Y-m-d', strtotime($pesanan['tanggal_transaksi']));
    if (!isset($revenue_by_date[$date])) {
        $revenue_by_date[$date] = 0;
    }
    $revenue_by_date[$date] += $pesanan['total_harga'];
}
$revenue_dates = json_encode(array_keys($revenue_by_date));
$revenue_values = json_encode(array_values($revenue_by_date));

if (isset($_GET['mark_as_read'])) {
    $notif_id = intval($_GET['mark_as_read']);
    $update_sql = "UPDATE notifications SET status = 'read' WHERE id = $notif_id";
    $conn->query($update_sql);
    header("Location: orders.php");
    exit;
}

// Query untuk mendapatkan email

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
    <title>Orders - Aryani GO</title>
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
            padding-left: 10px;
            padding-right: 10px;
            position: absolute;
            right: 1px;
            background-color: white;
            z-index: 1000;
        }

        .sidebar .dropdown-toggle:hover+.dropdown-menu,
        .sidebar .dropdown-menu:hover {
            background-color: darkorange;
            display: contents;

        }

        @media (max-width: 768px) {
            .sidebar .dropdown-menu {
                position: relative;
                width: auto;
            }
        }

        @media (max-width: 768px) {
            .navbar-nav .nav-item {
                margin-bottom: 10px;
            }

            .navbar-nav .btn {
                margin-right: auto;
                margin-top: 10px;
            }
        }

        #revenueChart {
            max-width: 100%;
            height: 900px;
            ;
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar" style="border-radius: 5px;">
        <h5 style="padding-left:10px;"><img src="../img/garpu.jpg" alt=""
                style="width:50px; color:white; border-radius:100%;" class="gambar">&nbsp;&nbsp;Side Bar</h5>
        <hr style="width: 50%; margin: 0 auto; border: 1px solid ;">
        <br>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="#" class="active">Orders</a>
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
                <a class="navbar-brand" href="#">Orders</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <div class="dropdown">
                                <button class="btn btn-light position-relative" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="fa-solid fa-bell fa-1x"></i>
                                    <?php if ($unread_count > 0): ?>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <?= $unread_count ?>
                                        </span>
                                    <?php endif; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" class="position">
                                    <li class="dropdown-header">Notifikasi Baru</li>
                                    <?php while ($row = $result2->fetch_assoc()): ?>
                                        <li class="dropdown-item">
                                            <p class="mb-1 text-truncate text-break"
                                                style="max-width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <?= htmlspecialchars(substr($row['message'], 0, 50)) ?>
                                                <?php if (strlen($row['message']) > 50): ?>...<?php endif; ?>
                                            </p>
                                            <small class="text-muted"><?= $row['created_at'] ?></small>
                                            <a href="orders.php?mark_as_read=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-success mt-1"> <i class="fas fa-envelope-open"></i>
                                                Tandai Sudah Dibaca</a>
                                        </li>
                                    <?php endwhile; ?>
                                    <?php if ($result2->num_rows == 0): ?>
                                        <li class="dropdown-item text-center text-muted">Tidak ada notifikasi baru.</li>
                                    <?php endif; ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li class="text-center"><a href="all_notifications.php"
                                            class="btn btn-sm btn-primary" id="markAllRead"><i
                                                class="fas fa-expand"></i> Lihat Semua</a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="profil.php">Profile</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-5">
            <div class="row">
                <div class="col-12">
                    <canvas id="revenueChart" style="margin-top: 0px;"></canvas>
                </div>
            </div>
        </div>
        <div class="container margin">
            <style>
                .margin {
                    margin-top: 1rem !important;
                }
            </style>
            <div class="table-responsive">
                <table class="table table-striped table-hover" style="width: 100%; font-size: 12px; margin: auto;">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No</th>
                            <th><i class="fa-solid fa-box-open"></i> Nama Produk</th>
                            <th><i class="fa-solid fa-user"></i> Penerima</th>
                            <th><i class="fa-solid fa-location-dot"></i> Alamat</th>
                            <th><i class="fas fa-envelope-open"></i> E-mail</th>
                            <th><i class="fa-solid fa-phone"></i> Telepon</th>
                            <th><i class="fa-solid fa-credit-card"></i> Metode Pembayaran</th>
                            <th><i class="fa-solid fa-money-bill-wave"></i> Total Harga</th>
                            <th><i class="fa-solid fa-calendar"></i> Tanggal Transaksi</th>
                            <th><i class="fa-solid fa-clipboard-check"></i> Status Pengiriman</th>
                            <th><i class="fa-solid fa-gears"></i> Aksi</th>
                            <th><i class="fas fa-coins"></i> Status Pembayaran</th>
                            <th><i class="fa-solid fa-gears"></i> Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_pesanan as $index => $pesanan): ?>
                            <tr class="text-center">
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($pesanan['nama_produk']) ?></td>
                                <td><?= htmlspecialchars($pesanan['nama_penerima']) ?></td>
                                <td><?= htmlspecialchars($pesanan['alamat']) ?></td>
                                <td>
                                    <?php $sql = "SELECT u.email  FROM users u JOIN sales_report s ON u.id = s.user_id WHERE s.user_id ='{$pesanan['user_id']}' AND u.role = 'user'";
                                    $result3 = $conn->query($sql);
                                    ?>
                                    <?php if ($result3->num_rows > 0) : ?>
                                        <?php if ($row = $result3->fetch_assoc()) : ?>
                                            <?= htmlspecialchars($row['email']) ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($pesanan['telepon']) ?></td>
                                <td><?= htmlspecialchars($pesanan['metode_pembayaran']) ?></td>
                                <td><?= htmlspecialchars(number_format($pesanan['total_harga'], 0, ',', '.')) ?> IDR</td>
                                <td><?= htmlspecialchars($pesanan['tanggal_transaksi']) ?></td>
                                <td>
                                    <span
                                        class="badge <?= $pesanan['stat'] == 'Sedang Diproses' ? 'bg-warning' : ($pesanan['stat'] == 'Sedang Diantar' ? 'bg-info' : ($pesanan['stat'] == 'Selesai' ? 'bg-success' : 'bg-danger')) ?>">
                                        <i
                                            class="fa-solid <?= $pesanan['stat'] == 'Sedang Diproses' ? 'fa-hourglass-start' : ($pesanan['stat'] == 'Sedang Diantar' ? 'fa-truck' : ($pesanan['stat'] == 'Selesai' ? 'fa-check-circle' : 'fa-times-circle')) ?>">
                                        </i> <?= htmlspecialchars($pesanan['stat']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id'] ?>">
                                        <div class="input-group">
                                            <select name="status" class="form-select form-select-sm">
                                                <option value="Sedang Diproses" <?= $pesanan['stat'] == 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
                                                <option value="Sedang Diantar" <?= $pesanan['stat'] == 'Sedang Diantar' ? 'selected' : '' ?>>Sedang Diantar</option>
                                                <option value="Selesai" <?= $pesanan['stat'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                                <option value="Dibatalkan" <?= $pesanan['stat'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </td>

                                <td>
                                    <span
                                        class="badge 
        <?= $pesanan['status_pembayaran'] == 'Belum bayar' ? 'bg-info' : ($pesanan['status_pembayaran'] == 'Sudah bayar' ? 'bg-success' : ($pesanan['status_pembayaran'] == 'Kedaluwarsa' ? 'bg-danger' : 'bg-success')) ?>">
                                        <i
                                            class="fa-solid <?= $pesanan['status_pembayaran'] == 'Belum bayar' ? 'fa-hourglass-start' : ($pesanan['status_pembayaran'] == 'Sudah bayar' ? 'fa-check-circle' : ($pesanan['status_pembayaran'] == 'Kedaluwarsa' ? 'fa-times-circle' : 'fa-check-circle')) ?>">
                                        </i> <?= htmlspecialchars($pesanan['status_pembayaran']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="id_pesanan" value="<?= $pesanan['id'] ?>">
                                        <div class="input-group">
                                            <select name="status_pembayaran" class="form-select form-select-sm">
                                                <option value="Belum bayar" <?= $pesanan['status_pembayaran'] == 'Belum bayar' ? 'selected' : '' ?>>Belum bayar</option>
                                                <option value="Sudah bayar" <?= $pesanan['status_pembayaran'] == 'Sudah bayar' ? 'selected' : '' ?>>Sudah bayar</option>
                                                <option value="Kedaluwarsa" <?= $pesanan['status_pembayaran'] == 'Kedaluwarsa' ? 'selected' : '' ?>>
                                                    Kedaluwarsa</option>
                                            </select>
                                            <button type="submit" name="update_status_pembayaran"
                                                class="btn btn-success btn-sm">
                                                <i class="fa-solid fa-save"></i> Simpan
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.toggle("show-sidebar");
        }
        document.getElementById('markAllRead').addEventListener('click', function(event) {
            event.preventDefault();

            fetch('mark_all_read.php', {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = 'all_notifications.php';
                    } else {
                        alert('Gagal menandai semua pesan sebagai terbaca: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
        const revenueDates = <?= $revenue_dates ?>;
        const revenueValues = <?= $revenue_values ?>;

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueDates,
                datasets: [{
                    label: 'Total Pendapatan (IDR)',
                    data: revenueValues,
                    borderColor: '#2980b9',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {},
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Pendapatan (IDR)'
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>