<?php
require_once 'includes/check_doctor.php';
include '../includes/header.php';

$search = $_GET['search'] ?? '';

// Search Query
$query = "
    SELECT p.name, p.med_record_no, p.id_patient, COUNT(m.id_record) as total_records, MAX(a.date) as last_visit
    FROM patients p
    LEFT JOIN appointments a ON p.id_patient = a.id_patient
    LEFT JOIN medical_records m ON a.id_appointment = m.id_appointment
    WHERE p.name LIKE '%$search%' OR p.med_record_no LIKE '%$search%'
    GROUP BY p.id_patient
    ORDER BY last_visit DESC
";
$patients = mysqli_query($conn, $query);
?>

<div class="flex-grow">
    <main class="max-w-7xl mx-auto mt-10 px-4 animate-fade-in mb-20">
        <h1 class="text-3xl font-extrabold text-navytube mb-2">📂 Riwayat Medis Pasien</h1>
        <p class="text-teal mb-8">Cari dan lihat rekam medis pasien.</p>

        <!-- Search Bar -->
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-teal mb-8">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Cari Nama atau No. RM..."
                    class="flex-grow p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                <button type="submit"
                    class="bg-navytube text-white px-8 rounded-lg font-bold hover:bg-teal transition">Cari</button>
            </form>
        </div>

        <!-- Patients List -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-skyblue text-navytube uppercase text-sm font-semibold">
                        <tr>
                            <th class="p-4 border-b border-teal">No. RM</th>
                            <th class="p-4 border-b border-teal">Nama Pasien</th>
                            <th class="p-4 border-b border-teal">Total Kunjungan</th>
                            <th class="p-4 border-b border-teal">Kunjungan Terakhir</th>
                            <th class="p-4 border-b border-teal text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if (mysqli_num_rows($patients) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($patients)): ?>
                                <tr class="hover:bg-beige transition border-b border-gray-100">
                                    <td class="p-4 font-bold text-teal"><?php echo $row['med_record_no']; ?></td>
                                    <td class="p-4 font-semibold text-navytube"><?php echo htmlspecialchars($row['name']); ?>
                                    </td>
                                    <td class="p-4"><?php echo $row['total_records']; ?> Rekam Medis</td>
                                    <td class="p-4">
                                        <?php echo $row['last_visit'] ? date('d F Y', strtotime($row['last_visit'])) : '-'; ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="view_patient_detail.php?id=<?php echo $row['id_patient']; ?>"
                                            class="inline-block bg-teal text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-navytube transition">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500 italic">Pasien tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <a href="dashboard.php" class="text-navytube font-bold hover:underline">← Kembali ke Dashboard</a>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>