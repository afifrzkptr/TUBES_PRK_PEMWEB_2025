<?php
/**
 * Check Admin Access
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Cek apakah role adalah Admin
if ($_SESSION['role'] !== 'Admin') {
    // Redirect ke halaman sesuai role
    switch ($_SESSION['role']) {
        case 'Dokter':
            header('Location: ../doctor/dashboard.php');
            break;
        case 'Pasien':
            header('Location: ../patient/dashboard.php');
            break;
        default:
            header('Location: ../auth/login.php');
    }
    exit();
}

// Include database connection
require_once '../config/config.lokal.php';

// Get admin info
$admin_name = $_SESSION['username'] ?? 'Admin';
$admin_email = $_SESSION['email'] ?? '';
?>
