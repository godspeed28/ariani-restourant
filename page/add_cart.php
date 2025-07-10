<?php
session_start();
require '../functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: ../post_logout.php');
    exit();
}

// Validasi apakah form dikirimkan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = intval($_POST['id_produk']); // Konversi ke integer untuk keamanan
    $kuantitas = intval($_POST['kuantitas']);

    // Query untuk mengambil data produk berdasarkan ID
    $sql = "SELECT nama, harga FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_produk);
    $stmt->execute();
    $result = $stmt->get_result();

    // Validasi apakah produk ditemukan
    if ($result->num_rows == 0) {
        echo "<script>alert('Produk tidak ditemukan.'); window.location.href = 'menu.php';</script>";
        exit;
    }

    $produk = $result->fetch_assoc(); // Ambil data produk
    $nama_produk = $produk['nama'];
    $harga_produk = $produk['harga'];

    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Tambahkan atau perbarui produk di keranjang
    if (isset($_SESSION['keranjang'][$id_produk]) && is_array($_SESSION['keranjang'][$id_produk])) {
        $_SESSION['keranjang'][$id_produk]['kuantitas'] += $kuantitas;
    } else {
        $_SESSION['keranjang'][$id_produk] = [
            'nama' => $nama_produk,
            'harga' => $harga_produk,
            'kuantitas' => $kuantitas
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" type="/image/png" href="../img/garpu.jpg" />
    <title>Cart - Aryani GO</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h4 {
            font-weight: 600;
            color: #343a40;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            font-weight: 600;
            border-radius: 20px;
            padding: 10px 20px;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .alert {
            background-color: #343a40;
            color: #f1f1f1;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h4 class="text-start mb-4"><img src="../img/garpu.jpg" alt=""
                style="width:50px; color:white; border-radius:50%;" class="gambar">Aryani GO</h4>
        <?php if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])): ?>
            <table class="table table-bordered table-hover">
                <thead class="text-center table-dark">
                    <tr>
                        <th>No</th>
                        <th><i class="bi bi-card-text"></i> &nbsp;Menu</th>
                        <th><i class="fas fa-tag"></i> &nbsp; Harga Satuan</th>
                        <th><i class="fas fa-sort-numeric-up-alt"></i>
                            &nbsp; Kuantitas</th>
                        <th> <i class="fas fa-coins"></i>
                            &nbsp;Total Harga</th>
                        <th> <i class="bi bi-gear"></i>&nbsp;Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $grand_total = 0;
                    foreach ($_SESSION['keranjang'] as $id => $item):
                        // Validasi data item untuk menghindari error
                        if (!is_array($item))
                            continue;
                        $total_harga = $item['harga'] * $item['kuantitas'];
                        $grand_total += $total_harga;
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($item['nama']); ?></td>
                            <td>Rp<?= number_format($item['harga'], 0, ',', '.'); ?></td>
                            <td class="text-center"><?= htmlspecialchars($item['kuantitas']); ?></td>
                            <td>Rp<?= number_format($total_harga, 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <form method="post" action="remove_cart.php" style="display:inline-block;">
                                    <input type="hidden" name="id_produk" value="<?= $id; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>
                                        &nbsp;Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Grand Total</th>
                        <th colspan="2">Rp<?= number_format($grand_total, 0, ',', '.'); ?></th>
                    </tr>
                </tfoot>
            </table>
            <div class="footer">
                <a href="menu.php" class="btn btn-secondary"><i class="fas fa-shopping-basket"></i>
                    Lanjut Belanja</a>
                <a href="checkout.php" class="btn btn-warning"><i class="fas fa-credit-card"></i>
                    Checkout</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                Keranjang belanja Anda kosong.
            </div>
            <div class="footer">
                <a href="menu.php" class="btn btn-warning"><i class="bi bi-cart-check"></i>
                    &nbsp; Belanja Sekarang</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>