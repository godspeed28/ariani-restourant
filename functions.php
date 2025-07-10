<?php

//koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "WM_Aryani");

function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
// fungsi tambah data
function tambah($data)
{

    global $conn;


    $nama = htmlspecialchars($data["nama"]);
    $harga = htmlspecialchars($data["harga"]);
    $kategori = htmlspecialchars($data["kategori"]);
    $stok = htmlspecialchars($data["stok"]);

    // upload gambar
    $gambar = upload();
    if (!$gambar) {

        return false;
    }

    // query insert data
    $query = "INSERT INTO menu
    VALUES
    ('','$nama','$harga','$gambar','$kategori','$stok')
    ";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}
function upload()
{

    $namaFile = $_FILES['gambar']['name'];
    $ukuranFile = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    // cek apakah tidak ada gambar yang diupload
    if ($error === 4) {
        echo "<script>
        alert('pilih gambar terlebih dahulu!');
        </script>";
        return false;
    }
    // cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));
    if (!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo "<script>
        alert('yang anda upload bukan gambar!');
        </script>";
        return false;
    }
    // cek jika ukuranya terlalu besar 
    if ($ukuranFile > 1000000) {
        echo "<script>
        alert('ukuran gambar terlalu besar!');
        </script>";
        return false;
    }
    // lolos pengecekan, gambar siap diupload
    // generate nama gambar baru
    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;

    move_uploaded_file($tmpName, '../img/' . $namaFileBaru);
    return $namaFileBaru;
}
// fungsi hapus
function hapus($id)
{
    global $conn;
    mysqli_query($conn, "DELETE FROM menu WHERE id = $id");

    return mysqli_affected_rows($conn);
}
// fungsi update
function ubah($data)
{
    global $conn;

    $id = $data["id"];
    $nama = htmlspecialchars($data["nama"]);
    $harga = htmlspecialchars($data["harga"]);
    $kategori = htmlspecialchars($data["kategori"]);
    $stok = htmlspecialchars($data["stok"]);
    $gambarLama = htmlspecialchars($data["gambarLama"]);


    // cek apakah user pilih gambar baru atau tidak
    if ($_FILES['gambar']['error'] === 4) {
        $gambar = $gambarLama;
    } else {
        $gambar = upload();
    }
    // query update data
    $query = "UPDATE menu SET
               nama = '$nama',
                harga = '$harga',
                 gambar = '$gambar',
                  kategori = '$kategori',
                   stok = '$stok'
                   WHERE id = $id               
               ";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}
function hapus_pesan($id) {
    global $conn;

    $deleted_at = date("Y-m-d H:i:s"); // Menyimpan waktu penghapusan

    // Query untuk memperbarui data (soft delete)
    $query = "UPDATE sales_report SET
                delete_at = '$deleted_at'
              WHERE id = $id";

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}
// fungsi searching
function cari($keyword)
{
    global $conn;

    // Escape karakter khusus untuk mencegah SQL Injection
    $keyword = mysqli_real_escape_string($conn, $keyword);

    // Query pencarian
    $query = "SELECT * FROM menu WHERE 
                nama LIKE '%$keyword%' OR 
                kategori LIKE '%$keyword%' OR 
                harga LIKE '%$keyword%'";

    return $conn->query($query);
}
// Ambil jumlah notifikasi yang belum terbaca
$count_sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE status = 'unread'";
$count_result = $conn->query($count_sql);
$count_row = $count_result->fetch_assoc();
$unread_count = $count_row['unread_count'];

// Ambil notifikasi yang belum terbaca
$notif_sql = "SELECT * FROM notifications WHERE status = 'unread' ORDER BY created_at DESC LIMIT 5";
$result2 = $conn->query($notif_sql);

// Tandai notifikasi sebagai terbaca
if (isset($_GET['mark_as_read'])) {
    $notif_id = intval($_GET['mark_as_read']);
    $update_sql = "UPDATE notifications SET status = 'read' WHERE id = $notif_id";
    $conn->query($update_sql);
    header("Location: users.php"); // Refresh halaman setelah perubahan
    exit;
}

// 
function getSectionContent($section_name) {
    global $conn;
    $query = $conn->prepare("SELECT content, image FROM sections WHERE section_name = ?");
    $query->bind_param("s", $section_name);
    $query->execute();
    return $query->get_result()->fetch_assoc();
}

function updateSectionContent($section_name, $content, $image = null) {
    global $conn;
    if ($image) {
        $query = $conn->prepare("UPDATE sections SET content = ?, image = ?, updated_at = NOW() WHERE section_name = ?");
        $query->bind_param("sss", $content, $image, $section_name);
    } else {
        $query = $conn->prepare("UPDATE sections SET content = ?, updated_at = NOW() WHERE section_name = ?");
        $query->bind_param("ss", $content, $section_name);
    }
    return $query->execute();
}