<?php

require '../functions.php';

session_start();

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
  // Jika bukan admin, arahkan ke login
  header('Location: ../post_logout.php');
  exit();
}

$id = $_GET["id"];

if (hapus($id) > 0) {
  echo "
    <script>
    alert('data berhasil dihapus!');
    document.location.href = 'product.php';
    </script>
    ";
} else {
  echo "
    <script>
     alert('data gagal dihapus!');
      document.location.href = 'product.php';
     </script>
    ";
}
