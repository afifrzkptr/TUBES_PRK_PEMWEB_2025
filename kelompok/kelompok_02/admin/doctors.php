<?php
require_once 'includes/check_admin.php';
require_once '../config/config.lokal.php';

$conn = connect_db();

$current_page = 'doctors';
$page_title = 'Manajemen Dokter';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id_doctor = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Dokter berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus dokter";
    }
    header("Location: doctors.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_doctor = isset($_POST['id_doctor']) ? (int)$_POST['id_doctor'] : 0;
    $id_user = (int)$_POST['id_user'];
    $name = trim($_POST['name']);
    $specialization = trim($_POST['specialization']);
    $phone = trim($_POST['phone']);
    $license_no = trim($_POST['license_no']);
    
    if ($id_doctor > 0) {
        // Update
        $stmt = $conn->prepare("UPDATE doctors SET id_user=?, name=?, specialization=?, phone=?, license_no=? WHERE id_doctor=?");
        $stmt->bind_param("issssi", $id_user, $name, $specialization, $phone, $license_no, $id_doctor);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO doctors (id_user, name, specialization, phone, license_no) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $id_user, $name, $specialization, $phone, $license_no);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success'] = $id_doctor > 0 ? "Dokter berhasil diupdate" : "Dokter berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menyimpan dokter: " . $conn->error;
    }
    header("Location: doctors.php");
    exit;
}

// Get users with role Dokter
$users_dokter = [];
$result = $conn->query("SELECT u.id_user, u.username FROM users u 
                        INNER JOIN roles r ON u.id_role = r.id_role 
                        WHERE r.role_name = 'Dokter' 
                        ORDER BY u.username");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users_dokter[] = $row;
    }
}

// Get all doctors
$query = "SELECT d.*, u.username 
          FROM doctors d 
          INNER JOIN users u ON d.id_user = u.id_user 
          ORDER BY d.id_doctor ASC";
$result = $conn->query($query);
$doctors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
} else {
    $_SESSION['error'] = "Error loading doctors: " . $conn->error;
}

// Get edit doctor if exists
$edit_doctor = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id_doctor = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_doctor = $stmt->get_result()->fetch_assoc();
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="icon">+</i> Tambah Dokter
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
        <h3>Daftar Dokter</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Dokter</th>
                        <th>Spesialisasi</th>
                        <th>No. Lisensi</th>
                        <th>Phone</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?= htmlspecialchars($doctor['id_doctor']) ?></td>
                        <td><strong><?= htmlspecialchars($doctor['name']) ?></strong></td>
                        <td>
                            <span class="badge badge-primary">
                                <?= htmlspecialchars($doctor['specialization']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($doctor['license_no']) ?></td>
                        <td><?= htmlspecialchars($doctor['phone']) ?></td>
                        <td>
                            <span class="badge badge-success"><?= htmlspecialchars($doctor['username']) ?></span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editDoctor(<?= json_encode($doctor) ?>)'>Edit</button>
                            <a href="?delete=<?= $doctor['id_doctor'] ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus dokter ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="doctorModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Dokter</h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="" id="doctorForm">
            <div class="modal-body">
                <input type="hidden" name="id_doctor" id="id_doctor">
                
                <div class="form-group">
                    <label>User (Dokter) *</label>
                    <select name="id_user" id="id_user" class="form-control" required>
                        <option value="">-- Pilih User --</option>
                        <?php foreach ($users_dokter as $user): ?>
                        <option value="<?= $user['id_user'] ?>">
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="name" id="name" class="form-control" required 
                           placeholder="Dr. Sinta Dewi, Sp.A">
                </div>
                
                <div class="form-group">
                    <label>Spesialisasi</label>
                    <input type="text" name="specialization" id="specialization" class="form-control" 
                           placeholder="Contoh: Kardiologi, Bedah Umum">
                </div>
                
                <div class="form-group">
                    <label>No. Lisensi (STR)</label>
                    <input type="text" name="license_no" id="license_no" class="form-control" 
                           placeholder="Contoh: STR-123456789">
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
function openModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Dokter';
    document.getElementById('id_doctor').value = '';
    document.getElementById('id_user').value = '';
    document.getElementById('name').value = '';
    document.getElementById('specialization').value = '';
    document.getElementById('license_no').value = '';
    document.getElementById('phone').value = '';
    document.getElementById('doctorModal').style.display = 'block';
}

function editDoctor(doctor) {
    document.getElementById('modalTitle').textContent = 'Edit Dokter';
    document.getElementById('id_doctor').value = doctor.id_doctor;
    document.getElementById('id_user').value = doctor.id_user;
    document.getElementById('name').value = doctor.name;
    document.getElementById('specialization').value = doctor.specialization || '';
    document.getElementById('license_no').value = doctor.license_no || '';
    document.getElementById('phone').value = doctor.phone || '';
    document.getElementById('doctorModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('doctorModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('doctorModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

<?php include 'includes/footer.php'; ?>
