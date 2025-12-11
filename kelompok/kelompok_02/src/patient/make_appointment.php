<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';

$conn = connect_db();
$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
$patient_id = 5;
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<main class="max-w-2xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-navytube mb-6">Buat Janji Temu</h1>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center">
            ✅ Janji temu berhasil dibuat!
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-center">
            ❌ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="make_appointment_action.php" method="POST" class="bg-white p-6 rounded-xl shadow-lg space-y-4">
        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">

        <div>
            <label class="block mb-1 font-medium text-navytube">Pilih Dokter</label>
            <select name="doctor_id" class="w-full border rounded px-3 py-2" required>
                <option value="">-- Pilih Dokter --</option>
                <?php while($doc = $result->fetch_assoc()): ?>
                    <option value="<?= $doc['id_doctor'] ?>"><?= $doc['name'] ?> (<?= $doc['specialization'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium text-navytube">Tanggal</label>
            <input type="date" name="date" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block mb-1 font-medium text-navytube">Waktu</label>
            <input type="time" name="time" class="w-full border rounded px-3 py-2" required>
        </div>

        <button type="submit" class="bg-navytube text-white px-4 py-2 rounded-md hover:bg-teal transition">
            Buat Janji Temu
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="appointment_list.php" class="text-teal hover:underline">Lihat Daftar Janji Temu Saya</a>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>