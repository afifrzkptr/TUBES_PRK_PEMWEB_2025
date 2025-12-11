<?php
session_start();
require_once '../config/db.php';

$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($email) || empty($token)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = "Password minimal 6 karakter";
    } elseif ($password !== $confirm) {
        $error = "Password tidak cocok";
    } else {
        $conn = connect_db();
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $email);

        if ($stmt->execute()) {
            $success = "Password berhasil diubah! Silakan login.";
        } else {
            $error = "Gagal mengubah password.";
        }
        close_db($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - RS Sistem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #F5EFEB;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2F4156;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #C8D9E6;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        button {
            background: #2F4156;
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        button:hover {
            background: #1e2a38;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2 style="margin-top:0; color:#2F4156; text-align:center;">Reset Password</h2>
        <p style="color:#666; text-align:center; font-size:14px; margin-bottom:30px;">
            Set password baru untuk akun<br><strong><?php echo htmlspecialchars($email); ?></strong>
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="login.php"
                style="display:block; text-align:center; text-decoration:none; color:#2F4156; font-weight:600;">Login
                Sekarang</a>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="confirm_password" placeholder="Ulangi password" required>
                </div>
                <button type="submit">Simpan Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>