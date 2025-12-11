require_once 'config/db.php';
$conn = connect_db();

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
echo "<h1>Generating Dummy Data...</h1>";

// 1. Get a valid Patient
$result_patient = $conn->query("SELECT id_patient, name FROM patients LIMIT 1");
if ($result_patient->num_rows === 0) {
throw new Exception("Error: Tidak ada data Pasien di database. Silakan register akun pasien terlebih dahulu.");
}
$patient = $result_patient->fetch_assoc();
$id_patient = $patient['id_patient'];
echo "<p>✅ Found Patient: <strong>{$patient['name']}</strong> (ID: $id_patient)</p>";

// 2. Get a valid Doctor
$result_doctor = $conn->query("SELECT id_doctor, name FROM doctors LIMIT 1");
if ($result_doctor->num_rows === 0) {
throw new Exception("Error: Tidak ada data Dokter di database. Silakan login admin dan tambah data dokter.");
}
$doctor = $result_doctor->fetch_assoc();
$id_doctor = $doctor['id_doctor'];
echo "<p>✅ Found Doctor: <strong>{$doctor['name']}</strong> (ID: $id_doctor)</p>";

// 3. Insert Appointments (Past Date)
$stmt_appt = $conn->prepare("INSERT INTO appointments (id_patient, id_doctor, date, time, status, queue_number) VALUES
(?, ?, ?, ?, 'Done', ?)");

// Appointment 1: 5 days ago
$date1 = date('Y-m-d', strtotime('-5 days'));
$time1 = '10:00:00';
$q1 = 1;
$stmt_appt->bind_param("iisss", $id_patient, $id_doctor, $date1, $time1, $q1);
$stmt_appt->execute();
$id_appt1 = $conn->insert_id;
echo "<p>✅ Created Appointment 1 (ID: $id_appt1) for Date: $date1</p>";

// Appointment 2: 30 days ago
$date2 = date('Y-m-d', strtotime('-30 days'));
$time2 = '14:00:00';
$q2 = 5;
$stmt_appt->bind_param("iisss", $id_patient, $id_doctor, $date2, $time2, $q2);
$stmt_appt->execute();
$id_appt2 = $conn->insert_id;
echo "<p>✅ Created Appointment 2 (ID: $id_appt2) for Date: $date2</p>";

// 4. Insert Medical Records
$stmt_record = $conn->prepare("INSERT INTO medical_records (id_appointment, diagnosis, treatment, notes) VALUES (?, ?,
?, ?)");

// Record 1
$diag1 = 'Flu Berat';
$treat1 = "Paracetamol 500mg (3x1)\nVitamin C\nIstirahat cukup";
$note1 = 'Pasien disarankan banyak minum air putih.';
$stmt_record->bind_param("isss", $id_appt1, $diag1, $treat1, $note1);
$stmt_record->execute();
echo "<p>✅ Created Medical Record for Appointment 1</p>";

// Record 2
$diag2 = 'Radang Tenggorokan';
$treat2 = "Amoxicillin 500mg (3x1)\nIbuprofen 400mg";
$note2 = 'Habiskan antibiotik.';
$stmt_record->bind_param("isss", $id_appt2, $diag2, $treat2, $note2);
$stmt_record->execute();
echo "<p>✅ Created Medical Record for Appointment 2</p>";

echo "<h2 style='color:green;'>SUCCESS! Dummy data has been inserted.</h2>";
echo "<p><a href='doctor/dashboard.php'>Go to Doctor Dashboard</a></p>";

} catch (Exception $e) {
echo "<h2 style='color:red;'>FAILED: " . $e->getMessage() . "</h2>";
}
?>