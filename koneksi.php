<?php
// =============================================
// FILE: koneksi.php
// FUNGSI: Koneksi database dan fungsi-fungsi helper
// =============================================

// ===== KONFIGURASI DATABASE =====
$host = 'localhost';
$username = 'root';
$passwor = '';
$database = 'kajian_islam';

// ===== MEMBUAT KONEKSI =====
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset ke UTF-8
mysqli_set_charset($koneksi, "utf8mb4");

// =============================================
// FUNGSI-FUNGSI HELPER
// =============================================

/**
 * Membersihkan input dari XSS dan SQL Injection
 */
function bersihkan($data) {
    global $koneksi;
    return mysqli_real_escape_string($koneksi, htmlspecialchars(strip_tags(trim($data))));
}

/**
 * Menampilkan notifikasi
 */
function notifikasi($pesan, $tipe = 'success') {
    $icon = $tipe == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    $class = $tipe == 'success' ? 'alert-success' : 'alert-danger';
    return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
        <i class="bi ' . $icon . ' me-2"></i>' . $pesan . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

/**
 * Format tanggal Indonesia
 */
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

/**
 * Format jam
 */
function format_jam($jam) {
    return date('H:i', strtotime($jam));
}

/**
 * Mendapatkan tanggal Hijriyah sederhana
 */
function getIslamicDate() {
    $islamicMonths = [
        'Muharram', 'Safar', 'Rabi\'ul Awwal', 'Rabi\'ul Akhir',
        'Jumadil Ula', 'Jumadil Akhir', 'Rajab', 'Sya\'ban',
        'Ramadhan', 'Syawal', 'Dzulqa\'dah', 'Dzulhijjah'
    ];
    $hijriYear = 1446;
    $hijriMonth = (int)date('n') % 12;
    $hijriDay = (int)date('j') % 30;
    return $hijriDay . ' ' . $islamicMonths[$hijriMonth] . ' ' . $hijriYear . ' H';
}

/**
 * Cek apakah user sudah login
 */
function cekLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: index.php');
        exit();
    }
}

/**
 * Cek role user (admin atau user)
 */
function cekRole($role = 'admin') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {
        header('Location: index.php');
        exit();
    }
}

/**
 * Ambil data user berdasarkan username
 */
function getUserByUsername($username) {
    global $koneksi;
    $username = bersihkan($username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_fetch_assoc($result);
}

/**
 * Cek apakah username sudah terdaftar
 */
function cekUsername($username) {
    global $koneksi;
    $username = bersihkan($username);
    $query = "SELECT id FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    return mysqli_num_rows($result) > 0;
}

/**
 * Registrasi user baru
 */
function registrasiUser($data) {
    global $koneksi;
    
    $username = bersihkan($data['username']);
    $password = $data['password'];
    $nama_lengkap = bersihkan($data['nama_lengkap']);
    $email = isset($data['email']) ? bersihkan($data['email']) : '';
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (username, password, nama_lengkap, email) 
              VALUES ('$username', '$hashed_password', '$nama_lengkap', '$email')";
    
    return mysqli_query($koneksi, $query);
}

/**
 * Login user
 */
function loginUser($username, $password) {
    global $koneksi;
    
    $username = bersihkan($username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}
?>
