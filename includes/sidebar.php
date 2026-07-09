<?php
// =============================================
// FILE: includes/sidebar.php
// FUNGSI: Sidebar premium warna KUNING
// =============================================

// Pastikan fungsi getIslamicDate() tersedia
if (!function_exists('getIslamicDate')) {
    require_once __DIR__ . '/../koneksi.php';
}

// Ambil total data untuk badge
$total_kajian = mysqli_fetch_assoc(mysqli_query($GLOBALS['koneksi'], "SELECT COUNT(*) as total FROM kajian"))['total'];
$total_ustaz = mysqli_fetch_assoc(mysqli_query($GLOBALS['koneksi'], "SELECT COUNT(*) as total FROM ustaz"))['total'];
$total_masjid = mysqli_fetch_assoc(mysqli_query($GLOBALS['koneksi'], "SELECT COUNT(*) as total FROM masjid"))['total'];

$lokasi = 'Batang, Jawa Tengah';
?>
<style>
/* ============================================
   SIDEBAR KUNING PREMIUM UNTUK SEMUA HALAMAN
============================================ */
.sidebar-premium {
    background: linear-gradient(180deg, #FFF8E1 0%, #FFEAA7 25%, #FFD93D 60%, #F6C90E 100%) !important;
    border-right: 1px solid rgba(255, 215, 0, 0.3) !important;
    min-height: calc(100vh - 72px) !important;
    padding: 20px 0 !important;
    position: sticky !important;
    top: 72px !important;
    overflow-y: auto !important;
    box-shadow: 2px 0 30px rgba(255, 215, 0, 0.12) !important;
    transition: all 0.3s ease !important;
}

/* Decorative pattern */
.sidebar-premium::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(255, 215, 0, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 215, 0, 0.08) 0%, transparent 50%);
    pointer-events: none;
}

/* ===== LOGO DI SIDEBAR ===== */
.sidebar-premium .brand-sidebar {
    padding: 0 20px 20px !important;
    border-bottom: 2px solid rgba(255, 215, 0, 0.2) !important;
    margin-bottom: 15px !important;
    position: relative !important;
}

.sidebar-premium .brand-sidebar .logo-wrapper {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

.sidebar-premium .brand-sidebar .logo-islamic {
    position: relative !important;
    width: 48px !important;
    height: 48px !important;
    flex-shrink: 0 !important;
    animation: logoPulseSidebar 3s ease-in-out infinite !important;
}

@keyframes logoPulseSidebar {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.sidebar-premium .brand-sidebar .logo-islamic svg {
    width: 100% !important;
    height: 100% !important;
    filter: drop-shadow(0 2px 10px rgba(184, 134, 11, 0.2));
}

.sidebar-premium .brand-sidebar .logo-text {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #2C1810 !important;
    display: flex !important;
    flex-direction: column !important;
    line-height: 1.2 !important;
}

.sidebar-premium .brand-sidebar .logo-text .main-title {
    font-size: 17px !important;
    font-weight: 800 !important;
    color: #2C1810 !important;
    letter-spacing: -0.3px !important;
}

.sidebar-premium .brand-sidebar .logo-text .main-title .gold {
    color: #8B6914 !important;
    position: relative !important;
}

.sidebar-premium .brand-sidebar .logo-text .main-title .gold::after {
    content: '' !important;
    position: absolute !important;
    bottom: -1px !important;
    left: 0 !important;
    width: 100% !important;
    height: 2px !important;
    background: linear-gradient(to right, #8B6914, #FFD700) !important;
    border-radius: 2px !important;
}

.sidebar-premium .brand-sidebar .logo-text .sub-title {
    font-size: 9px !important;
    color: #6B5B3E !important;
    font-weight: 400 !important;
    letter-spacing: 0.5px !important;
    margin-top: 1px !important;
}

.sidebar-premium .brand-sidebar .logo-text .sub-title i {
    color: #8B6914 !important;
    font-size: 8px !important;
}

.sidebar-premium .brand-sidebar .lokasi-text {
    font-size: 10px !important;
    color: #8B7B3E !important;
    margin-top: 4px !important;
    padding-left: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    font-weight: 500 !important;
}

.sidebar-premium .brand-sidebar .lokasi-text i {
    color: #8B6914 !important;
    font-size: 11px !important;
}

/* ===== NAV SIDEBAR ===== */
.sidebar-premium .nav-sidebar {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.sidebar-premium .nav-sidebar li {
    margin: 3px 10px !important;
}

.sidebar-premium .nav-sidebar li a {
    display: flex !important;
    align-items: center !important;
    padding: 10px 16px !important;
    border-radius: 12px !important;
    color: #2C1810 !important;
    text-decoration: none !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    gap: 12px !important;
    background: rgba(255, 255, 255, 0.25) !important;
    border: 1px solid rgba(255, 215, 0, 0.08) !important;
    position: relative !important;
}

.sidebar-premium .nav-sidebar li a i {
    font-size: 18px !important;
    width: 24px !important;
    text-align: center !important;
    color: #8B6914 !important;
    transition: all 0.3s ease !important;
}

.sidebar-premium .nav-sidebar li a:hover {
    background: rgba(255, 215, 0, 0.3) !important;
    color: #2C1810 !important;
    transform: translateX(4px) !important;
    border-color: rgba(255, 215, 0, 0.25) !important;
    box-shadow: 0 2px 15px rgba(255, 215, 0, 0.12) !important;
}

.sidebar-premium .nav-sidebar li a:hover i {
    color: #6B4E0A !important;
}

.sidebar-premium .nav-sidebar li a.active {
    background: linear-gradient(135deg, #FFD700, #F6C90E) !important;
    color: #2C1810 !important;
    font-weight: 600 !important;
    border-color: #FFD700 !important;
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.25) !important;
}

.sidebar-premium .nav-sidebar li a.active i {
    color: #2C1810 !important;
}

.sidebar-premium .nav-sidebar li a .badge-sidebar {
    margin-left: auto !important;
    background: rgba(44, 24, 16, 0.08) !important;
    color: #2C1810 !important;
    font-size: 10px !important;
    padding: 2px 10px !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
}

.sidebar-premium .nav-sidebar li a.active .badge-sidebar {
    background: rgba(44, 24, 16, 0.12) !important;
}

/* ===== SIDEBAR FOOTER ===== */
.sidebar-premium .sidebar-footer {
    padding: 20px 20px !important;
    border-top: 2px solid rgba(255, 215, 0, 0.12) !important;
    margin-top: 20px !important;
    background: rgba(255, 215, 0, 0.04) !important;
}

.sidebar-premium .sidebar-footer .footer-item {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    font-size: 12px !important;
    color: #2C1810 !important;
    padding: 4px 0 !important;
    font-weight: 400 !important;
}

.sidebar-premium .sidebar-footer .footer-item i {
    color: #8B6914 !important;
    font-size: 14px !important;
    width: 20px !important;
    text-align: center !important;
}

.sidebar-premium .sidebar-footer .footer-item .gold-text {
    color: #8B6914 !important;
    font-weight: 500 !important;
}

.sidebar-premium .sidebar-footer .divider-light {
    height: 1px !important;
    background: linear-gradient(to right, transparent, rgba(255, 215, 0, 0.15), transparent) !important;
    margin: 8px 0 !important;
}

.sidebar-premium .sidebar-footer .quote-text {
    font-size: 10px !important;
    color: #6B5B3E !important;
    justify-content: center !important;
    padding-top: 6px !important;
    font-style: italic !important;
    font-weight: 400 !important;
}

.sidebar-premium .sidebar-footer .quote-text i {
    color: #8B6914 !important;
    opacity: 0.6 !important;
}

/* ===== SCROLLBAR ===== */
.sidebar-premium::-webkit-scrollbar {
    width: 4px !important;
}

.sidebar-premium::-webkit-scrollbar-track {
    background: rgba(255, 215, 0, 0.08) !important;
}

.sidebar-premium::-webkit-scrollbar-thumb {
    background: #8B6914 !important;
    border-radius: 10px !important;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991px) {
    .sidebar-premium {
        display: none !important;
    }
}
</style>

<div class="sidebar-premium">
    <!-- Brand dengan Logo Islamic -->
    <div class="brand-sidebar">
        <div class="logo-wrapper">
            <!-- Logo Islamic SVG -->
            <div class="logo-islamic">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <!-- Bulan Sabit -->
                    <circle cx="45" cy="40" r="22" fill="#8B6914" opacity="0.12"/>
                    <path d="M45 18 C55 18 65 25 65 40 C65 55 55 62 45 62 C52 55 55 48 55 40 C55 32 52 25 45 18Z" 
                          fill="#8B6914"/>
                    <!-- Bintang -->
                    <polygon points="50,12 54,24 67,24 57,32 61,44 50,36 39,44 43,32 33,24 46,24" 
                             fill="#FFD700" opacity="0.6"/>
                    <polygon points="50,16 53,25 63,25 55,31 58,40 50,35 42,40 45,31 37,25 47,25" 
                             fill="#8B6914"/>
                    <!-- Kubah Masjid -->
                    <path d="M30 75 L30 62 L35 55 L40 50 L45 47 L50 50 L55 47 L60 50 L65 55 L70 62 L70 75 Z" 
                          fill="#2C1810" opacity="0.06"/>
                    <path d="M35 75 L35 65 L40 60 L45 57 L50 60 L55 57 L60 60 L65 65 L65 75 Z" 
                          fill="#2C1810" opacity="0.04"/>
                    <!-- Menara -->
                    <rect x="25" y="55" width="4" height="20" rx="1" fill="#2C1810" opacity="0.05"/>
                    <rect x="71" y="55" width="4" height="20" rx="1" fill="#2C1810" opacity="0.05"/>
                    <!-- Ornamen -->
                    <circle cx="50" cy="75" r="4" fill="#FFD700" opacity="0.12"/>
                    <path d="M48 80 L50 75 L52 80 L50 85 Z" fill="#8B6914" opacity="0.08"/>
                </svg>
            </div>
            <div class="logo-text">
                <span class="main-title">Sistem <span class="gold">Kajian</span></span>
                <span class="sub-title"><i class="bi bi-dot"></i> Informasi Jadwal Kajian Islam</span>
            </div>
        </div>
        <div class="lokasi-text">
            <i class="bi bi-geo-alt-fill"></i> <?php echo $lokasi; ?>
        </div>
    </div>

    <!-- Navigation -->
    <ul class="nav-sidebar">
        <li>
            <a href="../dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="../kajian/read.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'kajian') !== false ? 'active' : ''; ?>">
                <i class="bi bi-calendar-event"></i>
                <span>Data Kajian</span>
                <?php if ($total_kajian > 0): ?>
                <span class="badge-sidebar"><?php echo $total_kajian; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="../ustaz/read.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'ustaz') !== false ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Data Ustaz</span>
                <?php if ($total_ustaz > 0): ?>
                <span class="badge-sidebar"><?php echo $total_ustaz; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="../masjid/read.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'masjid') !== false ? 'active' : ''; ?>">
                <i class="bi bi-building"></i>
                <span>Data Masjid</span>
                <?php if ($total_masjid > 0): ?>
                <span class="badge-sidebar"><?php echo $total_masjid; ?></span>
                <?php endif; ?>
            </a>
        </li>
    </ul>

    <!-- Footer -->
    <div class="sidebar-footer">
        <div class="divider-light"></div>
        <div class="footer-item">
            <i class="bi bi-geo-alt-fill"></i>
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
        <div class="footer-item quote-text">
            <i class="bi bi-quote"></i>
            "Sebaik-baik manusia adalah yang bermanfaat"
        </div>
    </div>
</div>