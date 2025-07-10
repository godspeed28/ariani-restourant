<?php
session_start();
$_SESSION = []; 
session_unset();  // Menghapus semua data session
session_destroy();  // Menghancurkan session

// Hapus cookie
setcookie('id','',time() - 3600);
setcookie('key','',time() - 3600);

header('Location: post_logout.php');  // Arahkan ke halaman login setelah logout
exit();
