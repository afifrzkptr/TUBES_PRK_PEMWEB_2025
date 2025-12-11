<?php
require_once 'includes/check_admin.php';
require_once __DIR__ . '/../config/db.php';

$conn = connect_db();

$current_page = 'medical_records';
$page_title = 'Riwayat Medis Pasien';

// Get patient ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID Pasien tidak valid";
    header("Location: medical_records.php");
    exit;
}

$id_patient = (int) $_GET['id'];

// Get patient info
$stmt = $conn->prepare("SELECT * FROM patients WHERE id_patient = ?");
$stmt->bind_param("i", $id_patient);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
    $_SESSION['error'] = "Pasien tidak ditemukan";
    header("Location: medical_records.php");
    exit;
}

// Get all medical records for this patient
$query = "SELECT mr.*, 
          a.date, a.time, a.queue_number, a.status,
          d.name as doctor_name, d.specialization,
          d.license_no
          FROM medical_records mr 
          INNER JOIN appointments a ON mr.id_appointment = a.id_appointment
          INNER JOIN doctors d ON a.id_doctor = d.id_doctor
          WHERE a.id_patient = ?
          ORDER BY a.date DESC, a.time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_patient);
$stmt->execute();
$records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate statistics
$total_visits = count($records);
$latest_visit = $total_visits > 0 ? $records[0]['date'] : null;
$first_visit = $total_visits > 0 ? $records[$total_visits - 1]['date'] : null;

include 'includes/header.php';
?>

<!-- Load Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .patient-header {
        background: linear-gradient(135deg, #2F4156 0%, #567C8D 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .patient-header h2 {
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 28px;
    }

    .patient-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .info-label {
        font-size: 13px;
        opacity: 0.85;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 18px;
        font-weight: 600;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        text-align: center;
        border: 1px solid #f0f0f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-number {
        font-size: 36px;
        font-weight: 700;
        color: #2F4156;
        margin-bottom: 8px;
    }

    .stat-label {
        color: #666;
        font-size: 14px;
        font-weight: 500;
    }

    .timeline {
        position: relative;
        padding-left: 50px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #2F4156 0%, #567C8D 100%);
        border-radius: 2px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 35px;
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .timeline-item:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -37px;
        top: 24px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: white;
        border: 4px solid #2F4156;
        box-shadow: 0 0 0 4px rgba(47, 65, 86, 0.2);
        z-index: 1;
    }

    .timeline-date {
        font-size: 14px;
        color: #2F4156;
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .timeline-date>span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #C8D9E6;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .timeline-doctor {
        font-size: 14px;
        color: #555;
        margin-bottom: 18px;
        padding: 10px 14px;
        background: #F5EFEB;
        border-radius: 8px;
        border-left: 3px solid #2F4156;
    }

    .timeline-content {
        margin-top: 18px;
    }

    .timeline-section {
        margin-bottom: 18px;
    }

    .timeline-section:last-child {
        margin-bottom: 0;
    }

    .timeline-section-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
    }

    .timeline-section-content {
        padding: 14px 16px;
        background: #F5EFEB;
        border-radius: 8px;
        color: #555;
        line-height: 1.7;
        white-space: pre-wrap;
        border-left: 3px solid #C8D9E6;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 50px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
        font-size: 28px;
        color: #2f4156;
    }

    .page-header>div {
        display: flex;
        gap: 10px;
    }

    .print-button {
        float: right;
    }

    @media print {

        .page-header,
        .print-button,
        .sidebar,
        .btn {
            display: none !important;
        }

        .patient-header {
            background: #2F4156 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .timeline-item {
            page-break-inside: avoid;
        }
    }

    @media (max-width: 768px) {
        .timeline {
            padding-left: 30px;
        }

        .timeline::before {
            left: 10px;
        }

        .timeline-item::before {
            left: -27px;
        }

        .patient-info {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <h1><?= htmlspecialchars($page_title) ?></h1>
    <div>
        <button class="btn btn-secondary" onclick="window.print()">
            <i data-lucide="printer" style="width: 16px; height: 16px; vertical-align: middle; color: #2F4156;"></i>
            Print
        </button>
        <a href="medical_records.php" class="btn btn-secondary">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px; vertical-align: middle; color: #2F4156;"></i>
            Kembali
        </a>
    </div>
</div>

<!-- Patient Header -->
<div class="patient-header">
    <h2 style="margin: 0 0 10px 0; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="user" style="width: 28px; height: 28px; color: white;"></i>
        <?= htmlspecialchars($patient['name']) ?>
    </h2>
    <div class="patient-info">
        <div class="info-item">
            <span class="info-label">No. Rekam Medis</span>
            <span class="info-value"><?= htmlspecialchars($patient['med_record_no']) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Lahir</span>
            <span
                class="info-value"><?= $patient['birth_date'] ? date('d/m/Y', strtotime($patient['birth_date'])) : '-' ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Telepon</span>
            <span class="info-value"><?= htmlspecialchars($patient['phone'] ?: '-') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Alamat</span>
            <span class="info-value"><?= htmlspecialchars($patient['address'] ?: '-') ?></span>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $total_visits ?></div>
        <div class="stat-label">Total Kunjungan</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $latest_visit ? date('d/m/Y', strtotime($latest_visit)) : '-' ?></div>
        <div class="stat-label">Kunjungan Terakhir</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $first_visit ? date('d/m/Y', strtotime($first_visit)) : '-' ?></div>
        <div class="stat-label">Kunjungan Pertama</div>
    </div>
</div>

<!-- Medical Records Timeline -->
<div class="card">
    <div class="card-header">
        <h3 style="display: flex; align-items: center; gap: 10px;">
            <i data-lucide="clipboard-list" style="width: 20px; height: 20px; color: #2F4156;"></i>
            Riwayat Medis Lengkap
        </h3>
    </div>
    <div class="card-body">
        <?php if (empty($records)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>Belum ada riwayat medis untuk pasien ini.</p>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($records as $index => $record): ?>
                    <div class="timeline-item">
                        <div class="timeline-date" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <i data-lucide="calendar" style="width: 14px; height: 14px; color: #2F4156;"></i>
                                <?= date('d F Y', strtotime($record['date'])) ?>
                            </span>
                            <span>•</span>
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <i data-lucide="clock" style="width: 14px; height: 14px; color: #2F4156;"></i>
                                <?= date('H:i', strtotime($record['time'])) ?> WIB
                            </span>
                            <span>•</span>
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <i data-lucide="ticket" style="width: 14px; height: 14px; color: #2F4156;"></i>
                                Antrian #<?= $record['queue_number'] ?>
                            </span>
                        </div>
                        <div class="timeline-doctor" style="display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="stethoscope" style="width: 14px; height: 14px; color: #2F4156;"></i>
                            <strong><?= htmlspecialchars($record['doctor_name']) ?></strong>
                            <?php if ($record['specialization']): ?>
                                • <?= htmlspecialchars($record['specialization']) ?>
                            <?php endif; ?>
                        </div>

                        <div class="timeline-content">
                            <div class="timeline-section">
                                <div class="timeline-section-title">
                                    <i data-lucide="activity" style="width: 16px; height: 16px; color: #2F4156;"></i>
                                    Diagnosis
                                </div>
                                <div class="timeline-section-content">
                                    <?= nl2br(htmlspecialchars($record['diagnosis'])) ?>
                                </div>
                            </div>

                            <?php if (!empty($record['treatment'])): ?>
                                <div class="timeline-section">
                                    <div class="timeline-section-title">
                                        <i data-lucide="pill" style="width: 16px; height: 16px; color: #2F4156;"></i>
                                        Treatment / Tindakan
                                    </div>
                                    <div class="timeline-section-content">
                                        <?= nl2br(htmlspecialchars($record['treatment'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($record['notes'])): ?>
                                <div class="timeline-section">
                                    <div class="timeline-section-title">
                                        <i data-lucide="file-text" style="width: 16px; height: 16px; color: #2F4156;"></i>
                                        Catatan Tambahan
                                    </div>
                                    <div class="timeline-section-content">
                                        <?= nl2br(htmlspecialchars($record['notes'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Initialize Lucide Icons
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>

<?php include 'includes/footer.php'; ?>