<?php
// Global Config
include '../includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Edit Reservasi - Taman Cerdas';

include '../includes/database.php';

if (!$isLoggedIn) {
    header("Location: " . $base_path . "login.php");
    exit();
}

$reservation_id = $_GET['id'] ?? 0;
if (!filter_var($reservation_id, FILTER_VALIDATE_INT) || $reservation_id <= 0) {
    header("Location: " . $base_path . "user/index.php");
    exit();
}

$username = $_SESSION['username'];
$userName = $_SESSION['nama'];
$sql = "SELECT * FROM reservasi WHERE id = ? AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $reservation_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: " . $base_path . "user/index.php");
    exit();
}

$reservation = $result->fetch_assoc();
$stmt->close();

// Hanya bisa edit jika status masih pending
if ($reservation['status'] !== 'pending') {
    header("Location: " . $base_path . "user/index.php");
    exit();
}

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
csrf_validate_or_abort($base_path . "user/index.php?status=csrf_error");

    $new_startDate = $_POST['startDate'];
    $new_endDate = $_POST['endDate'];
    $new_pendopo = in_array('pendopo', $_POST['fasilitas'] ?? []) ? 1 : 0;
    $new_ruangBaca = in_array('ruang_baca', $_POST['fasilitas'] ?? []) ? 1 : 0;
    $new_tamanBermain = in_array('taman_bermain', $_POST['fasilitas'] ?? []) ? 1 : 0;

    // Validasi minimal 1 fasilitas
    if (!$new_pendopo && !$new_ruangBaca && !$new_tamanBermain) {
        $error_message = "Pilih minimal satu fasilitas.";
    } else {
        // Hitung total bayar baru
        $start = new DateTime($new_startDate);
        $end = new DateTime($new_endDate);
        $days = max(1, $end->diff($start)->days + 1);
        $new_total = ($new_pendopo * 50000 + $new_ruangBaca * 60000 + $new_tamanBermain * 45000) * $days;

        $update_sql = "UPDATE reservasi SET tanggal_mulai = ?, tanggal_selesai = ?, pendopo = ?, ruang_baca = ?, taman_bermain = ?, total_bayar = ? WHERE id = ? AND username = ? AND status = 'pending'";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssiiiiis", $new_startDate, $new_endDate, $new_pendopo, $new_ruangBaca, $new_tamanBermain, $new_total, $reservation_id, $username);

        if ($update_stmt->execute()) {
            header("Location: " . $base_path . "user/index.php?status=edit_success");
            exit();
        } else {
            $error_message = "Gagal memperbarui reservasi. Silakan coba lagi.";
        }
        $update_stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/components/user-dropdown.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/reservasi.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<main>
    <!-- Page Header -->
    <section class="reservasi-header">
        <div class="container">
            <div class="reservasi-header-content">
                <span class="page-badge">Edit</span>
                <h1>Edit Reservasi #<?php echo $reservation_id; ?></h1>
                <p>Perbarui data reservasi Anda sebelum diproses admin.</p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="<?php echo $base_path; ?>user/index.php">Profil</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Edit Reservasi</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="reservasi-form-section">
        <div class="container">
            <div class="reservasi-layout">
                <!-- Main Form -->
                <div class="reservasi-main">
                    <div class="reservasi-card">
                        <div class="reservasi-card-header">
                            <div class="card-header-icon">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h2>Edit Data Reservasi</h2>
                                <p>Ubah tanggal atau fasilitas yang ingin disewa</p>
                            </div>
                        </div>

                        <?php if (!empty($error_message)): ?>
                        <div style="padding: 0 32px;">
                            <div class="edit-alert error">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span><?php echo $error_message; ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form action="edit_reservation.php?id=<?php echo $reservation_id; ?>" method="POST" class="reservasi-form" id="editForm">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <!-- Step 1: Data Penyewa (readonly) -->
                            <div class="form-section">
                                <div class="form-section-label">
                                    <span class="step-indicator">1</span>
                                    <span>Data Penyewa</span>
                                </div>
                                <div class="form-group">
                                    <label for="name">
                                        <i class="bi bi-person"></i>
                                        Nama Penyewa
                                    </label>
                                    <input type="text" class="form-input" id="name" value="<?php echo htmlspecialchars($reservation['nama_penyewa']); ?>" readonly style="opacity: 0.7; cursor: not-allowed;">
                                </div>
                            </div>

                            <!-- Step 2: Tanggal -->
                            <div class="form-section">
                                <div class="form-section-label">
                                    <span class="step-indicator">2</span>
                                    <span>Ubah Tanggal</span>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="startDate">
                                            <i class="bi bi-calendar-event"></i>
                                            Tanggal Mulai
                                        </label>
                                        <input type="date" class="form-input" id="startDate" name="startDate" value="<?php echo $reservation['tanggal_mulai']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="endDate">
                                            <i class="bi bi-calendar-event"></i>
                                            Tanggal Selesai
                                        </label>
                                        <input type="date" class="form-input" id="endDate" name="endDate" value="<?php echo $reservation['tanggal_selesai']; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Fasilitas -->
                            <div class="form-section">
                                <div class="form-section-label">
                                    <span class="step-indicator">3</span>
                                    <span>Ubah Fasilitas</span>
                                </div>
                                <div class="facility-options">
                                    <label class="facility-option">
                                        <input type="checkbox" name="fasilitas[]" value="pendopo" <?php echo $reservation['pendopo'] ? 'checked' : ''; ?>>
                                        <div class="facility-option-card">
                                            <div class="facility-option-icon">
                                                <i class="bi bi-house-door"></i>
                                            </div>
                                            <div class="facility-option-info">
                                                <strong>Pendopo</strong>
                                                <span>Max 70 orang</span>
                                            </div>
                                            <div class="facility-option-price">Rp 50.000/hari</div>
                                            <div class="facility-check">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="facility-option">
                                        <input type="checkbox" name="fasilitas[]" value="ruang_baca" <?php echo $reservation['ruang_baca'] ? 'checked' : ''; ?>>
                                        <div class="facility-option-card">
                                            <div class="facility-option-icon">
                                                <i class="bi bi-book"></i>
                                            </div>
                                            <div class="facility-option-info">
                                                <strong>Ruang Baca</strong>
                                                <span>Max 35 orang</span>
                                            </div>
                                            <div class="facility-option-price">Rp 60.000/hari</div>
                                            <div class="facility-check">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="facility-option">
                                        <input type="checkbox" name="fasilitas[]" value="taman_bermain" <?php echo $reservation['taman_bermain'] ? 'checked' : ''; ?>>
                                        <div class="facility-option-card">
                                            <div class="facility-option-icon">
                                                <i class="bi bi-dribbble"></i>
                                            </div>
                                            <div class="facility-option-info">
                                                <strong>Taman Bermain</strong>
                                                <span>Max 15 anak</span>
                                            </div>
                                            <div class="facility-option-price">Rp 45.000/hari</div>
                                            <div class="facility-check">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="edit-actions">
                                <button type="submit" class="btn-submit-reservasi" id="btnSave">
                                    <i class="bi bi-check-circle"></i>
                                    Simpan Perubahan
                                </button>
                                <a href="<?php echo $base_path; ?>user/index.php" class="btn-cancel-edit">
                                    <i class="bi bi-x-lg"></i>
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="reservasi-sidebar">
                    <!-- Live Summary -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-receipt"></i>
                            <h3>Ringkasan</h3>
                        </div>
                        <div class="edit-summary">
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">Penyewa</span>
                                <span class="edit-summary-value"><?php echo htmlspecialchars($reservation['nama_penyewa']); ?></span>
                            </div>
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">Periode</span>
                                <span class="edit-summary-value" id="summaryPeriod">-</span>
                            </div>
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">Durasi</span>
                                <span class="edit-summary-value" id="summaryDays">-</span>
                            </div>
                            <div class="edit-summary-divider"></div>
                            <div class="edit-summary-item" id="summaryFacilities">
                                <span class="edit-summary-label">Fasilitas</span>
                                <span class="edit-summary-value">-</span>
                            </div>
                            <div class="edit-summary-divider"></div>
                            <div class="edit-summary-item total">
                                <span class="edit-summary-label">Total Biaya</span>
                                <span class="edit-summary-value" id="summaryTotal">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-info-circle"></i>
                            <h3>Status Reservasi</h3>
                        </div>
                        <div class="edit-status-info">
                            <div class="edit-status-badge pending">
                                <i class="bi bi-hourglass-split"></i>
                                <span>Menunggu Persetujuan</span>
                            </div>
                            <p class="edit-status-note">Reservasi masih bisa diubah selama status masih pending.</p>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="sidebar-card user-card">
                        <div class="user-card-content">
                            <div class="user-card-avatar">
                                <?php echo strtoupper(substr($userName, 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?php echo htmlspecialchars($userName); ?></strong>
                                <span>@<?php echo htmlspecialchars($username); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const checkboxes = document.querySelectorAll('input[name="fasilitas[]"]');

    const prices = {
        'pendopo': 50000,
        'ruang_baca': 60000,
        'taman_bermain': 45000
    };

    const facilityNames = {
        'pendopo': 'Pendopo',
        'ruang_baca': 'Ruang Baca',
        'taman_bermain': 'Taman Bermain'
    };

    // Set minimum date
    const today = new Date().toISOString().split('T')[0];
    startDate.setAttribute('min', today);
    endDate.setAttribute('min', today);

    startDate.addEventListener('change', function() {
        if (endDate.value && endDate.value < startDate.value) {
            endDate.value = startDate.value;
        }
        endDate.setAttribute('min', startDate.value);
        updateSummary();
    });

    endDate.addEventListener('change', updateSummary);
    checkboxes.forEach(cb => cb.addEventListener('change', updateSummary));

    function formatDate(dateStr) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const d = new Date(dateStr);
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function updateSummary() {
        // Period & days
        if (startDate.value && endDate.value) {
            document.getElementById('summaryPeriod').textContent = formatDate(startDate.value) + ' - ' + formatDate(endDate.value);
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const days = Math.max(1, Math.round((end - start) / (1000 * 60 * 60 * 24)) + 1);
            document.getElementById('summaryDays').textContent = days + ' hari';

            // Facilities & total
            let selectedFacilities = [];
            let dailyTotal = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selectedFacilities.push(facilityNames[cb.value]);
                    dailyTotal += prices[cb.value];
                }
            });

            const facilitiesEl = document.getElementById('summaryFacilities');
            if (selectedFacilities.length > 0) {
                facilitiesEl.innerHTML = '<span class="edit-summary-label">Fasilitas</span><span class="edit-summary-value">' + selectedFacilities.join(', ') + '</span>';
            } else {
                facilitiesEl.innerHTML = '<span class="edit-summary-label">Fasilitas</span><span class="edit-summary-value" style="color:#dc2626;">Belum dipilih</span>';
            }

            const total = dailyTotal * days;
            document.getElementById('summaryTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    // Initial update
    updateSummary();
});
</script>

</body>
</html>
