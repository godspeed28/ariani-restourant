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
