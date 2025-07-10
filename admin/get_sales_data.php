<?php

// Pastikan file functions.php ada jika dibutuhkan
require '../functions.php';

// Koneksi ke database
$host = 'localhost';
$dbname = 'wm_aryani';
$username = 'root'; // Ganti dengan username database Anda
$password = ''; // Ganti dengan password database Anda

try {
    // Membuat koneksi ke database menggunakan PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query untuk mendapatkan total harga per produk
    $query = "SELECT nama_produk, SUM(total_harga) AS total_harga 
    FROM sales_report 
    WHERE stat != 'Dibatalkan' 
    AND status_pembayaran NOT IN('Belum bayar','Kedaluwarsa')
    GROUP BY nama_produk";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Mengambil hasil query sebagai array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mengonversi data menjadi format JSON
    $jsonData = json_encode($result);
    
    // Memeriksa apakah terjadi kesalahan dalam pengkodean JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Jika ada kesalahan JSON, tampilkan error
        echo 'JSON Error: ' . json_last_error_msg();
    } else {
        // Jika tidak ada kesalahan, kirimkan data JSON
        echo $jsonData;
    }

} catch (PDOException $e) {
    // Menangani error yang terjadi selama koneksi atau query
    echo "Error: " . $e->getMessage();
} catch (Exception $e) {
    // Menangani error umum lainnya
    echo "Error: " . $e->getMessage();
} finally {
    // Menutup koneksi setelah selesai
    $conn = null;
}

?>
