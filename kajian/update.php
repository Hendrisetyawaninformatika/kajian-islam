<?php
// =============================================
// FILE: kajian/update.php
// FUNGSI: Halaman edit data kajian
// =============================================

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit();
}
require_once '../koneksi.php';

// Ambil data kajian berdasarkan ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$data = null;
$error = '';
$message = '';

if ($id > 0) {
    $query = "SELECT * FROM kajian WHERE id = $id";
    $result = mysqli_query($koneksi, $query);
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        $error = 'Data tidak ditemukan!';
    }
} else {
    $error = 'ID tidak valid!';
}

// Ambil data ustaz dan masjid untuk dropdown
$ustaz_list = mysqli_query($koneksi, "SELECT id, nama_ustaz FROM ustaz ORDER BY nama_ustaz ASC");
$masjid_list = mysqli_query($koneksi, "SELECT id, nama_masjid FROM masjid ORDER BY nama_masjid ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $judul = bersihkan($_POST['judul']);
    $tema = bersihkan($_POST['tema']);
    $tanggal = bersihkan($_POST['tanggal']);
    $jam = bersihkan($_POST['jam']);
    $ustaz_id = (int)$_POST['ustaz_id'];
    $masjid_id = (int)$_POST['masjid_id'];
    $deskripsi = bersihkan($_POST['deskripsi']);
    
    if (empty($judul) || empty($tema) || empty($tanggal) || empty($jam) || $ustaz_id == 0 || $masjid_id == 0) {
        $error = 'Semua field wajib diisi!';
    } else {
        $query = "UPDATE kajian SET 
                  judul = '$judul',
                  tema = '$tema',
                  tanggal = '$tanggal',
                  jam = '$jam',
                  ustaz_id = $ustaz_id,
                  masjid_id = $masjid_id,
                  deskripsi = '$deskripsi'
                  WHERE id = $id";
        
        if (mysqli_query($koneksi, $query)) {
            $message = notifikasi('Data kajian berhasil diperbarui!', 'success');
            $result = mysqli_query($koneksi, "SELECT * FROM kajian WHERE id = $id");
            $data = mysqli_fetch_assoc($result);
        } else {
            $error = 'Error: ' . mysqli_error($koneksi);
        }
    }
}

$total_kajian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kajian"))['total'];
$total_ustaz = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ustaz"))['total'];
$total_masjid = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM masjid"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kajian - Sistem Informasi Jadwal Kajian Islam</title>
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

        .form-card { background: white; border-radius: 18px; padding: 30px; box-shadow: 0 2px 20px rgba(0,0,0,0.04); border: 1px solid rgba(255,215,0,0.04); }
        .form-card .form-label { font-size: 13px; font-weight: 600; color: #2d2d3f; }
        .form-control, .form-select { border: 2px solid #eef0f5; border-radius: 12px; padding: 10px 14px; font-size: 13px; transition: all 0.3s ease; }
        .form-control:focus, .form-select:focus { border-color: #FFD700; box-shadow: 0 0 0 4px rgba(255,215,0,0.06); }
        .btn-save { background: linear-gradient(135deg, #FFD700, #f5a623); border: none; color: #0a0615; font-weight: 700; padding: 12px 30px; border-radius: 12px; transition: all 0.3s ease; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(255,215,0,0.25); }
        .btn-back { background: none; border: 1px solid #e0d5c8; color: #6a5a7a; padding: 12px 30px; border-radius: 12px; transition: all 0.3s ease; }
        .btn-back:hover { background: #f5f0eb; }

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

        .alert-custom { border-radius: 12px; padding: 12px 18px; font-size: 13px; font-weight: 500; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-custom.success { background: #e8f5e9; color: #2e7d32; border: 1px solid rgba(16,185,129,0.1); }
        .alert-custom.error { background: #fbe9e7; color: #c62828; border: 1px solid rgba(239,68,68,0.1); }
        .alert-custom i { font-size: 18px; }

        @media (max-width: 991px) { .sidebar { display: none; } .bottom-nav { display: flex; } .main-content { padding: 15px; } }
        @media (max-width: 576px) { .form-card { padding: 18px; } .page-title { font-size: 18px; } }
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
                            <h1 class="page-title"><i class="bi bi-pencil-square" style="color:#FFD700;"></i> Edit Kajian</h1>
                            <p class="page-subtitle">Perbarui data kajian</p>
                        </div>
                        <a href="read.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>

                    <?php if ($error && !$data): ?>
                    <div class="alert-custom error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php else: ?>
                    
                    <?php if ($message) echo $message; ?>
                    <?php if ($error): ?>
                    <div class="alert-custom error"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="form-card">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Judul Kajian <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="judul" required value="<?php echo htmlspecialchars($data['judul']); ?>">
                                    <div class="invalid-feedback">Judul wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tema <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="tema" required value="<?php echo htmlspecialchars($data['tema']); ?>">
                                    <div class="invalid-feedback">Tema wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal" required value="<?php echo $data['tanggal']; ?>">
                                    <div class="invalid-feedback">Tanggal wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jam <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam" required value="<?php echo $data['jam']; ?>">
                                    <div class="invalid-feedback">Jam wajib diisi</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ustaz <span class="text-danger">*</span></label>
                                    <select class="form-select" name="ustaz_id" required>
                                        <option value="">-- Pilih Ustaz --</option>
                                        <?php while ($row = mysqli_fetch_assoc($ustaz_list)): ?>
                                        <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $data['ustaz_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row['nama_ustaz']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Ustaz wajib dipilih</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Masjid <span class="text-danger">*</span></label>
                                    <select class="form-select" name="masjid_id" required>
                                        <option value="">-- Pilih Masjid --</option>
                                        <?php while ($row = mysqli_fetch_assoc($masjid_list)): ?>
                                        <option value="<?php echo $row['id']; ?>" <?php echo $row['id'] == $data['masjid_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row['nama_masjid']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <div class="invalid-feedback">Masjid wajib dipilih</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" rows="4"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                                </div>
                            </div>
                            <button type="submit" name="update" class="btn-save"><i class="bi bi-save"></i> Update</button>
                            <a href="read.php" class="btn-back ms-2"><i class="bi bi-x-circle"></i> Batal</a>
                        </form>
                    </div>
                    <?php endif; ?>
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

        // Form validation
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