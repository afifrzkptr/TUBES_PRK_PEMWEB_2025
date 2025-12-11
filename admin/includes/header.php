<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard Admin'; ?> - Sistem Informasi Rumah Sakit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Premium Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header with Logo -->
        <div class="sidebar-header">
            <div class="logo-container">
                <svg class="logo-icon" width="48" height="48" viewBox="0 0 48 48" fill="none">
                    <circle cx="24" cy="24" r="20" fill="url(#logoGradient)" opacity="0.2"/>
                    <path d="M24 16V32M16 24H32" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="logoGradient" x1="4" y1="4" x2="44" y2="44">
                            <stop offset="0%" stop-color="#C8D9E6"/>
                            <stop offset="100%" stop-color="#FFFFFF"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="logo-text">
                    <h2>Rumah Sakit</h2>
                    <p>Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="sidebar-nav">
            <!-- Main Menu Section -->
            <div class="nav-section">
                <span class="nav-section-title">MENU UTAMA</span>
                <a href="dashboard.php" class="nav-item <?php echo ($current_page === 'dashboard') ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 22V12H15V22" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <span>Dashboard</span>
                </a>

                <a href="users.php" class="nav-item <?php echo ($current_page === 'users') ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                            <path d="M23 21V19C22.9993 18.1137 22.7044 17.2528 22.1614 16.5523C21.6184 15.8519 20.8581 15.3516 20 15.13M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89318 18.7122 8.75608 18.1676 9.45769C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span>Users</span>
                </a>
            </div>

            <!-- Data Master Section -->
            <div class="nav-section">
                <span class="nav-section-title">DATA MASTER</span>
                <a href="patients.php" class="nav-item <?php echo ($current_page === 'patients') ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <span>Pasien</span>
                </a>

                <a href="doctors.php" class="nav-item <?php echo ($current_page === 'doctors') ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="currentColor" stroke-width="2"/>
                            <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                            <path d="M23 21V19C23 17.1 21.9 15.5 20.4 15.1M16 3.1C17.5 3.5 18.6 5.1 18.6 7C18.6 8.9 17.5 10.5 16 10.9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span>Dokter</span>
                </a>
            </div>

            <!-- Management Section -->
            <div class="nav-section">
                <span class="nav-section-title">MANAJEMEN</span>
                <a href="appointments.php" class="nav-item <?php echo ($current_page === 'appointments') ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="15" r="2" fill="currentColor"/>
                        </svg>
                    </div>
                    <span>Appointments</span>
                </a>

                <a href="medical_records.php" class="nav-item <?php echo ($current_page === 'medical_records') ? 'active' : ''; ?>">
                    <div class="nav-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M14 2V8H20M12 18V12M9 15H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span>Riwayat Medis</span>
                </a>
            </div>
        </nav>

        <!-- Sidebar Footer with User Profile -->
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="5" fill="currentColor" opacity="0.3"/>
                        <path d="M20 21C20 17.134 16.418 14 12 14C7.582 14 4 17.134 4 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <a href="../auth/logout.php" class="btn-logout" title="Logout">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 2.58579C3.96086 2.21071 4.46957 2 5 2H9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M16 17L21 12L16 7M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <main class="main-content">
        <!-- Content Area -->
        <div class="content-area">
