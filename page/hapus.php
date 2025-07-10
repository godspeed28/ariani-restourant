<?php  

session_start();

require '../functions.php';

$id = $_GET["id"];

if(hapus_pesan($id) > 0){
    echo"
    <script>
    alert('Pesan berhasil dihapus!');
    document.location.href = 'menu.php';
    </script>
    ";
}else{
    echo"
    <script>
     alert('Pesan gagal dihapus!');
      document.location.href = 'menu.php';
     </script>
    ";
}
?>