<?php
// mark_all_read.php
require '../functions.php'; // Ganti dengan file koneksi database Anda

$query = "UPDATE notifications SET status = 'read' WHERE status = 'unread'";
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
?>
