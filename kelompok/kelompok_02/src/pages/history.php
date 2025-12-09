<?php
require_once(dirname(__DIR__) . '/config/db.php');
require_once(dirname(__DIR__) . '/includes/header.php');

// ambil ID pasien
$id_patient = $_GET['id'] ?? null;

if (!$id_patient) {
    die("<div class='text-center text-red-500 font-semibold mt-10'>❌ ID pasien tidak ditemukan.</div>");
}

// ambil data pasien
$patient = $conn->query("SELECT * FROM patients WHERE id_patient = $id_patient")->fetch_assoc();

// ambil riwayat medis pasien
$query = "
SELECT m.id_record, a.date, a.time, d.name AS doctor_name, m.diagnosis, m.treatment, m.notes, m.created_at
FROM medical_records m
JOIN appointments a ON m.id_appointment = a.id_appointment
JOIN doctors d ON a.id_doctor = d.id_doctor
WHERE a.id_patient = $id_patient
ORDER BY m.created_at DESC
";
$result = $conn->query($query);
?>

<main class="max-w-6xl mx-auto mt-10 px-4 animate-fade-in">
  <!-- Judul Halaman -->
  <h2 class="text-4xl font-extrabold text-navytube mb-6 text-center">🩺 Riwayat Medis Pasien</h2>

  <!-- Kartu Biodata Pasien -->
  <div class="bg-skyblue shadow-lg rounded-2xl p-6 mb-8 border-l-4 border-teal">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h3 class="text-2xl font-bold text-navytube"><?php echo htmlspecialchars($patient['name']); ?></h3>
        <p class="text-gray-700">Nomor Rekam Medis: <strong class="text-teal"><?php echo htmlspecialchars($patient['med_record_no']); ?></strong></p>
        <p class="text-gray-700">Tanggal Lahir: <strong><?php echo htmlspecialchars($patient['birth_date'] ?? '—'); ?></strong></p>
      </div>
      <a href="../patient/patient.php" class="btn btn-secondary">← Kembali ke Daftar Pasien</a>
    </div>
  </div>

  <!-- Tabel Riwayat -->
  <div class="overflow-x-auto bg-white shadow-xl rounded-2xl border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-navytube text-white">
        <tr>
          <th class="py-3 px-4 text-left">Tanggal</th>
          <th class="py-3 px-4 text-left">Dokter</th>
          <th class="py-3 px-4 text-left">Diagnosis</th>
          <th class="py-3 px-4 text-left">Tindakan</th>
          <th class="py-3 px-4 text-left">Catatan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php $i = 0; while ($row = $result->fetch_assoc()): $i++; ?>
            <tr class="<?php echo $i % 2 == 0 ? 'bg-gray-50' : 'bg-white'; ?> hover:bg-skyblue/30 transition duration-300">
              <td class="py-3 px-4 font-medium text-gray-800"><?php echo htmlspecialchars($row['date'] . ' ' . $row['time']); ?></td>
              <td class="py-3 px-4 text-teal font-semibold"><?php echo htmlspecialchars($row['doctor_name']); ?></td>
              <td class="py-3 px-4"><?php echo htmlspecialchars($row['diagnosis']); ?></td>
              <td class="py-3 px-4"><?php echo htmlspecialchars($row['treatment'] ?? '—'); ?></td>
              <td class="py-3 px-4"><?php echo htmlspecialchars($row['notes'] ?? '—'); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="py-10 text-center text-gray-500">
              <div class="flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium">Belum ada riwayat medis untuk pasien ini.</p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once(dirname(__DIR__) . '/includes/footer.php'); ?>
