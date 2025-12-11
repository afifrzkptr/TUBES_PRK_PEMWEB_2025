<?php
include __DIR__ . '/../config/db.php';
$conn = connect_db();

// Ambil data POST
$doctor = $_POST['doctor_id'] ?? null;
$patient = $_POST['patient_id'] ?? null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;

// Validasi sederhana
if (!$doctor || !$patient || !$date || !$time) {
    header("Location: make_appointment.php?error=" . urlencode("Data tidak lengkap"));
    exit;
}

// Cek apakah dokter sudah ada janji di tanggal & jam ini
$stmt_check = $conn->prepare("SELECT * FROM appointments WHERE id_doctor = ? AND date = ? AND time = ?");
$stmt_check->bind_param("iss", $doctor, $date, $time);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    header("Location: make_appointment.php?error=" . urlencode("Jadwal dokter sudah terisi"));
    exit;
}

// Insert janji temu
$stmt = $conn->prepare("INSERT INTO appointments (id_patient, id_doctor, date, time, status) VALUES (?, ?, ?, ?, 'Scheduled')");
$stmt->bind_param("iiss", $patient, $doctor, $date, $time);

if ($stmt->execute()) {
    // Redirect ke list janji temu dengan pesan sukses
    header("Location: appointment_list.php?success=1");
    exit;
} else {
    header("Location: make_appointment.php?error=" . urlencode("Gagal membuat janji: " . $conn->error));
    exit;
}