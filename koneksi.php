<?php
// =============================================
// FILE: koneksi.php
// FUNGSI: Menghubungkan aplikasi ke database MySQL
// =============================================

// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'kajian_islam';

// Membuat koneksi ke MySQL
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set karakter encoding ke UTF-8 untuk mendukung huruf Arab
mysqli_set_charset($koneksi, "utf8mb4");

// Fungsi untuk membersihkan input dari XSS
function bersihkan($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(strip_tags($data)));
}

// Fungsi untuk menampilkan notifikasi
function notifikasi($pesan, $tipe = 'success') {
    $icon = $tipe == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    $class = $tipe == 'success' ? 'alert-success' : 'alert-danger';
    return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
        <i class="bi ' . $icon . ' me-2"></i>' . $pesan . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

// Fungsi untuk format tanggal Indonesia
function format_tanggal($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[(int)date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    return $tgl . ' ' . $bln . ' ' . $thn;
}

// Fungsi untuk format jam
function format_jam($jam) {
    return date('H:i', strtotime($jam));
}
?>