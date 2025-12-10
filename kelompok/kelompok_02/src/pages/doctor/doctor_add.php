<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $license_no = $_POST['license_no'];

    // sementara diasumsikan admin dengan ID 1 yang menambahkan dokter
    // nanti bisa diganti: $_SESSION['id_user']
    $id_user = 1;

    $stmt = $conn->prepare("
        INSERT INTO doctors (name, specialization, phone, license_no, id_user) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("ssssi", $name, $specialization, $phone, $license_no, $id_user);
    $stmt->execute();

    header("Location: doctor.php?add=success");
    exit;
}
?>

<main class="max-w-3xl mx-auto mt-14 px-4 animate-fade-in">

  <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200 border-l-4 border-teal-600">

    <h2 class="text-3xl font-bold text-navytube mb-6">Tambah Data Dokter</h2>

    <form method="POST" class="space-y-5">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Dokter</label>
        <input type="text" 
               name="name" 
               placeholder="Contoh: Dr. Sinta Dewi, Sp.A"
               class="input-field w-full"
               required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
        <input type="text" 
               name="specialization" 
               placeholder="Misal: Anak, Umum, Bedah"
               class="input-field w-full"
               required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
        <input type="text" 
               name="phone" 
               placeholder="08123xxxxxxx"
               class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor STR</label>
        <input type="text" 
               name="license_no" 
               placeholder="Masukkan nomor STR dokter"
               class="input-field w-full"
               required>
      </div>

      <button type="submit" 
              class="btn btn-primary w-full py-2 rounded-lg font-semibold shadow-sm hover:shadow transition">
        Simpan
      </button>

    </form>

  </div>
</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
