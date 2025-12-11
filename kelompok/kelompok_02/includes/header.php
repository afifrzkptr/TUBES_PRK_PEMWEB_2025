<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = 'http://localhost/kelompok/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_02/';
$current_role = $_SESSION['role'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistem Informasi Kesehatan</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navytube': '#2f4156',
                        'teal': '#567c8d',
                        'skyblue': '#c8d9e6',
                        'beige': '#f5efeb',
                    },
                }
            }
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        body {
            background-color: #f5efeb;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    <header class="bg-navytube text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-4">
            <div class="text-xl font-bold text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-skyblue" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Rumah Sakit
            </div>
            <nav>
                <span class="mr-4 text-skyblue">Halo,
                    <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong>
                    (<?php echo $current_role; ?>)</span>
                <a href="<?php echo $base_url; ?>auth/logout.php"
                    class="bg-teal hover:bg-white hover:text-navytube text-white px-3 py-2 rounded-md font-medium transition duration-300 ml-4">Logout</a>
            </nav>
        </div>
    </header>