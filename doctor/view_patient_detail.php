<?php
require_once 'includes/check_doctor.php';
include '../includes/header.php';

$id_patient = $_GET['id'] ?? 0;

// Get Patient Info
$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patients WHERE id_patient = $id_patient"));

if (!$patient) {
    echo "<div class='p-10 text-center font-bold text-red-600'>Pasien tidak ditemukan.</div>";
    include '../includes/footer.php';
    exit();
}

// Get Medical Records
$query_records = "
    SELECT m.*, a.date, a.time, d.name as doctor_name, d.specialization
    FROM medical_records m
    JOIN appointments a ON m.id_appointment = a.id_appointment
    JOIN doctors d ON a.id_doctor = d.id_doctor
    WHERE a.id_patient = $id_patient
    ORDER BY a.date DESC
";
$records = mysqli_query($conn, $query_records);
?>

<div class="flex-grow">
    <main class="max-w-5xl mx-auto mt-10 px-4 animate-fade-in mb-20">
        <a href="view_history.php" class="text-teal hover:text-navytube font-medium mb-4 inline-block">← Kembali ke
            Daftar Pasien</a>

        <div class="bg-white shadow-xl rounded-2xl p-8 border-t-4 border-teal mb-8">
            <h1 class="text-3xl font-extrabold text-navytube mb-2"><?php echo htmlspecialchars($patient['name']); ?>
            </h1>
            <p class="text-gray-600">No. RM: <strong class="text-teal"><?php echo $patient['med_record_no']; ?></strong>
            </p>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4">
                <div>
                    <span class="text-xs font-bold text-gray-500 uppercase">Telepon</span>
                    <p class="text-navytube"><?php echo $patient['phone']; ?></p>
                </div>
                <div class="md:col-span-2">
                    <span class="text-xs font-bold text-gray-500 uppercase">Alamat</span>
                    <p class="text-navytube"><?php echo $patient['address']; ?></p>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-bold text-navytube mb-6">Riwayat Pemeriksaan</h2>

        <div class="space-y-6">
            <?php if (mysqli_num_rows($records) > 0): ?>
                <?php while ($rec = mysqli_fetch_assoc($records)): ?>
                    <div
                        class="bg-white rounded-xl shadow-md border-l-4 border-skyblue overflow-hidden hover:shadow-lg transition">
                        <div class="bg-beige px-6 py-4 flex justify-between items-center border-b border-gray-100">
                            <div>
                                <p class="font-bold text-navytube text-lg"><?php echo date('d F Y', strtotime($rec['date'])); ?>
                                </p>
                                <p class="text-sm text-gray-500"><?php echo date('H:i', strtotime($rec['time'])); ?> WIB</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-teal"><?php echo $rec['doctor_name']; ?></p>
                                <p class="text-xs text-gray-500"><?php echo $rec['specialization']; ?></p>
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-bold text-navytube mb-2">Diagnosis</h3>
                                <p class="text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <?php echo nl2br(htmlspecialchars($rec['diagnosis'])); ?></p>
                            </div>
                            <div>
                                <h3 class="font-bold text-navytube mb-2">Tindakan / Obat</h3>
                                <p class="text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <?php echo nl2br(htmlspecialchars($rec['treatment'])); ?></p>
                            </div>
                            <?php if ($rec['notes']): ?>
                                <div class="md:col-span-2">
                                    <h3 class="font-bold text-navytube mb-2">Catatan Dokter</h3>
                                    <p class="text-gray-600 italic"><?php echo nl2br(htmlspecialchars($rec['notes'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="bg-white p-10 rounded-xl text-center shadow-lg">
                    <p class="text-gray-500 italic">Belum ada rekam medis untuk pasien ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>