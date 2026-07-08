<?php
// =============================================
// FILE: kajian/create.php
// FUNGSI: Halaman tambah data kajian
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

// Ambil data ustaz dan masjid untuk dropdown
$ustaz_list = mysqli_query($koneksi, "SELECT id, nama_ustaz FROM ustaz ORDER BY nama_ustaz ASC");
$masjid_list = mysqli_query($koneksi, "SELECT id, nama_masjid FROM masjid ORDER BY nama_masjid ASC");

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = bersihkan($_POST['judul']);
    $tema = bersihkan($_POST['tema']);
    $tanggal = bersihkan($_POST['tanggal']);
    $jam = bersihkan($_POST['jam']);
    $ustaz_id = (int)$_POST['ustaz_id'];
    $masjid_id = (int)$_POST['masjid_id'];
    $deskripsi = bersihkan($_POST['deskripsi']);
    
    // Validasi
    if (empty($judul) || empty($tema) || empty($tanggal) || empty($jam) || $ustaz_id == 0 || $masjid_id == 0) {
        $error = 'Semua field wajib diisi!';
    } else {
        $query = "INSERT INTO kajian (judul, tema, tanggal, jam, ustaz_id, masjid_id, deskripsi) 
                  VALUES ('$judul', '$tema', '$tanggal', '$jam', $ustaz_id, $masjid_id, '$deskripsi')";
        
        if (mysqli_query($koneksi, $query)) {
            $message = notifikasi('Data kajian berhasil ditambahkan!', 'success');
        } else {
            $error = 'Error: ' . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kajian - Sistem Informasi Jadwal Kajian Islam</title>
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
                    <h1 class="h2"><i class="bi bi-plus-circle text-warning"></i> Tambah Kajian</h1>
                    <a href="read.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <?php echo $message; ?>
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="judul" class="form-label fw-semibold">Judul Kajian <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul" name="judul" required placeholder="Contoh: Kajian Tafsir Al-Quran">
                                    <div class="invalid-feedback">Judul kajian wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tema" class="form-label fw-semibold">Tema <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tema" name="tema" required placeholder="Contoh: Tafsir Surat Al-Fatihah">
                                    <div class="invalid-feedback">Tema wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                    <div class="invalid-feedback">Tanggal wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jam" class="form-label fw-semibold">Jam <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="jam" name="jam" required>
                                    <div class="invalid-feedback">Jam wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ustaz_id" class="form-label fw-semibold">Ustaz <span class="text-danger">*</span></label>
                                    <select class="form-select" id="ustaz_id" name="ustaz_id" required>
                                        <option value="">-- Pilih Ustaz --</option>
                                        <?php while ($row = mysqli_fetch_assoc($ustaz_list)): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nama_ustaz']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Ustaz wajib dipilih</div>
                                    <small class="text-muted">Belum ada ustaz? <a href="../ustaz/create.php">Tambah ustaz</a></small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="masjid_id" class="form-label fw-semibold">Masjid <span class="text-danger">*</span></label>
                                    <select class="form-select" id="masjid_id" name="masjid_id" required>
                                        <option value="">-- Pilih Masjid --</option>
                                        <?php while ($row = mysqli_fetch_assoc($masjid_list)): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nama_masjid']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Masjid wajib dipilih</div>
                                    <small class="text-muted">Belum ada masjid? <a href="../masjid/create.php">Tambah masjid</a></small>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Masukkan deskripsi kajian..."></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                            <a href="read.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>

                <!-- Bottom Navigation -->
                <nav class="bottom-navbar">
                    <a href="../dashboard.php" class="nav-item">
                        <i class="bi bi-house"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="read.php" class="nav-item active">
                        <i class="bi bi-calendar-event"></i>
                        <span>Kajian</span>
                    </a>
                    <a href="create.php" class="nav-item" style="background: var(--secondary-color); border-radius: 50%; width: 60px; height: 60px; margin-top: -20px; box-shadow: 0 5px 20px var(--shadow-color);">
                        <i class="bi bi-plus-circle" style="font-size: 30px;"></i>
                        <span style="display: none;">Tambah</span>
                    </a>
                    <a href="../ustaz/read.php" class="nav-item">
                        <i class="bi bi-people"></i>
                        <span>Ustaz</span>
                    </a>
                    <a href="../masjid/read.php" class="nav-item">
                        <i class="bi bi-building"></i>
                        <span>Masjid</span>
                    </a>
                </nav>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>