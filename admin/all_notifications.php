<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wm_aryani";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil semua notifikasi
$notif_sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = $conn->query($notif_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Notifikasi - Aryani GO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
</head>

<body>
    <div class="container my-5">
        <style>
            .text-start {
                font-family: 'Oswald', cursive;
            }
        </style>
        <h4 class="text-start"><i class="fas fa-bell"></i> Semua Notifikasi</h4>
        <div class="list-group">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list-group-item">
                    <p><?= $cleanedText = html_entity_decode($row['message']) ?></p>
                    <small class="text-muted"><?= $row['created_at'] ?></small>
                    <span class="badge <?= $row['status'] == 'unread' ? 'bg-danger' : 'bg-success' ?>">
                        <?= $row['status'] == 'unread' ? 'Belum Dibaca' : '<i class="fas fa-envelope-open"></i> Sudah Dibaca' ?>
                    </span>
                </div>
            <?php endwhile; ?>
            <?php if ($result->num_rows == 0): ?>
                <div class="alert alert-info">Tidak ada notifikasi.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>