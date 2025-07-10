<?php
require '../functions.php';

$id = $_GET['id'];
$status = $_GET['status']; // 1 = aktif, 0 = nonaktif

$query = "UPDATE ads SET is_active = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $status, $id);

if ($stmt->execute()) {
    echo "<script>alert('Berhasil memperbarui iklan!');
    window.location.href = 'add_ad.php'</script>";
} else {
    echo "Gagal memperbarui iklan.";
}
?>
