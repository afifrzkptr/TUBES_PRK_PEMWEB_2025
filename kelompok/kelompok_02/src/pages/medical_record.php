<?php
require_once(dirname(__DIR__) . '/config/db.php');
require_once(dirname(__DIR__) . '/includes/header.php');

// Ambil daftar janji temu yang statusnya "Done" dan belum ada rekam medis
$query = "
SELECT a.id_appointment, p.name AS patient_name, d.name AS doctor_name, a.date, a.time
FROM appointments a
JOIN patients p ON a.id_patient = p.id_patient
JOIN doctors d ON a.id_doctor = d.id_doctor
LEFT JOIN medical_records m ON a.id_appointment = m.id_appointment
WHERE a.status = 'Done' AND m.id_record IS NULL
ORDER BY a.date DESC
";

$result = $conn->query($query);

// Input data rekam medis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_appointment = $_POST['id_appointment'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("
        INSERT INTO medical_records (id_appointment, diagnosis, treatment, notes)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $id_appointment, $diagnosis, $treatment, $notes);

    if ($stmt->execute()) {
        echo "
        <div class='max-w-2xl mx-auto mt-6 bg-green-100 text-green-800 border border-green-400 p-4 rounded-lg text-center font-semibold'>
          ✅ Rekam medis berhasil disimpan!
        </div>";
    } else {
        echo "
        <div class='max-w-2xl mx-auto mt-6 bg-red-100 text-red-800 border border-red-400 p-4 rounded-lg text-center font-semibold'>
          ❌ Gagal menyimpan data: " . htmlspecialchars($conn->error) . "
        </div>";
    }
}
?>

<main class="max-w-4xl mx-auto mt-16 px-4 animate-fade-in">

  <div class="rounded-3xl shadow-2xl border border-gray-200 bg-white overflow-hidden relative">

    <!-- Elegant Glow Border -->
    <div class="absolute inset-0 rounded-3xl ring-1 ring-gray-300/40 pointer-events-none"></div>

    <!-- Header -->
    <div class="bg-gradient-to-r from-[#1f3353] via-[#29446a] to-[#355985] text-white px-10 py-9 flex items-center gap-4 shadow-lg">
      <div class="text-4xl">🩺</div>
      <div>
        <h2 class="text-3xl font-bold tracking-wide">Input Rekam Medis</h2>
        <p class="text-gray-200 text-sm tracking-wide">
          Rekam hasil pemeriksaan berdasarkan janji temu yang telah selesai.
        </p>
      </div>
    </div>

    <!-- Form Container -->
    <div class="p-12 space-y-10 bg-gradient-to-b from-white to-[#f9fafb]">

      <form method="POST" class="space-y-10">

        <!-- PILIH JANJI TEMU -->
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">Pilih Janji Temu</label>
          <select name="id_appointment"
                  class="input-field w-full bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500"
                  required>
            <option value="">-- Pilih Janji Temu Selesai --</option>

            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['id_appointment']; ?>">
                  <?= htmlspecialchars($row['patient_name']); ?> • 
                  <?= htmlspecialchars($row['doctor_name']); ?> 
                  (<?= $row['date']; ?> <?= $row['time']; ?>)
                </option>
              <?php endwhile; ?>
            <?php else: ?>
              <option disabled>Tidak ada janji temu selesai</option>
            <?php endif; ?>
          </select>
        </div>

        <hr class="border-gray-300/60">

        <!-- DIAGNOSIS -->
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">Diagnosis</label>
          <textarea name="diagnosis"
                    class="input-field w-full h-28 bg-gray-50 border border-gray-300 rounded-xl 
                           focus:ring-2 focus:ring-teal-500"
                    placeholder="Tuliskan diagnosis utama pasien..."
                    required></textarea>
        </div>

        <!-- TINDAKAN -->
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">Tindakan / Obat</label>
          <textarea name="treatment"
                    class="input-field w-full h-28 bg-gray-50 border border-gray-300 rounded-xl 
                           focus:ring-2 focus:ring-teal-500"
                    placeholder="Tindakan medis, resep obat, atau instruksi lainnya..."></textarea>
        </div>

        <!-- CATATAN TAMBAHAN -->
        <div class="space-y-2">
          <label class="block text-sm font-semibold text-gray-700">Catatan Tambahan</label>
          <textarea name="notes"
                    class="input-field w-full h-24 bg-gray-50 border border-gray-300 rounded-xl 
                           focus:ring-2 focus:ring-teal-500"
                    placeholder="Catatan tambahan (opsional)..."></textarea>
        </div>

        <!-- BUTTON -->
        <button type="submit"
                class="w-full py-4 rounded-xl bg-gradient-to-r from-[#23395d] to-[#1f2f4b] 
                       text-white font-semibold text-lg shadow-lg 
                       hover:brightness-110 active:scale-[0.98] transition-all duration-200">
          Simpan Rekam Medis
        </button>

      </form>

    </div>
  </div>

</main>

<?php require_once(dirname(__DIR__) . '/includes/footer.php'); ?>
