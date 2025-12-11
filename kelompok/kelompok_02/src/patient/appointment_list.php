<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';

$conn = connect_db();
$patient_id = 5; // ambil dari session login nanti
$success = $_GET['success'] ?? null;

$stmt = $conn->prepare("
    SELECT a.*, d.name AS doctor_name, d.specialization
    FROM appointments a
    JOIN doctors d ON a.id_doctor = d.id_doctor
    WHERE a.id_patient = ?
    ORDER BY a.date DESC, a.time DESC
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="max-w-5xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-navytube mb-6">Daftar Janji Temu Saya</h1>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center">
            ✅ Janji temu berhasil dibuat!
        </div>
    <?php endif; ?>

    <table class="w-full border-collapse shadow-lg bg-white rounded-lg overflow-hidden">
        <thead class="bg-teal text-white">
            <tr>
                <th class="p-3 text-left">Dokter</th>
                <th class="p-3 text-left">Spesialisasi</th>
                <th class="p-3 text-left">Tanggal</th>
                <th class="p-3 text-left">Waktu</th>
                <th class="p-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-skyblue/20">
                        <td class="p-3"><?= $row['doctor_name'] ?></td>
                        <td class="p-3"><?= $row['specialization'] ?></td>
                        <td class="p-3"><?= $row['date'] ?></td>
                        <td class="p-3"><?= $row['time'] ?></td>
                        <td class="p-3"><?= $row['status'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="p-3 text-center">Belum ada janji temu.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>