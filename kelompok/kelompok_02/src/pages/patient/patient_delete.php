<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM patients WHERE id_patient = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: patient.php");
exit;
?>
