<?php
// =============================================
// FILE: masjid/delete.php
// FUNGSI: Proses hapus data masjid
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil nama untuk pesan
    $query = "SELECT nama_masjid FROM masjid WHERE id = $id";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    $nama = $data ? $data['nama_masjid'] : 'Data';
    
    $query_delete = "DELETE FROM masjid WHERE id = $id";
    
    if (mysqli_query($koneksi, $query_delete)) {
        $_SESSION['message'] = "✅ Data masjid '$nama' berhasil dihapus!";
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
