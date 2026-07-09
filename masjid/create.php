<?php
// =============================================
// FILE: masjid/create.php
// FUNGSI: Halaman tambah data masjid
// LOKASI: Batang, Jawa Tengah
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_masjid = bersihkan($_POST['nama_masjid']);
    $alamat = bersihkan($_POST['alamat']);
    $kecamatan = bersihkan($_POST['kecamatan']);
    $kota = bersihkan($_POST['kota']);
    $no_telepon = bersihkan($_POST['no_telepon']);
    
    if (empty($nama_masjid) || empty($alamat) || empty($kecamatan) || empty($kota)) {
        $error = 'Nama Masjid, Alamat, Kecamatan, dan Kota wajib diisi!';
    } else {
        $query = "INSERT INTO masjid (nama_masjid, alamat, kecamatan, kota, no_telepon) 
                  VALUES ('$nama_masjid', '$alamat', '$kecamatan', '$kota', '$no_telepon')";
        
        if (mysqli_query($koneksi, $query)) {
            $message = notifikasi('Data masjid berhasil ditambahkan!', 'success');
        } else {
            $error = 'Error: ' . mysqli_error($koneksi);
        }
    }
}

// Ambil total untuk sidebar & bottom nav
$total_kajian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kajian"))['total'];
$total_ustaz = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ustaz"))['total'];
$total_masjid = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM masjid"))['total'];

$lokasi = 'Batang, Jawa Tengah';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Masjid - Sistem Informasi Jadwal Kajian Islam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================
           RESET & BASE - SAMA DENGAN DASHBOARD
        ============================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8f7f4; min-height: 100vh; padding-bottom: 80px; }

        /* ============================================
           ISLAMIC BACKGROUND - SAMA DENGAN DASHBOARD
        ============================================ */
        .islamic-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(255,215,0,0.04) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 50%, rgba(255,215,0,0.02) 0%, transparent 60%),
                        linear-gradient(180deg, #fcfaf7 0%, #f5f0eb 100%);
            overflow: hidden;
        }
        .stars-container { position: absolute; width: 100%; height: 100%; pointer-events: none; }
        .star { position: absolute; border-radius: 50%; animation: twinkleStar var(--duration) ease-in-out infinite; }
        .star.gold { background: #FFD700; box-shadow: 0 0 10px rgba(255,215,0,0.2); }
        .star.white { background: #C5A55A; box-shadow: 0 0 8px rgba(197,165,90,0.1); }
        @keyframes twinkleStar { 0%,100% { opacity: 0.1; transform: scale(0.8); } 50% { opacity: 0.6; transform: scale(1.2); } }
        
        .moon-wrap { position: absolute; top: 5%; right: 5%; animation: floatMoon 12s ease-in-out infinite; }
        .moon { width: 60px; height: 60px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #ffe066, #f5a623); box-shadow: 0 0 60px rgba(255,215,0,0.1); position: relative; }
        .moon::after { content: ''; position: absolute; top: 9px; left: 17px; width: 20px; height: 20px; background: #fcfaf7; border-radius: 50%; opacity: 0.3; }
        @keyframes floatMoon { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-15px) rotate(3deg); } }
        
        .shooting-star { position: absolute; width: 100px; height: 2px; background: linear-gradient(to right, transparent, rgba(255,215,0,0.4), transparent); animation: shootStar 7s infinite linear; }
        .shooting-star:nth-child(1) { top: 10%; left: -100px; animation-delay: 0s; }
        .shooting-star:nth-child(2) { top: 30%; left: -100px; animation-delay: 3.5s; }
        .shooting-star:nth-child(3) { top: 50%; left: -100px; animation-delay: 7s; }
        @keyframes shootStar { 0% { transform: translateX(0) rotate(20deg); opacity: 0; } 8% { opacity: 1; } 85% { opacity: 1; } 100% { transform: translateX(1200px) rotate(20deg); opacity: 0; } }
        
        .geometric-pattern { position: absolute; bottom: -50px; left: -50px; width: 300px; height: 300px; opacity: 0.04; animation: rotatePattern 30s linear infinite; }
        .geometric-pattern::before { content: ''; position: absolute; width: 100%; height: 100%; background: radial-gradient(circle at 50% 50%, transparent 42%, #C5A55A 42%, #C5A55A 45%, transparent 45%), radial-gradient(circle at 50% 50%, transparent 22%, #C5A55A 22%, #C5A55A 25%, transparent 25%); border-radius: 50%; }
        @keyframes rotatePattern { 0% { transform: rotate(0deg) scale(1); } 50% { transform: rotate(180deg) scale(1.05); } 100% { transform: rotate(360deg) scale(1); } }
        
        .arabic-text { position: absolute; font-size: 24px; color: rgba(197,165,90,0.04); letter-spacing: 10px; font-family: 'Times New Roman', serif; animation: fadeArabic 20s ease-in-out infinite; }
        .arabic-text.top { top: 8%; left: 50%; transform: translateX(-50%); }
        .arabic-text.bottom { bottom: 10%; left: 50%; transform: translateX(-50%); }
        @keyframes fadeArabic { 0%,100% { opacity: 0.03; } 50% { opacity: 0.08; } }

        /* ============================================
           NAVBAR - SAMA DENGAN DASHBOARD
        ============================================ */
        .navbar-premium {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            padding: 10px 0;
            box-shadow: 0 2px 30px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255,215,0,0.06);
        }
        .navbar-premium .brand { font-size: 18px; font-weight: 700; color: #1a1a2e; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .navbar-premium .brand i { color: #FFD700; font-size: 24px; }
        .navbar-premium .brand .gold { color: #FFD700; }
        .navbar-premium .user-info { display: flex; align-items: center; gap: 12px; }
        .navbar-premium .user-info .user-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #FFD700, #f5a623); display: flex; align-items: center; justify-content: center; color: #0a0615; font-weight: 700; font-size: 14px; }
        .navbar-premium .user-info .user-name { font-size: 13px; font-weight: 500; color: #1a1a2e; }
        .navbar-premium .user-info .user-role { font-size: 9px; padding: 2px 12px; border-radius: 20px; background: linear-gradient(135deg, #FFD700, #f5a623); color: #0a0615; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .navbar-premium .user-info .time-display { font-size: 11px; color: #8a8a9a; font-weight: 300; display: flex; align-items: center; gap: 4px; }
        .navbar-premium .user-info .time-display i { color: #FFD700; }
        .btn-logout-premium { background: none; border: 1px solid #e8e0d8; color: #6a5a7a; padding: 5px 16px; border-radius: 20px; font-size: 12px; font-weight: 500; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; gap: 6px; }
        .btn-logout-premium:hover { background: #fef0ed; border-color: #ff6b6b; color: #ff6b6b; }
        .btn-logout-premium i { font-size: 14px; }

        /* ============================================
           SIDEBAR - SAMA DENGAN DASHBOARD
        ============================================ */
        .sidebar-premium {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,215,0,0.06);
            min-height: calc(100vh - 72px);
            padding: 20px 0;
            position: sticky;
            top: 72px;
            overflow-y: auto;
        }
        .sidebar-premium .brand-sidebar { padding: 0 20px 20px; border-bottom: 1px solid rgba(255,215,0,0.06); margin-bottom: 15px; }
        .sidebar-premium .brand-sidebar .logo-text { font-size: 16px; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 10px; }
        .sidebar-premium .brand-sidebar .logo-text i { color: #FFD700; font-size: 22px; }
        .sidebar-premium .brand-sidebar .logo-text .gold { color: #FFD700; }
        .sidebar-premium .brand-sidebar .sub-text { font-size: 11px; color: #8a8a9a; margin-top: 2px; padding-left: 32px; }
        .sidebar-premium .brand-sidebar .lokasi-text { font-size: 10px; color: #B8860B; margin-top: 4px; padding-left: 32px; display: flex; align-items: center; gap: 4px; }
        .sidebar-premium .nav-sidebar { list-style: none; padding: 0; margin: 0; }
        .sidebar-premium .nav-sidebar li { margin: 2px 10px; }
        .sidebar-premium .nav-sidebar li a { display: flex; align-items: center; padding: 10px 16px; border-radius: 12px; color: #4a4a5a; text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.3s ease; gap: 12px; }
        .sidebar-premium .nav-sidebar li a i { font-size: 18px; width: 24px; text-align: center; }
        .sidebar-premium .nav-sidebar li a:hover { background: rgba(255,215,0,0.08); color: #1a1a2e; transform: translateX(4px); }
        .sidebar-premium .nav-sidebar li a.active { background: linear-gradient(135deg, rgba(255,215,0,0.12), rgba(245,166,35,0.06)); color: #1a1a2e; font-weight: 600; }
        .sidebar-premium .nav-sidebar li a.active i { color: #FFD700; }
        .sidebar-premium .nav-sidebar li a .badge-sidebar { margin-left: auto; background: rgba(255,215,0,0.15); color: #B8860B; font-size: 10px; padding: 2px 10px; border-radius: 20px; font-weight: 600; }
        .sidebar-premium .sidebar-footer { padding: 20px 20px; border-top: 1px solid rgba(0,0,0,0.03); margin-top: 20px; }
        .sidebar-premium .sidebar-footer .footer-item { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #8a8a9a; padding: 4px 0; }
        .sidebar-premium .sidebar-footer .footer-item i { color: #FFD700; font-size: 14px; width: 20px; text-align: center; }
        .sidebar-premium .sidebar-footer .divider-light { height: 1px; background: linear-gradient(to right, transparent, rgba(255,215,0,0.08), transparent); margin: 10px 0; }

        /* ============================================
           MAIN CONTENT
        ============================================ */
        .main-content { padding: 24px 30px 40px; position: relative; z-index: 1; }
        .page-title { font-size: 24px; font-weight: 700; color: #1a1a2e; }
        .page-subtitle { color: #8a8a9a; font-size: 14px; font-weight: 400; }

        /* ============================================
           FORM CARD
        ============================================ */
        .form-card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.04);
            border: 1px solid rgba(255,215,0,0.04);
        }
        .form-card .form-label { font-size: 13px; font-weight: 600; color: #2d2d3f; }
        .form-control {
            border: 2px solid #eef0f5;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 4px rgba(255,215,0,0.06);
        }
        .btn-save {
            background: linear-gradient(135deg, #FFD700, #f5a623);
            border: none;
            color: #0a0615;
            font-weight: 700;
            padding: 12px 30px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(255,215,0,0.25); }
        .btn-back {
            background: none;
            border: 1px solid #e0d5c8;
            color: #6a5a7a;
            padding: 12px 30px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .btn-back:hover { background: #f5f0eb; }

        /* ============================================
           BOTTOM NAV - SAMA DENGAN DASHBOARD
        ============================================ */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,215,0,0.06);
            display: none; padding: 6px 0; z-index: 100;
            justify-content: space-around; align-items: center;
        }
        .bottom-nav a { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #8a8a9a; font-size: 9px; font-weight: 500; padding: 4px 10px; border-radius: 10px; transition: all 0.3s ease; gap: 1px; }
        .bottom-nav a i { font-size: 18px; }
        .bottom-nav a.active { color: #FFD700; }
        .bottom-nav .nav-center { background: linear-gradient(135deg, #FFD700, #f5a623); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-top: -18px; box-shadow: 0 4px 20px rgba(255,215,0,0.3); }
        .bottom-nav .nav-center i { color: #0a0615; font-size: 20px; }
        .bottom-nav .nav-center span { display: none; }

        /* ============================================
           ALERT
        ============================================ */
        .alert-custom { border-radius: 12px; padding: 12px 18px; font-size: 13px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-custom.success { background: #e8f5e9; color: #2e7d32; border: 1px solid rgba(16,185,129,0.1); }
        .alert-custom.error { background: #fbe9e7; color: #c62828; border: 1px solid rgba(239,68,68,0.1); }
        .alert-custom i { font-size: 18px; }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 991px) {
            .sidebar-premium { display: none; }
            .bottom-nav { display: flex; }
            .main-content { padding: 16px; }
            body { padding-bottom: 70px; }
        }
        @media (max-width: 576px) {
            .form-card { padding: 18px; }
            .page-title { font-size: 20px; }
            .navbar-premium .brand { font-size: 15px; }
            .navbar-premium .user-info .time-display { display: none; }
            .btn-logout-premium { padding: 4px 12px; font-size: 11px; }
            .moon { width: 40px; height: 40px; }
            .moon::after { top: 6px; left: 12px; width: 14px; height: 14px; }
        }
    </style>
</head>
<body>
    <!-- ============================================
         ISLAMIC BACKGROUND
    ============================================ -->
    <div class="islamic-bg">
        <div class="stars-container" id="stars"></div>
        <div class="moon-wrap"><div class="moon"></div></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="geometric-pattern"></div>
        <div class="arabic-text top">بِسْمِ اللَّهِ الرَّحْمَـٰنِ الرَّحِيمِ</div>
        <div class="arabic-text bottom">وَتَزَوَّدُوا فَإِنَّ خَيْرَ الزَّادِ التَّقْوَى</div>
    </div>

    <!-- ============================================
         NAVBAR
    ============================================ -->
    <nav class="navbar-premium">
        <div class="container-fluid">
            <div class="row align-items-center w-100">
                <div class="col-md-4 col-6">
                    <a href="../dashboard.php" class="brand">
                        <i class="bi bi-mosque"></i>
                        <span>Sistem <span class="gold">Kajian</span></span>
                    </a>
                </div>
                <div class="col-md-8 col-6 text-end">
                    <div class="user-info justify-content-end">
                        <span class="time-display">
                            <i class="bi bi-calendar3"></i> <?php echo date('d M Y'); ?>
                            <span style="margin:0 4px;color:#d0c8c0;">|</span>
                            <i class="bi bi-clock"></i> <?php echo date('H:i'); ?>
                        </span>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                        </div>
                        <span class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></span>
                        <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
                        <a href="../logout.php" class="btn-logout-premium">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================
         MAIN CONTENT
    ============================================ -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3">
                <div class="sidebar-premium">
                    <div class="brand-sidebar">
                        <div class="logo-text">
                            <i class="bi bi-mosque"></i>
                            <span>Sistem <span class="gold">Kajian</span></span>
                        </div>
                        <div class="sub-text">Informasi Jadwal Kajian Islam</div>
                        <div class="lokasi-text">
                            <i class="bi bi-geo-alt"></i> <?php echo $lokasi; ?>
                        </div>
                    </div>
                    <ul class="nav-sidebar">
                        <li><a href="../dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
                        <li><a href="../kajian/read.php"><i class="bi bi-calendar-event"></i> Data Kajian <span class="badge-sidebar"><?php echo $total_kajian; ?></span></a></li>
                        <li><a href="../ustaz/read.php"><i class="bi bi-people"></i> Data Ustaz <span class="badge-sidebar"><?php echo $total_ustaz; ?></span></a></li>
                        <li><a href="read.php" class="active"><i class="bi bi-building"></i> Data Masjid <span class="badge-sidebar"><?php echo $total_masjid; ?></span></a></li>
                    </ul>
                    <div class="sidebar-footer">
                        <div class="divider-light"></div>
                        <div class="footer-item"><i class="bi bi-geo-alt"></i> <span><?php echo $lokasi; ?></span></div>
                        <div class="footer-item"><i class="bi bi-calendar3"></i> <span><?php echo date('d F Y'); ?></span></div>
                        <div class="footer-item"><i class="bi bi-moon"></i> <span><?php echo getIslamicDate(); ?></span></div>
                        <div class="footer-item"><i class="bi bi-clock"></i> <span><?php echo date('H:i'); ?> WIB</span></div>
                    </div>
                </div>
            </div>

            <!-- Main -->
            <div class="col-lg-10 col-md-9">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="page-title"><i class="bi bi-building-add" style="color:#FFD700;"></i> Tambah Masjid</h1>
                            <p class="page-subtitle">Tambahkan data masjid baru</p>
                        </div>
                        <a href="read.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>

                    <?php if ($message) echo $message; ?>
                    <?php if ($error): ?>
                    <div class="alert-custom error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-card">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Masjid <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_masjid" required placeholder="Contoh: Masjid Agung Batang">
                                    <div class="invalid-feedback">Nama masjid wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="alamat" required placeholder="Contoh: Jl. Masjid No. 1, Batang">
                                    <div class="invalid-feedback">Alamat wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="kecamatan" required placeholder="Contoh: Batang">
                                    <div class="invalid-feedback">Kecamatan wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="kota" required placeholder="Contoh: Batang">
                                    <div class="invalid-feedback">Kota wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" name="no_telepon" placeholder="Contoh: (0285) 123456">
                                </div>
                            </div>
                            <button type="submit" class="btn-save"><i class="bi bi-save"></i> Simpan</button>
                            <a href="read.php" class="btn-back ms-2"><i class="bi bi-x-circle"></i> Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         BOTTOM NAVIGATION
    ============================================ -->
    <nav class="bottom-nav">
        <a href="../dashboard.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
        <a href="../kajian/read.php"><i class="bi bi-calendar-event"></i><span>Kajian</span></a>
        <a href="../kajian/create.php" class="nav-center"><i class="bi bi-plus-lg"></i><span>Tambah</span></a>
        <a href="../ustaz/read.php"><i class="bi bi-people"></i><span>Ustaz</span></a>
        <a href="read.php" class="active"><i class="bi bi-building"></i><span>Masjid</span></a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // CREATE STARS
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('stars');
            const count = 60;
            for (let i = 0; i < count; i++) {
                const star = document.createElement('div');
                const size = 1.5 + Math.random() * 2.5;
                const isGold = Math.random() > 0.6;
                star.className = 'star ' + (isGold ? 'gold' : 'white');
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.setProperty('--duration', (2 + Math.random() * 4) + 's');
                star.style.animationDelay = Math.random() * 5 + 's';
                star.style.width = size + 'px';
                star.style.height = size + 'px';
                container.appendChild(star);
            }
        });

        // ============================================
        // FORM VALIDATION
        // ============================================
        (function() {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>