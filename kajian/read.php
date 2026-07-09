<?php
// =============================================
// FILE: kajian/read.php
// FUNGSI: Halaman daftar/data kajian
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$filter_condition = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = bersihkan($_GET['search']);
    $filter_condition .= " AND (k.judul LIKE '%$search%' OR k.tema LIKE '%$search%' OR u.nama_ustaz LIKE '%$search%' OR m.nama_masjid LIKE '%$search%')";
}
if (isset($_GET['filter_tanggal']) && !empty($_GET['filter_tanggal'])) {
    $tanggal = bersihkan($_GET['filter_tanggal']);
    $filter_condition .= " AND k.tanggal = '$tanggal'";
}
if (isset($_GET['filter_masjid']) && !empty($_GET['filter_masjid'])) {
    $masjid_id = (int)$_GET['filter_masjid'];
    $filter_condition .= " AND k.masjid_id = $masjid_id";
}
if (isset($_GET['filter_ustaz']) && !empty($_GET['filter_ustaz'])) {
    $ustaz_id = (int)$_GET['filter_ustaz'];
    $filter_condition .= " AND k.ustaz_id = $ustaz_id";
}

// Query total data
$query_total = "SELECT COUNT(*) as total FROM kajian k 
                JOIN ustaz u ON k.ustaz_id = u.id 
                JOIN masjid m ON k.masjid_id = m.id 
                WHERE $filter_condition";
$total_result = mysqli_query($koneksi, $query_total);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

// Query data
$query = "SELECT k.*, u.nama_ustaz, m.nama_masjid 
          FROM kajian k 
          JOIN ustaz u ON k.ustaz_id = u.id 
          JOIN masjid m ON k.masjid_id = m.id 
          WHERE $filter_condition 
          ORDER BY k.tanggal DESC, k.jam DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

// Ambil data untuk filter
$masjid_list = mysqli_query($koneksi, "SELECT id, nama_masjid FROM masjid ORDER BY nama_masjid ASC");
$ustaz_list = mysqli_query($koneksi, "SELECT id, nama_ustaz FROM ustaz ORDER BY nama_ustaz ASC");

// Hapus data dengan konfirmasi
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query_delete = "DELETE FROM kajian WHERE id = $id";
    if (mysqli_query($koneksi, $query_delete)) {
        echo '<script>alert("✅ Data kajian berhasil dihapus!"); window.location.href = "read.php";</script>';
    } else {
        echo '<script>alert("❌ Error: ' . mysqli_error($koneksi) . '");</script>';
    }
}

// Ambil total untuk bottom nav
$total_kajian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kajian"))['total'];
$total_ustaz = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ustaz"))['total'];
$total_masjid = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM masjid"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kajian - Sistem Informasi Jadwal Kajian Islam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8f7f4; padding-bottom: 80px; }
        
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
        
        .moon-wrap { position: absolute; top: 3%; right: 4%; animation: floatMoon 12s ease-in-out infinite; }
        .moon { width: 50px; height: 50px; border-radius: 50%; background: radial-gradient(circle at 35% 30%, #ffe066, #f5a623); box-shadow: 0 0 40px rgba(255,215,0,0.1); position: relative; }
        .moon::after { content: ''; position: absolute; top: 8px; left: 14px; width: 18px; height: 18px; background: #fcfaf7; border-radius: 50%; opacity: 0.3; }
        @keyframes floatMoon { 0%,100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-15px) rotate(3deg); } }
        
        .shooting-star { position: absolute; width: 80px; height: 2px; background: linear-gradient(to right, transparent, rgba(255,215,0,0.4), transparent); animation: shootStar 7s infinite linear; }
        .shooting-star:nth-child(1) { top: 10%; left: -80px; animation-delay: 0s; }
        .shooting-star:nth-child(2) { top: 30%; left: -80px; animation-delay: 3.5s; }
        @keyframes shootStar { 0% { transform: translateX(0) rotate(20deg); opacity: 0; } 8% { opacity: 1; } 85% { opacity: 1; } 100% { transform: translateX(1100px) rotate(20deg); opacity: 0; } }
        
        .geometric-pattern { position: absolute; bottom: -50px; left: -50px; width: 300px; height: 300px; opacity: 0.04; animation: rotatePattern 30s linear infinite; }
        .geometric-pattern::before { content: ''; position: absolute; width: 100%; height: 100%; background: radial-gradient(circle at 50% 50%, transparent 42%, #C5A55A 42%, #C5A55A 45%, transparent 45%), radial-gradient(circle at 50% 50%, transparent 22%, #C5A55A 22%, #C5A55A 25%, transparent 25%); border-radius: 50%; }
        @keyframes rotatePattern { 0% { transform: rotate(0deg) scale(1); } 50% { transform: rotate(180deg) scale(1.05); } 100% { transform: rotate(360deg) scale(1); } }

        .navbar-custom {
            background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); padding: 10px 0;
            box-shadow: 0 2px 30px rgba(0,0,0,0.04); position: sticky; top: 0; z-index: 100;
            border-bottom: 1px solid rgba(255,215,0,0.08);
        }
        .navbar-custom .brand { font-size: 18px; font-weight: 700; color: #1a1a2e; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .navbar-custom .brand i { color: #FFD700; font-size: 22px; }
        .navbar-custom .brand .gold { color: #FFD700; }
        .btn-logout { background: none; border: 1px solid #e0d5c8; color: #6a5a7a; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; transition: all 0.3s ease; text-decoration: none; }
        .btn-logout:hover { background: #fee; border-color: #ff6b6b; color: #ff6b6b; }

        .sidebar {
            background: rgba(255,255,255,0.92); backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255,215,0,0.06); min-height: calc(100vh - 70px); padding: 20px 0;
            position: sticky; top: 70px;
        }
        .sidebar .nav-link { color: #4a4a5a; padding: 10px 20px; margin: 2px 10px; border-radius: 10px; transition: all 0.3s ease; font-weight: 500; font-size: 13px; }
        .sidebar .nav-link i { margin-right: 10px; font-size: 16px; width: 22px; text-align: center; }
        .sidebar .nav-link:hover { background: rgba(255,215,0,0.08); color: #1a1a2e; transform: translateX(4px); }
        .sidebar .nav-link.active { background: linear-gradient(135deg, rgba(255,215,0,0.12), rgba(245,166,35,0.08)); color: #1a1a2e; font-weight: 600; }
        .sidebar .nav-link.active i { color: #FFD700; }

        .main-content { padding: 20px 25px 40px; position: relative; z-index: 1; }
        .page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; }
        .page-subtitle { color: #8a8a9a; font-size: 13px; }

        .filter-card { background: white; border-radius: 18px; padding: 20px; box-shadow: 0 2px 20px rgba(0,0,0,0.04); border: 1px solid rgba(255,215,0,0.04); margin-bottom: 24px; }
        .table-card { background: white; border-radius: 18px; padding: 0; box-shadow: 0 2px 20px rgba(0,0,0,0.04); border: 1px solid rgba(255,215,0,0.04); overflow: hidden; }
        .table-card .card-header { padding: 16px 20px; border-bottom: 1px solid rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; background: transparent; }
        .table-card .card-header h5 { font-size: 15px; font-weight: 600; color: #1a1a2e; margin: 0; }
        .table-card .card-header h5 i { color: #FFD700; margin-right: 8px; }
        .table-card .card-body { padding: 0; }
        .table-custom { margin: 0; }
        .table-custom thead th { background: rgba(255,215,0,0.04); color: #4a4a5a; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 14px; border-bottom: 1px solid rgba(0,0,0,0.03); }
        .table-custom tbody td { padding: 10px 14px; font-size: 13px; color: #2a2a3a; border-bottom: 1px solid rgba(0,0,0,0.02); vertical-align: middle; }
        .table-custom tbody tr:hover { background: rgba(255,215,0,0.03); }
        .badge-time { background: rgba(255,215,0,0.1); color: #B8860B; font-weight: 600; padding: 2px 10px; border-radius: 20px; font-size: 11px; }
        .badge-tema { background: rgba(147,51,234,0.08); color: #7C3AED; font-weight: 500; padding: 2px 10px; border-radius: 20px; font-size: 11px; }
        .btn-action { padding: 4px 8px; border-radius: 8px; font-size: 13px; transition: all 0.3s ease; }
        .btn-action:hover { transform: scale(1.05); }
        .btn-edit { background: rgba(255,215,0,0.1); color: #B8860B; border: none; }
        .btn-edit:hover { background: #FFD700; color: #0a0615; }
        .btn-delete { background: rgba(239,68,68,0.1); color: #EF4444; border: none; }
        .btn-delete:hover { background: #EF4444; color: white; }
        .btn-view { background: rgba(59,130,246,0.1); color: #3B82F6; border: none; }
        .btn-view:hover { background: #3B82F6; color: white; }

        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(255,255,255,0.95); backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,215,0,0.06); display: none; padding: 6px 0; z-index: 100;
            justify-content: space-around; align-items: center;
        }
        .bottom-nav a { display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #8a8a9a; font-size: 9px; font-weight: 500; padding: 4px 10px; border-radius: 10px; transition: all 0.3s ease; gap: 1px; }
        .bottom-nav a i { font-size: 18px; }
        .bottom-nav a.active { color: #FFD700; }
        .bottom-nav .nav-center { background: linear-gradient(135deg, #FFD700, #f5a623); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-top: -18px; box-shadow: 0 4px 20px rgba(255,215,0,0.3); }
        .bottom-nav .nav-center i { color: #0a0615; font-size: 20px; }
        .bottom-nav .nav-center span { display: none; }

        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state i { font-size: 48px; color: #e0d5c8; margin-bottom: 12px; }
        .empty-state p { color: #8a8a9a; font-size: 14px; }

        @media (max-width: 991px) { .sidebar { display: none; } .bottom-nav { display: flex; } .main-content { padding: 15px; } }
        @media (max-width: 576px) { .filter-card .row { gap: 8px; } .table-custom thead th, .table-custom tbody td { padding: 6px 8px; font-size: 11px; } }
    </style>
</head>
<body>
    <div class="islamic-bg">
        <div class="stars-container" id="stars"></div>
        <div class="moon-wrap"><div class="moon"></div></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="geometric-pattern"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom">
        <div class="container-fluid">
            <div class="row align-items-center w-100">
                <div class="col-md-4 col-6">
                    <a href="../dashboard.php" class="brand">
                        <i class="bi bi-mosque"></i>
                        <span>Sistem <span class="gold">Kajian</span></span>
                    </a>
                </div>
                <div class="col-md-8 col-6 text-end">
                    <span style="font-size:12px;color:#8a8a9a;margin-right:12px;"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['nama_lengkap']; ?></span>
                    <a href="../logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3">
                <div class="sidebar">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="../dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link active" href="read.php"><i class="bi bi-calendar-event"></i> Data Kajian</a></li>
                        <li class="nav-item"><a class="nav-link" href="../ustaz/read.php"><i class="bi bi-people"></i> Data Ustaz</a></li>
                        <li class="nav-item"><a class="nav-link" href="../masjid/read.php"><i class="bi bi-building"></i> Data Masjid</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main -->
            <div class="col-lg-10 col-md-9">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="page-title"><i class="bi bi-calendar-event" style="color:#FFD700;"></i> Data Kajian</h1>
                            <p class="page-subtitle">Kelola semua jadwal kajian</p>
                        </div>
                        <a href="create.php" class="btn" style="background:linear-gradient(135deg,#FFD700,#f5a623);color:#0a0615;font-weight:600;border-radius:12px;padding:10px 24px;">
                            <i class="bi bi-plus-circle"></i> Tambah Kajian
                        </a>
                    </div>

                    <!-- Filter -->
                    <div class="filter-card">
                        <form method="GET" action="" class="row g-2">
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" name="search" placeholder="🔍 Cari..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control form-control-sm" name="filter_tanggal" value="<?php echo isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="filter_masjid">
                                    <option value="">Semua Masjid</option>
                                    <?php while ($row = mysqli_fetch_assoc($masjid_list)): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['filter_masjid']) && $_GET['filter_masjid'] == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['nama_masjid']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select form-select-sm" name="filter_ustaz">
                                    <option value="">Semua Ustaz</option>
                                    <?php while ($row = mysqli_fetch_assoc($ustaz_list)): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['filter_ustaz']) && $_GET['filter_ustaz'] == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['nama_ustaz']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm w-100" style="background:linear-gradient(135deg,#FFD700,#f5a623);color:#0a0615;font-weight:600;border-radius:10px;">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="table-card">
                        <div class="card-header">
                            <h5><i class="bi bi-list"></i> Daftar Kajian</h5>
                            <span style="font-size:12px;color:#8a8a9a;">Total: <?php echo $total_data; ?> data</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-custom">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul</th>
                                            <th>Tema</th>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Ustaz</th>
                                            <th>Masjid</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['judul']); ?></strong></td>
                                            <td><span class="badge-tema"><?php echo htmlspecialchars($row['tema']); ?></span></td>
                                            <td><?php echo format_tanggal($row['tanggal']); ?></td>
                                            <td><span class="badge-time"><?php echo format_jam($row['jam']); ?></span></td>
                                            <td><i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nama_ustaz']); ?></td>
                                            <td><i class="bi bi-building"></i> <?php echo htmlspecialchars($row['nama_masjid']); ?></td>
                                            <td>
                                                <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-action btn-edit"><i class="bi bi-pencil"></i></a>
                                                <a href="javascript:void(0)" onclick="konfirmasiHapus('read.php?delete=1&id=<?php echo $row['id']; ?>', 'Apakah Anda yakin ingin menghapus kajian &quot;<?php echo htmlspecialchars($row['judul']); ?>&quot;?')" class="btn btn-action btn-delete"><i class="bi bi-trash"></i></a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                        <?php else: ?>
                                        <tr>
                                            <td colspan="8">
                                                <div class="empty-state">
                                                    <i class="bi bi-inbox"></i>
                                                    <p>Belum ada data kajian</p>
                                                    <a href="create.php" class="btn" style="background:linear-gradient(135deg,#FFD700,#f5a623);color:#0a0615;font-weight:600;border-radius:10px;padding:8px 20px;">
                                                        <i class="bi bi-plus-circle"></i> Tambah Kajian
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav class="p-3">
                                <ul class="pagination justify-content-center mb-0">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>&filter_tanggal=<?php echo isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : ''; ?>&filter_masjid=<?php echo isset($_GET['filter_masjid']) ? htmlspecialchars($_GET['filter_masjid']) : ''; ?>&filter_ustaz=<?php echo isset($_GET['filter_ustaz']) ? htmlspecialchars($_GET['filter_ustaz']) : ''; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>&filter_tanggal=<?php echo isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : ''; ?>&filter_masjid=<?php echo isset($_GET['filter_masjid']) ? htmlspecialchars($_GET['filter_masjid']) : ''; ?>&filter_ustaz=<?php echo isset($_GET['filter_ustaz']) ? htmlspecialchars($_GET['filter_ustaz']) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>&filter_tanggal=<?php echo isset($_GET['filter_tanggal']) ? htmlspecialchars($_GET['filter_tanggal']) : ''; ?>&filter_masjid=<?php echo isset($_GET['filter_masjid']) ? htmlspecialchars($_GET['filter_masjid']) : ''; ?>&filter_ustaz=<?php echo isset($_GET['filter_ustaz']) ? htmlspecialchars($_GET['filter_ustaz']) : ''; ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="../dashboard.php"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
        <a href="read.php" class="active"><i class="bi bi-calendar-event"></i><span>Kajian</span></a>
        <a href="create.php" class="nav-center"><i class="bi bi-plus-lg"></i><span>Tambah</span></a>
        <a href="../ustaz/read.php"><i class="bi bi-people"></i><span>Ustaz</span></a>
        <a href="../masjid/read.php"><i class="bi bi-building"></i><span>Masjid</span></a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Stars
        (function() {
            const container = document.getElementById('stars');
            for (let i = 0; i < 50; i++) {
                const star = document.createElement('div');
                const size = 1.5 + Math.random() * 2;
                star.className = 'star ' + (Math.random() > 0.6 ? 'gold' : 'white');
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.setProperty('--duration', (2 + Math.random() * 4) + 's');
                star.style.animationDelay = Math.random() * 5 + 's';
                star.style.width = size + 'px';
                star.style.height = size + 'px';
                container.appendChild(star);
            }
        })();

        // Konfirmasi Hapus
        function konfirmasiHapus(url, pesan) {
            if (confirm(pesan || 'Apakah Anda yakin ingin menghapus data ini?')) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</body>
</html>