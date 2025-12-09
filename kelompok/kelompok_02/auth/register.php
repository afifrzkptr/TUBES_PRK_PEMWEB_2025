<?php
session_start();

// Jika sudah login, redirect ke halaman yang sesuai berdasarkan role
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'patient';
    switch ($role) {
        case 'admin':
            header('Location: ../admin/dashboard.php');
            break;
        case 'doctor':
            header('Location: ../doctor/dashboard.php');
            break;
        case 'patient':
            header('Location: ../patient/dashboard.php');
            break;
        default:
            header('Location: ../index.php');
    }
    exit();
}

$error = '';
$success = '';

// TODO: INTEGRASI DATABASE
// Nanti ganti dummy data ini dengan INSERT ke database
// Query: INSERT INTO users (nama, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())
// Password: $hashed_password = password_hash($password, PASSWORD_DEFAULT);
// Cek email exists: SELECT COUNT(*) FROM users WHERE email = ?

// DUMMY DATA - Simulasi registrasi tanpa database (HAPUS setelah integrasi database)
$dummyUsers = [
    'admin@rs.com',
    'doctor@rs.com',
    'patient@rs.com'
];

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = 'Semua field harus diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok';
    } elseif (!in_array($role, ['admin', 'dokter', 'pasien'])) {
        $error = 'Role tidak valid';
    } else {
        // Cek apakah email sudah terdaftar (dummy - nanti ganti dengan query database)
        if (in_array($email, $dummyUsers)) {
            $error = 'Email sudah terdaftar. Silakan gunakan email lain.';
        } else {
            // TODO: Setelah integrasi database, implementasikan:
            // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // $stmt = $conn->prepare("INSERT INTO users (nama, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            // $stmt->bind_param("ssss", $nama, $email, $hashed_password, $role);
            // if ($stmt->execute()) {
            //     $success = 'Registrasi berhasil! Silakan login.';
            // } else {
            //     $error = 'Terjadi kesalahan. Silakan coba lagi.';
            // }
            
            // Simulasi registrasi berhasil (dummy)
            $success = 'Registrasi berhasil! Silakan login dengan email: ' . htmlspecialchars($email);
            
            // Reset form
            $nama = '';
            $email = '';
            $role = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Informasi Rumah Sakit</title>
    <link rel="stylesheet" href="../assets/css/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="register-wrapper">
        <!-- Left Side - Branding -->
        <div class="register-brand">
            <div class="brand-content">
                <div class="brand-logo">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="60" height="60" rx="12" fill="white" fill-opacity="0.1"/>
                        <path d="M30 15C21.716 15 15 21.716 15 30C15 38.284 21.716 45 30 45C38.284 45 45 38.284 45 30C45 21.716 38.284 15 30 15ZM35 31.5H31.5V35C31.5 35.825 30.825 36.5 30 36.5C29.175 36.5 28.5 35.825 28.5 35V31.5H25C24.175 31.5 23.5 30.825 23.5 30C23.5 29.175 24.175 28.5 25 28.5H28.5V25C28.5 24.175 29.175 23.5 30 23.5C30.825 23.5 31.5 24.175 31.5 25V28.5H35C35.825 28.5 36.5 29.175 36.5 30C36.5 30.825 35.825 31.5 35 31.5Z" fill="white"/>
                    </svg>
                </div>
                <h1 class="brand-title">Bergabung<br>Bersama Kami</h1>
                <p class="brand-subtitle">Daftar sekarang dan nikmati kemudahan dalam mengelola sistem kesehatan</p>
                <div class="brand-features">
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Gratis & Mudah Digunakan</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Data Terenkripsi Aman</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Akses Multi Platform</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="register-container">
            <div class="register-box">
                <div class="register-header">
                    <h2>Buat Akun Baru</h2>
                    <p>Lengkapi formulir di bawah untuk membuat akun</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M10 6V10M10 14H10.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="register-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M10 10C12.0711 10 13.75 8.32107 13.75 6.25C13.75 4.17893 12.0711 2.5 10 2.5C7.92893 2.5 6.25 4.17893 6.25 6.25C6.25 8.32107 7.92893 10 10 10Z" stroke="#567C8D" stroke-width="1.5"/>
                                    <path d="M17.5 17.5C17.5 14.0482 14.1421 11.25 10 11.25C5.85786 11.25 2.5 14.0482 2.5 17.5" stroke="#567C8D" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <input 
                                    type="text" 
                                    id="nama" 
                                    name="nama" 
                                    placeholder="John Doe"
                                    value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M3.333 5.833C3.333 4.913 4.08 4.167 5 4.167H15C15.92 4.167 16.667 4.913 16.667 5.833V14.167C16.667 15.087 15.92 15.833 15 15.833H5C4.08 15.833 3.333 15.087 3.333 14.167V5.833Z" stroke="#567C8D" stroke-width="1.5"/>
                                    <path d="M3.333 5.833L10 10.833L16.667 5.833" stroke="#567C8D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    placeholder="nama@email.com"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5.833 9.167V6.667C5.833 4.365 7.698 2.5 10 2.5C12.302 2.5 14.167 4.365 14.167 6.667V9.167M6.667 9.167H13.333C14.254 9.167 15 9.913 15 10.833V15.833C15 16.754 14.254 17.5 13.333 17.5H6.667C5.746 17.5 5 16.754 5 15.833V10.833C5 9.913 5.746 9.167 6.667 9.167Z" stroke="#567C8D" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Minimal 6 karakter"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M5.833 9.167V6.667C5.833 4.365 7.698 2.5 10 2.5C12.302 2.5 14.167 4.365 14.167 6.667V9.167M6.667 9.167H13.333C14.254 9.167 15 9.913 15 10.833V15.833C15 16.754 14.254 17.5 13.333 17.5H6.667C5.746 17.5 5 16.754 5 15.833V10.833C5 9.913 5.746 9.167 6.667 9.167Z" stroke="#567C8D" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    placeholder="Ulangi password"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role Pengguna</label>
                        <div class="select-wrapper">
                            <svg class="select-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M6.25 3.75H3.75V6.25M3.75 13.75V16.25H6.25M13.75 16.25H16.25V13.75M16.25 6.25V3.75H13.75" stroke="#567C8D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <select id="role" name="role" required>
                                <option value="">Pilih role Anda</option>
                                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="dokter" <?php echo (isset($_POST['role']) && $_POST['role'] === 'dokter') ? 'selected' : ''; ?>>Dokter</option>
                                <option value="pasien" <?php echo (isset($_POST['role']) && $_POST['role'] === 'pasien') ? 'selected' : ''; ?>>Pasien</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-register">
                        <span>Daftar Sekarang</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.167 10H15.833M15.833 10L10.833 5M15.833 10L10.833 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
                
                <div class="register-footer">
                    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 Sistem Informasi RS DB | Tugas Akhir Praktikum Pemrograman Web</p>
                 <p>Kelompok 02 - Afif, Akeyla, Dara, Nabila Putri</p>
            </div>
        </div>
    </div>
</body>
</html>
