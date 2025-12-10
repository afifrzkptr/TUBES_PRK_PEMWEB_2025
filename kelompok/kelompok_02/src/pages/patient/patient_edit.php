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

<main class="max-w-3xl mx-auto mt-14 px-4 animate-fade-in">

  <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200">

    <h2 class="text-3xl font-bold text-navytube mb-6">Edit Data Pasien</h2>

    <form method="POST" class="space-y-5">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input 
          type="text" 
          name="name" 
          value="<?php echo htmlspecialchars($patient['name']); ?>" 
          class="input-field w-full" 
          required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
        <input 
          type="date" 
          name="birth_date" 
          value="<?php echo htmlspecialchars($patient['birth_date']); ?>" 
          class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
        <input 
          type="text" 
          name="address" 
          value="<?php echo htmlspecialchars($patient['address']); ?>" 
          class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
        <input 
          type="text" 
          name="phone" 
          value="<?php echo htmlspecialchars($patient['phone']); ?>" 
          class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekam Medis</label>
        <input 
          type="text" 
          name="med_record_no" 
          value="<?php echo htmlspecialchars($patient['med_record_no']); ?>" 
          class="input-field w-full" 
          required>
      </div>

      <button type="submit" 
              class="btn btn-primary w-full py-2 rounded-lg font-semibold shadow-sm hover:shadow transition">
        Update
      </button>

    </form>

  </div>

</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
