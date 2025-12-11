<?php
// Include check admin dan database config (session_start already in check_admin.php)
require_once 'includes/check_admin.php';
require_once '../config/db.php';

// Set current page untuk highlight menu
$current_page = 'dashboard';
$page_title = 'Dashboard Overview';

// Database connection
$conn = connect_db();

// Query statistik
$stats = [];

// Total Pasien
$query_patients = "SELECT COUNT(*) as total FROM patients";
$result = mysqli_query($conn, $query_patients);
$stats['total_patients'] = mysqli_fetch_assoc($result)['total'];

// Total Dokter
$query_doctors = "SELECT COUNT(*) as total FROM doctors";
$result = mysqli_query($conn, $query_doctors);
$stats['total_doctors'] = mysqli_fetch_assoc($result)['total'];

// Janji Temu Hari Ini
$today = date('Y-m-d');
$query_today = "SELECT COUNT(*) as total FROM appointments WHERE date = '$today'";
$result = mysqli_query($conn, $query_today);
$stats['today_appointments'] = mysqli_fetch_assoc($result)['total'];

// Antrian Aktif (status Scheduled atau In Progress)
$query_queue = "SELECT COUNT(*) as total FROM appointments WHERE status IN ('Scheduled', 'In Progress') AND date = '$today'";
$result = mysqli_query($conn, $query_queue);
$stats['active_queue'] = mysqli_fetch_assoc($result)['total'];

// Query appointment terakhir dengan JOIN
$query_recent = "
    SELECT 
        a.id_appointment,
        a.date,
        a.time,
        a.status,
        a.queue_number,
        p.name as patient_name,
        d.name as doctor_name,
        d.specialization
    FROM appointments a
    JOIN patients p ON a.id_patient = p.id_patient
    JOIN doctors d ON a.id_doctor = d.id_doctor
    ORDER BY a.date DESC, a.time DESC
    LIMIT 5
";
$recent_appointments = mysqli_query($conn, $query_recent);

// Query distribusi status appointment hari ini
$query_status = "
    SELECT 
        status,
        COUNT(*) as count
    FROM appointments
    WHERE date = '$today'
    GROUP BY status
";
$status_distribution = mysqli_query($conn, $query_status);

// Data untuk grafik appointment - Hari Ini, Minggu Ini, Bulan Ini
$chart_data = [];

// Hari ini
$query_today_chart = "SELECT COUNT(*) as total FROM appointments WHERE date = '$today'";
$result = mysqli_query($conn, $query_today_chart);
$chart_data['today'] = mysqli_fetch_assoc($result)['total'];

// Minggu ini (7 hari terakhir)
$week_ago = date('Y-m-d', strtotime('-7 days'));
$query_week = "SELECT COUNT(*) as total FROM appointments WHERE date BETWEEN '$week_ago' AND '$today'";
$result = mysqli_query($conn, $query_week);
$chart_data['week'] = mysqli_fetch_assoc($result)['total'];

// Bulan ini
$month_start = date('Y-m-01');
$query_month = "SELECT COUNT(*) as total FROM appointments WHERE date BETWEEN '$month_start' AND '$today'";
$result = mysqli_query($conn, $query_month);
$chart_data['month'] = mysqli_fetch_assoc($result)['total'];

// Data untuk grafik per status (semua waktu)
$query_status_all = "
    SELECT 
        status,
        COUNT(*) as count
    FROM appointments
    GROUP BY status
";
$status_all = mysqli_query($conn, $query_status_all);
$status_data = [];
while ($row = mysqli_fetch_assoc($status_all)) {
    $status_data[$row['status']] = $row['count'];
}

// Include header
include 'includes/header.php';
?>

<!-- Dashboard Stats Grid -->
<div class="stats-grid">
    <!-- Card 1: Total Pasien -->
    <div class="stat-card">
        <div class="stat-icon primary">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path
                    d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21"
                    stroke="currentColor" stroke-width="2" />
                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" />
            </svg>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_patients']); ?></h3>
            <p>Total Pasien</p>
        </div>
    </div>

    <!-- Card 2: Total Dokter -->
    <div class="stat-card">
        <div class="stat-icon success">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21" stroke="currentColor"
                    stroke-width="2" />
                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                <path
                    d="M23 21V19C23 17.1 21.9 15.5 20.4 15.1M16 3.1C17.5 3.5 18.6 5.1 18.6 7C18.6 8.9 17.5 10.5 16 10.9"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['total_doctors']); ?></h3>
            <p>Total Dokter</p>
        </div>
    </div>

    <!-- Card 3: Appointment Hari Ini -->
    <div class="stat-card">
        <div class="stat-icon warning">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="18" rx="3" stroke="currentColor" stroke-width="2" />
                <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                <circle cx="12" cy="15" r="2" fill="currentColor" />
            </svg>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['today_appointments']); ?></h3>
            <p>Janji Temu Hari Ini</p>
        </div>
    </div>

    <!-- Card 4: Antrian Aktif -->
    <div class="stat-card">
        <div class="stat-icon danger">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
            </svg>
        </div>
        <div class="stat-info">
            <h3><?php echo number_format($stats['active_queue']); ?></h3>
            <p>Antrian Aktif</p>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid mt-4">
    <!-- Grafik Appointment per Periode -->
    <div class="card">
        <div class="card-header">
            <h2>Statistik Appointment</h2>
        </div>
        <div class="card-body">
            <canvas id="appointmentChart" style="max-height: 300px;"></canvas>
        </div>
    </div>

    <!-- Grafik Status Appointment -->
    <div class="card">
        <div class="card-header">
            <h2>Distribusi Status</h2>
        </div>
        <div class="card-body">
            <canvas id="statusChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="dashboard-grid">
    <!-- Recent Appointments Card -->
    <div class="card">
        <div class="card-header">
            <h2>Janji Temu Terbaru</h2>
            <a href="appointments.php" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Spesialisasi</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($recent_appointments) > 0):
                            while ($appointment = mysqli_fetch_assoc($recent_appointments)):
                                // Determine badge class based on status (sesuai enum database)
                                $badge_class = '';
                                switch ($appointment['status']) {
                                    case 'Done':
                                        $badge_class = 'badge-success';
                                        break;
                                    case 'Scheduled':
                                        $badge_class = 'badge-primary';
                                        break;
                                    case 'In Progress':
                                        $badge_class = 'badge-warning';
                                        break;
                                    case 'Canceled':
                                        $badge_class = 'badge-danger';
                                        break;
                                    default:
                                        $badge_class = 'badge-primary';
                                }
                                ?>
                                <tr>
                                    <td><strong>#<?php echo htmlspecialchars($appointment['queue_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['specialization']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($appointment['date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($appointment['time'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $badge_class; ?>">
                                            <?php echo ucfirst($appointment['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px;">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none"
                                        style="opacity: 0.3; margin: 0 auto 16px;">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                                        <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                    <p style="color: #6B7280; margin: 0;">Belum ada data janji temu</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Cards -->
    <div class="sidebar-cards">
        <!-- Status Distribution Card -->
        <div class="card">
            <div class="card-header">
                <h2>Status Hari Ini</h2>
            </div>
            <div class="card-body">
                <div class="status-list">
                    <?php
                    $status_data = [];
                    $total_status = 0;
                    while ($status = mysqli_fetch_assoc($status_distribution)) {
                        $status_data[] = $status;
                        $total_status += $status['count'];
                    }

                    if (count($status_data) > 0):
                        foreach ($status_data as $status):
                            $percentage = ($total_status > 0) ? round(($status['count'] / $total_status) * 100) : 0;
                            $badge_class = '';
                            switch ($status['status']) {
                                case 'confirmed':
                                    $badge_class = 'badge-success';
                                    break;
                                case 'pending':
                                    $badge_class = 'badge-warning';
                                    break;
                                case 'cancelled':
                                    $badge_class = 'badge-danger';
                                    break;
                                default:
                                    $badge_class = 'badge-primary';
                            }
                            ?>
                            <div class="status-item">
                                <div class="status-info">
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($status['status']); ?>
                                    </span>
                                    <span class="status-count"><?php echo $status['count']; ?></span>
                                </div>
                                <div class="status-bar">
                                    <div class="status-bar-fill <?php echo $badge_class; ?>"
                                        style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <p style="text-align: center; color: #6B7280; padding: 20px;">Tidak ada data untuk hari ini</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h2>Quick Actions</h2>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="patients.php" class="action-card">
                        <div class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C4.46957 15 3.96086 15.4214 3.58579 16.1716C3.21071 16.9217 3 17.9391 3 19V21"
                                    stroke="currentColor" stroke-width="2" />
                                <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                                <path d="M20 8V14M23 11H17" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <h4>Tambah Pasien</h4>
                    </a>

                    <a href="doctors.php" class="action-card">
                        <div class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M17 21V19C17 16.7909 15.2091 15 13 15H5C2.79086 15 1 16.7909 1 19V21"
                                    stroke="currentColor" stroke-width="2" />
                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2" />
                                <path d="M23 11H17M20 8V14" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <h4>Tambah Dokter</h4>
                    </a>

                    <a href="appointments.php" class="action-card">
                        <div class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="3" stroke="currentColor"
                                    stroke-width="2" />
                                <path d="M16 2V6M8 2V6M3 10H21" stroke="currentColor" stroke-width="2" />
                                <path d="M12 14H12.01M16 14H16.01M8 14H8.01M12 18H12.01M16 18H16.01M8 18H8.01"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <h4>Buat Appointment</h4>
                    </a>

                    <a href="medical_records.php" class="action-card">
                        <div class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z"
                                    stroke="currentColor" stroke-width="2" />
                                <path d="M14 2V8H20M12 11V17M9 14H15" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </div>
                        <h4>Lihat Rekam Medis</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Data dari PHP
    const chartData = {
        today: <?php echo $chart_data['today']; ?>,
        week: <?php echo $chart_data['week']; ?>,
        month: <?php echo $chart_data['month']; ?>
    };

    const statusData = {
        scheduled: <?php echo $status_data['Scheduled'] ?? 0; ?>,
        inProgress: <?php echo $status_data['In Progress'] ?? 0; ?>,
        done: <?php echo $status_data['Done'] ?? 0; ?>,
        canceled: <?php echo $status_data['Canceled'] ?? 0; ?>
    };

    // Konfigurasi warna dari color palette
    const colors = {
        primary: '#2F4156',
        secondary: '#567C8D',
        cream: '#F5EFEB',
        lightBlue: '#C8D9E6',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        info: '#3B82F6'
    };

    // Chart 1: Appointment per Periode (Bar Chart)
    const ctxAppointment = document.getElementById('appointmentChart').getContext('2d');
    const appointmentChart = new Chart(ctxAppointment, {
        type: 'bar',
        data: {
            labels: ['Hari Ini', 'Minggu Ini (7 hari)', 'Bulan Ini'],
            datasets: [{
                label: 'Jumlah Appointment',
                data: [chartData.today, chartData.week, chartData.month],
                backgroundColor: [
                    colors.primary,
                    colors.secondary,
                    colors.lightBlue
                ],
                borderColor: [
                    colors.primary,
                    colors.secondary,
                    colors.secondary
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: colors.primary,
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: colors.lightBlue,
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: colors.cream
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Chart 2: Status Distribution (Doughnut Chart)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Scheduled', 'In Progress', 'Done', 'Canceled'],
            datasets: [{
                label: 'Jumlah',
                data: [statusData.scheduled, statusData.inProgress, statusData.done, statusData.canceled],
                backgroundColor: [
                    colors.info,
                    colors.warning,
                    colors.success,
                    colors.danger
                ],
                borderColor: '#FFFFFF',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: colors.primary,
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: colors.lightBlue,
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.parsed;
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
</script>

<?php
// Close database connection
close_db($conn);

// Include footer
include 'includes/footer.php';
?>