<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Buat Reservasi - Taman Cerdas';

// Page-specific includes
include '../includes/database.php';

if (!$isLoggedIn) {
    echo "<script>window.location.href = '{$base_path}index.php#reservasi-btn-hero';</script>";
    exit();
}

$userName = $_SESSION['nama'];
$username = $_SESSION['username'];
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
                <span class="page-badge">Reservasi</span>
                <h1>Buat Reservasi Baru</h1>
                <p>Lengkapi formulir di bawah untuk memesan fasilitas Taman Cerdas.</p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="<?php echo $base_path; ?>event.php">Event</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Reservasi</span>
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
                                <h2>Formulir Reservasi</h2>
                                <p>Isi data reservasi Anda dengan lengkap</p>
                            </div>
                        </div>

                        <form id="reservasiForm" class="reservasi-form">
                            <!-- Step 1: Data Penyewa -->
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
                                    <input type="text" class="form-input" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>
                                </div>
                            </div>

                            <!-- Step 2: Tanggal -->
                            <div class="form-section">
                                <div class="form-section-label">
                                    <span class="step-indicator">2</span>
                                    <span>Pilih Tanggal</span>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="startDate">
                                            <i class="bi bi-calendar-event"></i>
                                            Tanggal Mulai
                                        </label>
                                        <input type="date" class="form-input" id="startDate" name="startDate" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="endDate">
                                            <i class="bi bi-calendar-event"></i>
                                            Tanggal Selesai
                                        </label>
                                        <input type="date" class="form-input" id="endDate" name="endDate" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Fasilitas -->
                            <div class="form-section">
                                <div class="form-section-label">
                                    <span class="step-indicator">3</span>
                                    <span>Pilih Fasilitas</span>
                                </div>
                                <div class="facility-options">
                                    <label class="facility-option">
                                        <input type="checkbox" name="fasilitas[]" value="pendopo">
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
                                        <input type="checkbox" name="fasilitas[]" value="ruang_baca">
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
                                        <input type="checkbox" name="fasilitas[]" value="taman_bermain">
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

                            <button type="submit" class="btn-submit-reservasi" id="btnSubmit">
                                <i class="bi bi-search"></i>
                                Cek Ketersediaan & Buat Reservasi
                            </button>
                        </form>

                        <!-- Result Area -->
                        <div id="reservasiResult" class="reservasi-result" style="display: none;"></div>

                        <!-- Payment Section (hidden by default) -->
                        <div id="paymentSection" class="payment-section" style="display: none;">
                            <div class="payment-header">
                                <div class="payment-header-icon">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div>
                                    <h3>Pembayaran</h3>
                                    <p>Total: <strong id="paymentTotal">Rp 0</strong></p>
                                </div>
                            </div>

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

                            <!-- Upload Bukti -->
                            <form id="paymentForm" action="process_reservation.php" method="POST" enctype="multipart/form-data">
                                <div class="upload-section">
                                    <label class="upload-label">
                                        <i class="bi bi-image"></i> Upload Bukti Transfer
                                    </label>
                                    <div class="upload-dropzone" id="dropzone">
                                        <input type="file" name="bukti_transfer" id="buktiFile" accept="image/jpeg,image/png,image/jpg" hidden>
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
                                    <div class="payment-actions-row">
                                        <button type="submit" class="btn-skip-payment" id="btnSkipPayment" formnovalidate>
                                            <i class="bi bi-clock"></i>
                                            Lewati, Upload Nanti
                                        </button>
                                        <button type="submit" class="btn-submit-payment" id="btnSubmitPayment" disabled>
                                            <i class="bi bi-check-circle"></i>
                                            Kirim Bukti Pembayaran
                                        </button>
                                    </div>
                                    <p class="payment-skip-note">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Batas upload bukti: <strong>1x24 jam</strong>. Reservasi otomatis batal jika bukti tidak dikirim.
                                    </p>
                                    <button type="button" class="btn-back-confirm" onclick="backToConfirm()">
                                        <i class="bi bi-arrow-left"></i>
                                        Kembali
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="reservasi-sidebar">
                    <!-- Info Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-info-circle"></i>
                            <h3>Informasi Reservasi</h3>
                        </div>
                        <ul class="info-list">
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span>Reservasi akan diproses oleh admin dalam 1x24 jam</span>
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span>Upload bukti pembayaran saat reservasi atau maksimal 1x24 jam setelahnya</span>
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span>Reservasi otomatis batal jika bukti tidak diupload dalam 24 jam</span>
                            </li>
                            <li>
                                <i class="bi bi-check2-circle"></i>
                                <span>Harga sudah termasuk akses toilet dan parkir</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Price Summary -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-tag"></i>
                            <h3>Daftar Harga</h3>
                        </div>
                        <div class="price-list">
                            <div class="price-item">
                                <div class="price-facility">
                                    <i class="bi bi-house-door"></i>
                                    <span>Pendopo</span>
                                </div>
                                <span class="price-amount">Rp 50.000/hari</span>
                            </div>
                            <div class="price-item">
                                <div class="price-facility">
                                    <i class="bi bi-book"></i>
                                    <span>Ruang Baca</span>
                                </div>
                                <span class="price-amount">Rp 60.000/hari</span>
                            </div>
                            <div class="price-item">
                                <div class="price-facility">
                                    <i class="bi bi-dribbble"></i>
                                    <span>Taman Bermain</span>
                                </div>
                                <span class="price-amount">Rp 45.000/hari</span>
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservasiForm');
    const resultDiv = document.getElementById('reservasiResult');
    const btn = document.getElementById('btnSubmit');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    // Set minimum date ke hari ini
    const today = new Date().toISOString().split('T')[0];
    startDate.setAttribute('min', today);
    endDate.setAttribute('min', today);

    startDate.addEventListener('change', function() {
        if (endDate.value && endDate.value < startDate.value) {
            endDate.value = startDate.value;
        }
        endDate.setAttribute('min', startDate.value);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Validasi
        if (!formData.get('name').trim()) {
            showResult('error', 'Nama penyewa harus diisi.');
            return;
        }
        if (!formData.get('startDate') || !formData.get('endDate')) {
            showResult('error', 'Tanggal mulai dan selesai harus diisi.');
            return;
        }
        const checkedFacilities = formData.getAll('fasilitas[]');
        if (checkedFacilities.length === 0) {
            showResult('error', 'Pilih minimal satu fasilitas.');
            return;
        }

        // Loading
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Mengecek ketersediaan...';

        fetch('check_availability.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-search"></i> Cek Ketersediaan & Buat Reservasi';

            if (data.error) {
                showResult('error', data.message);
                return;
            }

            if (data.available) {
                // Simpan ke session via non-AJAX call
                const sessionForm = new FormData(form);
                fetch('check_availability.php', {
                    method: 'POST',
                    body: sessionForm
                });

                let facilitiesHtml = '';
                data.available_facilities.forEach(f => {
                    facilitiesHtml += `
                        <div class="result-detail-row">
                            <span><i class="bi bi-check-circle-fill"></i> ${f.nama}</span>
                            <span class="detail-price">Rp ${f.harga.toLocaleString('id-ID')}</span>
                        </div>
                    `;
                });

                resultDiv.innerHTML = `
                    <div class="result-content success">
                        <div class="result-icon-circle success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h4>Fasilitas Tersedia!</h4>
                        <p class="result-subtitle">Berikut ringkasan reservasi Anda:</p>

                        <div class="result-summary">
                            <div class="result-detail-row">
                                <span><i class="bi bi-person"></i> Penyewa</span>
                                <span class="detail-value">${document.getElementById('name').value}</span>
                            </div>
                            <div class="result-detail-row">
                                <span><i class="bi bi-calendar3"></i> Periode</span>
                                <span class="detail-value">${formatDate(data.start_date)} - ${formatDate(data.end_date)} (${data.duration_days} hari)</span>
                            </div>
                            <div class="result-divider"></div>
                            ${facilitiesHtml}
                            <div class="result-divider"></div>
                            <div class="result-detail-row total">
                                <span>Total Biaya</span>
                                <span>${data.total_bayar_formatted}</span>
                            </div>
                        </div>

                        <div class="result-actions">
                            <button type="button" class="btn-confirm-reservasi" onclick="showPaymentSection()">
                                <i class="bi bi-arrow-right"></i>
                                Lanjut ke Pembayaran
                            </button>
                            <button type="button" class="btn-cancel-reservasi" onclick="resetForm()">
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Ubah Data
                            </button>
                        </div>
                    </div>
                `;
                resultDiv.style.display = 'block';
                resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                // Disable form
                form.querySelectorAll('input, button').forEach(el => el.disabled = true);
            } else {
                let conflictHtml = data.conflict_facilities.map(f =>
                    `<span class="conflict-tag"><i class="bi bi-x-circle"></i> ${f}</span>`
                ).join('');

                resultDiv.innerHTML = `
                    <div class="result-content error">
                        <div class="result-icon-circle error">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h4>Fasilitas Tidak Tersedia</h4>
                        <p class="result-subtitle">Fasilitas berikut sudah dipesan pada tanggal tersebut:</p>
                        <div class="conflict-tags">${conflictHtml}</div>
                        <p class="result-hint">Silakan pilih tanggal lain atau fasilitas yang berbeda.</p>
                    </div>
                `;
                resultDiv.style.display = 'block';
                resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-search"></i> Cek Ketersediaan & Buat Reservasi';
            showResult('error', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
        });
    });

    window.resetForm = function() {
        resultDiv.style.display = 'none';
        form.querySelectorAll('input, button').forEach(el => el.disabled = false);
        btn.innerHTML = '<i class="bi bi-search"></i> Cek Ketersediaan & Buat Reservasi';
    };

    function showResult(type, message) {
        resultDiv.innerHTML = `
            <div class="result-simple ${type}">
                <i class="bi bi-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function formatDate(dateStr) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const d = new Date(dateStr);
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    // === Payment Section Logic ===
    const paymentSection = document.getElementById('paymentSection');
    const dropzone = document.getElementById('dropzone');
    const buktiFile = document.getElementById('buktiFile');
    const previewImg = document.getElementById('previewImg');
    const uploadPreview = document.getElementById('uploadPreview');
    const dropzoneContent = document.getElementById('dropzoneContent');
    const btnSubmitPayment = document.getElementById('btnSubmitPayment');

    // Payment tabs
    document.querySelectorAll('.payment-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.payment-tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        });
    });

    // Dropzone click
    dropzone.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-preview')) return;
        buktiFile.click();
    });

    // Drag & drop
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

    // File input change
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

    window.removePreview = function() {
        buktiFile.value = '';
        uploadPreview.style.display = 'none';
        dropzoneContent.style.display = 'flex';
        btnSubmitPayment.disabled = true;
    };

    window.showPaymentSection = function() {
        resultDiv.style.display = 'none';
        paymentSection.style.display = 'block';
        // Set total from result
        const totalEl = document.querySelector('.result-detail-row.total span:last-child');
        if (totalEl) {
            document.getElementById('paymentTotal').textContent = totalEl.textContent;
        }
        paymentSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    window.backToConfirm = function() {
        paymentSection.style.display = 'none';
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    window.copyAccountNumber = function() {
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
    };

    // Submit payment form with loading state
    const btnSkipPayment = document.getElementById('btnSkipPayment');
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const clickedBtn = e.submitter;
        if (clickedBtn === btnSkipPayment) {
            // Skip: remove file input value so no file is sent
            buktiFile.value = '';
            btnSkipPayment.disabled = true;
            btnSkipPayment.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Memproses...';
        } else {
            btnSubmitPayment.disabled = true;
            btnSubmitPayment.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Memproses...';
        }
    });
});
</script>

</body>
</html>
