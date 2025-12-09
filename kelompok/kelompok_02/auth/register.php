<?php
session_start();

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

$error = '';
$success = '';

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
        // TODO: Koneksi database dan simpan user
        // Untuk saat ini, ini hanya template
        // Nanti akan diintegrasikan dengan database
        
        // Contoh proses simpan (ganti dengan query database)
        // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // if ($user_saved) {
        //     $success = 'Registrasi berhasil! Silakan login.';
        // } else {
        //     $error = 'Email sudah terdaftar';
        // }
        
        $error = 'Fungsi registrasi belum diimplementasikan';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Kesehatan</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <h1>Daftar Akun</h1>
                <p>Buat akun baru Anda</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="register-form">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        placeholder="Masukkan nama lengkap Anda"
                        value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Masukkan email Anda"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Minimal 6 karakter"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Masukkan ulang password Anda"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="dokter" <?php echo (isset($_POST['role']) && $_POST['role'] === 'dokter') ? 'selected' : ''; ?>>Dokter</option>
                        <option value="pasien" <?php echo (isset($_POST['role']) && $_POST['role'] === 'pasien') ? 'selected' : ''; ?>>Pasien</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-register">Daftar</button>
            </form>
            
            <div class="register-footer">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
