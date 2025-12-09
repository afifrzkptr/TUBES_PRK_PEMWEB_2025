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

<main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 animate-fade-in">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-extrabold text-navytube">Manajemen Data Pasien</h1>
    <a href="patient_add.php" class="btn btn-primary">+ Tambah Pasien</a>
  </div>

  <table class="min-w-full border">
    <thead class="table-header">
      <tr>
        <th class="py-2 px-4 text-left">ID</th>
        <th class="py-2 px-4 text-left">Nama</th>
        <th class="py-2 px-4 text-left">Tanggal Lahir</th>
        <th class="py-2 px-4 text-left">Alamat</th>
        <th class="py-2 px-4 text-left">No. Telepon</th>
        <th class="py-2 px-4 text-left">No. Rekam Medis</th>
        <th class="py-2 px-4 text-left">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="table-row">
          <td class="py-2 px-4"><?php echo safe($row['id_patient']); ?></td>
          <td class="py-2 px-4"><?php echo safe($row['name']); ?></td>
          <td class="py-2 px-4"><?php echo safe($row['birth_date']); ?></td>
          <td class="py-2 px-4"><?php echo safe($row['address']); ?></td>
          <td class="py-2 px-4"><?php echo safe($row['phone']); ?></td>
          <td class="py-2 px-4"><?php echo safe($row['med_record_no']); ?></td>
          <td class="py-2 px-4 space-x-2">
            <a href="patient_edit.php?id=<?php echo $row['id_patient']; ?>" class="btn btn-secondary">Edit</a>
            <a href="patient_delete.php?id=<?php echo $row['id_patient']; ?>"
               onclick="return confirm('Yakin ingin menghapus data ini?');"
               class="btn btn-danger">Hapus</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
