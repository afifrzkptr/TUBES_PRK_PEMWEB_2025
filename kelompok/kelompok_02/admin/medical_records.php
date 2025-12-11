<?php
require_once 'includes/check_admin.php';
require_once '../config/db.php';

$conn = connect_db();

$current_page = 'medical_records';
$page_title = 'Riwayat Medis';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM medical_records WHERE id_record = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Riwayat medis berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus riwayat medis";
    }
    header("Location: medical_records.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_record = isset($_POST['id_record']) ? (int) $_POST['id_record'] : 0;
    $id_appointment = (int) $_POST['id_appointment'];
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $notes = trim($_POST['notes']);

    if ($id_record > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE medical_records SET id_appointment=?, diagnosis=?, treatment=?, notes=? WHERE id_record=?");
        $stmt->bind_param("isssi", $id_appointment, $diagnosis, $treatment, $notes, $id_record);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO medical_records (id_appointment, diagnosis, treatment, notes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_appointment, $diagnosis, $treatment, $notes);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $id_record > 0 ? "Riwayat medis berhasil diupdate" : "Riwayat medis berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menyimpan riwayat medis: " . $conn->error;
    }
    header("Location: medical_records.php");
    exit;
}

// Get all appointments with status Done
$appointments = [];
$result = $conn->query("SELECT a.id_appointment, a.date, a.time, a.queue_number,
                        p.name as patient_name, p.med_record_no,
                        d.name as doctor_name, d.specialization
                        FROM appointments a 
                        INNER JOIN patients p ON a.id_patient = p.id_patient
                        INNER JOIN doctors d ON a.id_doctor = d.id_doctor
                        WHERE a.status = 'Done'
                        ORDER BY a.date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

// Get all medical records
$query = "SELECT mr.*, 
          a.date, a.time, a.queue_number,
          p.name as patient_name, p.med_record_no,
          d.name as doctor_name, d.specialization
          FROM medical_records mr 
          INNER JOIN appointments a ON mr.id_appointment = a.id_appointment
          INNER JOIN patients p ON a.id_patient = p.id_patient
          INNER JOIN doctors d ON a.id_doctor = d.id_doctor
          ORDER BY mr.id_record ASC";
$result = $conn->query($query);
$medical_records = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $medical_records[] = $row;
    }
} else {
    $_SESSION['error'] = "Error loading medical records: " . $conn->error;
}

// Get edit medical record if exists
$edit_record = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM medical_records WHERE id_record = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_record = $stmt->get_result()->fetch_assoc();
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="icon">+</i> Tambah Riwayat Medis
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
        <h3>Daftar Riwayat Medis</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Diagnosis</th>
                        <th>Treatment</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medical_records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['id_record']) ?></td>
                            <td><?= date('d/m/Y', strtotime($record['date'])) ?></td>
                            <td>
                                <?= htmlspecialchars($record['patient_name']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($record['med_record_no']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($record['doctor_name']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($record['specialization']) ?></small>
                            </td>
                            <td><?= htmlspecialchars(substr($record['diagnosis'], 0, 50)) ?>...</td>
                            <td><?= htmlspecialchars(substr($record['treatment'], 0, 50)) ?>...</td>
                            <td>
                                <button class="btn btn-sm btn-info"
                                    onclick='viewRecord(<?= json_encode($record) ?>)'>Lihat</button>
                                <button class="btn btn-sm btn-warning"
                                    onclick='editRecord(<?= json_encode($record) ?>)'>Edit</button>
                                <a href="?delete=<?= $record['id_record'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus riwayat medis ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="recordModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Riwayat Medis</h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="" id="medicalRecordForm">
            <div class="modal-body">
                <input type="hidden" name="id_record" id="id_record">

                <div class="form-group">
                    <label>Appointment (Done) *</label>
                    <select name="id_appointment" id="id_appointment" class="form-control" required>
                        <option value="">-- Pilih Appointment --</option>
                        <?php foreach ($appointments as $appointment): ?>
                            <option value="<?= $appointment['id_appointment'] ?>">
                                <?= date('d/m/Y', strtotime($appointment['date'])) ?> -
                                <?= htmlspecialchars($appointment['patient_name']) ?>
                                (Q: <?= $appointment['queue_number'] ?>) -
                                <?= htmlspecialchars($appointment['doctor_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Diagnosis *</label>
                    <textarea name="diagnosis" id="diagnosis" class="form-control" rows="4" required
                        placeholder="Hasil diagnosis..."></textarea>
                </div>

                <div class="form-group">
                    <label>Treatment</label>
                    <textarea name="treatment" id="treatment" class="form-control" rows="4"
                        placeholder="Tindakan yang dilakukan..."></textarea>
                </div>

                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"
                        placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal View -->
<div id="viewModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Detail Riwayat Medis</h3>
            <span class="modal-close" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="record-detail">
                <div class="detail-row">
                    <strong>Tanggal:</strong>
                    <span id="view_date"></span>
                </div>
                <div class="detail-row">
                    <strong>Pasien:</strong>
                    <span id="view_patient"></span>
                </div>
                <div class="detail-row">
                    <strong>Dokter:</strong>
                    <span id="view_doctor"></span>
                </div>
                <hr>
                <div class="detail-row">
                    <strong>Diagnosis:</strong>
                    <p id="view_diagnosis"></p>
                </div>
                <div class="detail-row">
                    <strong>Treatment:</strong>
                    <p id="view_treatment"></p>
                </div>
                <div class="detail-row">
                    <strong>Catatan:</strong>
                    <p id="view_notes"></p>
                </div>
                <div class="detail-row">
                    <strong>Dibuat:</strong>
                    <span id="view_created_at"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Tutup</button>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Riwayat Medis';
        document.getElementById('id_record').value = '';
        document.getElementById('id_appointment').value = '';
        document.getElementById('diagnosis').value = '';
        document.getElementById('treatment').value = '';
        document.getElementById('notes').value = '';
        document.getElementById('recordModal').style.display = 'block';
    }

    function editRecord(record) {
        document.getElementById('modalTitle').textContent = 'Edit Riwayat Medis';
        document.getElementById('id_record').value = record.id_record;
        document.getElementById('id_appointment').value = record.id_appointment;
        document.getElementById('diagnosis').value = record.diagnosis;
        document.getElementById('treatment').value = record.treatment || '';
        document.getElementById('notes').value = record.notes || '';
        document.getElementById('recordModal').style.display = 'block';
    }

    function viewRecord(record) {
        const formatDate = (dateStr) => {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        };

        document.getElementById('view_date').textContent = formatDate(record.date) + ' ' + record.time.substring(0, 5);
        document.getElementById('view_patient').textContent = record.patient_name + ' (' + record.med_record_no + ')';
        document.getElementById('view_doctor').textContent = record.doctor_name + ' - ' + record.specialization;
        document.getElementById('view_diagnosis').textContent = record.diagnosis;
        document.getElementById('view_treatment').textContent = record.treatment || '-';
        document.getElementById('view_notes').textContent = record.notes || '-';
        document.getElementById('view_created_at').textContent = formatDate(record.created_at);
        document.getElementById('viewModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('recordModal').style.display = 'none';
    }

    function closeViewModal() {
        document.getElementById('viewModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const recordModal = document.getElementById('recordModal');
        const viewModal = document.getElementById('viewModal');
        if (event.target === recordModal) {
            closeModal();
        }
        if (event.target === viewModal) {
            closeViewModal();
        }
    }
</script>

<?php include 'includes/footer.php'; ?>