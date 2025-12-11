<?php
require_once 'includes/check_admin.php';
require_once __DIR__ . '/../config/db.php';

$conn = connect_db();

$current_page = 'users';
$page_title = 'Manajemen Users';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "User berhasil dihapus";
    } else {
        $_SESSION['error'] = "Gagal menghapus user";
    }
    header("Location: users.php");
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = isset($_POST['id_user']) ? (int) $_POST['id_user'] : 0;
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $id_role = (int) $_POST['id_role'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id_user > 0) {
        // Update
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, id_role=?, is_active=? WHERE id_user=?");
            $stmt->bind_param("sssiii", $username, $email, $hashed_password, $id_role, $is_active, $id_user);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, id_role=?, is_active=? WHERE id_user=?");
            $stmt->bind_param("ssiii", $username, $email, $id_role, $is_active, $id_user);
        }
    } else {
        // Insert
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, id_role, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $username, $email, $hashed_password, $id_role, $is_active);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $id_user > 0 ? "User berhasil diupdate" : "User berhasil ditambahkan";
    } else {
        $_SESSION['error'] = "Gagal menyimpan user: " . $conn->error;
    }
    header("Location: users.php");
    exit;
}

// Get all roles
$roles = [];
$result = $conn->query("SELECT * FROM roles ORDER BY id_role");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
}

// Get all users with role names
$query = "SELECT u.*, r.role_name 
          FROM users u 
          LEFT JOIN roles r ON u.id_role = r.id_role 
          ORDER BY u.id_user ASC";
$result = $conn->query($query);
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    $_SESSION['error'] = "Error loading users: " . $conn->error;
}

// Get edit user if exists
$edit_user = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_user = $stmt->get_result()->fetch_assoc();
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <button class="btn btn-primary" onclick="openModal()">
        <i class="icon">+</i> Tambah User
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
        <h3>Daftar Users</h3>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <div class="search-container" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px; align-items: center;">
                <div style="position: relative; flex: 1; max-width: 400px;">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari berdasarkan username, email, atau role..." style="padding-left: 35px;"
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
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php foreach ($users as $user): ?>
                        <tr class="user-row" data-username="<?= strtolower(htmlspecialchars($user['username'])) ?>"
                            data-email="<?= strtolower(htmlspecialchars($user['email'])) ?>"
                            data-role="<?= strtolower(htmlspecialchars($user['role_name'])) ?>">
                            <td><?= htmlspecialchars($user['id_user']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span
                                    class="badge badge-<?= $user['role_name'] === 'Admin' ? 'danger' : ($user['role_name'] === 'Dokter' ? 'primary' : 'success') ?>">
                                    <?= htmlspecialchars($user['role_name']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning"
                                    onclick='editUser(<?= json_encode($user) ?>)'>Edit</button>
                                <a href="?delete=<?= $user['id_user'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah User</h3>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST" action="" id="userForm">
            <div class="modal-body">
                <input type="hidden" name="id_user" id="id_user">

                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password <span id="passwordNote" style="display:none;">(kosongkan jika tidak ingin
                            mengubah)</span></label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>

                <div class="form-group">
                    <label>Role *</label>
                    <select name="id_role" id="id_role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id_role'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_active" id="is_active" style="width: auto; margin: 0;">
                        <span>Active</span>
                    </label>
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
        const userRows = document.querySelectorAll('.user-row');
        const resultCount = document.getElementById('resultCount');
        const totalUsers = userRows.length;

        // Update count on load
        updateResultCount(totalUsers, totalUsers);

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;

            userRows.forEach(function (row) {
                const username = row.getAttribute('data-username');
                const email = row.getAttribute('data-email');
                const role = row.getAttribute('data-role');

                if (username.includes(searchTerm) ||
                    email.includes(searchTerm) ||
                    role.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateResultCount(visibleCount, totalUsers);

            // Show/hide "no results" message
            showNoResults(visibleCount === 0, searchTerm);
        });

        function updateResultCount(visible, total) {
            if (visible === total) {
                resultCount.textContent = `${total} users`;
            } else {
                resultCount.textContent = `${visible} dari ${total} users`;
            }
        }

        function showNoResults(show, searchTerm) {
            const tableBody = document.getElementById('userTableBody');
            let noResultRow = document.getElementById('noResultRow');

            if (show) {
                if (!noResultRow) {
                    noResultRow = document.createElement('tr');
                    noResultRow.id = 'noResultRow';
                    noResultRow.innerHTML = `
                    <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
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
        document.getElementById('modalTitle').textContent = 'Tambah User';
        document.getElementById('id_user').value = '';
        document.getElementById('username').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('id_role').value = '';
        document.getElementById('is_active').checked = true;
        document.getElementById('password').required = true;
        document.getElementById('passwordNote').style.display = 'none';
        document.getElementById('userModal').style.display = 'block';
    }

    function editUser(user) {
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('id_user').value = user.id_user;
        document.getElementById('username').value = user.username;
        document.getElementById('email').value = user.email;
        document.getElementById('password').value = '';
        document.getElementById('id_role').value = user.id_role;
        document.getElementById('is_active').checked = user.is_active == 1;
        document.getElementById('password').required = false;
        document.getElementById('passwordNote').style.display = 'inline';
        document.getElementById('userModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    window.onclick = function (event) {
        const modal = document.getElementById('userModal');
        if (event.target === modal) {
            closeModal();
        }
    }
</script>

<?php include 'includes/footer.php'; ?>