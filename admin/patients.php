<?php
require_once 'includes/check_admin.php';
require_once __DIR__ . '/../config/db.php';

$conn = connect_db();

$current_page = 'patients';
$page_title = 'Manajemen Pasien';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM patients WHERE id_patient = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Pasien berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus pasien";
    }
    header("Location: patients.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_patient = isset($_POST['id_patient']) ? (int) $_POST['id_patient'] : 0;
    $id_user = !empty($_POST['id_user']) ? (int) $_POST['id_user'] : null;
    $name = trim($_POST['name']);
    $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $med_record_no = trim($_POST['med_record_no']);

    if ($id_patient > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE patients SET id_user=?, name=?, birth_date=?, address=?, phone=?, med_record_no=? WHERE id_patient=?");
        $stmt->bind_param("isssssi", $id_user, $name, $birth_date, $address, $phone, $med_record_no, $id_patient);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO patients (id_user, name, birth_date, address, phone, med_record_no) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id_user, $name, $birth_date, $address, $phone, $med_record_no);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $id_patient > 0 ? "Pasien berhasil diupdate" : "Pasien berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menyimpan pasien: " . $conn->error;
    }
    header("Location: patients.php");
    exit;
}

// Get users with role Pasien
$users_pasien = [];
$result = $conn->query("SELECT u.id_user, u.username FROM users u 
                        INNER JOIN roles r ON u.id_role = r.id_role 
                        WHERE r.role_name = 'Pasien' 
                        ORDER BY u.username");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users_pasien[] = $row;
    }
}

// Get all patients
$query = "SELECT p.*, u.username 
          FROM patients p 
          LEFT JOIN users u ON p.id_user = u.id_user 
          ORDER BY p.id_patient ASC";
$result = $conn->query($query);
$patients = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
} else {
    $_SESSION['error'] = "Error loading patients: " . $conn->error;
}

// Get edit patient if exists
$edit_patient = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id_patient = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_patient = $stmt->get_result()->fetch_assoc();
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="icon">+</i> Tambah Pasien
    </button>
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
                        placeholder="Cari berdasarkan nama, no. rekam medis, atau phone..." style="padding-left: 35px;"
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
                        <th>ID</th>
                        <th>No. Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal Lahir</th>
                        <th>Phone</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="patientTableBody">
                    <?php foreach ($patients as $patient): ?>
                        <tr class="patient-row" data-name="<?= strtolower(htmlspecialchars($patient['name'])) ?>"
                            data-medrecord="<?= strtolower(htmlspecialchars($patient['med_record_no'])) ?>"
                            data-phone="<?= strtolower(htmlspecialchars($patient['phone'])) ?>">
                            <td><?= htmlspecialchars($patient['id_patient']) ?></td>
                            <td><strong><?= htmlspecialchars($patient['med_record_no']) ?></strong></td>
                            <td><?= htmlspecialchars($patient['name']) ?></td>
                            <td><?= $patient['birth_date'] ? date('d/m/Y', strtotime($patient['birth_date'])) : '-' ?></td>
                            <td><?= htmlspecialchars($patient['phone']) ?></td>
                            <td>
                                <?php if ($patient['username']): ?>
                                    <span class="badge badge-success"><?= htmlspecialchars($patient['username']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick='editPatient(<?= json_encode($patient) ?>)'>Edit</button>
                                <a href="?delete=<?= $patient['id_patient'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus pasien ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="patientModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Pasien</h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="" id="patientForm">
            <div class="modal-body">
                <input type="hidden" name="id_patient" id="id_patient">

                <div class="form-group">
                    <label>User (Opsional)</label>
                    <select name="id_user" id="id_user" class="form-control">
                        <option value="">-- Pilih User (Opsional) --</option>
                        <?php foreach ($users_pasien as $user): ?>
                            <option value="<?= $user['id_user'] ?>">
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>No. Rekam Medis *</label>
                    <input type="text" name="med_record_no" id="med_record_no" class="form-control" required
                        placeholder="Contoh: MR-2025-001">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
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
                const phone = row.getAttribute('data-phone');

                if (name.includes(searchTerm) ||
                    medRecord.includes(searchTerm) ||
                    phone.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateResultCount(visibleCount, totalPatients);

            // Show/hide "no results" message
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

    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Pasien';
        document.getElementById('id_patient').value = '';
        document.getElementById('id_user').value = '';
        document.getElementById('med_record_no').value = '';
        document.getElementById('name').value = '';
        document.getElementById('birth_date').value = '';
        document.getElementById('address').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('patientModal').style.display = 'block';
    }

    function editPatient(patient) {
        document.getElementById('modalTitle').textContent = 'Edit Pasien';
        document.getElementById('id_patient').value = patient.id_patient;
        document.getElementById('id_user').value = patient.id_user || '';
        document.getElementById('med_record_no').value = patient.med_record_no;
        document.getElementById('name').value = patient.name;
        document.getElementById('birth_date').value = patient.birth_date || '';
        document.getElementById('address').value = patient.address || '';
        document.getElementById('phone').value = patient.phone || '';
        document.getElementById('patientModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('patientModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('patientModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<?php include 'includes/footer.php'; ?>