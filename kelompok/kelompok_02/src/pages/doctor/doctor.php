<?php
require_once(dirname(__DIR__, 2) . '/config/db.php');
require_once(dirname(__DIR__, 2) . '/includes/header.php');

// Filter spesialisasi
$filter = $_GET['specialization'] ?? '';
$where = $filter ? "WHERE specialization = '$filter'" : '';

// Ambil data dokter
$result = $conn->query("SELECT * FROM doctors $where ORDER BY id_doctor DESC");

// Ambil semua spesialisasi unik (untuk filter dropdown)
$specs = $conn->query("SELECT DISTINCT specialization FROM doctors ORDER BY specialization ASC");
?>

<main class="max-w-7xl mx-auto mt-10 px-4 sm:px-6 lg:px-8 animate-fade-in">
  <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
    <div>
      <h1 class="text-3xl font-extrabold text-navytube">Manajemen Data Dokter</h1>
      <p class="text-sm text-gray-600">Kelola data dokter dan spesialisasi di rumah sakit.</p>
    </div>

    <div class="flex gap-2 items-center">
      <form method="GET">
        <select name="specialization" class="input-field" onchange="this.form.submit()">
          <option value="">Semua Spesialisasi</option>
          <?php while ($s = $specs->fetch_assoc()): ?>
            <option value="<?php echo $s['specialization']; ?>" <?php if ($filter == $s['specialization']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($s['specialization']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </form>
      <a href="doctor_add.php" class="btn btn-primary">+ Tambah Dokter</a>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200 rounded-xl shadow-md">
      <thead class="bg-navytube text-white">
        <tr>
          <th class="py-2 px-4">ID</th>
          <th class="py-2 px-4">Nama Dokter</th>
          <th class="py-2 px-4">Spesialisasi</th>
          <th class="py-2 px-4">Telepon</th>
          <th class="py-2 px-4">Nomor STR</th>
          <th class="py-2 px-4">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white">
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="border-b hover:bg-skyblue transition">
              <td class="py-2 px-4"><?php echo $row['id_doctor']; ?></td>
              <td class="py-2 px-4 font-semibold text-navytube"><?php echo htmlspecialchars($row['name']); ?></td>
              <td class="py-2 px-4 text-teal"><?php echo htmlspecialchars($row['specialization']); ?></td>
              <td class="py-2 px-4"><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></td>
              <td class="py-2 px-4"><?php echo htmlspecialchars($row['license_no']); ?></td>
              <td class="py-2 px-4 space-x-2">
                <a href="doctor_edit.php?id=<?php echo $row['id_doctor']; ?>" class="btn btn-secondary">Edit</a>
                <a href="doctor_delete.php?id=<?php echo $row['id_doctor']; ?>" 
                   onclick="return confirm('Yakin ingin menghapus dokter ini?');" 
                   class="btn btn-danger">Hapus</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data dokter.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php require_once(dirname(__DIR__, 2) . '/includes/footer.php'); ?>
