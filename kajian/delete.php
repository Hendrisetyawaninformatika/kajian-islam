<?php
// =============================================
// FILE: kajian/delete.php
// FUNGSI: Proses hapus data kajian
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil judul untuk pesan
    $query = "SELECT judul FROM kajian WHERE id = $id";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    $judul = $data ? $data['judul'] : 'Data';
    
    $query_delete = "DELETE FROM kajian WHERE id = $id";
    
    if (mysqli_query($koneksi, $query_delete)) {
        $_SESSION['message'] = "✅ Data kajian '$judul' berhasil dihapus!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "❌ Error: " . mysqli_error($koneksi);
        $_SESSION['message_type'] = 'error';
    }
} else {
    $_SESSION['message'] = "❌ ID tidak valid!";
    $_SESSION['message_type'] = 'error';
}

header('Location: read.php');
exit();
?>