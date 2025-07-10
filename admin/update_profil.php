<?php
require '../functions.php';
session_start();

// Validasi session
if (!isset($_SESSION['username']) || !isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header('Location: ../post_logout.php');
    exit;
}

$username = $_SESSION['username'];

// Fungsi untuk validasi input
function sanitize_input($input) {
    return strip_tags(trim($input));
}

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil dan sanitasi data yang dikirim melalui POST
    $newUsername = sanitize_input($_POST['username']);
    $newEmail = sanitize_input($_POST['email']);
    $newPassword = isset($_POST['password']) ? sanitize_input($_POST['password']) : '';

    // Validasi tambahan jika diperlukan (misalnya, format email)
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email tidak valid.'); window.location.href = 'profil.php';</script>";
        exit;
    }

    // Proses password jika ada perubahan
    if (!empty($newPassword)) {
        // Hash password baru
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    // Update query
    if (isset($hashedPassword)) {
        // Jika password baru diubah
        $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE username = ? AND role = 'admin'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $newUsername, $newEmail, $hashedPassword, $username);
    } else {
        // Jika password tidak diubah
        $sql = "UPDATE users SET username = ?, email = ? WHERE username = ? AND role = 'admin'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $newUsername, $newEmail, $username);
    }

    // Eksekusi query
    if ($stmt->execute()) {
        // Update session jika username atau email berubah
        $_SESSION['username'] = $newUsername;
        $_SESSION['email'] = $newEmail;

        // Redirect atau memberi tahu pengguna
        echo "<script>alert('Profil berhasil diperbarui.'); window.location.href = 'profil.php';</script>";
    } else {
        echo "Terjadi kesalahan saat memperbarui profil.";
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>
