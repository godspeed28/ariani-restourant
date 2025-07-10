<?php

require '../functions.php';

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: ../post_logout.php');
    exit();
}

if (empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang belanja kosong.'); window.location.href = 'menu.php';</script>";
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wm_aryani";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total = 0;

$keranjang = $_SESSION['keranjang'];
$stok_kurang = [];

// Periksa stok produk
foreach ($keranjang as $id_produk => $item) {
    $sql = "SELECT stok FROM menu WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_produk);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produk = $result->fetch_assoc();
        $stok_tersedia = intval($produk['stok']);

        if ($item['kuantitas'] > $stok_tersedia) {
            $stok_kurang[] = [
                'nama' => $item['nama'],
                'kuantitas_diminta' => $item['kuantitas'],
                'stok_tersedia' => $stok_tersedia,
            ];
        }
    }
}

if (!empty($stok_kurang)) {
    // Kosongkan keranjang belanja setelah proses checkout
    unset($_SESSION['keranjang']);
    // Tampilkan pesan error jika stok kurang
    echo "<script>alert('Beberapa produk melebihi stok yang tersedia. Mohon periksa kembali.');
    window.location.href = 'menu.php';
    </script>";
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aryani GO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" type="/image/png" href="../img/garpu.jpg" />
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #343A40;
            color: white;
        }

        .btn-success {
            background-color: #ff7f00;
            border-color: #ff7f00;
        }

        .btn-success:hover {
            background-color: #e66d00;
            border-color: #e66d00;
        }

        .menu-table th,
        .menu-table td {
            vertical-align: middle;
            text-align: center;
        }

        .footer {
            background-color: #343A40;
            color: white;
            padding: 10px 0;
        }

        .footer small {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .footer small i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <img src="../img/garpu.jpg" alt="Logo" style="width:50px; border-radius:50%; margin-right:15px;">
                <h4 class="mb-0">Aryani GO</h4>
            </div>
            <div class="card-body">
                <h5 class="mb-4"><i class="fas fa-shopping-cart"></i> Rincian Pesanan Anda</h5>
                <table class="table table-striped menu-table">
                    <thead>
                        <tr>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($_SESSION['keranjang'] as $id_produk => $item):
                            $sql = "SELECT * FROM menu WHERE id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('i', $id_produk);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0):
                                $produk = $result->fetch_assoc();
                                $nama_produk = htmlspecialchars($produk['nama']);
                                $harga_produk = (float) $produk['harga'];
                                $kuantitas = (int) $item['kuantitas'];
                                $total_harga = $harga_produk * $kuantitas;
                                $total += $total_harga;
                        ?>
                                <tr>
                                    <td><?= $nama_produk ?></td>
                                    <td>Rp <?= number_format($harga_produk, 0, ',', '.') ?></td>
                                    <td><?= $kuantitas ?></td>
                                    <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
                            <?php endif;
                        endforeach; ?>
                            <?php
                            $total_semua = $total + 7000;
                            ?>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Biaya Pengiriman</td>
                                    <td class="fw-bold">Rp. 7.000</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Pembayaran</td>
                                    <td class="fw-bold">Rp <?= number_format($total_semua, 0, ',', '.') ?></td>
                                    <?php 
                                   
                                    $_SESSION['total_pembayaran'] = $total_semua;?>
                                </tr>
                    </tbody>
                </table>

                <form action="proses_checkout.php" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="nama_penerima" class="form-label"><i class="fas fa-user"></i> Nama Penerima</label>
                        <input type="text" id="nama_penerima" name="nama_penerima" class="form-control" placeholder="Masukkan nama penerima" required>
                    </div>
                    <div class="mb-3">
                        <label for="telepon" class="form-label"><i class="fas fa-phone"></i> Nomor Telepon</label>
                        <input type="tel" id="telepon" name="telepon" class="form-control" placeholder="Masukkan nomor telepon" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label"><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</label>
                        <textarea id="alamat" name="alamat" class="form-control" rows="4" placeholder="Masukkan alamat pengiriman" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="metode_pembayaran" class="form-label"><i class="fas fa-credit-card"></i> Metode Pembayaran</label>
                        <select id="metode_pembayaran" name="metode_pembayaran" class="form-select" required>
                            <option value="" disabled selected>Pilih metode pembayaran</option>
                            <option value="COD">Cash On Delivery (COD)</option>
                            <option value="Midtrans(E-Wallet, Kartu Kredit/Debit, Transfer Bank, QRIS)">Pembayaran Fleksibel</option> <!-- Add this option -->
                        </select>
                    </div>
                    <button id="bayarButton" type="submit" class="btn btn-success d-block w-100"><i class="fas fa-check-circle"></i> Konfirmasi Pembelian</button>
                </form>
            </div>
            <div class="footer text-center">
                <small> <i class="bi bi-emoji-smile-fill text-warning"></i> Terima kasih telah berbelanja di Aryani GO</small>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


<?php
$conn->close();
?>