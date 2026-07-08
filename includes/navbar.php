<?php
// =============================================
// FILE: includes/navbar.php
// FUNGSI: Navbar untuk halaman-halaman admin
// =============================================
?>
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