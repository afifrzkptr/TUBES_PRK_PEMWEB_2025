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

<main class="max-w-3xl mx-auto mt-14 px-4 animate-fade-in">

  <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200">

    <h2 class="text-3xl font-bold text-navytube mb-6">Tambah Data Pasien</h2>

    <form method="POST" class="space-y-5">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="name" placeholder="Masukkan nama pasien"
               class="input-field w-full" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
        <input type="date" name="birth_date" class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
        <input type="text" name="address" placeholder="Masukkan alamat pasien"
               class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
        <input type="text" name="phone" placeholder="08123xxxxxxx"
               class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekam Medis</label>
        <input type="text" name="med_record_no" placeholder="Nomor rekam medis pasien"
               class="input-field w-full" required>
      </div>

      <button type="submit" 
              class="btn btn-primary w-full py-2 rounded-lg font-semibold shadow-sm hover:shadow transition">
        Simpan
      </button>

    </form>
  </div>

</main>


<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
