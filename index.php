<?php
// =============================================
// FILE: index.php
// FUNGSI: Halaman Login dan Registrasi Full HP
// =============================================

session_start();
require_once 'koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';

// =============================================
// PROSES REGISTRASI
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = isset($_POST['email']) ? mysqli_real_escape_string($koneksi, $_POST['email']) : '';
    
    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $error = 'Username, Password, dan Nama Lengkap wajib diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan Konfirmasi Password tidak sama!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } else {
        $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah digunakan! Silakan pilih username lain.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, password, nama_lengkap, email) 
                      VALUES ('$username', '$hashed_password', '$nama_lengkap', '$email')";
            
            if (mysqli_query($koneksi, $query)) {
                $success = 'Registrasi berhasil! Silakan login dengan akun Anda.';
                $mode = 'login';
            } else {
                $error = 'Registrasi gagal: ' . mysqli_error($koneksi);
            }
        }
    }
}

// =============================================
// PROSES LOGIN
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan Password wajib diisi!';
    } else {
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($koneksi, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $mode == 'login' ? 'Login' : 'Registrasi'; ?> - Sistem Informasi Jadwal Kajian Islam</title>
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

        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0615;
            position: relative;
            padding: 12px;
        }

        /* ============================================
           BACKGROUND
        ============================================ */
        .bg-layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: 
                radial-gradient(ellipse at 30% 20%, #1a0a2e 0%, #0a0615 50%, #05030a 100%);
            overflow: hidden;
        }

        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
        }
        .glow-orb.gold {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1), transparent);
            top: -100px; right: -100px;
        }
        .glow-orb.purple {
            width: 250px; height: 250px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.08), transparent);
            bottom: -80px; left: -80px;
        }

        /* ============================================
           STARS
        ============================================ */
        .stars-container {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .star {
            position: absolute;
            border-radius: 50%;
            background: white;
            animation: twinkle var(--duration) ease-in-out infinite;
        }

        .star.gold {
            background: #FFD700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.1; transform: scale(0.8); }
            50% { opacity: 0.8; transform: scale(1.2); }
        }

        /* ============================================
           MOON
        ============================================ */
        .moon-wrap {
            position: absolute;
            top: 3%;
            right: 4%;
            animation: moonFloat 10s ease-in-out infinite;
        }

        .moon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #ffe066, #f5a623);
            box-shadow: 0 0 40px rgba(255, 215, 0, 0.1);
            position: relative;
        }

        .moon::after {
            content: '';
            position: absolute;
            top: 8px;
            left: 14px;
            width: 18px;
            height: 18px;
            background: #0a0615;
            border-radius: 50%;
            opacity: 0.25;
        }

        @keyframes moonFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(3deg); }
        }

        /* ============================================
           CARD
        ============================================ */
        .login-card {
            background: rgba(18, 12, 35, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 22px;
            padding: 28px 22px 24px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 215, 0, 0.03);
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 215, 0, 0.04);
            animation: cardIn 0.6s ease-out;
            max-height: 98vh;
            overflow-y: auto;
        }

        .login-card::-webkit-scrollbar {
            width: 3px;
        }
        .login-card::-webkit-scrollbar-thumb {
            background: rgba(255, 215, 0, 0.2);
            border-radius: 10px;
        }

        @keyframes cardIn {
            0% { opacity: 0; transform: translateY(20px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ============================================
           HEADER - LEBIH KECIL UNTUK HP
        ============================================ */
        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .logo-box {
            display: inline-block;
            position: relative;
            margin-bottom: 10px;
        }

        .logo-box .ring {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1px solid rgba(255, 215, 0, 0.06);
            animation: ringSpin 6s linear infinite;
        }

        @keyframes ringSpin {
            0% { transform: rotate(0deg) scale(1); opacity: 0.2; }
            50% { transform: rotate(180deg) scale(1.08); opacity: 0.5; }
            100% { transform: rotate(360deg) scale(1); opacity: 0.2; }
        }

        .logo-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #FFD700, #f5a623);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 30px rgba(255, 215, 0, 0.1);
            position: relative;
        }

        .logo-icon i {
            font-size: 26px;
            color: #0a0615;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #f0ebff;
            margin-bottom: 1px;
            letter-spacing: -0.3px;
        }

        .header h1 .gold {
            color: #FFD700;
        }

        .header .sub {
            color: #7a6a8a;
            font-size: 12px;
            font-weight: 400;
        }

        .header .sub .dot {
            color: #FFD700;
            display: inline-block;
            animation: dotAnim 2s ease-in-out infinite;
        }

        @keyframes dotAnim {
            0%, 100% { transform: translateY(0); opacity: 0.4; }
            50% { transform: translateY(-2px); opacity: 1; }
        }

        .header .time {
            display: block;
            margin-top: 5px;
            font-size: 10px;
            color: #4a3a5a;
            font-weight: 300;
            letter-spacing: 0.3px;
        }

        .header .time .g {
            color: #FFD700;
        }

        /* ============================================
           DIVIDER
        ============================================ */
        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .divider .line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(255, 215, 0, 0.06), transparent);
        }

        .divider .gem {
            width: 5px;
            height: 5px;
            background: #FFD700;
            transform: rotate(45deg);
            opacity: 0.2;
            flex-shrink: 0;
        }

        /* ============================================
           ALERT - LEBIH KECIL
        ============================================ */
        .alert-box {
            border-radius: 10px;
            border: none;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: alertIn 0.4s ease-out;
        }

        @keyframes alertIn {
            0% { opacity: 0; transform: translateY(-6px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .alert-box.error {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.06);
        }

        .alert-box.success {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.06);
        }

        .alert-box i {
            font-size: 16px;
        }

        .alert-box .close-btn {
            margin-left: auto;
            font-size: 11px;
            padding: 2px 6px;
            background: transparent;
            border: none;
            color: inherit;
            opacity: 0.4;
            cursor: pointer;
        }

        /* ============================================
           FORM - LEBIH KOMPAK UNTUK HP
        ============================================ */
        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            font-size: 11px;
            font-weight: 500;
            color: #a090b0;
            margin-bottom: 4px;
            display: block;
            letter-spacing: 0.2px;
        }

        .form-group label .req {
            color: #ff6b6b;
        }

        .input-wrap {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .input-wrap:focus-within {
            border-color: rgba(255, 215, 0, 0.15);
            background: rgba(255, 255, 255, 0.05);
        }

        .input-wrap .icon {
            padding: 0 10px 0 12px;
            color: #3a2a4a;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-wrap:focus-within .icon {
            color: #FFD700;
        }

        .input-wrap input {
            border: none;
            background: transparent;
            padding: 10px 12px 10px 0;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            color: #f0ebff;
            width: 100%;
            outline: none;
            font-weight: 400;
        }

        .input-wrap input::placeholder {
            color: #3a2a4a;
            font-weight: 300;
            font-size: 11px;
        }

        .input-wrap .toggle {
            padding-right: 12px;
            cursor: pointer;
            color: #3a2a4a;
            font-size: 14px;
            transition: all 0.3s ease;
            background: transparent;
            border: none;
        }

        .input-wrap .toggle:hover {
            color: #FFD700;
        }

        /* ============================================
           PASSWORD STRENGTH
        ============================================ */
        .pw-strength {
            height: 2px;
            border-radius: 2px;
            margin-top: 4px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.04);
            width: 0%;
        }

        .pw-hint {
            font-size: 9px;
            color: #3a2a4a;
            margin-top: 2px;
            font-weight: 300;
        }

        /* ============================================
           BUTTONS - LEBIH KECIL UNTUK HP
        ============================================ */
        .btn-primary-custom {
            background: linear-gradient(135deg, #FFD700, #f5a623);
            border: none;
            color: #0a0615;
            font-weight: 700;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.3px;
            margin-top: 2px;
            box-shadow: 0 3px 20px rgba(255, 215, 0, 0.06);
            cursor: pointer;
        }

        .btn-primary-custom:active {
            transform: scale(0.98);
        }

        .btn-primary-custom i {
            margin-right: 6px;
        }

        .btn-success-custom {
            background: linear-gradient(135deg, #10B981, #059669);
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.3px;
            box-shadow: 0 3px 20px rgba(16, 185, 129, 0.06);
            cursor: pointer;
        }

        .btn-success-custom:active {
            transform: scale(0.98);
        }

        .btn-success-custom i {
            margin-right: 6px;
        }

        /* ============================================
           TOGGLE MODE
        ============================================ */
        .toggle-mode {
            text-align: center;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.02);
        }

        .toggle-mode p {
            font-size: 12px;
            color: #5a4a6a;
            font-weight: 400;
        }

        .toggle-mode a {
            color: #FFD700;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .toggle-mode a:active {
            opacity: 0.7;
        }

        /* ============================================
           QUOTE
        ============================================ */
        .quote {
            text-align: center;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.02);
            font-size: 10px;
            color: #3a2a4a;
            font-weight: 300;
            line-height: 1.6;
        }

        .quote .q-icon {
            color: #FFD700;
            margin-right: 3px;
            opacity: 0.2;
        }

        .quote small {
            display: block;
            margin-top: 2px;
            font-weight: 400;
            color: #2a1a3a;
            font-size: 9px;
        }

        /* ============================================
           RESPONSIVE - HP
        ============================================ */
        @media (max-width: 480px) {
            .login-card {
                padding: 20px 16px 18px;
                border-radius: 18px;
                max-width: 100%;
            }

            .header h1 {
                font-size: 17px;
            }

            .logo-icon {
                width: 48px;
                height: 48px;
            }

            .logo-icon i {
                font-size: 22px;
            }

            .input-wrap input {
                font-size: 11px;
                padding: 9px 10px 9px 0;
            }

            .btn-primary-custom,
            .btn-success-custom {
                padding: 10px;
                font-size: 12px;
            }

            .moon {
                width: 40px;
                height: 40px;
            }

            .moon::after {
                top: 6px;
                left: 11px;
                width: 14px;
                height: 14px;
            }

            .moon-wrap {
                top: 2%;
                right: 3%;
            }

            .header .sub {
                font-size: 11px;
            }

            .form-group {
                margin-bottom: 10px;
            }

            .form-group label {
                font-size: 10px;
            }

            .toggle-mode p {
                font-size: 11px;
            }

            .toggle-mode a {
                font-size: 11px;
            }

            .quote {
                font-size: 9px;
                margin-top: 12px;
                padding-top: 10px;
            }
        }

        @media (max-width: 360px) {
            .login-card {
                padding: 16px 12px 14px;
                border-radius: 14px;
            }

            .header h1 {
                font-size: 15px;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
            }

            .logo-icon i {
                font-size: 18px;
            }

            .input-wrap input {
                font-size: 10px;
                padding: 7px 8px 7px 0;
            }

            .btn-primary-custom,
            .btn-success-custom {
                padding: 8px;
                font-size: 11px;
            }

            .header .sub {
                font-size: 10px;
            }
        }

        /* ============================================
           SCROLLBAR HIDE
        ============================================ */
        .login-card {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 215, 0, 0.1) transparent;
        }
    </style>
</head>
<body>
    <!-- ============================================
         BACKGROUND
    ============================================ -->
    <div class="bg-layer">
        <div class="glow-orb gold"></div>
        <div class="glow-orb purple"></div>

        <!-- Stars -->
        <div class="stars-container" id="stars"></div>

        <!-- Moon -->
        <div class="moon-wrap">
            <div class="moon"></div>
        </div>
    </div>

    <!-- ============================================
         CARD
    ============================================ -->
    <div class="login-card">
        <!-- Header -->
        <div class="header">
            <div class="logo-box">
                <div class="ring"></div>
                <div class="logo-icon">
                    <i class="bi bi-mosque"></i>
                </div>
            </div>
            <h1>Selamat <span class="gold">Datang</span></h1>
            <div class="sub">
                <span class="dot">✦</span>
                <?php echo $mode == 'login' ? 'Masuk ke sistem kajian islam' : 'Daftar untuk mulai menggunakan'; ?>
            </div>
            <span class="time">
                <span class="g">✦</span>
                <?php echo date('d M Y'); ?>
                <span style="margin:0 4px;color:#2a1a3a;">·</span>
                <?php echo date('H:i'); ?>
                <span class="g">✦</span>
            </span>
        </div>

        <!-- Divider -->
        <div class="divider">
            <span class="line"></span>
            <span class="gem"></span>
            <span class="line"></span>
        </div>

        <!-- Alert -->
        <?php if ($error): ?>
        <div class="alert-box error">
            <i class="bi bi-exclamation-circle"></i>
            <?php echo $error; ?>
            <button class="close-btn" onclick="this.parentElement.remove()">✕</button>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert-box success">
            <i class="bi bi-check-circle"></i>
            <?php echo $success; ?>
            <button class="close-btn" onclick="this.parentElement.remove()">✕</button>
        </div>
        <?php endif; ?>

        <!-- ===== FORM LOGIN ===== -->
        <?php if ($mode == 'login'): ?>
        <form method="POST" action="" class="needs-validation" novalidate>
            <input type="hidden" name="login" value="1">

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-person"></i></span>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username" autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password" autocomplete="current-password">
                    <button type="button" class="toggle" onclick="togglePass('password')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </button>
        </form>

        <div class="toggle-mode">
            <p>Belum punya akun? <a href="?mode=register">Daftar di sini</a></p>
        </div>

        <!-- ===== FORM REGISTRASI ===== -->
        <?php else: ?>
        <form method="POST" action="" class="needs-validation" novalidate>
            <input type="hidden" name="register" value="1">

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap <span class="req">*</span></label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-person-badge"></i></span>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan nama lengkap">
                </div>
            </div>

            <div class="form-group">
                <label for="username_reg">Username <span class="req">*</span></label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-person"></i></span>
                    <input type="text" id="username_reg" name="username" required placeholder="Minimal 3 karakter" minlength="3">
                </div>
                <div class="pw-hint">Username minimal 3 karakter dan harus unik</div>
            </div>

            <div class="form-group">
                <label for="email_reg">Email</label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-envelope"></i></span>
                    <input type="email" id="email_reg" name="email" placeholder="Masukkan email (opsional)">
                </div>
            </div>

            <div class="form-group">
                <label for="password_reg">Password <span class="req">*</span></label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password_reg" name="password" required placeholder="Minimal 6 karakter" minlength="6">
                    <button type="button" class="toggle" onclick="togglePass('password_reg')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="pw-strength" id="pwStrength"></div>
                <div class="pw-hint">Password minimal 6 karakter</div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password <span class="req">*</span></label>
                <div class="input-wrap">
                    <span class="icon"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password">
                    <button type="button" class="toggle" onclick="togglePass('confirm_password')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-success-custom">
                <i class="bi bi-person-plus"></i> Daftar Sekarang
            </button>
        </form>

        <div class="toggle-mode">
            <p>Sudah punya akun? <a href="?mode=login">Login di sini</a></p>
        </div>
        <?php endif; ?>

        <!-- Quote -->
        <div class="quote">
            <span class="q-icon">ﷺ</span>
            "Barang siapa yang menempuh jalan untuk mencari ilmu,
            maka Allah akan memudahkan baginya jalan menuju surga."
            <small>— HR. Muslim</small>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // STARS
        // ============================================
        (function() {
            const container = document.getElementById('stars');
            const count = 80;
            for (let i = 0; i < count; i++) {
                const star = document.createElement('div');
                const size = 1 + Math.random() * 2;
                const isGold = Math.random() > 0.65;
                star.className = 'star' + (isGold ? ' gold' : '');
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.setProperty('--duration', (2 + Math.random() * 3) + 's');
                star.style.animationDelay = Math.random() * 5 + 's';
                star.style.width = size + 'px';
                star.style.height = size + 'px';
                container.appendChild(star);
            }
        })();

        // ============================================
        // TOGGLE PASSWORD
        // ============================================
        function togglePass(id) {
            const input = document.getElementById(id);
            const icon = input.parentElement.querySelector('.toggle i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        // ============================================
        // PASSWORD STRENGTH
        // ============================================
        (function() {
            const pw = document.getElementById('password_reg');
            const bar = document.getElementById('pwStrength');
            if (pw && bar) {
                pw.addEventListener('keyup', function() {
                    const v = this.value;
                    let s = 0;
                    if (v.length >= 6) s++;
                    if (/[a-z]/.test(v)) s++;
                    if (/[A-Z]/.test(v)) s++;
                    if (/[0-9]/.test(v)) s++;
                    if (/[$@#&!]/.test(v)) s++;
                    const colors = ['#ef4444', '#f59e0b', '#fbbf24', '#34d399', '#10b981'];
                    const widths = ['20%', '40%', '60%', '80%', '100%'];
                    const idx = Math.min(s, 4);
                    bar.style.width = widths[idx];
                    bar.style.background = colors[idx];
                });
            }
        })();

        // ============================================
        // PASSWORD MATCH
        // ============================================
        (function() {
            const pw = document.getElementById('password_reg');
            const cp = document.getElementById('confirm_password');
            if (pw && cp) {
                cp.addEventListener('keyup', function() {
                    this.setCustomValidity(this.value && this.value !== pw.value ? 'Password tidak sama!' : '');
                });
                pw.addEventListener('keyup', function() {
                    if (cp.value && cp.value !== this.value) {
                        cp.setCustomValidity('Password tidak sama!');
                    } else {
                        cp.setCustomValidity('');
                    }
                });
            }
        })();

        // ============================================
        // AUTO CLOSE ALERT
        // ============================================
        document.querySelectorAll('.alert-box').forEach(function(el) {
            setTimeout(function() {
                el.style.transition = 'opacity 0.4s';
                el.style.opacity = '0';
                setTimeout(function() { el.remove(); }, 400);
            }, 5000);
        });

        // ============================================
        // FORM VALIDATION
        // ============================================
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
