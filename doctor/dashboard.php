<?php
require_once 'includes/check_doctor.php';
include '../includes/header.php';

// Get doctor info
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM doctors WHERE id_user = $user_id";
$result = mysqli_query($conn, $query);
$doctor = mysqli_fetch_assoc($result);
$id_doctor = $doctor['id_doctor'];

// Filter Date
$filter_date = $_GET['date'] ?? date('Y-m-d');

// Get appointments for this doctor
$query_appointments = "
    SELECT a.*, p.name as patient_name, p.med_record_no
    FROM appointments a
    JOIN patients p ON a.id_patient = p.id_patient
    WHERE a.id_doctor = $id_doctor AND a.date = '$filter_date'
    ORDER BY a.time ASC
";
$appointments = mysqli_query($conn, $query_appointments);

// Handle Status Update
if (isset($_POST['update_status'])) {
    $id_appt = $_POST['id_appointment'];
    $new_status = $_POST['status'];
    $conn->query("UPDATE appointments SET status = '$new_status' WHERE id_appointment = $id_appt");
    header("Location: dashboard.php?date=$filter_date");
    exit();
}
?>
<div class="flex-grow">
    <main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 animate-fade-in mb-10">
        <h1 class="text-4xl font-extrabold text-navytube">Dashboard Dokter</h1>
        <div class="flex items-center gap-2 mt-2">
            <p class="text-xl text-teal">Selamat Datang, <strong
                    class="text-navytube"><?php echo htmlspecialchars($doctor['name']); ?></strong>
                (<?php echo $doctor['specialization']; ?>)</p>
            <a href="profile.php"
                class="text-sm bg-skyblue text-navytube px-3 py-1 rounded-full font-bold hover:bg-teal hover:text-white transition">✎
                Edit Profil</a>
        </div>

        <div class="mt-8 bg-white p-6 rounded-xl shadow-xl border-t-4 border-teal">
            <div class="flex flex-wrap justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-navytube">Jadwal Praktek Hari Ini</h2>
                <div class="flex gap-4">
                    <a href="view_history.php"
                        class="bg-navytube text-white px-4 py-2 rounded-lg font-medium hover:bg-teal transition shadow-md">📂
                        Cari Riwayat Pasien</a>
                    <form class="flex items-center gap-2">
                        <label class="font-medium text-navytube">Tanggal:</label>
                        <input type="date" name="date" value="<?php echo $filter_date; ?>" onchange="this.form.submit()"
                            class="p-2 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </form>
                </div>
            </div>

            <?php if (mysqli_num_rows($appointments) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-skyblue text-navytube uppercase text-sm font-semibold">
                            <tr>
                                <th class="p-4 border-b border-teal">Waktu</th>
                                <th class="p-4 border-b border-teal">Antrian</th>
                                <th class="p-4 border-b border-teal">Pasien</th>
                                <th class="p-4 border-b border-teal">Status</th>
                                <th class="p-4 border-b border-teal">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php while ($row = mysqli_fetch_assoc($appointments)): ?>
                                <tr class="hover:bg-beige transition duration-200 border-b border-gray-100">
                                    <td class="p-4 font-bold text-teal"><?php echo date('H:i', strtotime($row['time'])); ?></td>
                                    <td class="p-4"><span
                                            class="bg-navytube text-white cursor-default rounded-full h-8 w-8 flex items-center justify-center font-bold text-sm"><?php echo $row['queue_number']; ?></span>
                                    </td>
                                    <td class="p-4">
                                        <div class="font-semibold text-navytube">
                                            <?php echo htmlspecialchars($row['patient_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500"><?php echo $row['med_record_no']; ?></div>
                                    </td>
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
                                    <td class="p-4">
                                        <?php if ($row['status'] == 'Scheduled'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="id_appointment"
                                                    value="<?php echo $row['id_appointment']; ?>">
                                                <input type="hidden" name="status" value="In Progress">
                                                <button type="submit" name="update_status"
                                                    class="bg-teal hover:bg-navytube text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300">
                                                    ▶ Panggil
                                                </button>
                                            </form>
                                        <?php elseif ($row['status'] == 'In Progress'): ?>
                                            <a href="input_record.php?id=<?php echo $row['id_appointment']; ?>"
                                                class="bg-navytube hover:bg-teal text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-300 inline-flex items-center gap-1">
                                                📝 Rekam Medis
                                            </a>
                                        <?php else: ?>
                                            <span class="text-green-600 font-bold flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                Selesai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-skyblue mx-auto mb-4" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xl text-teal font-medium">Tidak ada jadwal praktek pada tanggal ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php
include '../includes/footer.php';
close_db($conn);
?>