<?php
// =============================================
// FILE: includes/sidebar.php
// FUNGSI: Sidebar untuk halaman-halaman admin
// =============================================
?>
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="../dashboard.php">
                    <i class="bi bi-house"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'kajian') !== false ? 'active' : ''; ?>" href="../kajian/read.php">
                    <i class="bi bi-calendar-event"></i> Data Kajian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'ustaz') !== false ? 'active' : ''; ?>" href="../ustaz/read.php">
                    <i class="bi bi-people"></i> Data Ustaz
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'masjid') !== false ? 'active' : ''; ?>" href="../masjid/read.php">
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