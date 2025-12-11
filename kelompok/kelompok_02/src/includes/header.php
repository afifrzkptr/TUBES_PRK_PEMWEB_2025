<?php
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        body {
            background-color: #f5efeb; 
        }
    </style>
</head>
<body class="min-h-screen"> 
    
    <header class="bg-navytube text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-4">
            <div class="text-xl font-bold text-white">RS DB</div>
            <nav>
                <a href="dashboard.php" class="text-skyblue hover:text-white px-3 py-2 rounded-md font-medium transition duration-300">Dashboard</a>
                <a href="logout.php" class="bg-teal hover:bg-white hover:text-navytube text-white px-3 py-2 rounded-md font-medium transition duration-300 ml-4">Logout</a>
            </nav>
        </div>
    </header>