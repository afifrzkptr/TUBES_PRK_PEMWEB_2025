<?php
require_once 'includes/check_admin.php';
require_once '../config/db.php';

$conn = connect_db();

$current_page = 'appointments';
$page_title = 'Manajemen Appointments';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id_appointment = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Appointment berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus appointment";
    }
    header("Location: appointments.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_appointment = isset($_POST['id_appointment']) ? (int) $_POST['id_appointment'] : 0;
    $id_patient = (int) $_POST['id_patient'];
    $id_doctor = (int) $_POST['id_doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $queue_number = (int) $_POST['queue_number'];
    $status = $_POST['status'];

    if ($id_appointment > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE appointments SET id_patient=?, id_doctor=?, date=?, time=?, queue_number=?, status=? WHERE id_appointment=?");
        $stmt->bind_param("iissisi", $id_patient, $id_doctor, $date, $time, $queue_number, $status, $id_appointment);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO appointments (id_patient, id_doctor, date, time, queue_number, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissis", $id_patient, $id_doctor, $date, $time, $queue_number, $status);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $id_appointment > 0 ? "Appointment berhasil diupdate" : "Appointment berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menyimpan appointment: " . $conn->error;
    }
    header("Location: appointments.php");
    exit;
}

// Get all patients
$patients = [];
$result = $conn->query("SELECT id_patient, name, med_record_no FROM patients ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

// Get all doctors
$doctors = [];
$result = $conn->query("SELECT id_doctor, name, specialization FROM doctors ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Get all appointments
$query = "SELECT a.*, 
          p.name as patient_name, p.med_record_no,
          d.name as doctor_name, d.specialization 
          FROM appointments a 
          INNER JOIN patients p ON a.id_patient = p.id_patient 
          INNER JOIN doctors d ON a.id_doctor = d.id_doctor 
          ORDER BY a.id_appointment ASC";
$result = $conn->query($query);
$appointments = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
} else {
    $_SESSION['error'] = "Error loading appointments: " . $conn->error;
}

// Get edit appointment if exists
$edit_appointment = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id_appointment = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_appointment = $stmt->get_result()->fetch_assoc();
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="icon">+</i> Tambah Appointment
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
        <h3>Daftar Appointments</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>No. Antrian</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['id_appointment']) ?></td>
                            <td><strong><?= htmlspecialchars($appointment['queue_number']) ?></strong></td>
                            <td><?= date('d/m/Y', strtotime($appointment['date'])) ?></td>
                            <td><?= date('H:i', strtotime($appointment['time'])) ?></td>
                            <td>
                                <?= htmlspecialchars($appointment['patient_name']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($appointment['med_record_no']) ?></small>
                            </td>
                            <td>
                                <?= htmlspecialchars($appointment['doctor_name']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($appointment['specialization']) ?></small>
                            </td>
                            <td>
                                <?php
                                $badge_class = 'secondary';
                                switch ($appointment['status']) {
                                    case 'Scheduled':
                                        $badge_class = 'primary';
                                        break;
                                    case 'In Progress':
                                        $badge_class = 'warning';
                                        break;
                                    case 'Done':
                                        $badge_class = 'success';
                                        break;
                                    case 'Canceled':
                                        $badge_class = 'danger';
                                        break;
                                }
                                ?>
                                <span class="badge badge-<?= $badge_class ?>">
                                    <?= htmlspecialchars($appointment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick='editAppointment(<?= json_encode($appointment) ?>)'>Edit</button>
                                <a href="?delete=<?= $appointment['id_appointment'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus appointment ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Appointment</h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="" id="appointmentForm">
            <div class="modal-body">
                <input type="hidden" name="id_appointment" id="id_appointment">

                <div class="form-group">
                    <label>Pasien *</label>
                    <select name="id_patient" id="id_patient" class="form-control" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?= $patient['id_patient'] ?>">
                                <?= htmlspecialchars($patient['name']) ?>
                                (<?= htmlspecialchars($patient['med_record_no']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Dokter *</label>
                    <select name="id_doctor" id="id_doctor" class="form-control" required>
                        <option value="">-- Pilih Dokter --</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['id_doctor'] ?>">
                                <?= htmlspecialchars($doctor['name']) ?> -
                                <?= htmlspecialchars($doctor['specialization']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal *</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Waktu *</label>
                    <input type="time" name="time" id="time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>No. Antrian *</label>
                    <input type="number" name="queue_number" id="queue_number" class="form-control" required min="1"
                        placeholder="1">
                </div>

                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Done">Done</option>
                        <option value="Canceled">Canceled</option>
                    </select>
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
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Appointment';
        document.getElementById('id_appointment').value = '';
        document.getElementById('id_patient').value = '';
        document.getElementById('id_doctor').value = '';
        document.getElementById('date').value = '';
        document.getElementById('time').value = '';
        document.getElementById('queue_number').value = '';
        document.getElementById('status').value = 'Scheduled';
        document.getElementById('appointmentModal').style.display = 'block';
    }

    function editAppointment(appointment) {
        document.getElementById('modalTitle').textContent = 'Edit Appointment';
        document.getElementById('id_appointment').value = appointment.id_appointment;
        document.getElementById('id_patient').value = appointment.id_patient;
        document.getElementById('id_doctor').value = appointment.id_doctor;
        document.getElementById('date').value = appointment.date;
        document.getElementById('time').value = appointment.time;
        document.getElementById('queue_number').value = appointment.queue_number;
        document.getElementById('status').value = appointment.status;
        document.getElementById('appointmentModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('appointmentModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('appointmentModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<?php include 'includes/footer.php'; ?>