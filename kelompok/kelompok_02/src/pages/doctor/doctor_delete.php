<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
$id = $_GET['id'] ?? null;
if (!$id) die("ID dokter tidak ditemukan.");

$stmt = $conn->prepare("DELETE FROM doctors WHERE id_doctor = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: doctor.php?delete=success");
exit;
?>
