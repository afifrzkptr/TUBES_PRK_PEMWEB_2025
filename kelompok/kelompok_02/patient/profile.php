<?php
require_once 'includes/check_patient.php';

// Get current data
$user_id = $_SESSION['user_id'];
$query = "
    SELECT p.*, u.email, u.username 
    FROM patients p 
    JOIN users u ON p.id_user = u.id_user 
    WHERE p.id_user = $user_id
";
$patient = mysqli_fetch_assoc(mysqli_query($conn, $query));

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

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

            // Update Patient (Name, Phone, Address)
            $stmt_p = $conn->prepare("UPDATE patients SET name = ?, phone = ?, address = ? WHERE id_user = ?");
            $stmt_p->bind_param("sssi", $name, $phone, $address, $user_id);
            $stmt_p->execute();

            $conn->commit();
            $success = "Profil berhasil diperbarui!";

            // Update Session Data
            $_SESSION['email'] = $email;

            // Refresh data
            $patient = mysqli_fetch_assoc(mysqli_query($conn, $query));

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

            <h2 class="text-3xl font-bold text-navytube mb-2">👤 Edit Profil</h2>
            <p class="text-teal mb-8">Perbarui informasi pribadi Anda.</p>

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
                        <input type="text" name="name" value="<?php echo htmlspecialchars($patient['name']); ?>"
                            required
                            class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                    </div>

                    <div>
                        <label class="block font-semibold text-navytube mb-2">No. Rekam Medis (Tidak dapat
                            diubah)</label>
                        <input type="text" value="<?php echo htmlspecialchars($patient['med_record_no']); ?>" disabled
                            class="w-full p-4 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-navytube mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>" required
                        class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                </div>

                <div>
                    <label class="block font-semibold text-navytube mb-2">Nomor Telepon</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required
                        class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal">
                </div>

                <div>
                    <label class="block font-semibold text-navytube mb-2">Alamat Lengkap</label>
                    <textarea name="address" required
                        class="w-full p-4 border border-skyblue rounded-lg focus:outline-none focus:border-teal h-32"><?php echo htmlspecialchars($patient['address']); ?></textarea>
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