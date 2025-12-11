<?php
require_once 'includes/check_patient.php';
// Include shared header matching the "src" design
include '../includes/header.php';

// Get patient info
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM patients WHERE id_user = $user_id";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);
$id_patient = $patient['id_patient'];

// Get appointments
$query_appointments = "
    SELECT a.*, d.name as doctor_name, d.specialization 
    FROM appointments a
    JOIN doctors d ON a.id_doctor = d.id_doctor
    WHERE a.id_patient = $id_patient
    ORDER BY a.date DESC, a.time DESC
";
$appointments = mysqli_query($conn, $query_appointments);


// Get stats count
$query_count = "SELECT COUNT(*) as total FROM appointments WHERE id_patient = $id_patient";
$count_result = mysqli_fetch_assoc(mysqli_query($conn, $query_count));
$total_appts = $count_result['total'];

// Stats
$query_next = "SELECT * FROM appointments WHERE id_patient = $id_patient AND status = 'Scheduled' AND date >= CURDATE() ORDER BY date ASC LIMIT 1";
$next_appointment = mysqli_fetch_assoc(mysqli_query($conn, $query_next));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien - RS Sistem</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navytube: '#2F4156',
                        teal: '#008080',
                        skyblue: '#87CEEB',
                        beige: '#F5F5DC',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-beige min-h-screen flex flex-col">
    <div class="flex-grow">
        <main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 animate-fade-in mb-10">
            <h1 class="text-4xl font-extrabold text-navytube">Dashboard Pasien</h1>
            <p class="text-xl text-teal mt-2">Selamat Datang, <strong
                    class="text-navytube"><?php echo htmlspecialchars($patient['name']); ?></strong></p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Next Appointment Card -->
                <div
                    class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-teal transition duration-500 hover:scale-[1.02]">
                    <h2 class="text-2xl font-semibold text-navytube mb-4">Jadwal Berikutnya</h2>
                    <?php if ($next_appointment): ?>
                        <div class="bg-skyblue/20 p-4 rounded-lg border border-skyblue">
                            <p class="text-lg font-bold text-navytube">
                                <?php echo date('d F Y', strtotime($next_appointment['date'])); ?>
                            </p>
                            <p class="text-3xl font-extrabold text-teal my-2">
                                <?php echo date('H:i', strtotime($next_appointment['time'])); ?>
                            </p>
                            <p class="text-sm text-gray-600">No. Antrian: <span
                                    class="font-bold text-lg"><?php echo $next_appointment['queue_number']; ?></span></p>
                        </div>
                    <?php else: ?>
                        <p class="text-teal italic">Tidak ada jadwal kunjungan mendatang.</p>
                    <?php endif; ?>
                    <div class="mt-6">
                        <a href="book_appointment.php"
                            class="block w-full bg-navytube text-white text-center py-3 rounded-lg font-semibold hover:bg-teal transition duration-300">
                            + Buat Janji Temu Baru
                        </a>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="bg-skyblue p-6 rounded-xl shadow-xl transition duration-500 hover:scale-[1.02] relative">
                    <h2 class="text-2xl font-semibold text-navytube mb-4 border-b pb-2 border-teal">Info Pasien</h2>
                    <div class="absolute top-6 right-6">
                        <a href="profile.php" class="text-teal hover:text-navytube text-sm font-bold">✎ Edit</a>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex justify-between">
                            <span class="text-navytube font-medium">No. RM</span>
                            <span class="font-bold text-navytube"><?php echo $patient['med_record_no']; ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-navytube font-medium">Email</span>
                            <span class="text-teal truncate ml-2"><?php echo $_SESSION['email']; ?></span>
                        </li>
                        <li class="flex justify-between">
                            <span class="text-navytube font-medium">Telepon</span>
                            <span class="text-teal"><?php echo $patient['phone']; ?></span>
                        </li>
                        <li class="block">
                            <span class="text-navytube font-medium block">Alamat</span>
                            <span class="text-teal text-sm"><?php echo $patient['address']; ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Stats -->
                <div class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-teal">
                    <h3 class="text-xl font-semibold text-navytube">Total Kunjungan</h3>
                    <p class="text-5xl text-teal mt-2 font-bold"><?php echo $total_appts; ?></p>
                    <p class="text-sm text-gray-500 mt-4">Rekap seluruh riwayat janji temu Anda.</p>
                </div>
            </div>

            <!-- History Table -->
            <div class="mt-10 bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="bg-navytube p-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        Riwayat Kunjungan
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-skyblue text-navytube uppercase text-sm font-semibold">
                            <tr>
                                <th class="p-4 border-b border-teal">Tanggal</th>
                                <th class="p-4 border-b border-teal">Waktu</th>
                                <th class="p-4 border-b border-teal">Dokter</th>
                                <th class="p-4 border-b border-teal">Poli</th>
                                <th class="p-4 border-b border-teal">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (mysqli_num_rows($appointments) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($appointments)): ?>
                                    <tr
                                        class="hover:bg-beige transition duration-200 border-b border-gray-100 last:border-none">
                                        <td class="p-4 font-medium"><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                                        <td class="p-4"><?php echo date('H:i', strtotime($row['time'])); ?></td>
                                        <td class="p-4 font-semibold text-navytube">
                                            <?php echo htmlspecialchars($row['doctor_name']); ?>
                                        </td>
                                        <td class="p-4 text-teal"><?php echo htmlspecialchars($row['specialization']); ?></td>
                                        <td class="p-4">
                                            <?php
                                            $statusClass = match ($row['status']) {
                                                'Scheduled' => 'bg-blue-100 text-blue-800',
                                                'In Progress' => 'bg-yellow-100 text-yellow-800',
                                                'Done' => 'bg-green-100 text-green-800',
                                                'Canceled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>">
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500 italic">Belum ada riwayat
                                        kunjungan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
<?php
include '../includes/footer.php';
close_db($conn);
?>