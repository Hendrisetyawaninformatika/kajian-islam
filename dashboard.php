<?php
// =============================================
// FILE: dashboard.php
// FUNGSI: Halaman Dashboard Premium
// LOKASI: Batang, Jawa Tengah
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

// Ambil total untuk footer sidebar
$total_kajian_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kajian"))['total'];
$total_ustaz_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ustaz"))['total'];
$total_masjid_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM masjid"))['total'];

// Data lokasi
$lokasi = 'Batang, Jawa Tengah';
$waktu_sholat = [
    'Subuh' => '04:30',
    'Dzuhur' => '11:45',
    'Ashar' => '15:15',
    'Maghrib' => '17:45',
    'Isya' => '19:00'
];
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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8f7f4; min-height: 100vh; padding-bottom: 80px; }

        /* ============================================
           ISLAMIC BACKGROUND PREMIUM
        ============================================ */
        .islamic-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0;
            background: 
                radial-gradient(ellipse at 20% 50%, rgba(255,215,0,0.05) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 50%, rgba(255,215,0,0.03) 0%, transparent 60%),
                radial-gradient(ellipse at 50% 100%, rgba(16,185,129,0.03) 0%, transparent 40%),
                linear-gradient(180deg, #fcfaf7 0%, #f5f0eb 100%);
            overflow: hidden;
        }

        /* ===== BINTANG BERKILAU ===== */
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

        .star.gold { background: #FFD700; box-shadow: 0 0 15px rgba(255,215,0,0.3); }
        .star.white { background: #C5A55A; box-shadow: 0 0 10px rgba(197,165,90,0.15); }
        .star.big { width: 6px; height: 6px; }

        @keyframes twinkleStar {
            0%, 100% { opacity: 0.1; transform: scale(0.8); }
            50% { opacity: 0.8; transform: scale(1.3); box-shadow: 0 0 25px rgba(255,215,0,0.4); }
        }

        /* ===== BULAN SABIT BESAR ===== */
        .moon-wrap {
            position: absolute;
            top: 4%;
            right: 4%;
            animation: floatMoon 15s ease-in-out infinite;
        }

        .moon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #ffe066, #e8a800);
            box-shadow: 0 0 80px rgba(255,215,0,0.15), 0 0 150px rgba(255,215,0,0.05);
            position: relative;
        }

        .moon::after {
            content: '';
            position: absolute;
            top: 12px;
            left: 22px;
            width: 30px;
            height: 30px;
            background: #fcfaf7;
            border-radius: 50%;
            opacity: 0.25;
        }

        .moon::before {
            content: '';
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 12px;
            height: 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        @keyframes floatMoon {
            0%, 100% { transform: translateY(0) rotate(0deg) scale(1); }
            33% { transform: translateY(-20px) rotate(3deg) scale(1.02); }
            66% { transform: translateY(-10px) rotate(-2deg) scale(0.98); }
        }

        /* ===== SHOOTING STARS ===== */
        .shooting-star {
            position: absolute;
            width: 120px;
            height: 2px;
            background: linear-gradient(to right, transparent, rgba(255,215,0,0.6), transparent);
            animation: shootStar 8s infinite linear;
        }

        .shooting-star::before {
            content: '';
            position: absolute;
            top: -4px;
            right: 0;
            width: 8px;
            height: 8px;
            background: #FFD700;
            border-radius: 50%;
            box-shadow: 0 0 25px rgba(255,215,0,0.6);
        }

        .shooting-star:nth-child(1) { top: 10%; left: -120px; animation-delay: 0s; }
        .shooting-star:nth-child(2) { top: 25%; left: -120px; animation-delay: 3s; }
        .shooting-star:nth-child(3) { top: 45%; left: -120px; animation-delay: 5.5s; }
        .shooting-star:nth-child(4) { top: 65%; left: -120px; animation-delay: 8s; }

        @keyframes shootStar {
            0% { transform: translateX(0) rotate(25deg); opacity: 0; }
            5% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translateX(1300px) rotate(25deg); opacity: 0; }
        }

        /* ===== GEOMETRIC PATTERN ===== */
        .geometric-pattern {
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 400px;
            height: 400px;
            opacity: 0.04;
            animation: rotatePattern 40s linear infinite;
        }

        .geometric-pattern::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 50% 50%, transparent 42%, #C5A55A 42%, #C5A55A 45%, transparent 45%),
                radial-gradient(circle at 50% 50%, transparent 22%, #C5A55A 22%, #C5A55A 25%, transparent 25%),
                radial-gradient(circle at 50% 50%, transparent 10%, #C5A55A 10%, #C5A55A 12%, transparent 12%);
            border-radius: 50%;
        }

        .geometric-pattern::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 70%;
            height: 70%;
            transform: translate(-50%, -50%) rotate(45deg);
            border: 1px solid #C5A55A;
            border-radius: 50%;
            opacity: 0.5;
        }

        @keyframes rotatePattern {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.08); }
            100% { transform: rotate(360deg) scale(1); }
        }

        /* ===== ORNAMEN ISLAMI MELAYANG ===== */
        .islamic-ornament {
            position: absolute;
            font-size: 100px;
            color: rgba(197,165,90,0.04);
            animation: floatOrnament 20s ease-in-out infinite;
        }

        .islamic-ornament.tl {
            top: 2%;
            left: 3%;
            font-size: 150px;
            animation-delay: 0s;
        }

        .islamic-ornament.br {
            bottom: 15%;
            right: 3%;
            font-size: 120px;
            animation-delay: 7s;
        }

        .islamic-ornament.tr {
            top: 30%;
            right: 8%;
            font-size: 80px;
            animation-delay: 14s;
        }

        @keyframes floatOrnament {
            0%, 100% { transform: translate(0, 0) rotate(0deg) scale(1); }
            25% { transform: translate(15px, -20px) rotate(5deg) scale(1.05); }
            50% { transform: translate(-10px, 10px) rotate(-5deg) scale(0.95); }
            75% { transform: translate(20px, 15px) rotate(3deg) scale(1.03); }
        }

        /* ===== KALIGRAFI ARAB ===== */
        .arabic-calligraphy {
            position: absolute;
            font-size: 32px;
            color: rgba(197,165,90,0.04);
            letter-spacing: 15px;
            font-family: 'Traditional Arabic', 'Times New Roman', serif;
            animation: calligraphyFade 25s ease-in-out infinite;
        }

        .arabic-calligraphy.top {
            top: 6%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 36px;
        }

        .arabic-calligraphy.bottom {
            bottom: 8%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 28px;
            animation-delay: 5s;
        }

        @keyframes calligraphyFade {
            0%, 100% { opacity: 0.03; transform: translateX(-50%) scale(1); }
            50% { opacity: 0.08; transform: translateX(-50%) scale(1.03); }
        }

        /* ===== SILUET MASJID ===== */
        .masjid-silhouette {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 180px;
            z-index: 0;
            opacity: 0.06;
        }

        .masjid-silhouette svg {
            width: 100%;
            height: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        /* ============================================
           NAVBAR PREMIUM
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
           SIDEBAR PREMIUM
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
        .page-title { font-size: 24px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px; }
        .page-subtitle { color: #8a8a9a; font-size: 14px; font-weight: 400; }

        /* ============================================
           LOKASI BADGE
        ============================================ */
        .lokasi-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,215,0,0.08);
            color: #B8860B;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid rgba(255,215,0,0.1);
            animation: pulseBadge 3s ease-in-out infinite;
        }

        @keyframes pulseBadge {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255,215,0,0); }
            50% { box-shadow: 0 0 20px rgba(255,215,0,0.08); }
        }

        .lokasi-badge i { color: #FFD700; }

        /* ============================================
           STATS CARDS
        ============================================ */
        .stats-row { margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 18px; padding: 22px 24px; box-shadow: 0 2px 20px rgba(0,0,0,0.04); transition: all 0.4s cubic-bezier(0.25,0.46,0.45,0.94); border: 1px solid rgba(255,215,0,0.04); position: relative; overflow: hidden; height: 100%; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 40px rgba(0,0,0,0.06); }
        .stat-card .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 12px; }
        .stat-card .stat-icon.gold { background: rgba(255,215,0,0.1); color: #FFD700; }
        .stat-card .stat-icon.green { background: rgba(16,185,129,0.1); color: #10B981; }
        .stat-card .stat-icon.blue { background: rgba(59,130,246,0.1); color: #3B82F6; }
        .stat-card .stat-icon.purple { background: rgba(147,51,234,0.1); color: #9333EA; }
        .stat-card .stat-number { font-size: 32px; font-weight: 800; color: #1a1a2e; line-height: 1.2; }
        .stat-card .stat-label { font-size: 13px; color: #8a8a9a; font-weight: 400; }
        .stat-card .stat-change { font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 20px; margin-top: 4px; display: inline-block; }
        .stat-card .stat-change.up { background: rgba(16,185,129,0.1); color: #10B981; }
        .stat-card .stat-change.down { background: rgba(239,68,68,0.1); color: #EF4444; }
        .stat-card .stat-glow { position: absolute; top: -50%; right: -20%; width: 150px; height: 150px; border-radius: 50%; opacity: 0.04; pointer-events: none; }
        .stat-card .stat-glow.gold { background: #FFD700; }
        .stat-card .stat-glow.green { background: #10B981; }
        .stat-card .stat-glow.blue { background: #3B82F6; }
        .stat-card .stat-glow.purple { background: #9333EA; }

        /* ============================================
           SECTION CARDS
        ============================================ */
        .section-card { background: white; border-radius: 18px; padding: 0; box-shadow: 0 2px 20px rgba(0,0,0,0.04); border: 1px solid rgba(255,215,0,0.04); overflow: hidden; margin-bottom: 24px; }
        .section-card .card-header-custom { padding: 18px 24px; border-bottom: 1px solid rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; }
        .section-card .card-header-custom h5 { font-size: 16px; font-weight: 600; color: #1a1a2e; margin: 0; }
        .section-card .card-header-custom h5 i { color: #FFD700; margin-right: 8px; }
        .section-card .card-header-custom .badge-custom { font-size: 11px; padding: 4px 14px; border-radius: 20px; background: rgba(255,215,0,0.1); color: #B8860B; font-weight: 500; }
        .section-card .card-body-custom { padding: 20px 24px; }

        /* ============================================
           TABLE
        ============================================ */
        .table-custom { margin: 0; }
        .table-custom thead th { background: rgba(255,215,0,0.04); color: #4a4a5a; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; border-bottom: 1px solid rgba(0,0,0,0.03); }
        .table-custom tbody td { padding: 12px 16px; font-size: 13px; color: #2a2a3a; border-bottom: 1px solid rgba(0,0,0,0.02); vertical-align: middle; }
        .table-custom tbody tr:hover { background: rgba(255,215,0,0.03); }
        .badge-time { background: rgba(255,215,0,0.1); color: #B8860B; font-weight: 600; padding: 3px 10px; border-radius: 20px; font-size: 11px; }
        .badge-tema { background: rgba(147,51,234,0.08); color: #7C3AED; font-weight: 500; padding: 3px 10px; border-radius: 20px; font-size: 11px; }

        /* ============================================
           EMPTY STATE
        ============================================ */
        .empty-state { text-align: center; padding: 30px 20px; }
        .empty-state i { font-size: 48px; color: #e0d5c8; margin-bottom: 12px; }
        .empty-state p { color: #8a8a9a; font-size: 14px; }

        /* ============================================
           BOTTOM NAV
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
           RESPONSIVE
        ============================================ */
        @media (max-width: 991px) {
            .sidebar-premium { display: none; }
            .main-content { padding: 16px; }
            .bottom-nav { display: flex; }
            body { padding-bottom: 70px; }
            .stat-card .stat-number { font-size: 26px; }
            .page-title { font-size: 20px; }
            .moon { width: 50px; height: 50px; }
            .moon::after { top: 8px; left: 14px; width: 18px; height: 18px; }
        }
        @media (max-width: 576px) {
            .stat-card { padding: 16px 18px; }
            .stat-card .stat-number { font-size: 22px; }
            .stat-card .stat-icon { width: 40px; height: 40px; font-size: 18px; }
            .section-card .card-header-custom { padding: 14px 16px; flex-direction: column; align-items: flex-start; gap: 6px; }
            .section-card .card-body-custom { padding: 14px 16px; }
            .table-custom thead th, .table-custom tbody td { padding: 8px 10px; font-size: 12px; }
            .moon { width: 40px; height: 40px; }
            .moon::after { top: 6px; left: 12px; width: 14px; height: 14px; }
            .navbar-premium .brand { font-size: 15px; }
            .navbar-premium .user-info .time-display { display: none; }
            .btn-logout-premium { padding: 4px 12px; font-size: 11px; }
            .islamic-ornament { font-size: 60px; }
            .arabic-calligraphy { font-size: 20px; letter-spacing: 8px; }
            .masjid-silhouette { height: 100px; }
        }

        /* ============================================
           SCROLLBAR
        ============================================ */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f5f0eb; }
        ::-webkit-scrollbar-thumb { background: #e0d5c8; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #C5A55A; }

        /* ============================================
           ANIMASI FADE IN
        ============================================ */
        .fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }

        .slide-up { animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <!-- ============================================
         ISLAMIC BACKGROUND PREMIUM
    ============================================ -->
    <div class="islamic-bg">
        <div class="stars-container" id="stars"></div>
        <div class="moon-wrap"><div class="moon"></div></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="geometric-pattern"></div>
        <div class="islamic-ornament tl">﷽</div>
        <div class="islamic-ornament br">ﷲ</div>
        <div class="islamic-ornament tr">✦</div>
        <div class="arabic-calligraphy top">بِسْمِ اللَّهِ الرَّحْمَـٰنِ الرَّحِيمِ</div>
        <div class="arabic-calligraphy bottom">وَتَزَوَّدُوا فَإِنَّ خَيْرَ الزَّادِ التَّقْوَى</div>
        <div class="masjid-silhouette">
            <svg viewBox="0 0 1200 180" preserveAspectRatio="none">
                <path d="M0,180 L0,90 L50,70 L100,90 L100,50 L150,30 L200,50 L200,90 L250,70 L300,90 L300,50 L350,30 L400,50 L400,90 L450,70 L500,90 L500,40 L550,15 L600,40 L600,90 L650,70 L700,90 L700,50 L750,30 L800,50 L800,90 L850,70 L900,90 L900,50 L950,30 L1000,50 L1000,90 L1050,70 L1100,90 L1100,50 L1150,30 L1200,50 L1200,180 Z" 
                      fill="rgba(255,215,0,0.03)"/>
                <path d="M0,180 L0,110 L50,95 L100,110 L100,80 L150,65 L200,80 L200,110 L250,95 L300,110 L300,80 L350,65 L400,80 L400,110 L450,95 L500,110 L500,75 L550,55 L600,75 L600,110 L650,95 L700,110 L700,80 L750,65 L800,80 L800,110 L850,95 L900,110 L900,80 L950,65 L1000,80 L1000,110 L1050,95 L1100,110 L1100,80 L1150,65 L1200,80 L1200,180 Z" 
                      fill="rgba(255,215,0,0.05)"/>
                <path d="M0,180 L0,130 L50,120 L100,130 L100,105 L150,92 L200,105 L200,130 L250,120 L300,130 L300,105 L350,92 L400,105 L400,130 L450,120 L500,130 L500,100 L550,85 L600,100 L600,130 L650,120 L700,130 L700,105 L750,92 L800,105 L800,130 L850,120 L900,130 L900,105 L950,92 L1000,105 L1000,130 L1050,120 L1100,130 L1100,105 L1150,92 L1200,105 L1200,180 Z" 
                      fill="rgba(255,215,0,0.08)"/>
            </svg>
        </div>
    </div>

    <!-- ============================================
         NAVBAR PREMIUM
    ============================================ -->
    <nav class="navbar-premium">
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
                        <a href="logout.php" class="btn-logout-premium">
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
                        <li><a href="dashboard.php" class="active"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
                        <li>
                            <a href="kajian/read.php">
                                <i class="bi bi-calendar-event"></i> Data Kajian
                                <?php if ($total_kajian_all > 0): ?>
                                <span class="badge-sidebar"><?php echo $total_kajian_all; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="ustaz/read.php">
                                <i class="bi bi-people"></i> Data Ustaz
                                <?php if ($total_ustaz_all > 0): ?>
                                <span class="badge-sidebar"><?php echo $total_ustaz_all; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="masjid/read.php">
                                <i class="bi bi-building"></i> Data Masjid
                                <?php if ($total_masjid_all > 0): ?>
                                <span class="badge-sidebar"><?php echo $total_masjid_all; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>

                    <div class="sidebar-footer">
                        <div class="divider-light"></div>
                        <div class="footer-item">
                            <i class="bi bi-geo-alt"></i>
                            <span><?php echo $lokasi; ?></span>
                        </div>
                        <div class="footer-item">
                            <i class="bi bi-calendar3"></i>
                            <span><?php echo date('d F Y'); ?></span>
                        </div>
                        <div class="footer-item">
                            <i class="bi bi-moon"></i>
                            <span><?php echo getIslamicDate(); ?></span>
                        </div>
                        <div class="footer-item">
                            <i class="bi bi-clock"></i>
                            <span><?php echo date('H:i'); ?> WIB</span>
                        </div>
                        <div class="divider-light"></div>
                        <div class="footer-item" style="font-size:10px;color:#b0b0c0;justify-content:center;padding-top:4px;">
                            <span>✨ "Sebaik-baik manusia adalah yang bermanfaat"</span>
                        </div>
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
                            <span class="lokasi-badge">
                                <i class="bi bi-geo-alt"></i> <?php echo $lokasi; ?>
                                <i class="bi bi-dot" style="font-size:6px;"></i>
                                <i class="bi bi-clock"></i> <?php echo date('H:i'); ?> WIB
                            </span>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row stats-row g-4">
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow gold"></div>
                                <div class="stat-icon gold"><i class="bi bi-calendar-event"></i></div>
                                <div class="stat-number counter" data-target="<?php echo $total_kajian; ?>">0</div>
                                <div class="stat-label">Total Kajian</div>
                                <span class="stat-change up"><i class="bi bi-arrow-up"></i> +<?php echo $total_kajian > 0 ? $total_kajian : 0; ?></span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow green"></div>
                                <div class="stat-icon green"><i class="bi bi-people"></i></div>
                                <div class="stat-number counter" data-target="<?php echo $total_ustaz; ?>">0</div>
                                <div class="stat-label">Total Ustaz</div>
                                <span class="stat-change up"><i class="bi bi-arrow-up"></i> +<?php echo $total_ustaz > 0 ? $total_ustaz : 0; ?></span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow blue"></div>
                                <div class="stat-icon blue"><i class="bi bi-building"></i></div>
                                <div class="stat-number counter" data-target="<?php echo $total_masjid; ?>">0</div>
                                <div class="stat-label">Total Masjid</div>
                                <span class="stat-change up"><i class="bi bi-arrow-up"></i> +<?php echo $total_masjid > 0 ? $total_masjid : 0; ?></span>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6">
                            <div class="stat-card">
                                <div class="stat-glow purple"></div>
                                <div class="stat-icon purple"><i class="bi bi-clock-history"></i></div>
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
                            <span class="badge-custom"><i class="bi bi-calendar3"></i> <?php echo date('d F Y'); ?></span>
                        </div>
                        <div class="card-body-custom">
                            <?php if ($total_hari_ini > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-custom">
                                    <thead><tr><th>Jam</th><th>Judul Kajian</th><th>Ustaz</th><th>Masjid</th><th>Tema</th></tr></thead>
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

                    <!-- Kajian Terbaru & Ustaz Terbaru -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="card-header-custom">
                                    <h5><i class="bi bi-clock-history"></i> Kajian Terbaru</h5>
                                    <a href="kajian/read.php" style="color:#FFD700;font-size:12px;font-weight:500;text-decoration:none;">Lihat semua <i class="bi bi-arrow-right"></i></a>
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
                                    <div class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada data kajian</p></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="section-card">
                                <div class="card-header-custom">
                                    <h5><i class="bi bi-people"></i> Ustaz Terbaru</h5>
                                    <a href="ustaz/read.php" style="color:#FFD700;font-size:12px;font-weight:500;text-decoration:none;">Lihat semua <i class="bi bi-arrow-right"></i></a>
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
                                            <span style="font-size:10px;color:#8a8a9a;"><i class="bi bi-clock"></i> <?php echo date('d/m/Y', strtotime($row['created_at'])); ?></span>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada data ustaz</p></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Masjid Terbaru -->
                    <div class="section-card mt-4">
                        <div class="card-header-custom">
                            <h5><i class="bi bi-building"></i> Masjid Terbaru</h5>
                            <a href="masjid/read.php" style="color:#FFD700;font-size:12px;font-weight:500;text-decoration:none;">Lihat semua <i class="bi bi-arrow-right"></i></a>
                        </div>
                        <div class="card-body-custom">
                            <?php if (mysqli_num_rows($masjid_terbaru) > 0): ?>
                            <div class="row g-3">
                                <?php while ($row = mysqli_fetch_assoc($masjid_terbaru)): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div style="background:rgba(255,215,0,0.03);border-radius:12px;padding:14px 16px;border:1px solid rgba(255,215,0,0.05);">
                                        <div style="font-weight:600;font-size:14px;color:#1a1a2e;"><?php echo htmlspecialchars($row['nama_masjid']); ?></div>
                                        <div style="font-size:11px;color:#8a8a9a;margin-top:4px;"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($row['alamat']); ?></div>
                                        <div style="font-size:11px;color:#8a8a9a;"><i class="bi bi-map"></i> <?php echo htmlspecialchars($row['kecamatan']); ?>, <?php echo htmlspecialchars($row['kota']); ?></div>
                                        <?php if ($row['no_telepon']): ?>
                                        <div style="font-size:11px;color:#8a8a9a;"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($row['no_telepon']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada data masjid</p></div>
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
                            <br>
                            <span style="font-size:10px;color:#b0b0c0;">
                                <i class="bi bi-geo-alt"></i> <?php echo $lokasi; ?>
                            </span>
                        </p>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         BOTTOM NAVIGATION
    ============================================ -->
    <nav class="bottom-nav">
        <a href="dashboard.php" class="active"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
        <a href="kajian/read.php"><i class="bi bi-calendar-event"></i><span>Kajian</span></a>
        <a href="kajian/create.php" class="nav-center"><i class="bi bi-plus-lg"></i><span>Tambah</span></a>
        <a href="ustaz/read.php"><i class="bi bi-people"></i><span>Ustaz</span></a>
        <a href="masjid/read.php"><i class="bi bi-building"></i><span>Masjid</span></a>
    </nav>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // CREATE STARS
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('stars');
            const count = 80;
            for (let i = 0; i < count; i++) {
                const star = document.createElement('div');
                const size = 1.5 + Math.random() * 3;
                const isGold = Math.random() > 0.65;
                const isBig = size > 3.5;
                star.className = 'star ' + (isGold ? 'gold' : 'white') + (isBig ? ' big' : '');
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.setProperty('--duration', (2 + Math.random() * 5) + 's');
                star.style.animationDelay = Math.random() * 6 + 's';
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
                    const duration = 1800;
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
    </script>
</body>
</html>