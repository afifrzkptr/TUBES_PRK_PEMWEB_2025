<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $med_record_no = $_POST['med_record_no'];

    $stmt = $conn->prepare("INSERT INTO patients (name, birth_date, address, phone, med_record_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $birth_date, $address, $phone, $med_record_no);
    $stmt->execute();

    header("Location: patient.php");
    exit;
}
?>

<main class="max-w-3xl mx-auto mt-10 px-4 animate-fade-in">
  <div class="card">
    <h2 class="text-2xl font-bold text-navytube mb-4">Tambah Data Pasien</h2>
    <form method="POST" class="space-y-4">
      <input type="text" name="name" placeholder="Nama Lengkap" class="input-field" required>
      <input type="date" name="birth_date" class="input-field">
      <input type="text" name="address" placeholder="Alamat" class="input-field">
      <input type="text" name="phone" placeholder="No. Telepon" class="input-field">
      <input type="text" name="med_record_no" placeholder="Nomor Rekam Medis" class="input-field" required>
      <button type="submit" class="btn btn-primary w-full">Simpan</button>
    </form>
  </div>
</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
