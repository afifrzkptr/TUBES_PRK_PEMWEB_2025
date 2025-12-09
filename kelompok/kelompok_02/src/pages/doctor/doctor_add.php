<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $license_no = $_POST['license_no'];

    $stmt = $conn->prepare("INSERT INTO doctors (name, specialization, phone, license_no) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $specialization, $phone, $license_no);
    $stmt->execute();

    header("Location: doctor.php?add=success");
    exit;
}
?>

<main class="max-w-3xl mx-auto mt-10 px-4 animate-fade-in">
  <div class="card border-l-4 border-teal">
    <h2 class="text-2xl font-bold text-navytube mb-4">Tambah Dokter Baru</h2>
    <form method="POST" class="space-y-4">
      <input type="text" name="name" placeholder="Nama Lengkap Dokter" class="input-field" required>
      <input type="text" name="specialization" placeholder="Spesialisasi (misal: Anak, Umum, Bedah)" class="input-field" required>
      <input type="text" name="phone" placeholder="Nomor Telepon" class="input-field">
      <input type="text" name="license_no" placeholder="Nomor STR" class="input-field" required>
      <button type="submit" class="btn btn-primary w-full">Simpan Dokter</button>
    </form>
  </div>
</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
