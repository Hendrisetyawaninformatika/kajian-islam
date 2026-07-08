<?php
// =============================================
// FILE: index.php
// FUNGSI: Halaman login aplikasi
// =============================================

session_start();
require_once 'koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // Username: admin, Password: admin123
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Jadwal Kajian Islam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #F5E6CA, #FFE4B5);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
            padding-bottom: 0;
        }
        .login-card {
            background: rgba(255, 248, 240, 0.95);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(197, 165, 90, 0.4);
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 215, 0, 0.3);
            margin: 20px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .login-header .logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #FFD700, #F5A623);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .login-header .logo i {
            font-size: 50px;
            color: #2C1810;
        }
        .login-header h2 {
            color: #2C1810;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .login-header p {
            color: #6B5B3E;
            font-size: 14px;
        }
        .btn-login {
            background: linear-gradient(135deg, #FFD700, #F5A623);
            border: none;
            color: #2C1810;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.4);
            color: #2C1810;
        }
        .form-control {
            border: 2px solid #F5E6CA;
            border-radius: 12px;
            padding: 12px 15px;
            background: white;
        }
        .form-control:focus {
            border-color: #FFD700;
            box-shadow: 0 0 0 0.25rem rgba(255, 215, 0, 0.25);
        }
        .input-group-text {
            background: #F5E6CA;
            border: 2px solid #F5E6CA;
            border-radius: 12px 0 0 12px;
        }
        .input-group .form-control {
            border-radius: 0 12px 12px 0;
            border-left: none;
        }
        .input-group {
            border-radius: 12px;
        }
        .islamic-quote {
            text-align: center;
            font-size: 14px;
            color: #6B5B3E;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
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

    <div class="login-card fade-in">
        <div class="login-header">
            <div class="logo">
                <i class="bi bi-mosque"></i>
            </div>
            <h2>Sistem Kajian Islam</h2>
            <p>Masuk untuk mengelola jadwal kajian</p>
            <small class="text-muted">🕌 <?php echo date('d F Y'); ?> | <?php echo date('H:i'); ?></small>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
                    <div class="invalid-feedback">Username wajib diisi</div>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
                    <div class="invalid-feedback">Password wajib diisi</div>
                </div>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </button>
        </form>
        
        <div class="islamic-quote">
            <i class="bi bi-quote"></i>
            "Barang siapa yang menempuh jalan untuk mencari ilmu, maka Allah akan memudahkan baginya jalan menuju surga."
            <br><small>- HR. Muslim</small>
        </div>
        
        <div class="mt-3 text-center text-muted small">
            <p>Default: <strong>admin</strong> / <strong>admin123</strong></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>p