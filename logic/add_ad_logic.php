<?php

require '../functions.php';

function handleAdSubmission($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $end_date = $_POST['end_date'];

        // Validasi input
        if (empty($title) || empty($content) || empty($end_date)) {
            return "Semua field harus diisi.";
        }

        // Handle image upload
        $imageUrl = null;
        $targetDir = "../img/";

        if (!empty($_FILES['image']['name'])) {
            // Validasi ekstensi gambar
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $imageExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (!in_array($imageExtension, $allowedExtensions)) {
                return "Format gambar tidak diperbolehkan. Harus JPG, PNG, atau WEBP.";
            }

            // Validasi ukuran gambar
            $maxSize = 5 * 1024 * 1024; // 5 MB
            if ($_FILES['image']['size'] > $maxSize) {
                return "Ukuran gambar terlalu besar. Maksimal 5MB.";
            }

            // Validasi apakah file benar-benar gambar
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);
            if ($imageInfo === false) {
                return "File yang diunggah bukan gambar.";
            }

            // Mengunggah gambar
            $imageName = uniqid() . '.' . $imageExtension;
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imageUrl = $imageName; // Simpan hanya nama file untuk referensi ke direktori img
            } else {
                return "Gagal mengunggah gambar!";
            }
        }

        // Simpan data ke database
        $query = "INSERT INTO ads (title, content, image_url, end_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $title, $content, $imageUrl, $end_date);

        if ($stmt->execute()) {
            return "Iklan berhasil ditambahkan!";
        } else {
            return "Gagal menambahkan iklan: " . $conn->error;
        }
    }
}

function fetchAllAds($conn) {
    $query = "SELECT id, title, content, image_url, is_active, end_date FROM ads";
    $result = $conn->query($query);
    return $result;
}
