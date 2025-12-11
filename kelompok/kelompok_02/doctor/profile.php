<?php
require_once 'includes/check_doctor.php';

// Get current data
$user_id = $_SESSION['user_id'];
$query = "
    SELECT d.*, u.email, u.username 
    FROM doctors d 
    JOIN users u ON d.id_user = u.id_user 
    WHERE d.id_user = $user_id
";
$doctor = mysqli_fetch_assoc(mysqli_query($conn, $query));

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $spec = trim($_POST['specialization']);
    $license = trim($_POST['license_no']);

    // Check for duplicate email (excluding current user)
    $stmt_check = $conn->prepare("SELECT id_user FROM users WHERE email = ? AND id_user != ?");
    $stmt_check->bind_param("si", $email, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Email sudah digunakan oleh pengguna lain.";
    } else {
        // Begin Transaction
        $conn->begin_transaction();
        try {
            // Update User (Email)
            $stmt_u = $conn->prepare("UPDATE users SET email = ? WHERE id_user = ?");
            $stmt_u->bind_param("si", $email, $user_id);
            $stmt_u->execute();

            // Update Doctor (Name, Phone, Spec, License)
            $stmt_d = $conn->prepare("UPDATE doctors SET name = ?, phone = ?, specialization = ?, license_no = ? WHERE id_user = ?");
            $stmt_d->bind_param("ssssi", $name, $phone, $spec, $license, $user_id);
            $stmt_d->execute();

            $conn->commit();
            $success = "Profil berhasil diperbarui!";

            // Update Session Data
            $_SESSION['email'] = $email;

            // Refresh data
            $doctor = mysqli_fetch_assoc(mysqli_query($conn, $query));

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Gagal memperbarui profil: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="flex-grow">
    <main class="max-w-4xl mx-auto mt-10 px-4 animate-fade-in mb-20">
        <div class="bg-white shadow-xl rounded-2xl p-8 border-t-4 border-teal relative">
            <a href="dashboard.php" class="absolute top-8 right-8 text-teal hover:text-navytube font-medium">✕
                Kembali</a>

            <h2 class="text-3xl font-bold text-navytube mb-2">👤 Edit Profil Dokter</h2>
            <p class="text-teal mb-8">Perbarui informasi praktek Anda.</p>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    <strong class="font-bold">Berhasil!</strong>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <strong class="font-bold">Error!</strong>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold text-navytube mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($doctor['name']); ?>" required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>
                    <div>
                        <label class="block font-semibold text-navytube mb-2">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>"
                            required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold text-navytube mb-2">Spesialisasi</label>
                        <input type="text" name="specialization"
                            value="<?php echo htmlspecialchars($doctor['specialization']); ?>" required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>
                    <div>
                        <label class="block font-semibold text-navytube mb-2">Nomor STR</label>
                        <input type="text" name="license_no"
                            value="<?php echo htmlspecialchars($doctor['license_no']); ?>" required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-navytube mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required
                        class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-navytube text-white py-4 rounded-xl text-lg font-bold hover:bg-teal transition duration-300 shadow-lg">
                        💾 Simpan Perubahan
                    </button>
                    <div class="mt-4 text-center">
                        <a href="dashboard.php" class="text-gray-500 hover:text-navytube text-sm font-medium">Batal &
                            Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
include '../includes/footer.php';
close_db($conn);
?>