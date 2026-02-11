<?php
// Global Config
include 'includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = './';
$page_title = 'Event & Reservasi - Taman Cerdas Salatiga';
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
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/event.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <!-- Section 1: Page Header -->
    <section class="event-header">
        <div class="container">
            <div class="event-header-content">
                <span class="page-badge">Event & Reservasi</span>
                <h1>Event & Reservasi Taman Cerdas</h1>
                <p>Temukan dan reservasi fasilitas terbaik untuk acara Anda di Taman Cerdas Salatiga.</p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Event</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Section 2: Hero / About Event -->
    <section class="event-about">
        <div class="container">
            <div class="event-about-grid">
                <div class="event-about-text">
                    <span class="section-label">Tentang Reservasi</span>
                    <h2>Reservasi Fasilitas dengan Mudah</h2>
                    <p>Reservasi Taman Cerdas adalah layanan yang dirancang untuk memudahkan Anda dalam memesan fasilitas di Taman Cerdas Salatiga. Pilih fasilitas, tentukan tanggal, dan nikmati pengalaman terbaik bersama keluarga atau komunitas.</p>
                    <p>Dengan sistem reservasi online, Anda dapat mengatur jadwal kunjungan tanpa harus datang langsung ke lokasi.</p>
                    <div class="event-highlights">
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <strong>Pemesanan Tempat</strong>
                                <span>Pilih tanggal & fasilitas sesuai kebutuhan</span>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div>
                                <strong>Status Reservasi</strong>
                                <span>Pantau status reservasi secara real-time</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="event-about-image">
                    <img src="<?php echo $base_path; ?>assets/images/portrait_taman_cerdas.jpg" alt="Taman Cerdas Salatiga">
                    <div class="image-badge">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Taman Cerdas Salatiga</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Cek Ketersediaan -->
    <section class="event-availability">
        <div class="container">
            <div class="section-header-event">
                <span class="section-label">Ketersediaan</span>
                <h2>Cek Ketersediaan Fasilitas</h2>
                <p>Pastikan fasilitas yang Anda inginkan tersedia pada tanggal pilihan Anda.</p>
            </div>

            <div class="availability-card">
                <form id="availabilityForm" class="availability-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date-start">
                                <i class="bi bi-calendar-event"></i>
                                Tanggal Mulai
                            </label>
                            <input type="date" class="form-input" id="date-start" name="startDate" required>
                        </div>
                        <div class="form-group">
                            <label for="date-end">
                                <i class="bi bi-calendar-event"></i>
                                Tanggal Selesai
                            </label>
                            <input type="date" class="form-input" id="date-end" name="endDate" required>
                        </div>
                    </div>

                    <div class="facility-selection">
                        <label class="facility-label">Pilih Fasilitas</label>
                        <div class="facility-options">
                            <label class="facility-option">
                                <input type="checkbox" name="fasilitas[]" value="pendopo">
                                <div class="facility-option-card">
                                    <i class="bi bi-house-door"></i>
                                    <span>Pendopo</span>
                                </div>
                            </label>
                            <label class="facility-option">
                                <input type="checkbox" name="fasilitas[]" value="ruang_baca">
                                <div class="facility-option-card">
                                    <i class="bi bi-book"></i>
                                    <span>Ruang Baca</span>
                                </div>
                            </label>
                            <label class="facility-option">
                                <input type="checkbox" name="fasilitas[]" value="taman_bermain">
                                <div class="facility-option-card">
                                    <i class="bi bi-dribbble"></i>
                                    <span>Taman Bermain</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-check-availability" id="btnCheckAvailability">
                        <i class="bi bi-search"></i>
                        Cek Ketersediaan
                    </button>
                </form>

                <!-- Result Area -->
                <div id="availabilityResult" class="availability-result" style="display: none;"></div>
            </div>
        </div>
    </section>

    <!-- Section 4: Fasilitas Showcase -->
    <section class="event-facilities">
        <div class="container">
            <div class="section-header-event">
                <span class="section-label">Fasilitas</span>
                <h2>Fasilitas yang Tersedia</h2>
                <p>Berbagai fasilitas yang dapat Anda sewa untuk kegiatan dan acara Anda.</p>
            </div>

            <div class="facilities-grid">
                <!-- Pendopo -->
                <div class="facility-card">
                    <div class="facility-image">
                        <img src="<?php echo $base_path; ?>assets/images/Pendopo.jpg" alt="Pendopo">
                        <span class="facility-badge">Populer</span>
                    </div>
                    <div class="facility-body">
                        <h3>Pendopo</h3>
                        <p class="facility-desc">Area serbaguna yang cocok untuk berbagai acara, dari pertemuan komunitas hingga kegiatan budaya.</p>
                        <div class="facility-price">
                            <span class="currency">Rp</span>
                            <span class="price">50.000</span>
                            <span class="period">/hari</span>
                        </div>
                        <div class="facility-meta">
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                <span>Max 70 orang</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-wifi"></i>
                                <span>Free WiFi</span>
                            </div>
                        </div>
                        <ul class="facility-features">
                            <li><i class="bi bi-check-circle-fill"></i> Area serbaguna</li>
                            <li><i class="bi bi-check-circle-fill"></i> Akses Toilet</li>
                            <li><i class="bi bi-check-circle-fill"></i> Parkir luas</li>
                        </ul>
                        <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-facility">
                            <i class="bi bi-calendar-plus"></i>
                            Reservasi
                        </a>
                    </div>
                </div>

                <!-- Ruang Baca -->
                <div class="facility-card featured">
                    <div class="facility-image">
                        <img src="<?php echo $base_path; ?>assets/images/Ruang_baca.jpg" alt="Ruang Baca">
                        <span class="facility-badge">Best Value</span>
                    </div>
                    <div class="facility-body">
                        <h3>Ruang Baca</h3>
                        <p class="facility-desc">Ruang baca nyaman dengan koleksi buku lengkap, ideal untuk kegiatan literasi dan diskusi.</p>
                        <div class="facility-price">
                            <span class="currency">Rp</span>
                            <span class="price">60.000</span>
                            <span class="period">/hari</span>
                        </div>
                        <div class="facility-meta">
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                <span>Max 35 orang</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-book"></i>
                                <span>1000+ Buku</span>
                            </div>
                        </div>
                        <ul class="facility-features">
                            <li><i class="bi bi-check-circle-fill"></i> Free WiFi</li>
                            <li><i class="bi bi-check-circle-fill"></i> Akses Toilet</li>
                            <li><i class="bi bi-check-circle-fill"></i> AC & nyaman</li>
                        </ul>
                        <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-facility">
                            <i class="bi bi-calendar-plus"></i>
                            Reservasi
                        </a>
                    </div>
                </div>

                <!-- Taman Bermain -->
                <div class="facility-card">
                    <div class="facility-image">
                        <img src="<?php echo $base_path; ?>assets/images/Taman Bermain.jpg" alt="Taman Bermain">
                        <span class="facility-badge">Anak-anak</span>
                    </div>
                    <div class="facility-body">
                        <h3>Taman Bermain</h3>
                        <p class="facility-desc">Area bermain aman dan edukatif untuk anak-anak dengan berbagai wahana menarik.</p>
                        <div class="facility-price">
                            <span class="currency">Rp</span>
                            <span class="price">45.000</span>
                            <span class="period">/hari</span>
                        </div>
                        <div class="facility-meta">
                            <div class="meta-item">
                                <i class="bi bi-people"></i>
                                <span>Max 15 anak</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-shield-check"></i>
                                <span>Area aman</span>
                            </div>
                        </div>
                        <ul class="facility-features">
                            <li><i class="bi bi-check-circle-fill"></i> Alat bermain edukatif</li>
                            <li><i class="bi bi-check-circle-fill"></i> Tempat tunggu ortu</li>
                            <li><i class="bi bi-check-circle-fill"></i> Area outdoor</li>
                        </ul>
                        <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-facility">
                            <i class="bi bi-calendar-plus"></i>
                            Reservasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Cara Reservasi -->
    <section class="event-steps">
        <div class="container">
            <div class="section-header-event">
                <span class="section-label">Panduan</span>
                <h2>Cara Melakukan Reservasi</h2>
                <p>Ikuti langkah mudah berikut untuk memesan fasilitas di Taman Cerdas.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3>Cek Ketersediaan</h3>
                    <p>Pilih tanggal dan fasilitas yang diinginkan, lalu cek ketersediaannya.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h3>Isi Form Reservasi</h3>
                    <p>Lengkapi data diri dan detail reservasi pada form yang tersedia.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h3>Tunggu Konfirmasi</h3>
                    <p>Admin akan memproses dan mengkonfirmasi reservasi Anda.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">4</div>
                    <div class="step-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h3>Upload Bukti Bayar</h3>
                    <p>Setelah dikonfirmasi, upload bukti pembayaran untuk menyelesaikan reservasi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 6: Info (Target Market & Kontribusi) -->
    <section class="event-info">
        <div class="container">
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3>Target Market</h3>
                    <p>Reservasi Taman Cerdas dirancang untuk digunakan oleh berbagai kalangan yang ingin memesan fasilitas di taman, seperti pelajar, pekerja, komunitas, dan wisatawan yang mencari ruang untuk kegiatan mereka.</p>
                    <ul class="info-list">
                        <li><i class="bi bi-check2"></i> Pelajar & mahasiswa</li>
                        <li><i class="bi bi-check2"></i> Komunitas & organisasi</li>
                        <li><i class="bi bi-check2"></i> Wisatawan & keluarga</li>
                    </ul>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h3>Kontribusi</h3>
                    <p>Reservasi Taman Cerdas membantu meningkatkan efisiensi dalam pengelolaan fasilitas publik, memudahkan masyarakat dalam mengakses ruang terbuka, serta mendukung kegiatan edukatif dan rekreatif.</p>
                    <ul class="info-list">
                        <li><i class="bi bi-check2"></i> Efisiensi pengelolaan fasilitas</li>
                        <li><i class="bi bi-check2"></i> Akses mudah bagi masyarakat</li>
                        <li><i class="bi bi-check2"></i> Mendukung kegiatan edukatif</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 7: CTA -->
    <section class="event-cta">
        <div class="container">
            <div class="cta-box">
                <div class="cta-text">
                    <h2>Siap Melakukan Reservasi?</h2>
                    <p>Pesan fasilitas Taman Cerdas sekarang dan wujudkan acara impian Anda.</p>
                </div>
                <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-cta-event">
                    <i class="bi bi-calendar-check"></i>
                    Reservasi Sekarang
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('availabilityForm');
    const resultDiv = document.getElementById('availabilityResult');
    const btn = document.getElementById('btnCheckAvailability');
    const dateStart = document.getElementById('date-start');
    const dateEnd = document.getElementById('date-end');

    // Set minimum date ke hari ini
    const today = new Date().toISOString().split('T')[0];
    dateStart.setAttribute('min', today);
    dateEnd.setAttribute('min', today);

    // Auto-set end date minimal sama dengan start date
    dateStart.addEventListener('change', function() {
        if (dateEnd.value && dateEnd.value < dateStart.value) {
            dateEnd.value = dateStart.value;
        }
        dateEnd.setAttribute('min', dateStart.value);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Validasi client-side
        if (!formData.get('startDate') || !formData.get('endDate')) {
            showResult('error', 'Tanggal mulai dan selesai harus diisi.');
            return;
        }

        const checkedFacilities = formData.getAll('fasilitas[]');
        if (checkedFacilities.length === 0) {
            showResult('error', 'Pilih minimal satu fasilitas.');
            return;
        }

        // Loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Mengecek...';

        fetch('<?php echo $base_path; ?>reservasi/check_availability.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-search"></i> Cek Ketersediaan';

            if (data.error) {
                showResult('error', data.message);
                return;
            }

            if (data.available) {
                let facilitiesHtml = '';
                data.available_facilities.forEach(f => {
                    facilitiesHtml += `
                        <div class="result-facility-item">
                            <span class="result-facility-name">
                                <i class="bi bi-check-circle-fill"></i> ${f.nama}
                            </span>
                            <span class="result-facility-price">Rp ${f.harga.toLocaleString('id-ID')}</span>
                        </div>
                    `;
                });

                showResult('success', `
                    <div class="result-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h4>Fasilitas Tersedia!</h4>
                    <p class="result-date">
                        <i class="bi bi-calendar3"></i>
                        ${formatDate(data.start_date)} - ${formatDate(data.end_date)}
                        <span class="result-duration">(${data.duration_days} hari)</span>
                    </p>
                    <div class="result-facilities-list">
                        ${facilitiesHtml}
                    </div>
                    <div class="result-total">
                        <span>Total Biaya</span>
                        <strong>${data.total_bayar_formatted}</strong>
                    </div>
                    <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-proceed-reservation">
                        <i class="bi bi-arrow-right-circle"></i>
                        Lanjut Reservasi
                    </a>
                `);
            } else {
                let conflictHtml = data.conflict_facilities.map(f =>
                    `<span class="conflict-badge"><i class="bi bi-x-circle"></i> ${f}</span>`
                ).join('');

                showResult('error', `
                    <div class="result-icon warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h4>Fasilitas Tidak Tersedia</h4>
                    <p class="result-date">
                        <i class="bi bi-calendar3"></i>
                        ${formatDate(data.start_date)} - ${formatDate(data.end_date)}
                    </p>
                    <p class="result-conflict-text">Fasilitas berikut sudah dipesan pada tanggal tersebut:</p>
                    <div class="result-conflict-list">
                        ${conflictHtml}
                    </div>
                    <p class="result-suggestion">Silakan pilih tanggal lain atau fasilitas yang berbeda.</p>
                `);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-search"></i> Cek Ketersediaan';
            showResult('error', 'Terjadi kesalahan. Silakan coba lagi.');
        });
    });

    function showResult(type, content) {
        if (typeof content === 'string' && !content.includes('<div')) {
            // Simple text message
            resultDiv.innerHTML = `
                <div class="result-simple ${type}">
                    <i class="bi bi-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                    <span>${content}</span>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `<div class="result-content ${type}">${content}</div>`;
        }
        resultDiv.style.display = 'block';
        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function formatDate(dateStr) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const d = new Date(dateStr);
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }
});
</script>

</body>
</html>
