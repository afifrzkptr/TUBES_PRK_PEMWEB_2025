<?php
require_once(dirname(__DIR__) . '/config/db.php');
require_once(dirname(__DIR__) . '/includes/header.php');

// ambil ID pasien
$id_patient = $_GET['id'] ?? null;

if (!$id_patient) {
    echo "
    <main class='max-w-4xl mx-auto mt-20 text-center'>
      <div class='text-red-600 text-2xl font-semibold mb-4'>❌ ID pasien tidak ditemukan.</div>
      <a href='../patient/patient.php' class='text-navytube underline text-lg'>Kembali ke daftar pasien</a>
    </main>";
    require_once(dirname(__DIR__) . '/includes/footer.php');
    exit;
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


<main class="max-w-6xl mx-auto mt-12 px-4 animate-fade-in">

  <!-- Header -->
  <div class="text-center mb-10">
    <h2 class="text-4xl font-extrabold text-navytube">Riwayat Medis Pasien</h2>
  </div>

  <!-- Biodata Pasien -->
  <div class="bg-white border border-gray-200 shadow-lg rounded-2xl p-6 mb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
      
      <div class="space-y-1">
        <h3 class="text-2xl font-bold text-navytube">
          <?php echo htmlspecialchars($patient['name']); ?>
        </h3>

        <p class="text-gray-700">
          Nomor Rekam Medis: 
          <span class="font-semibold text-teal-700">
            <?php echo htmlspecialchars($patient['med_record_no']); ?>
          </span>
        </p>

        <p class="text-gray-700">
          Tanggal Lahir: 
          <strong><?php echo htmlspecialchars($patient['birth_date'] ?? '—'); ?></strong>
        </p>
      </div>

      <a href="../patient/patient.php" 
         class="px-4 py-2 bg-gray-100 border rounded-md hover:bg-gray-200 transition text-gray-700 font-semibold mt-4 md:mt-0">
        ← Kembali
      </a>
    </div>
  </div>

  <!-- Tabel Riwayat -->
  <div class="overflow-hidden bg-white shadow-xl rounded-2xl border border-gray-200">
    <table class="min-w-full">
      <thead class="bg-navytube text-white">
        <tr>
          <th class="py-3 px-5 text-left">Tanggal</th>
          <th class="py-3 px-5 text-left">Dokter</th>
          <th class="py-3 px-5 text-left">Diagnosis</th>
          <th class="py-3 px-5 text-left">Tindakan</th>
          <th class="py-3 px-5 text-left">Catatan</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">
        <?php if ($result && $result->num_rows > 0): ?>

          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-skyblue/30 transition">
              <td class="py-3 px-5 font-medium text-gray-800">
                <?php echo htmlspecialchars($row['date'] . ' • ' . $row['time']); ?>
              </td>
              <td class="py-3 px-5 text-teal font-semibold">
                <?php echo htmlspecialchars($row['doctor_name']); ?>
              </td>
              <td class="py-3 px-5"><?php echo htmlspecialchars($row['diagnosis']); ?></td>
              <td class="py-3 px-5"><?php echo htmlspecialchars($row['treatment'] ?? '—'); ?></td>
              <td class="py-3 px-5"><?php echo htmlspecialchars($row['notes'] ?? '—'); ?></td>
            </tr>
          <?php endwhile; ?>

        <?php else: ?>
          <tr>
            <td colspan="5" class="py-12 text-center text-gray-500">
              <div class="flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-lg font-medium">Belum ada riwayat medis untuk pasien ini.</p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>


<?php require_once(dirname(__DIR__) . '/includes/footer.php'); ?>
