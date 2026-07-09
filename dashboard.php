<?php
// =============================================
// FILE: dashboard.php
// FUNGSI: Halaman Dashboard Premium
// =============================================

session_start();
require_once 'koneksi.php';

// Cek login
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

// Ambil total data
$total_kajian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kajian"))['total'];
$total_ustaz = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ustaz"))['total'];
$total_masjid = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM masjid"))['total'];

// Ambil kajian hari ini
$today = date('Y-m-d');
$query_kajian_hari_ini = "SELECT k.*, u.nama_ustaz, m.nama_masjid 
                          FROM kajian k 
                          JOIN ustaz u ON k.ustaz_id = u.id 
                          JOIN masjid m ON k.masjid_id = m.id 
                          WHERE k.tanggal = '$today' 
                          ORDER BY k.jam ASC";
$kajian_hari_ini = mysqli_query($koneksi, $query_kajian_hari_ini);
$total_hari_ini = mysqli_num_rows($kajian_hari_ini);

// Ambil kajian terbaru
$query_kajian_terbaru = "SELECT k.*, u.nama_ustaz, m.nama_masjid 
                         FROM kajian k 
                         JOIN ustaz u ON k.ustaz_id = u.id 
                         JOIN masjid m ON k.masjid_id = m.id 
                         ORDER BY k.tanggal DESC, k.jam DESC 
                         LIMIT 5";
$kajian_terbaru = mysqli_query($koneksi, $query_kajian_terbaru);

// Ambil ustaz terbaru
$query_ustaz_terbaru = "SELECT * FROM ustaz ORDER BY created_at DESC LIMIT 5";
$ustaz_terbaru = mysqli_query($koneksi, $query_ustaz_terbaru);

// Ambil masjid terbaru
$query_masjid_terbaru = "SELECT * FROM masjid ORDER BY created_at DESC LIMIT 5";
$masjid_terbaru = mysqli_query($koneksi, $query_masjid_terbaru);

// Fungsi getIslamicDate (jika belum ada di koneksi.php)
if (!function_exists('getIslamicDate')) {
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
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Jadwal Kajian Islam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ============================================
           RESET & BASE
        ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f7f4;
            min-height: 100vh;
        }

        /* ============================================
           ISLAMIC BACKGROUND
        ============================================ */
        .islamic-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(255, 215, 0, 0.04) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(255, 215, 0, 0.02) 0%, transparent 60%),
                linear-gradient(180deg, #fcfaf7 0%, #f5f0eb 100%);
            overflow: hidden;
        }

        .stars-container {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .star {
            position: absolute;
            border-radius: 50%;
            animation: twinkleStar var(--duration) ease-in-out infinite;
        }

        .star.gold {
            background: #FFD700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        }
        .star.white {
            background: #C5A55A;
            box-shadow: 0 0 8px rgba(197, 165, 90, 0.1);
        }

        @keyframes twinkleStar {
            0%, 100% { opacity: 0.1; transform: scale(0.8); }
            50% { opacity: 0.6; transform: scale(1.2); }
        }

        .moon-wrap {
            position: absolute;
            top: 5%;
            right: 5%;
            animation: floatMoon 12s ease-in-out infinite;
        }

        .moon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #ffe066, #f5a623);
            box-shadow: 0 0 60px rgba(255, 215, 0, 0.1);
            position: relative;
        }

        .moon::after {
            content: '';
            position: absolute;
            top: 10px;
            left: 20px;
            width: 24px;
            height: 24px;
            background: #fcfaf7;
            border-radius: 50%;
            opacity: 0.3;
        }

        @keyframes floatMoon {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(3deg); }
        }

        .shooting-star {
            position: absolute;
            width: 100px;
            height: 2px;
            background: linear-gradient(to right, transparent, rgba(255, 215, 0, 0.4), transparent);
            animation: shootStar 7s infinite linear;
        }

        .shooting-star:nth-child(1) { top: 10%; left: -100px; animation-delay: 0s; }
        .shooting-star:nth-child(2) { top: 30%; left: -100px; animation-delay: 3.5s; }
        .shooting-star:nth-child(3) { top: 50%; left: -100px; animation-delay: 7s; }

        @keyframes shootStar {
            0% { transform: translateX(0) rotate(20deg); opacity: 0; }
            8% { opacity: 1; }
            85% { opacity: 1; }
            100% { transform: translateX(1200px) rotate(20deg); opacity: 0; }
        }

        .geometric-pattern {
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            opacity: 0.04;
            animation: rotatePattern 30s linear infinite;
        }

        .geometric-pattern::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 50% 50%, transparent 42%, #C5A55A 42%, #C5A55A 45%, transparent 45%),
                radial-gradient(circle at 50% 50%, transparent 22%, #C5A55A 22%, #C5A55A 25%, transparent 25%);
            border-radius: 50%;
        }

        @keyframes rotatePattern {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.05); }
            100% { transform: rotate(360deg) scale(1); }
        }

        .arabic-text {
            position: absolute;
            font-size: 24px;
            color: rgba(197, 165, 90, 0.04);
            letter-spacing: 10px;
            font-family: 'Times New Roman', serif;
            animation: fadeArabic 20s ease-in-out infinite;
        }

        .arabic-text.top {
            top: 8%;
            left: 50%;
            transform: translateX(-50%);
        }
        .arabic-text.bottom {
            bottom: 10%;
            left: 50%;
            transform: translateX(-50%);
        }

        @keyframes fadeArabic {
            0%, 100% { opacity: 0.03; }
            50% { opacity: 0.08; }
        }

        /* ============================================
           NAVBAR
        ============================================ */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 12px 0;
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.04);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 215, 0, 0.08);
        }

        .navbar-custom .brand {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-custom .brand i {
            color: #FFD700;
            font-size: 24px;
        }

        .navbar-custom .brand .gold {
            color: #FFD700;
        }

        .navbar-custom .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-custom .user-info .name {
            font-size: 13px;
            font-weight: 500;
            color: #1a1a2e;
        }

        .navbar-custom .user-info .role {
            font-size: 10px;
            padding: 2px 12px;
            border-radius: 20px;
            background: linear-gradient(135deg, #FFD700, #f5a623);
            color: #0a0615;
            font-weight: 600;
        }

        .navbar-custom .user-info .time {
            font-size: 11px;
            color: #8a8a9a;
        }

        .btn-logout {
            background: none;
            border: 1px solid #e0d5c8;
            color: #6a5a7a;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: #fee;
            border-color: #ff6b6b;
            color: #ff6b6b;
        }

        /* ============================================
           SIDEBAR
        ============================================ */
        .sidebar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 215, 0, 0.06);
            min-height: calc(100vh - 80px);
            padding: 20px 0;
            position: sticky;
            top: 80px;
        }

        .sidebar .nav-link {
            color: #4a4a5a;
            padding: 12px 24px;
            margin: 2px 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 215, 0, 0.08);
            color: #1a1a2e;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.12), rgba(245, 166, 35, 0.08));
            color: #1a1a2e;
            font-weight: 600;
        }

        .sidebar .nav-link.active i {
            color: #FFD700;
        }

        .sidebar .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(0, 0, 0, 0.03);
            margin-top: 20px;
        }

        .sidebar .sidebar-footer small {
            color: #8a8a9a;
            font-size: 11px;
            display: block;
        }

        .sidebar .sidebar-footer .gold-text {
            color: #FFD700;
        }

        /* ============================================
           MAIN CONTENT
        ============================================ */
        .main-content {
            padding: 24px 30px 40px;
            position: relative;
            z-index: 1;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: #8a8a9a;
            font-size: 14px;
            font-weight: 400;
        }

        /* ============================================
           STATS CARDS
        ============================================ */
        .stats-row {
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 18px;
            padding: 22px 24px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 1px solid rgba(255, 215, 0, 0.04);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06);
        }

        .stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 12px;
        }

        .stat-card .stat-icon.gold {
            background: rgba(255, 215, 0, 0.1);
            color: #FFD700;
        }
        .stat-card .stat-icon.green {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }
        .stat-card .stat-icon.blue {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
        }
        .stat-card .stat-icon.purple {
            background: rgba(147, 51, 234, 0.1);
            color: #9333EA;
        }

        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a2e;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: #8a8a9a;
            font-weight: 400;
        }

        .stat-card .stat-change {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
            margin-top: 4px;
            display: inline-block;
        }

        .stat-card .stat-change.up {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }

        .stat-card .stat-change.down {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }

        .stat-card .stat-glow {
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            opacity: 0.04;
            pointer-events: none;
        }

        .stat-card .stat-glow.gold {
            background: #FFD700;
        }
        .stat-card .stat-glow.green {
            background: #10B981;
        }
        .stat-card .stat-glow.blue {
            background: #3B82F6;
        }
        .stat-card .stat-glow.purple {
            background: #9333EA;
        }

        /* ============================================
           CARDS
        ============================================ */
        .section-card {
            background: white;
            border-radius: 18px;
            padding: 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 215, 0, 0.04);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .section-card .card-header-custom {
            padding: 18px 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-card .card-header-custom h5 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a2e;
            margin: 0;
        }

        .section-card .card-header-custom h5 i {
            color: #FFD700;
            margin-right: 8px;
        }

        .section-card .card-header-custom .badge-custom {
            font-size: 11px;
            padding: 4px 14px;
            border-radius: 20px;
            background: rgba(255, 215, 0, 0.1);
            color: #B8860B;
            font-weight: 500;
        }

        .section-card .card-body-custom {
            padding: 20px 24px;
        }

        /* ============================================
           TABLE
        ============================================ */
        .table-custom {
            margin: 0;
        }

        .table-custom thead th {
            background: rgba(255, 215, 0, 0.04);
            color: #4a4a5a;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }

        .table-custom tbody td {
            padding: 12px 16px;
            font-size: 13px;
            color: #2a2a3a;
            border-bottom: 1px solid rgba(0, 0, 0, 0.02);
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background: rgba(255, 215, 0, 0.03);
        }

        .table-custom .badge-time {
            background: rgba(255, 215, 0, 0.1);
            color: #B8860B;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
        }

        .table-custom .badge-tema {
            background: rgba(147, 51, 234, 0.08);
            color: #7C3AED;
            font-weight: 500;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
        }

        /* ============================================
           EMPTY STATE
        ============================================ */
        .empty-state {
            text-align: center;
            padding: 30px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: #e0d5c8;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #8a8a9a;
            font-size: 14px;
        }

        /* ============================================
           BOTTOM NAV
        ============================================ */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 215, 0, 0.06);
            display: none;
            padding: 8px 0;
            z-index: 100;
            justify-content: space-around;
            align-items: center;
        }

        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #8a8a9a;
            font-size: 10px;
            font-weight: 500;
            padding: 4px 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
            gap: 2px;
        }

        .bottom-nav a i {
            font-size: 20px;
        }

        .bottom-nav a.active {
            color: #FFD700;
        }

        .bottom-nav a.active i {
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
        }

        .bottom-nav .nav-center {
            background: linear-gradient(135deg, #FFD700, #f5a623);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -20px;
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3);
        }

        .bottom-nav .nav-center i {
            color: #0a0615;
            font-size: 24px;
        }

        .bottom-nav .nav-center span {
            display: none;
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }

            .main-content {
                padding: 16px;
            }

            .bottom-nav {
                display: flex;
            }

            body {
                padding-bottom: 70px;
            }

            .stat-card .stat-number {
                font-size: 26px;
            }

            .page-title {
                font-size: 20px;
            }
        }

        @media (max-width: 576px) {
            .stat-card {
                padding: 16px 18px;
            }

            .stat-card .stat-number {
                font-size: 22px;
            }

            .stat-card .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .section-card .card-header-custom {
                padding: 14px 16px;
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .section-card .card-body-custom {
                padding: 14px 16px;
            }

            .table-custom thead th,
            .table-custom tbody td {
                padding: 8px 10px;
                font-size: 12px;
            }

            .moon {
                width: 40px;
                height: 40px;
            }

            .moon::after {
                top: 6px;
                left: 12px;
                width: 14px;
                height: 14px;
            }

            .navbar-custom .brand {
                font-size: 16px;
            }

            .navbar-custom .user-info .name {
                font-size: 12px;
            }

            .navbar-custom .user-info .time {
                display: none;
            }
        }

        /* ============================================
           SCROLLBAR
        ============================================ */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f5f0eb;
        }

        ::-webkit-scrollbar-thumb {
            background: #e0d5c8;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #C5A55A;
        }
    </style>
</head>
<body>
    <!-- ============================================
         ISLAMIC BACKGROUND
    ============================================ -->
    <div class="islamic-bg">
        <div class="stars-container" id="stars"></div>
        <div class="moon-wrap">
            <div class="moon"></div>
        </div>
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
    <nav class="navbar-custom">
        <div class="container-fluid">
            <div class="row align-items-center w-100">
                <div class="col-md-4 col-6">
                    <a href="dashboard.php" class="brand">
                        <i class="bi bi-mosque"></i>
                        <span>Sistem <span class="gold">Kajian</span></span>
                    </a>
                </div>
                <div class="col-md-8 col-6 text-end">
                    <div class="user-info justify-content-end">
                        <span class="time"><i class="bi bi-calendar3"></i> <?php echo date('d M Y'); ?></span>
                        <span class="name"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['nama_lengkap']; ?></span>
                        <span class="role"><?php echo ucfirst($_SESSION['role']); ?></span>
                        <a href="logout.php" class="btn-logout">
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
                <div class="sidebar">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-grid-1x2-fill"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="kajian/read.php">
                                <i class="bi bi-calendar-event"></i> Data Kajian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="ustaz/read.php">
                                <i class="bi bi-people"></i> Data Ustaz
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="masjid/read.php">
                                <i class="bi bi-building"></i> Data Masjid
                            </a>
                        </li>
                    </ul>
                    <div class="sidebar-footer">
                        <small>
                            <i class="bi bi-moon"></i> <?php echo getIslamicDate(); ?>
                            <br>
                            <span class="gold-text">✦</span> <?php echo date('d F Y'); ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Main -->
            <div class="col-lg-10 col-md-9">
                <div class="main-content">
                    <!-- Page Title -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="page-title">Dashboard</h1>
                            <p class="page-subtitle">
                                <i class="bi bi-dot" style="color:#FFD700;"></i>
                                Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?>!
                            </p>
                        </div>
                        <div>
                            <span class="badge" style="background:rgba(255,215,0,0.1);color:#B8860B;padding:8px 16px;border-radius:20px;">
                                <i class="bi bi-calendar-check"></i> <?php echo date('l, d F Y'); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row stats-row g-4">
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow gold"></div>
                                <div class="stat-icon gold">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <div class="stat-number counter" data-target="<?php echo $total_kajian; ?>">0</div>
                                <div class="stat-label">Total Kajian</div>
                                <span class="stat-change up"><i class="bi bi-arrow-up"></i> +<?php echo $total_kajian > 0 ? $total_kajian : 0; ?></span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow green"></div>
                                <div class="stat-icon green">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-number counter" data-target="<?php echo $total_ustaz; ?>">0</div>
                                <div class="stat-label">Total Ustaz</div>
                                <span class="stat-change up"><i class="bi bi-arrow-up"></i> +<?php echo $total_ustaz > 0 ? $total_ustaz : 0; ?></span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow blue"></div>
                                <div class="stat-icon blue">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div class="stat-number counter" data-target="<?php echo $total_masjid; ?>">0</div>
                                <div class="stat-label">Total Masjid</div>
                                <span class="stat-change up"><i class="bi bi-arrow-up"></i> +<?php echo $total_masjid > 0 ? $total_masjid : 0; ?></span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow purple"></div>
                                <div class="stat-icon purple">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="stat-number counter" data-target="<?php echo $total_hari_ini; ?>">0</div>
                                <div class="stat-label">Kajian Hari Ini</div>
                                <span class="stat-change <?php echo $total_hari_ini > 0 ? 'up' : 'down'; ?>">
                                    <i class="bi bi-<?php echo $total_hari_ini > 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                    <?php echo $total_hari_ini > 0 ? '+' . $total_hari_ini : '0'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Hari Ini -->
                    <div class="section-card">
                        <div class="card-header-custom">
                            <h5><i class="bi bi-clock"></i> Jadwal Kajian Hari Ini</h5>
                            <span class="badge-custom">
                                <i class="bi bi-calendar3"></i> <?php echo date('d F Y'); ?>
                            </span>
                        </div>
                        <div class="card-body-custom">
                            <?php if ($total_hari_ini > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-custom">
                                    <thead>
                                        <tr>
                                            <th>Jam</th>
                                            <th>Judul Kajian</th>
                                            <th>Ustaz</th>
                                            <th>Masjid</th>
                                            <th>Tema</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($kajian_hari_ini)): ?>
                                        <tr>
                                            <td><span class="badge-time"><i class="bi bi-clock"></i> <?php echo date('H:i', strtotime($row['jam'])); ?></span></td>
                                            <td><strong><?php echo htmlspecialchars($row['judul']); ?></strong></td>
                                            <td><i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nama_ustaz']); ?></td>
                                            <td><i class="bi bi-building"></i> <?php echo htmlspecialchars($row['nama_masjid']); ?></td>
                                            <td><span class="badge-tema"><?php echo htmlspecialchars($row['tema']); ?></span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                <p>Tidak ada kajian hari ini</p>
                                <a href="kajian/create.php" class="btn btn-sm" style="background:linear-gradient(135deg,#FFD700,#f5a623);color:#0a0615;font-weight:600;border-radius:20px;padding:8px 24px;">
                                    <i class="bi bi-plus-circle"></i> Tambah Kajian
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Kajian Terbaru -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="card-header-custom">
                                    <h5><i class="bi bi-clock-history"></i> Kajian Terbaru</h5>
                                    <a href="kajian/read.php" style="color:#FFD700;font-size:12px;font-weight:500;text-decoration:none;">
                                        Lihat semua <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                                <div class="card-body-custom">
                                    <?php if (mysqli_num_rows($kajian_terbaru) > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php while ($row = mysqli_fetch_assoc($kajian_terbaru)): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center" style="border:none;border-bottom:1px solid rgba(0,0,0,0.03);padding:10px 0;">
                                            <div>
                                                <div style="font-weight:600;font-size:13px;color:#1a1a2e;"><?php echo htmlspecialchars($row['judul']); ?></div>
                                                <div style="font-size:11px;color:#8a8a9a;">
                                                    <i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nama_ustaz']); ?>
                                                    <span style="margin:0 4px;">·</span>
                                                    <i class="bi bi-building"></i> <?php echo htmlspecialchars($row['nama_masjid']); ?>
                                                    <span style="margin:0 4px;">·</span>
                                                    <i class="bi bi-calendar3"></i> <?php echo format_tanggal($row['tanggal']); ?>
                                                </div>
                                            </div>
                                            <span class="badge-time" style="font-size:10px;"><?php echo date('H:i', strtotime($row['jam'])); ?></span>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada data kajian</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="card-header-custom">
                                    <h5><i class="bi bi-people"></i> Ustaz Terbaru</h5>
                                    <a href="ustaz/read.php" style="color:#FFD700;font-size:12px;font-weight:500;text-decoration:none;">
                                        Lihat semua <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                                <div class="card-body-custom">
                                    <?php if (mysqli_num_rows($ustaz_terbaru) > 0): ?>
                                    <div class="list-group list-group-flush">
                                        <?php while ($row = mysqli_fetch_assoc($ustaz_terbaru)): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center" style="border:none;border-bottom:1px solid rgba(0,0,0,0.03);padding:10px 0;">
                                            <div>
                                                <div style="font-weight:600;font-size:13px;color:#1a1a2e;"><?php echo htmlspecialchars($row['nama_ustaz']); ?></div>
                                                <div style="font-size:11px;color:#8a8a9a;">
                                                    <span class="badge-tema"><?php echo htmlspecialchars($row['bidang_keilmuan']); ?></span>
                                                    <?php if ($row['no_hp']): ?>
                                                    <span style="margin-left:4px;"><i class="bi bi-phone"></i> <?php echo htmlspecialchars($row['no_hp']); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <span style="font-size:10px;color:#8a8a9a;">
                                                <i class="bi bi-clock"></i> <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                            </span>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada data ustaz</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Masjid Terbaru -->
                    <div class="section-card mt-4">
                        <div class="card-header-custom">
                            <h5><i class="bi bi-building"></i> Masjid Terbaru</h5>
                            <a href="masjid/read.php" style="color:#FFD700;font-size:12px;font-weight:500;text-decoration:none;">
                                Lihat semua <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="card-body-custom">
                            <?php if (mysqli_num_rows($masjid_terbaru) > 0): ?>
                            <div class="row g-3">
                                <?php while ($row = mysqli_fetch_assoc($masjid_terbaru)): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div style="background:rgba(255,215,0,0.03);border-radius:12px;padding:14px 16px;border:1px solid rgba(255,215,0,0.05);">
                                        <div style="font-weight:600;font-size:14px;color:#1a1a2e;"><?php echo htmlspecialchars($row['nama_masjid']); ?></div>
                                        <div style="font-size:11px;color:#8a8a9a;margin-top:4px;">
                                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($row['alamat']); ?>
                                        </div>
                                        <div style="font-size:11px;color:#8a8a9a;">
                                            <i class="bi bi-map"></i> <?php echo htmlspecialchars($row['kecamatan']); ?>, <?php echo htmlspecialchars($row['kota']); ?>
                                        </div>
                                        <?php if ($row['no_telepon']): ?>
                                        <div style="font-size:11px;color:#8a8a9a;">
                                            <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($row['no_telepon']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Belum ada data masjid</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer class="text-center mt-4" style="padding:20px 0;border-top:1px solid rgba(0,0,0,0.03);">
                        <p style="font-size:12px;color:#8a8a9a;margin:0;">
                            <i class="bi bi-mosque" style="color:#FFD700;"></i>
                            Sistem Informasi Jadwal Kajian Islam &copy; <?php echo date('Y'); ?>
                            <span style="margin:0 6px;">·</span>
                            <span style="font-weight:300;">"Sebaik-baik manusia adalah yang bermanfaat bagi orang lain"</span>
                        </p>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         BOTTOM NAVIGATION (Mobile)
    ============================================ -->
    <nav class="bottom-nav">
        <a href="dashboard.php" class="active">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="kajian/read.php">
            <i class="bi bi-calendar-event"></i>
            <span>Kajian</span>
        </a>
        <a href="kajian/create.php" class="nav-center">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah</span>
        </a>
        <a href="ustaz/read.php">
            <i class="bi bi-people"></i>
            <span>Ustaz</span>
        </a>
        <a href="masjid/read.php">
            <i class="bi bi-building"></i>
            <span>Masjid</span>
        </a>
    </nav>

    <!-- Scripts -->
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

            // ============================================
            // COUNTER ANIMATION
            // ============================================
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (target > 0) {
                    const duration = 1500;
                    const step = target / (duration / 16);
                    let current = 0;
                    const updateCounter = () => {
                        current += step;
                        if (current < target) {
                            counter.textContent = Math.round(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target;
                        }
                    };
                    updateCounter();
                }
            });
        });

        // ============================================
        // AUTO DISMISS ALERT
        // ============================================
        document.querySelectorAll('.alert').forEach(function(el) {
            setTimeout(function() {
                el.style.transition = 'opacity 0.4s';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 400);
            }, 5000);
        });
    </script>
</body>
</html>