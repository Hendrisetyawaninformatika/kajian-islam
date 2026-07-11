<?php
// =============================================
// FILE: logout.php
// FUNGSI: Proses logout dan menghapus session
// =============================================

session_start();
session_destroy();
header('Location: index.php');
exit();
?>
