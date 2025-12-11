<?php
require_once 'includes/check_admin.php';
require_once __DIR__ . '/../config/db.php';

$conn = connect_db();

$current_page = 'medical_records';
$page_title = 'Riwayat Medis Pasien';

// Get patients with their medical records count (only show patients with medical records)
$query = "SELECT 
          p.id_patient,
          p.name,
          p.med_record_no,
          p.birth_date,
          p.phone,
          COUNT(mr.id_record) as total_records,
          MAX(a.date) as last_visit
          FROM patients p
          INNER JOIN appointments a ON p.id_patient = a.id_patient
          INNER JOIN medical_records mr ON a.id_appointment = mr.id_appointment
          GROUP BY p.id_patient, p.name, p.med_record_no, p.birth_date, p.phone
          HAVING COUNT(mr.id_record) > 0
          ORDER BY MAX(a.date) DESC";

$result = $conn->query($query);
$patients = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
} else {
    $_SESSION['error'] = "Error loading patients: " . $conn->error;
}

include 'includes/header.php';
?>

<!-- Load Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Daftar Pasien</h3>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <div class="search-container" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="position: relative; flex: 1; max-width: 400px;">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari pasien berdasarkan nama atau no. rekam medis..." style="padding-left: 35px;"
                        autocomplete="off">
                    <svg style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px;"
                        viewBox="0 0 24 24" fill="none" stroke="#2f4156" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>
                <span id="resultCount" style="color: #666; font-size: 14px; min-width: 120px;"></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table" id="patientsTable">
                <thead>
                    <tr>
                        <th>No. Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal Lahir</th>
                        <th>Phone</th>
                        <th>Total Kunjungan</th>
                        <th>Kunjungan Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody">
                    <?php foreach ($patients as $patient): ?>
                        <tr class="patient-row" data-name="<?= strtolower(htmlspecialchars($patient['name'])) ?>"
                            data-medrecord="<?= strtolower(htmlspecialchars($patient['med_record_no'])) ?>">
                            <td><strong><?= htmlspecialchars($patient['med_record_no']) ?></strong></td>
                            <td><?= htmlspecialchars($patient['name']) ?></td>
                            <td><?= $patient['birth_date'] ? date('d/m/Y', strtotime($patient['birth_date'])) : '-' ?></td>
                            <td><?= htmlspecialchars($patient['phone'] ?: '-') ?></td>
                            <td>
                                <span class="badge badge-<?= $patient['total_records'] > 0 ? 'info' : 'secondary' ?>">
                                    <?= $patient['total_records'] ?> kunjungan
                                </span>
                            </td>
                            <td><?= $patient['last_visit'] ? date('d/m/Y', strtotime($patient['last_visit'])) : '-' ?></td>
                            <td>
                                <?php if ($patient['total_records'] > 0): ?>
                                    <a href="patient_medical_history.php?id=<?= $patient['id_patient'] ?>"
                                        class="btn btn-sm btn-primary"
                                        style="display: inline-flex; align-items: center; gap: 6px;">
                                        <i data-lucide="clipboard-list" style="width: 14px; height: 14px;"></i>
                                        Lihat Riwayat
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada riwayat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Live Search Functionality
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const patientRows = document.querySelectorAll('.patient-row');
        const resultCount = document.getElementById('resultCount');
        const totalPatients = patientRows.length;

        // Update count on load
        updateResultCount(totalPatients, totalPatients);

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;

            patientRows.forEach(function (row) {
                const name = row.getAttribute('data-name');
                const medRecord = row.getAttribute('data-medrecord');

                if (name.includes(searchTerm) || medRecord.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateResultCount(visibleCount, totalPatients);
            showNoResults(visibleCount === 0, searchTerm);
        });

        function updateResultCount(visible, total) {
            if (visible === total) {
                resultCount.textContent = `${total} pasien`;
            } else {
                resultCount.textContent = `${visible} dari ${total} pasien`;
            }
        }

        function showNoResults(show, searchTerm) {
            const tableBody = document.getElementById('patientTableBody');
            let noResultRow = document.getElementById('noResultRow');

            if (show) {
                if (!noResultRow) {
                    noResultRow = document.createElement('tr');
                    noResultRow.id = 'noResultRow';
                    noResultRow.innerHTML = `
                    <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                        Tidak ada hasil yang ditemukan untuk "<strong>${searchTerm}</strong>"
                    </td>
                `;
                    tableBody.appendChild(noResultRow);
                }
            } else {
                if (noResultRow) {
                    noResultRow.remove();
                }
            }
        }
    });

    // Initialize Lucide Icons
    lucide.createIcons();
</script>

<?php include 'includes/footer.php'; ?>