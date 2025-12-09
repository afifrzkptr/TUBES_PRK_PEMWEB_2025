<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data user dari session
$nama = $_SESSION['nama'] ?? 'User';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? 'guest';

// Translate role ke Bahasa Indonesia
$roleText = [
    'admin' => 'Administrator',
    'doctor' => 'Dokter',
    'patient' => 'Pasien'
];
$roleName = $roleText[$role] ?? 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Berhasil - Sistem Informasi Rumah Sakit</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .success-container {
            max-width: 500px;
            width: 100%;
            text-align: center;
            background: white;
            padding: 48px;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(47, 65, 86, 0.08);
            border: 1px solid #C8D9E6;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2F4156 0%, #567C8D 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
        }
        
        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2F4156;
            margin-bottom: 12px;
        }
        
        p {
            font-size: 16px;
            color: #2F4156;
            opacity: 0.7;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        .user-info {
            background: #F5EFEB;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            text-align: left;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #C8D9E6;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-size: 14px;
            font-weight: 600;
            color: #2F4156;
        }
        
        .info-value {
            font-size: 14px;
            color: #567C8D;
            font-weight: 500;
        }
        
        .role-badge {
            display: inline-block;
            padding: 6px 16px;
            background: #2F4156;
            color: white;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #2F4156;
            color: white;
        }
        
        .btn-primary:hover {
            background: #567C8D;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: white;
            color: #2F4156;
            border: 2px solid #C8D9E6;
        }
        
        .btn-secondary:hover {
            border-color: #567C8D;
            color: #567C8D;
        }
        
        .alert-info {
            background: #E8F4F8;
            border: 1px solid #B8E6F5;
            color: #0D5C75;
            padding: 16px;
            border-radius: 10px;
            margin-top: 24px;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 6L9 17L4 12" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        
        <h1>Login Berhasil!</h1>
        <p>Selamat datang kembali, Anda berhasil masuk ke sistem.</p>
        
        <div class="user-info">
            <div class="info-item">
                <span class="info-label">Nama</span>
                <span class="info-value"><?php echo htmlspecialchars($nama); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Role</span>
                <span class="role-badge"><?php echo htmlspecialchars($roleName); ?></span>
            </div>
        </div>
        
        <div class="btn-group">
            <a href="logout.php" class="btn btn-secondary">Logout</a>
            <a href="login.php" class="btn btn-primary">Ke Halaman Login</a>
        </div>
        
        <div class="alert-info">
            <strong>📌 Catatan:</strong> Halaman dashboard belum tersedia. Ini adalah halaman sementara untuk menunjukkan login berhasil. Session Anda tetap aktif.
        </div>
    </div>
</body>
</html>
