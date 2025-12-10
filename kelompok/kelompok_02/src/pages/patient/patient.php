<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

// Fungsi aman untuk menampilkan teks (hindari error NULL + XSS)
function safe($value) {
  return htmlspecialchars($value ?? '—', ENT_QUOTES, 'UTF-8');
}

// Ambil data pasien dari database
$query = "SELECT * FROM patients ORDER BY id_patient DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<main class="max-w-6xl mx-auto mt-14 px-4 sm:px-6 lg:px-8">

  <div class="bg-white rounded-xl shadow-md p-8 border border-gray-200">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-navytube">Manajemen Data Pasien</h1>
        <p class="text-gray-600 text-sm mt-1">
          Kelola data pasien.
        </p>
      </div>

      <a href="patient_add.php" 
         class="btn btn-primary px-4 py-2 rounded-md font-semibold shadow-sm text-white bg-[#23395d] hover:bg-[#1d2f4f] transition">
        + Tambah Pasien
      </a>
    </div>

    <!-- Table -->
    <div class="overflow-hidden border rounded-lg">
      <table class="min-w-full bg-white">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="py-3 px-4 text-left text-sm font-semibold">ID</th>
            <th class="py-3 px-4 text-left text-sm font-semibold">Nama</th>
            <th class="py-3 px-4 text-left text-sm font-semibold">Tanggal Lahir</th>
            <th class="py-3 px-4 text-left text-sm font-semibold">Alamat</th>
            <th class="py-3 px-4 text-left text-sm font-semibold">No. Telepon</th>
            <th class="py-3 px-4 text-left text-sm font-semibold">No. Rekam Medis</th>
            <th class="py-3 px-4 text-left text-sm font-semibold">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-100 transition">
              <td class="py-3 px-4"><?php echo safe($row['id_patient']); ?></td>
              <td class="py-3 px-4"><?php echo safe($row['name']); ?></td>
              <td class="py-3 px-4"><?php echo safe($row['birth_date']); ?></td>
              <td class="py-3 px-4"><?php echo safe($row['address']); ?></td>
              <td class="py-3 px-4"><?php echo safe($row['phone']); ?></td>
              <td class="py-3 px-4">
                <span class="px-2 py-1 bg-gray-200 rounded text-sm">
                  <?php echo safe($row['med_record_no']); ?>
                </span>
              </td>
              <td class="py-3 px-4 space-x-2">
                <a href="patient_edit.php?id=<?php echo $row['id_patient']; ?>" 
                   class="px-3 py-1 text-sm rounded border hover:bg-gray-200 transition">
                  Edit
                </a>
                <a href="patient_delete.php?id=<?php echo $row['id_patient']; ?>"
                   onclick="return confirm('Yakin ingin menghapus data ini?');"
                   class="px-3 py-1 text-sm rounded border hover:bg-red-200 transition">
                  Hapus
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
