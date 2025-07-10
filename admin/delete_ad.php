<?php
require '../functions.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus iklan berdasarkan ID
    $query = "DELETE FROM ads WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Iklan berhasil dihapus!'); window.location.href = 'add_ad.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus iklan: " . $conn->error . "'); window.history.back();</script>";
    }
}
