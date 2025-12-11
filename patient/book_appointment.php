<?php
require_once 'includes/check_patient.php';

$success = '';
$error = '';

// Get doctors list
$query_doctors = "SELECT * FROM doctors ORDER BY specialization, name";
$doctors = mysqli_query($conn, $query_doctors);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_patient = $_POST['id_patient'];
    $id_doctor = $_POST['id_doctor'];
    $date = $_POST['date'];
    $time = $_POST['time']; // Idealnya divalidasi dengan slot yang tersedia

    // Simple validation
    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        $error = "Tanggal tidak boleh kurang dari hari ini.";
    } else {
        // Generate Queue Number (Simple logic: count existing + 1 for that day)
        $query_queue = "SELECT COUNT(*) as total FROM appointments WHERE id_doctor = $id_doctor AND date = '$date'";
        $queue_result = mysqli_fetch_assoc(mysqli_query($conn, $query_queue));
        $queue_number = $queue_result['total'] + 1;

        $stmt = $conn->prepare("INSERT INTO appointments (id_patient, id_doctor, date, time, status, queue_number) VALUES (?, ?, ?, ?, 'Scheduled', ?)");
        $stmt->bind_param("iissi", $id_patient, $id_doctor, $date, $time, $queue_number);

        if ($stmt->execute()) {
            $success = "Janji temu berhasil dibuat! Nomor antrian Anda: $queue_number";
        } else {
            $error = "Gagal membuat janji temu: " . $conn->error;
        }
    }
}

// Get patient ID for form
$user_id = $_SESSION['user_id'];
$patient = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_patient FROM patients WHERE id_user = $user_id"));
?>

<?php
// Include header
include '../includes/header.php';
?>

<div class="flex-grow">
    <main class="max-w-4xl mx-auto mt-10 px-4 animate-fade-in mb-20">
        <div class="bg-white shadow-xl rounded-2xl p-8 border-t-4 border-teal relative">
            <a href="dashboard.php" class="absolute top-8 right-8 text-teal hover:text-navytube font-medium">✕ Batal</a>

            <h2 class="text-3xl font-bold text-navytube mb-2">Buat Janji Temu</h2>
            <p class="text-teal mb-8">Pilih dokter dan waktu kunjungan yang sesuai.</p>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                    role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline"><?php echo $success; ?></span>
                    <div class="mt-2">
                        <a href="dashboard.php" class="underline font-bold hover:text-green-900">Kembali ke Dashboard</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="id_patient" value="<?php echo $patient['id_patient']; ?>">

                <div>
                    <label class="block font-semibold text-navytube mb-2">Pilih Dokter</label>
                    <div class="relative">
                        <select name="id_doctor" required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal appearance-none bg-white">
                            <option value="">-- Pilih Dokter Spesialis --</option>
                            <?php while ($doc = mysqli_fetch_assoc($doctors)): ?>
                                <option value="<?php echo $doc['id_doctor']; ?>">
                                    <?php echo $doc['specialization'] . " - " . $doc['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-teal">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold text-navytube mb-2">Tanggal Kunjungan</label>
                        <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>"
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>

                    <div>
                        <label class="block font-semibold text-navytube mb-2">Waktu (Jam)</label>
                        <input type="time" name="time" required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-navytube text-white py-4 rounded-xl text-lg font-bold hover:bg-teal transition duration-300 shadow-lg">
                        Konfirmasi Janji Temu
                    </button>
                    <div class="mt-4 text-center">
                        <a href="dashboard.php" class="text-gray-500 hover:text-navytube text-sm font-medium">Kembali ke
                            Dashboard</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
include '../includes/footer.php';
close_db($conn);
?>