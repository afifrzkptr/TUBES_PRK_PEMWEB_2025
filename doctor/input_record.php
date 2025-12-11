<?php
require_once 'includes/check_doctor.php';

$id_appointment = $_GET['id'] ?? 0;
$success = '';

// Get Appointment Detail
$query = "
    SELECT a.*, p.name as patient_name, p.med_record_no, d.name as doctor_name
    FROM appointments a
    JOIN patients p ON a.id_patient = p.id_patient
    JOIN doctors d ON a.id_doctor = d.id_doctor
    WHERE a.id_appointment = $id_appointment
";
$appt = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$appt) {
    die("Appointment tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $notes = $_POST['notes'];

    // Insert Medical Record
    $stmt = $conn->prepare("INSERT INTO medical_records (id_appointment, diagnosis, treatment, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_appointment, $diagnosis, $treatment, $notes);

    if ($stmt->execute()) {
        // Update appointment status to DONE
        $conn->query("UPDATE appointments SET status = 'Done' WHERE id_appointment = $id_appointment");
        echo "
        <script>
            alert('Rekam Medis Berhasil Disimpan');
            window.location.href = 'dashboard.php';
        </script>";
        exit();
    } else {
        $error = "Gagal menyimpan: " . $conn->error;
    }
}

include '../includes/header.php';
?>
<main class="max-w-4xl mx-auto mt-10 px-4 animate-fade-in mb-20">
    <div class="bg-white shadow-xl rounded-2xl p-8 border-t-4 border-teal relative">
        <a href="dashboard.php" class="absolute top-8 right-8 text-teal hover:text-navytube font-medium">✕ Batal</a>

        <h2 class="text-3xl font-bold text-navytube mb-2">🩺 Input Rekam Medis</h2>
        <p class="text-teal mb-6">Silakan isi data hasil pemeriksaan untuk pasien ini.</p>

        <!-- Patient Card -->
        <div class="bg-skyblue/20 p-6 rounded-xl border border-skyblue mb-8 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide font-bold">Pasien</p>
                <p class="text-2xl font-bold text-navytube"><?php echo htmlspecialchars($appt['patient_name']); ?></p>
                <p class="text-teal font-medium">No. RM: <?php echo $appt['med_record_no']; ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 uppercase tracking-wide font-bold">Waktu Konsultasi</p>
                <p class="text-xl font-bold text-navytube"><?php echo date('d F Y', strtotime($appt['date'])); ?></p>
                <p class="text-teal font-medium"><?php echo date('H:i', strtotime($appt['time'])); ?></p>
            </div>
        </div>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block font-semibold text-navytube mb-2">Diagnosis</label>
                <textarea name="diagnosis"
                    placeholder="Contoh: Infeksi Saluran Pernapasan Akut (ISPA). Gejala demam 3 hari..."
                    class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal min-h-[100px]"
                    required></textarea>
            </div>

            <div>
                <label class="block font-semibold text-navytube mb-2">Tindakan / Resep Obat</label>
                <textarea name="treatment" placeholder="Contoh: Paracetamol 500mg (3x1). Istirahat yang cukup..."
                    class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal min-h-[100px]"></textarea>
            </div>

            <div>
                <label class="block font-semibold text-navytube mb-2">Catatan Tambahan</label>
                <textarea name="notes" placeholder="Catatan tambahan (opsional) untuk kontrol berikutnya..."
                    class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal"></textarea>
            </div>

            <button type="submit"
                class="w-full bg-navytube text-white py-4 rounded-xl text-lg font-bold hover:bg-teal transition duration-300 shadow-lg">
                💾 Simpan & Selesaikan
            </button>
        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>