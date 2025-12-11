<?php
require_once 'config/db.php';

$conn = connect_db();
$message = "";
$status = "error";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = 'admin@klinik.com';
    $password = 'admin123';
    $username = 'admin_klinik';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $id_role = 1; // Admin role

    // Cek apakah user sudah ada
    $check = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ?, is_active = 1 WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();
        $message = "Password admin berhasil direset!";
        $status = "success";
    } else {
        // Buat user baru
        $stmt = $conn->prepare("INSERT INTO users (id_role, username, password, email, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("isss", $id_role, $username, $hashed_password, $email);
        $stmt->execute();
        $message = "Akun admin berhasil dibuat!";
        $status = "success";
    }

    close_db($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Setup Admin Account</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f0f2f5;
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .btn {
            background: #2F4156;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 1rem;
        }

        .btn:hover {
            background: #1e2a38;
        }

        .success {
            color: green;
            margin-bottom: 1rem;
        }

        .info {
            text-align: left;
            background: #e9ecef;
            padding: 1rem;
            border-radius: 4px;
            margin-top: 1rem;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2>Setup Admin</h2>
        <?php if ($message): ?>
            <p class="success"><?php echo $message; ?></p>
            <p>Silakan login di <a href="auth/login.php">Halaman Login</a></p>
        <?php else: ?>
            <p>Klik tombol di bawah untuk membuat atau mereset akun admin.</p>
            <div class="info">
                <strong>Default Credentials:</strong><br>
                Email: admin@klinik.com<br>
                Password: admin123
            </div>
            <form method="POST">
                <button type="submit" class="btn">Buat Akun Admin</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>