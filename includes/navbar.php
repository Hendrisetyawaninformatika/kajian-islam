<?php
// =============================================
// FILE: includes/navbar.php
// FUNGSI: Navbar premium dengan Islamic design
// =============================================
?>
<style>
.navbar-premium {
    background: rgba(255,255,255,0.97);
    backdrop-filter: blur(20px);
    padding: 10px 0;
    box-shadow: 0 2px 30px rgba(0,0,0,0.03);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(255,215,0,0.06);
}

.navbar-premium .brand {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a2e;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
}

.navbar-premium .brand i {
    color: #FFD700;
    font-size: 24px;
}

.navbar-premium .brand .gold {
    color: #FFD700;
}

.navbar-premium .user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.navbar-premium .user-info .user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FFD700, #f5a623);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0a0615;
    font-weight: 700;
    font-size: 14px;
}

.navbar-premium .user-info .user-name {
    font-size: 13px;
    font-weight: 500;
    color: #1a1a2e;
}

.navbar-premium .user-info .user-role {
    font-size: 9px;
    padding: 2px 12px;
    border-radius: 20px;
    background: linear-gradient(135deg, #FFD700, #f5a623);
    color: #0a0615;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.navbar-premium .user-info .time-display {
    font-size: 11px;
    color: #8a8a9a;
    font-weight: 300;
    display: flex;
    align-items: center;
    gap: 4px;
}

.navbar-premium .user-info .time-display i {
    color: #FFD700;
}

.btn-logout-premium {
    background: none;
    border: 1px solid #e8e0d8;
    color: #6a5a7a;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-logout-premium:hover {
    background: #fef0ed;
    border-color: #ff6b6b;
    color: #ff6b6b;
}

.btn-logout-premium i {
    font-size: 14px;
}

@media (max-width: 576px) {
    .navbar-premium .user-info .time-display {
        display: none;
    }
    .navbar-premium .user-info .user-name {
        font-size: 12px;
    }
    .navbar-premium .brand {
        font-size: 15px;
    }
    .btn-logout-premium {
        padding: 4px 12px;
        font-size: 11px;
    }
}
</style>

<nav class="navbar-premium">
    <div class="container-fluid">
        <div class="row align-items-center w-100">
            <div class="col-md-4 col-6">
                <a href="../dashboard.php" class="brand">
                    <i class="bi bi-mosque"></i>
                    <span>Sistem <span class="gold">Kajian</span></span>
                </a>
            </div>
            <div class="col-md-8 col-6 text-end">
                <div class="user-info justify-content-end">
                    <span class="time-display">
                        <i class="bi bi-calendar3"></i> <?php echo date('d M Y'); ?>
                        <span style="margin:0 4px;color:#d0c8c0;">|</span>
                        <i class="bi bi-clock"></i> <?php echo date('H:i'); ?>
                    </span>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                    </div>
                    <span class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></span>
                    <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
                    <a href="../logout.php" class="btn-logout-premium">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
