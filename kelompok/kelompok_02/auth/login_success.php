<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data user dari session
$username = $_SESSION['username'] ?? 'User';
$email = $_SESSION['email'] ?? '';
$role = $_SESSION['role'] ?? 'guest'; // Admin, Dokter, Pasien

// Translate role ke Bahasa Indonesia
$roleText = [
    'Admin' => 'Administrator',
    'Dokter' => 'Dokter',
    'Pasien' => 'Pasien'
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
            color: #2F4156;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        
        p {
            color: #567C8D;
            font-size: 16px;
            margin-bottom: 32px;
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
            color: #567C8D;
            font-size: 14px;
            font-weight: 500;
        }
        
        .info-value {
            color: #2F4156;
            font-size: 15px;
            font-weight: 600;
        }
        
        .role-badge {
            background: linear-gradient(135deg, #2F4156 0%, #567C8D 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 150px;
        }
        
        .btn-primary {
            background: #2F4156;
            color: white;
        }
        
        .btn-primary:hover {
            background: #567C8D;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(47, 65, 86, 0.2);
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
            text-align: left;
        }
        
        @media (max-width: 480px) {
            .success-container {
                padding: 32px 24px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
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
                <span class="info-label">Username</span>
                <span class="info-value"><?php echo htmlspecialchars($username); ?></span>
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
            <a href="logout.php" class="btn btn-secondary">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M7.5 17.5H4.167C3.72 17.5 3.333 17.113 3.333 16.667V3.333C3.333 2.887 3.72 2.5 4.167 2.5H7.5M13.333 14.167L17.5 10M17.5 10L13.333 5.833M17.5 10H7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Logout
            </a>
        </div>
        
        <div class="alert-info">
            <strong>ℹ️ Info:</strong> Ini adalah halaman sementara setelah login. Dashboard akan ditambahkan nanti sesuai dengan role Anda (<?php echo htmlspecialchars($role); ?>).
        </div>
    </div>
</body>
</html>
