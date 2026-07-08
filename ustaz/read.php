<?php
// =============================================
// FILE: ustaz/read.php
// FUNGSI: Halaman daftar/data ustaz
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

// Search
$search_condition = "1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = bersihkan($_GET['search']);
    $search_condition .= " AND (nama_ustaz LIKE '%$search%' OR bidang_keilmuan LIKE '%$search%' OR alamat LIKE '%$search%')";
}

// Query total data
$query_total = "SELECT COUNT(*) as total FROM ustaz WHERE $search_condition";
$total_result = mysqli_query($koneksi, $query_total);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_data / $limit);

// Query data
$query = "SELECT * FROM ustaz WHERE $search_condition ORDER BY nama_ustaz ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

// Hapus data
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query_delete = "DELETE FROM ustaz WHERE id = $id";
    if (mysqli_query($koneksi, $query_delete)) {
        echo '<script>
            alert("Data ustaz berhasil dihapus!");
            window.location.href = "read.php";
        </script>';
    } else {
        echo '<script>alert("Error: ' . mysqli_error($koneksi) . '");</script>';
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
    <title>Data Ustaz - Sistem Informasi Jadwal Kajian Islam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
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
                    <a href="../logout.php" class="btn btn-sm btn-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid main-content">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="bi bi-people text-warning"></i> Data Ustaz</h1>
                    <a href="create.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Ustaz
                    </a>
                </div>
                
                <!-- Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="search" placeholder="🔍 Cari nama ustaz, bidang keilmuan, alamat..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Ustaz</th>
                                        <th>Bidang Keilmuan</th>
                                        <th>Nomor HP</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><strong><?php echo htmlspecialchars($row['nama_ustaz']); ?></strong></td>
                                        <td><span class="badge bg-success"><?php echo htmlspecialchars($row['bidang_keilmuan']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                        <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                                        <td>
                                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="konfirmasiHapus('read.php?delete=1&id=<?php echo $row['id']; ?>', 'Apakah Anda yakin ingin menghapus ustaz &quot;<?php echo htmlspecialchars($row['nama_ustaz']); ?>&quot;?')" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                            <p class="text-muted mt-2">Belum ada data ustaz</p>
                                            <a href="create.php" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-circle"></i> Tambah Ustaz
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                        
                        <div class="text-muted small mt-2">
                            Total: <?php echo $total_data; ?> data
                        </div>
                    </div>
                </div>

                <!-- Bottom Navigation -->
                <nav class="bottom-navbar">
                    <a href="../dashboard.php" class="nav-item">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="../kajian/read.php" class="nav-item">
                        <i class="bi bi-calendar-event"></i>
                        <span>Kajian</span>
                        <?php if ($total_kajian > 0): ?>
                        <span class="badge-nav"><?php echo $total_kajian; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="../kajian/create.php" class="nav-item" style="background: var(--secondary-color); border-radius: 50%; width: 60px; height: 60px; margin-top: -20px; box-shadow: 0 5px 20px var(--shadow-color);">
                        <i class="bi bi-plus-circle" style="font-size: 30px;"></i>
                        <span style="display: none;">Tambah</span>
                    </a>
                    <a href="read.php" class="nav-item active">
                        <i class="bi bi-people"></i>
                        <span>Ustaz</span>
                        <?php if ($total_ustaz > 0): ?>
                        <span class="badge-nav"><?php echo $total_ustaz; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="../masjid/read.php" class="nav-item">
                        <i class="bi bi-building"></i>
                        <span>Masjid</span>
                        <?php if ($total_masjid > 0): ?>
                        <span class="badge-nav"><?php echo $total_masjid; ?></span>
                        <?php endif; ?>
                    </a>
                </nav>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>