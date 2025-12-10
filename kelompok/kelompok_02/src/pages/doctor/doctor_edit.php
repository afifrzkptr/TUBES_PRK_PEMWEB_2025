<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

$id = $_GET['id'] ?? null;
if (!$id) die("ID dokter tidak ditemukan.");

$doctor = $conn->query("SELECT * FROM doctors WHERE id_doctor = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $license_no = $_POST['license_no'];

    $stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, phone=?, license_no=? WHERE id_doctor=?");
    $stmt->bind_param("ssssi", $name, $specialization, $phone, $license_no, $id);
    $stmt->execute();

    header("Location: doctor.php?update=success");
    exit;
}
?>

<main class="max-w-3xl mx-auto mt-14 px-4 animate-fade-in">

  <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200 border-l-4 border-navytube">

    <h2 class="text-3xl font-bold text-navytube mb-6">Edit Data Dokter</h2>

    <form method="POST" class="space-y-5">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Dokter</label>
        <input type="text" 
               name="name" 
               value="<?php echo htmlspecialchars($doctor['name']); ?>" 
               class="input-field w-full"
               required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
        <input type="text" 
               name="specialization" 
               value="<?php echo htmlspecialchars($doctor['specialization']); ?>" 
               class="input-field w-full"
               required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
        <input type="text" 
               name="phone" 
               value="<?php echo htmlspecialchars($doctor['phone']); ?>" 
               class="input-field w-full">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor STR</label>
        <input type="text" 
               name="license_no" 
               value="<?php echo htmlspecialchars($doctor['license_no']); ?>" 
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
