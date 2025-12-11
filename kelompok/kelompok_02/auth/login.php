<?php
session_start();
require_once '../config/db.php';

// Jika sudah login, redirect ke halaman success sementara
if (isset($_SESSION['user_id'])) {
    header('Location: login_success.php');
    exit();
}

$error = '';

// Proses login dengan DATABASE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi';
    } else {
        $conn = connect_db();

        // Query: JOIN users dengan roles untuk mendapatkan role_name
        $stmt = $conn->prepare("
            SELECT u.id_user, u.username, u.email, u.password, u.is_active, r.role_name
            FROM users u
            JOIN roles r ON u.id_role = r.id_role
            WHERE u.email = ? AND u.is_active = 1
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil - simpan ke session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role_name']; // Admin, Dokter, Pasien
                $_SESSION['login_time'] = time();

                $stmt->close();
                close_db($conn);

                // Redirect berdasarkan role
                switch ($user['role_name']) {
                    case 'Admin':
                        header('Location: ../admin/dashboard.php');
                        break;
                    case 'Dokter':
                        header('Location: ../doctor/dashboard.php');
                        break;
                    case 'Pasien':
                        header('Location: ../patient/dashboard.php');
                        break;
                    default:
                        header('Location: login_success.php');
                }
                exit();
            } else {
                $error = 'Email atau password salah';
            }
        } else {
            $error = 'Email atau password salah';
        }

        $stmt->close();
        close_db($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Rumah Sakit</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-wrapper">
        <!-- Left Side - Branding -->
        <div class="login-brand">
            <div class="brand-content">
                <div class="brand-logo">
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="60" height="60" rx="12" fill="white" fill-opacity="0.1" />
                        <path
                            d="M30 15C21.716 15 15 21.716 15 30C15 38.284 21.716 45 30 45C38.284 45 45 38.284 45 30C45 21.716 38.284 15 30 15ZM35 31.5H31.5V35C31.5 35.825 30.825 36.5 30 36.5C29.175 36.5 28.5 35.825 28.5 35V31.5H25C24.175 31.5 23.5 30.825 23.5 30C23.5 29.175 24.175 28.5 25 28.5H28.5V25C28.5 24.175 29.175 23.5 30 23.5C30.825 23.5 31.5 24.175 31.5 25V28.5H35C35.825 28.5 36.5 29.175 36.5 30C36.5 30.825 35.825 31.5 35 31.5Z"
                            fill="white" />
                    </svg>
                </div>
                <h1 class="brand-title">Sistem Informasi<br>Rumah Sakit</h1>
                <p class="brand-subtitle">Kelola jadwal, antrian, dan rekam medis dengan cepat dan terpercaya</p>
                <div class="brand-features">
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Manajemen Data Pasien</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Jadwal Dokter Otomatis</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M16.667 5L7.5 14.167L3.333 10" stroke="white" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Keamanan Data Terjamin</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-container">
            <div class="login-box">
                <div class="login-header">
                    <h2>Selamat Datang Kembali</h2>
                    <p>Silakan masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18Z"
                                stroke="currentColor" stroke-width="2" />
                            <path d="M10 6V10M10 14H10.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="login-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path
                                    d="M3.333 5.833C3.333 4.913 4.08 4.167 5 4.167H15C15.92 4.167 16.667 4.913 16.667 5.833V14.167C16.667 15.087 15.92 15.833 15 15.833H5C4.08 15.833 3.333 15.087 3.333 14.167V5.833Z"
                                    stroke="#567C8D" stroke-width="1.5" />
                                <path d="M3.333 5.833L10 10.833L16.667 5.833" stroke="#567C8D" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <input type="email" id="email" name="email" placeholder="nama@email.com"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path
                                    d="M5.833 9.167V6.667C5.833 4.365 7.698 2.5 10 2.5C12.302 2.5 14.167 4.365 14.167 6.667V9.167M6.667 9.167H13.333C14.254 9.167 15 9.913 15 10.833V15.833C15 16.754 14.254 17.5 13.333 17.5H6.667C5.746 17.5 5 16.754 5 15.833V10.833C5 9.913 5.746 9.167 6.667 9.167Z"
                                    stroke="#567C8D" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            <input type="password" id="password" name="password" placeholder="Masukkan password"
                                required>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">Ingat saya</span>
                        </label>
                        <a href="forgot_password.php" class="forgot-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        <span>Masuk Sekarang</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.167 10H15.833M15.833 10L10.833 5M15.833 10L10.833 15" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </form>

                <div class="login-footer">
                    <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; 2025 Sistem Informasi Rumah Sakit | Tugas Akhir Praktikum Pemrograman Web</p>
                <p>Kelompok 02 - Afif, Akeyla, Dara, Nabila Putri</p>
            </div>
        </div>
    </div>
</body>

</html>