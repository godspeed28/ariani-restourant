<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = intval($_POST['id_produk']);

    if (isset($_SESSION['keranjang'][$id_produk])) {
        unset($_SESSION['keranjang'][$id_produk]);
    }
}

echo "<script>alert('produk berhasil dihapus!');
window.location.href = 'add_cart.php';</script> ";
