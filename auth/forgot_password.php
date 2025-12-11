<?php
session_start();
require_once '../config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'Email harus diisi';
    } else {
        $conn = connect_db();
        $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Simulation: Instead of sending email, we redirect to reset page with a token
            // In a real app, generate a token, save to DB, and email the link.
            $token = bin2hex(random_bytes(16));
            // Simulate saving token...

            // Redirect to reset password (Simulation)
            header("Location: reset_password.php?email=" . urlencode($email) . "&token=" . $token);
            exit();
        } else {
            $error = 'Email tidak ditemukan';
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
    <title>Lupa Password - RS Sistem</title>
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

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #567C8D;
            text-decoration: none;
            font-size: 14px;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2 style="margin-top:0; color:#2F4156; text-align:center;">Lupa Password</h2>
        <p style="color:#666; text-align:center; font-size:14px; margin-bottom:30px;">Masukkan email akun Anda untuk
            mereset password.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="nama@email.com" required>
            </div>
            <button type="submit">Kirim Link Reset</button>
        </form>

        <a href="login.php" class="back-link">Kembali ke Login</a>
    </div>
</body>

</html>