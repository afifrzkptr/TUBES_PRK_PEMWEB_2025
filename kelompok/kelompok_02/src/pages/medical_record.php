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

<main class="max-w-4xl mx-auto mt-10 px-4 animate-fade-in">
  <div class="bg-white shadow-xl rounded-2xl p-8 border-t-4 border-teal">
    <h2 class="text-3xl font-bold text-navytube mb-6 text-center">🩺 Input Rekam Medis Pasien</h2>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block font-semibold text-navytube mb-2">Pilih Janji Temu</label>
        <select name="id_appointment" class="input-field w-full" required>
          <option value="">-- Pilih Janji Temu Selesai --</option>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <option value="<?php echo $row['id_appointment']; ?>">
                <?php echo htmlspecialchars($row['patient_name'] . " - " . $row['doctor_name'] . " (" . $row['date'] . " " . $row['time'] . ")"); ?>
              </option>
            <?php endwhile; ?>
          <?php else: ?>
            <option disabled>Tidak ada janji temu yang selesai</option>
          <?php endif; ?>
        </select>
      </div>

      <div>
        <label class="block font-semibold text-navytube mb-2">Diagnosis</label>
        <textarea name="diagnosis" placeholder="Masukkan diagnosis pasien..." class="input-field w-full" required></textarea>
      </div>

      <div>
        <label class="block font-semibold text-navytube mb-2">Tindakan / Obat</label>
        <textarea name="treatment" placeholder="Masukkan tindakan atau resep obat..." class="input-field w-full"></textarea>
      </div>

      <div>
        <label class="block font-semibold text-navytube mb-2">Catatan Tambahan</label>
        <textarea name="notes" placeholder="Catatan tambahan (opsional)..." class="input-field w-full"></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-full py-3 text-lg font-semibold">
        💾 Simpan Rekam Medis
      </button>
    </form>
  </div>
</main>

<?php require_once(dirname(__DIR__) . '/includes/footer.php'); ?>
