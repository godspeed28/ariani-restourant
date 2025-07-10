<?php

require '../functions.php';

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../post_logout.php');
    exit();
}

if (!isset($_SESSION['username']) || !isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header('Location: ../post_logout.php');
    exit;
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    echo "Admin tidak ditemukan.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="../img/garpu.jpg" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Profil Admin - Aryani GO</title>
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 900px;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-body {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #333;
        }

        .form-label {
            font-weight: 500;
        }

        .small-text {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">
            <i class="fas fa-user-cog"></i> Profil Admin
        </h2>
        <!-- Kartu Informasi Pribadi -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-info-circle"></i> Informasi Pribadi
                </h5>
                <p><strong><i class="fas fa-user"></i> Nama:</strong> <?= htmlspecialchars($admin['username']); ?></p>
                <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?= htmlspecialchars($admin['email']); ?></p>
                <p><strong><i class="fas fa-user-tag"></i> Role:</strong> <?= htmlspecialchars($admin['role']); ?></p>
            </div>
        </div>
        <!-- Kartu Edit Profil -->
        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-edit"></i> Edit Profil
                </h5>
                <form action="update_profil.php" method="post" enctype="multipart/form-data">
                    <!-- Input Nama -->
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Nama
                        </label>
                        <input type="text" class="form-control" id="username" name="username"
                            value="<?= $admin['username']; ?>" required>
                    </div>
                    <!-- Input Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $admin['email']; ?>"
                            required>
                    </div>
                    <!-- Input Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password Baru
                        </label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted small-text">Kosongkan jika tidak ingin mengganti
                            password.</small>
                    </div>
                    <!-- Tombol Simpan -->
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>