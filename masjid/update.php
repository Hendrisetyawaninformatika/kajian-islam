<?php
// =============================================
// FILE: masjid/update.php
// FUNGSI: Halaman edit data masjid
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
    $query = "SELECT * FROM masjid WHERE id = $id";
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
    $nama_masjid = bersihkan($_POST['nama_masjid']);
    $alamat = bersihkan($_POST['alamat']);
    $kecamatan = bersihkan($_POST['kecamatan']);
    $kota = bersihkan($_POST['kota']);
    $no_telepon = bersihkan($_POST['no_telepon']);
    
    if (empty($nama_masjid) || empty($alamat) || empty($kecamatan) || empty($kota)) {
        $error = 'Nama Masjid, Alamat, Kecamatan, dan Kota wajib diisi!';
    } else {
        $query = "UPDATE masjid SET 
                  nama_masjid = '$nama_masjid',
                  alamat = '$alamat',
                  kecamatan = '$kecamatan',
                  kota = '$kota',
                  no_telepon = '$no_telepon'
                  WHERE id = $id";
        
        if (mysqli_query($koneksi, $query)) {
            $message = notifikasi('Data masjid berhasil diperbarui!', 'success');
            $result = mysqli_query($koneksi, "SELECT * FROM masjid WHERE id = $id");
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
    <title>Edit Masjid - Sistem Informasi Jadwal Kajian Islam</title>
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
                    <h1 class="h2"><i class="bi bi-building-gear text-warning"></i> Edit Masjid</h1>
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
                                    <label for="nama_masjid" class="form-label fw-semibold">Nama Masjid <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_masjid" name="nama_masjid" required value="<?php echo htmlspecialchars($data['nama_masjid']); ?>">
                                    <div class="invalid-feedback">Nama masjid wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="alamat" class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="alamat" name="alamat" required value="<?php echo htmlspecialchars($data['alamat']); ?>">
                                    <div class="invalid-feedback">Alamat wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kecamatan" class="form-label fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kecamatan" name="kecamatan" required value="<?php echo htmlspecialchars($data['kecamatan']); ?>">
                                    <div class="invalid-feedback">Kecamatan wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kota" class="form-label fw-semibold">Kota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kota" name="kota" required value="<?php echo htmlspecialchars($data['kota']); ?>">
                                    <div class="invalid-feedback">Kota wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_telepon" class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($data['no_telepon']); ?>">
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
                    <a href="../ustaz/read.php" class="nav-item">
                        <i class="bi bi-people"></i>
                        <span>Ustaz</span>
                        <?php if ($total_ustaz > 0): ?>
                        <span class="badge-nav"><?php echo $total_ustaz; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="read.php" class="nav-item active">
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