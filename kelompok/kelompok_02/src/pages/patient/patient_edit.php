<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

// ambil ID dari URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID pasien tidak ditemukan.");
}

// ambil data pasien dari DB
$result = $conn->query("SELECT * FROM patients WHERE id_patient = $id");
$patient = $result->fetch_assoc();

if (!$patient) {
    die("Data pasien tidak ditemukan.");
}

// jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'] ?: null;
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $med_record_no = $_POST['med_record_no'];

    $stmt = $conn->prepare("
        UPDATE patients
        SET name = ?, birth_date = ?, address = ?, phone = ?, med_record_no = ?
        WHERE id_patient = ?
    ");
    $stmt->bind_param("sssssi", $name, $birth_date, $address, $phone, $med_record_no, $id);

    if ($stmt->execute()) {
        header("Location: patient.php?update=success");
        exit;
    } else {
        echo "<div class='alert alert-error'>Gagal update data: " . $conn->error . "</div>";
    }
}
?>

<main class="max-w-3xl mx-auto mt-10 px-4 animate-fade-in">
  <div class="card">
    <h2 class="text-2xl font-bold text-navytube mb-4">Edit Data Pasien</h2>

    <form method="POST" class="space-y-4">
      <input type="text" name="name" value="<?php echo htmlspecialchars($patient['name']); ?>" class="input-field" required>
      <input type="date" name="birth_date" value="<?php echo htmlspecialchars($patient['birth_date']); ?>" class="input-field">
      <input type="text" name="address" value="<?php echo htmlspecialchars($patient['address']); ?>" class="input-field">
      <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" class="input-field">
      <input type="text" name="med_record_no" value="<?php echo htmlspecialchars($patient['med_record_no']); ?>" class="input-field" required>
      <button type="submit" class="btn btn-primary w-full">Update</button>
    </form>
  </div>
</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
