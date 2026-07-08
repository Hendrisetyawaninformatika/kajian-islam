<?php
// =============================================
// FILE: dashboard.php
// FUNGSI: Halaman utama/dashboard aplikasi
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}
require_once 'koneksi.php';

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Jadwal Kajian Islam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Islamic Animated Background -->
    <div class="islamic-bg">
        <div class="stars"></div>
        <div class="moon"></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="shooting-star"></div>
        <div class="geometric-pattern"></div>
    </div>

    <!-- Top Header -->
    <div class="top-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="brand">
                    <i class="bi bi-mosque"></i>
                    <span>Sistem Informasi Jadwal Kajian Islam</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-dark"><i class="bi bi-person-circle"></i> <?php echo $_SESSION['username']; ?></span>
                    <span class="text-dark"><i class="bi bi-clock"></i> <?php echo date('d F Y H:i'); ?></span>
                    <a href="logout.php" class="btn btn-sm btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-house"></i> Dashboard
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
                    <hr>
                    <div class="p-3">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?php echo date('d F Y'); ?>
                            <br>
                            <i class="bi bi-moon"></i> <?php echo getIslamicDate(); ?>
                        </small>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="text-muted">
                            <i class="bi bi-calendar-check"></i> Selamat datang, <?php echo $_SESSION['username']; ?>!
                        </span>
                    </div>
                </div>

                <!-- Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card card-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Total Kajian</h6>
                                        <h2 class="text-white counter" data-target="<?php echo $total_kajian; ?>">0</h2>
                                    </div>
                                    <i class="bi bi-calendar-event" style="font-size: 3rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="kajian/read.php" class="text-white text-decoration-none">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card card-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Total Ustaz</h6>
                                        <h2 class="text-white counter" data-target="<?php echo $total_ustaz; ?>">0</h2>
                                    </div>
                                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="ustaz/read.php" class="text-white text-decoration-none">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card card-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Total Masjid</h6>
                                        <h2 class="text-white counter" data-target="<?php echo $total_masjid; ?>">0</h2>
                                    </div>
                                    <i class="bi bi-building" style="font-size: 3rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="masjid/read.php" class="text-white text-decoration-none">
                                    Lihat Detail <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card card-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Kajian Hari Ini</h6>
                                        <h2 class="text-white counter" data-target="<?php echo $total_hari_ini; ?>">0</h2>
                                    </div>
                                    <i class="bi bi-clock" style="font-size: 3rem; opacity: 0.7;"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <span class="text-white"><?php echo format_tanggal($today); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jadwal Kajian Hari Ini -->
                <div class="card mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2 text-warning"></i>
                            Jadwal Kajian Hari Ini
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($total_hari_ini > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                        <td><span class="badge bg-warning text-dark"><?php echo format_jam($row['jam']); ?></span></td>
                                        <td><strong><?php echo htmlspecialchars($row['judul']); ?></strong></td>
                                        <td><i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nama_ustaz']); ?></td>
                                        <td><i class="bi bi-building"></i> <?php echo htmlspecialchars($row['nama_masjid']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tema']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Tidak ada kajian hari ini</p>
                            <a href="kajian/create.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle"></i> Tambah Kajian
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kajian Terbaru -->
                <div class="card">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock me-2 text-warning"></i>
                            Kajian Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($kajian_terbaru) > 0): ?>
                        <div class="list-group">
                            <?php while ($row = mysqli_fetch_assoc($kajian_terbaru)): ?>
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($row['judul']); ?></h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nama_ustaz']); ?>
                                        | <i class="bi bi-building"></i> <?php echo htmlspecialchars($row['nama_masjid']); ?>
                                        | <i class="bi bi-calendar"></i> <?php echo format_tanggal($row['tanggal']); ?>
                                        | <i class="bi bi-clock"></i> <?php echo format_jam($row['jam']); ?>
                                    </small>
                                </div>
                                <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($row['tema']); ?></span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-muted">Belum ada data kajian</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="footer">
                    <div class="text-center">
                        <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistem Informasi Jadwal Kajian Islam | UAS Pemrograman Web</p>
                        <small class="text-muted">🕌 "Sebaik-baik manusia adalah yang bermanfaat bagi orang lain"</small>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-navbar">
        <a href="dashboard.php" class="nav-item active">
            <i class="bi bi-house"></i>
            <span>Dashboard</span>
        </a>
        <a href="kajian/read.php" class="nav-item">
            <i class="bi bi-calendar-event"></i>
            <span>Kajian</span>
            <?php if ($total_kajian > 0): ?>
            <span class="badge-nav"><?php echo $total_kajian; ?></span>
            <?php endif; ?>
        </a>
        <a href="kajian/create.php" class="nav-item" style="background: var(--secondary-color); border-radius: 50%; width: 60px; height: 60px; margin-top: -20px; box-shadow: 0 5px 20px var(--shadow-color);">
            <i class="bi bi-plus-circle" style="font-size: 30px;"></i>
            <span style="display: none;">Tambah</span>
        </a>
        <a href="ustaz/read.php" class="nav-item">
            <i class="bi bi-people"></i>
            <span>Ustaz</span>
            <?php if ($total_ustaz > 0): ?>
            <span class="badge-nav"><?php echo $total_ustaz; ?></span>
            <?php endif; ?>
        </a>
        <a href="masjid/read.php" class="nav-item">
            <i class="bi bi-building"></i>
            <span>Masjid</span>
            <?php if ($total_masjid > 0): ?>
            <span class="badge-nav"><?php echo $total_masjid; ?></span>
            <?php endif; ?>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>