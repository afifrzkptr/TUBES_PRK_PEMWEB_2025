<?php
include(__DIR__ . '/../config/db.php'); 
include(__DIR__ . '/../includes/header.php'); 


$current_user_role = 'Admin'; // Ganti role di sini: 'Admin', 'Dokter', atau 'Pasien'
$user_name = 'Afif'; 

// ------------------------------------------------------------------
// KONTEN UTAMA DASHBOARD
// ------------------------------------------------------------------

?>
<main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 animate-fade-in">
    <h1 class="text-4xl font-extrabold text-navytube">Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h1>
    <p class="text-xl text-teal mt-2">Anda masuk sebagai pengguna dengan peran: <strong class="text-navytube font-bold"><?php echo htmlspecialchars($current_user_role); ?></strong></p>
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <?php if ($current_user_role == 'Admin'): ?>
            <div class="bg-skyblue p-6 rounded-xl shadow-xl transition duration-500 ease-in-out transform hover:scale-[1.02]">
                <h2 class="text-2xl font-semibold text-navytube mb-4 border-b pb-2 border-teal">Dashboard Administrator</h2>
                <p class="text-navytube mb-4 font-medium">Sebagai Admin, Anda bertanggung jawab mengelola seluruh data sistem:</p>
                <ul class="list-disc list-inside space-y-2 text-teal">
                    <li><span class="text-navytube font-medium">Kelola Akun Pengguna</span> (Tugas Akeyla)</li>
                    <li><span class="text-navytube font-medium">Kelola Data Dokter</span> (Tugas Nabila Putri)</li>
                    <li><span class="text-navytube font-medium">Kelola Data Pasien</span> (Tugas Nabila Putri)</li>
                </ul>
            </div>
        <?php elseif ($current_user_role == 'Dokter'): ?>
            <div class="bg-skyblue p-6 rounded-xl shadow-xl transition duration-500 ease-in-out transform hover:scale-[1.02]">
                <h2 class="text-2xl font-semibold text-navytube mb-4 border-b pb-2 border-teal">Dashboard Dokter</h2>
                <p class="text-navytube mb-4 font-medium">Kelola jadwal janji temu dan riwayat medis pasien:</p>
                <ul class="list-disc list-inside space-y-2 text-teal">
                    <li><span class="text-navytube font-medium">Lihat Jadwal Janji Temu Hari Ini</span> (Tugas Dara)</li>
                    <li><span class="text-navytube font-medium">Input Rekam Medis Pasien</span> (Tugas Nabila Putri)</li>
                    <li><span class="text-navytube font-medium">Proteksi Halaman Sesi</span> (Tugas Afif)</li>
                </ul>
            </div>
        <?php elseif ($current_user_role == 'Pasien'): ?>
            <div class="bg-skyblue p-6 rounded-xl shadow-xl transition duration-500 ease-in-out transform hover:scale-[1.02]">
                <h2 class="text-2xl font-semibold text-navytube mb-4 border-b pb-2 border-teal">Dashboard Pasien</h2>
                <p class="text-navytube mb-4 font-medium">Lihat status janji temu dan informasi layanan:</p>
                <ul class="list-disc list-inside space-y-2 text-teal">
                    <li><span class="text-navytube font-medium">Pesan Janji Temu Baru</span> (Tugas Dara)</li>
                    <li><span class="text-navytube font-medium">Lihat Riwayat Janji Temu</span> (Tugas Dara)</li>
                    <li><span class="text-navytube font-medium">Login & Registrasi Akun</span> (Tugas Akeyla)</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-teal transition duration-300">
             <h3 class="text-xl font-semibold text-navytube">Total Janji Temu Hari Ini</h3>
             <p class="text-5xl text-teal mt-2 font-bold">0</p>
             <p class="text-sm text-gray-500 mt-4">Statistik ini akan di-query langsung dari database.</p>
        </div>

    </div>
</main>
<?php

include(__DIR__ . '/../includes/footer.php');
?>