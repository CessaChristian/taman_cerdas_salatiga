<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Upload Bukti Pembayaran - Taman Cerdas';

include '../includes/database.php';
include '../includes/auto_cancel.php';

if (!$isLoggedIn) {
    header("Location: " . $base_path . "login.php");
    exit();
}

$reservation_id = $_GET['id'] ?? null;
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

// Hanya bisa upload jika pending dan belum ada bukti
if ($reservation['status'] !== 'pending' || !empty($reservation['bukti_transfer'])) {
    header("Location: " . $base_path . "user/index.php");
    exit();
}

// Cek deadline 24 jam
$created = new DateTime($reservation['created_at']);
$deadline = clone $created;
$deadline->modify('+24 hours');
$now = new DateTime();
$is_expired = $now > $deadline;

if ($is_expired) {
    header("Location: " . $base_path . "user/index.php");
    exit();
}

$remaining = $now->diff($deadline);

$upload_message = '';
$upload_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["bukti_transfer"])) {
    $file = $_FILES["bukti_transfer"];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $upload_message = "Gagal mengupload file. Silakan coba lagi.";
        $upload_type = "error";
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file['type'], $allowed_types) || !in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $upload_message = "Format file harus JPG, JPEG, atau PNG.";
            $upload_type = "error";
        } elseif ($file['size'] > $max_size) {
            $upload_message = "Ukuran file maksimal 2MB.";
            $upload_type = "error";
        } else {
            $safe_username = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
            $new_filename = $reservation_id . '-' . $safe_username . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $update_sql = "UPDATE reservasi SET bukti_transfer = ? WHERE id = ? AND username = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sis", $new_filename, $reservation_id, $username);
                $update_stmt->execute();
                $update_stmt->close();

                header("Location: " . $base_path . "user/index.php?status=payment_uploaded");
                exit();
            } else {
                $upload_message = "Gagal menyimpan file. Silakan coba lagi.";
                $upload_type = "error";
            }
        }
    }
}

// Get facilities
$facilities = [];
if ($reservation['pendopo']) $facilities[] = 'Pendopo';
if ($reservation['ruang_baca']) $facilities[] = 'Ruang Baca';
if ($reservation['taman_bermain']) $facilities[] = 'Taman Bermain';

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
                <span class="page-badge">Pembayaran</span>
                <h1>Upload Bukti Transfer</h1>
                <p>Kirim bukti pembayaran untuk reservasi #<?php echo $reservation_id; ?></p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="<?php echo $base_path; ?>user/index.php">Profil</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Upload Bukti</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="reservasi-form-section">
        <div class="container">
            <div class="reservasi-layout">
                <!-- Main Content -->
                <div class="reservasi-main">
                    <div class="reservasi-card">
                        <div class="reservasi-card-header">
                            <div class="card-header-icon">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <div>
                                <h2>Pembayaran Reservasi</h2>
                                <p>Transfer dan upload bukti pembayaran Anda</p>
                            </div>
                        </div>

                        <!-- Payment Section (reuse styles from reservasi.css) -->
                        <div class="payment-section" style="display: block;">
                            <!-- Deadline Warning -->
                            <div class="upload-deadline-banner">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <strong>Batas upload: <?php echo $remaining->h; ?> jam <?php echo $remaining->i; ?> menit lagi</strong>
                                    <span>Reservasi otomatis batal jika bukti tidak dikirim tepat waktu.</span>
                                </div>
                            </div>

                            <?php if (!empty($upload_message)): ?>
                            <div class="edit-alert <?php echo $upload_type; ?>" style="margin-bottom: 20px;">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span><?php echo $upload_message; ?></span>
                            </div>
                            <?php endif; ?>

                            <!-- Payment Tabs -->
                            <div class="payment-tabs">
                                <button type="button" class="payment-tab active" data-tab="transfer">
                                    <i class="bi bi-bank"></i> Transfer Bank
                                </button>
                                <button type="button" class="payment-tab" data-tab="qris">
                                    <i class="bi bi-qr-code"></i> QRIS
                                </button>
                            </div>

                            <!-- Tab Content: Transfer -->
                            <div class="payment-tab-content active" id="tab-transfer">
                                <div class="bank-info-card">
                                    <div class="bank-logo">
                                        <span>BCA</span>
                                    </div>
                                    <div class="bank-details">
                                        <span class="bank-label">Nomor Rekening</span>
                                        <div class="bank-account-row">
                                            <span class="bank-account-number" id="accountNumber">1234567890</span>
                                            <button type="button" class="btn-copy" onclick="copyAccountNumber()" title="Salin">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                        <span class="bank-account-name">a/n Taman Cerdas Salatiga</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Content: QRIS -->
                            <div class="payment-tab-content" id="tab-qris">
                                <div class="qris-display">
                                    <div class="qris-placeholder">
                                        <i class="bi bi-qr-code-scan"></i>
                                        <p>Scan QRIS untuk pembayaran</p>
                                        <span class="qris-note">Gunakan aplikasi e-wallet atau mobile banking Anda</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Form -->
                            <form action="upload_payment.php?id=<?php echo $reservation_id; ?>" method="POST" enctype="multipart/form-data" id="uploadForm">
                                <div class="upload-section">
                                    <label class="upload-label">
                                        <i class="bi bi-image"></i> Upload Bukti Transfer
                                    </label>
                                    <div class="upload-dropzone" id="dropzone">
                                        <input type="file" name="bukti_transfer" id="buktiFile" accept="image/jpeg,image/png,image/jpg" required hidden>
                                        <div class="dropzone-content" id="dropzoneContent">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                            <p>Drag & drop atau <span class="dropzone-link">pilih file</span></p>
                                            <span class="dropzone-hint">JPG, JPEG, PNG (maks. 2MB)</span>
                                        </div>
                                        <div class="upload-preview" id="uploadPreview" style="display: none;">
                                            <img id="previewImg" src="" alt="Preview">
                                            <button type="button" class="btn-remove-preview" onclick="removePreview()">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="payment-actions">
                                    <button type="submit" class="btn-submit-payment" id="btnSubmitPayment" disabled>
                                        <i class="bi bi-check-circle"></i>
                                        Kirim Bukti Pembayaran
                                    </button>
                                    <a href="<?php echo $base_path; ?>user/index.php" class="btn-back-confirm" style="text-decoration: none; text-align: center;">
                                        <i class="bi bi-arrow-left"></i>
                                        Kembali ke Profil
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="reservasi-sidebar">
                    <!-- Reservation Summary -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-receipt"></i>
                            <h3>Detail Reservasi</h3>
                        </div>
                        <div class="edit-summary">
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">ID Reservasi</span>
                                <span class="edit-summary-value">#<?php echo $reservation_id; ?></span>
                            </div>
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">Penyewa</span>
                                <span class="edit-summary-value"><?php echo htmlspecialchars($reservation['nama_penyewa']); ?></span>
                            </div>
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">Periode</span>
                                <span class="edit-summary-value"><?php echo date('d M Y', strtotime($reservation['tanggal_mulai'])); ?> - <?php echo date('d M Y', strtotime($reservation['tanggal_selesai'])); ?></span>
                            </div>
                            <div class="edit-summary-item">
                                <span class="edit-summary-label">Fasilitas</span>
                                <span class="edit-summary-value"><?php echo implode(', ', $facilities); ?></span>
                            </div>
                            <div class="edit-summary-divider"></div>
                            <div class="edit-summary-item total">
                                <span class="edit-summary-label">Total Bayar</span>
                                <span class="edit-summary-value">Rp <?php echo number_format($reservation['total_bayar'], 0, ',', '.'); ?></span>
                            </div>
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
// Payment tabs
document.querySelectorAll('.payment-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.payment-tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

// Dropzone
const dropzone = document.getElementById('dropzone');
const buktiFile = document.getElementById('buktiFile');
const previewImg = document.getElementById('previewImg');
const uploadPreview = document.getElementById('uploadPreview');
const dropzoneContent = document.getElementById('dropzoneContent');
const btnSubmitPayment = document.getElementById('btnSubmitPayment');

dropzone.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-preview')) return;
    buktiFile.click();
});

dropzone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('dragover');
});
dropzone.addEventListener('dragleave', function() {
    this.classList.remove('dragover');
});
dropzone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('dragover');
    if (e.dataTransfer.files.length) {
        buktiFile.files = e.dataTransfer.files;
        handleFileSelect(e.dataTransfer.files[0]);
    }
});

buktiFile.addEventListener('change', function() {
    if (this.files.length) handleFileSelect(this.files[0]);
});

function handleFileSelect(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!validTypes.includes(file.type)) {
        alert('Format file harus JPG, JPEG, atau PNG.');
        buktiFile.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file maksimal 2MB.');
        buktiFile.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        uploadPreview.style.display = 'block';
        dropzoneContent.style.display = 'none';
        btnSubmitPayment.disabled = false;
    };
    reader.readAsDataURL(file);
}

function removePreview() {
    buktiFile.value = '';
    uploadPreview.style.display = 'none';
    dropzoneContent.style.display = 'flex';
    btnSubmitPayment.disabled = true;
}

function copyAccountNumber() {
    const num = document.getElementById('accountNumber').textContent;
    navigator.clipboard.writeText(num).then(() => {
        const btn = document.querySelector('.btn-copy');
        btn.innerHTML = '<i class="bi bi-check-lg"></i>';
        btn.classList.add('copied');
        setTimeout(() => {
            btn.innerHTML = '<i class="bi bi-clipboard"></i>';
            btn.classList.remove('copied');
        }, 2000);
    });
}

document.getElementById('uploadForm').addEventListener('submit', function() {
    btnSubmitPayment.disabled = true;
    btnSubmitPayment.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Mengupload...';
});
</script>

</body>
</html>
