<?php
// =============================================
// FILE: ustaz/update.php
// FUNGSI: Halaman edit data ustaz
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = null;
$error = '';
$message = '';

if ($id > 0) {
    $query = "SELECT * FROM ustaz WHERE id = $id";
    $result = mysqli_query($koneksi, $query);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $error = 'Data tidak ditemukan!';
    }
} else {
    $error = 'ID tidak valid!';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $nama_ustaz = bersihkan($_POST['nama_ustaz']);
    $bidang_keilmuan = bersihkan($_POST['bidang_keilmuan']);
    $no_hp = bersihkan($_POST['no_hp']);
    $alamat = bersihkan($_POST['alamat']);
    
    if (empty($nama_ustaz) || empty($bidang_keilmuan)) {
        $error = 'Nama Ustaz dan Bidang Keilmuan wajib diisi!';
    } else {
        $query = "UPDATE ustaz SET 
                  nama_ustaz = '$nama_ustaz',
                  bidang_keilmuan = '$bidang_keilmuan',
                  no_hp = '$no_hp',
                  alamat = '$alamat'
                  WHERE id = $id";
        
        if (mysqli_query($koneksi, $query)) {
            $message = notifikasi('Data ustaz berhasil diperbarui!', 'success');
            $result = mysqli_query($koneksi, "SELECT * FROM ustaz WHERE id = $id");
            $data = mysqli_fetch_assoc($result);
        } else {
            $error = 'Error: ' . mysqli_error($koneksi);
        }
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
    <title>Edit Ustaz - Sistem Informasi Jadwal Kajian Islam</title>
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
                    <h1 class="h2"><i class="bi bi-person-gear text-warning"></i> Edit Ustaz</h1>
                    <a href="read.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <?php if ($error && !$data): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php else: ?>
                
                <?php echo $message; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_ustaz" class="form-label fw-semibold">Nama Ustaz <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_ustaz" name="nama_ustaz" required value="<?php echo htmlspecialchars($data['nama_ustaz']); ?>">
                                    <div class="invalid-feedback">Nama ustaz wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bidang_keilmuan" class="form-label fw-semibold">Bidang Keilmuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="bidang_keilmuan" name="bidang_keilmuan" required value="<?php echo htmlspecialchars($data['bidang_keilmuan']); ?>">
                                    <div class="invalid-feedback">Bidang keilmuan wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo htmlspecialchars($data['alamat']); ?>">
                                </div>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                            <a href="read.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

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