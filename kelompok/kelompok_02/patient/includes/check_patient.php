<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

if ($_SESSION['role'] !== 'Pasien') {
    // Redirect based on role
    switch ($_SESSION['role']) {
        case 'Admin':
            header('Location: ../admin/dashboard.php');
            break;
        case 'Dokter':
            header('Location: ../doctor/dashboard.php');
            break;
        default:
            header('Location: ../auth/login.php');
    }
    exit();
}

require_once '../config/db.php';
$conn = connect_db();

// End of file