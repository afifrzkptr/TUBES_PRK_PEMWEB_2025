<?php
include __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';

$conn = connect_db();
$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
?>

<main class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-navytube mb-6">Daftar Dokter</h1>
    <table class="w-full border-collapse shadow-lg bg-white rounded-lg overflow-hidden">
        <thead class="bg-teal text-white">
            <tr>
                <th class="p-3 text-left">Nama</th>
                <th class="p-3 text-left">Spesialisasi</th>
                <th class="p-3 text-left">No. Telp</th>
                <th class="p-3 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()) { ?>
            <tr class="border-b hover:bg-skyblue/20">
                <td class="p-3"><?= $row['name'] ?></td>
                <td class="p-3"><?= $row['specialization'] ?></td>
                <td class="p-3"><?= $row['phone'] ?></td>
                <td class="p-3">
                    <a href="make_appointment.php?doctor_id=<?= $row['id_doctor'] ?>" 
                        class="bg-navytube text-white px-3 py-1 rounded-md hover:bg-teal transition">
                        Buat Janji Temu
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>