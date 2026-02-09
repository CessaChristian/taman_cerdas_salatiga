<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = './';
$page_title = 'Taman Cerdas Salatiga - Ruang Edukasi & Rekreasi';

// Page-specific includes
include 'includes/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Global Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <!-- Page-specific CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/home.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>

        <!-- Floating Elements -->
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>

        <div class="hero-content">
            <span class="hero-badge">
                <i class="bi bi-geo-alt-fill"></i>
                Salatiga, Jawa Tengah
            </span>
            <h1 class="hero-title">
                Selamat Datang di<br>
                <span class="gradient-text">Taman Cerdas</span>
            </h1>
            <p class="hero-subtitle">Tempat di mana rekreasi, edukasi, dan alam berpadu harmonis untuk pengalaman tak terlupakan.</p>

            <div class="hero-buttons">
                <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-hero-primary" data-is-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
                    <i class="bi bi-calendar-check"></i>
                    Reservasi Sekarang
                </a>
                <a href="#fasilitas" class="btn-hero-secondary">
                    <i class="bi bi-arrow-down-circle"></i>
                    Jelajahi
                </a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number" data-count="500">0</span>
                    <span class="stat-label">Pengunjung/Bulan</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number" data-count="8">0</span>
                    <span class="stat-label">Fasilitas</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-number" data-count="50">0</span>
                    <span class="stat-label">Event/Tahun</span>
                </div>
            </div>
        </div>

        <a href="#fasilitas" class="scroll-indicator">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
            <span>Scroll</span>
        </a>
    </section>

    <!-- Facilities Section -->
    <section id="fasilitas" class="facilities-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Fasilitas</span>
                <h2 class="section-title">Fasilitas Unggulan Kami</h2>
                <p class="section-subtitle">Jelajahi berbagai fasilitas yang kami sediakan untuk menunjang aktivitas Anda.</p>
            </div>

            <div class="facilities-grid">
                <!-- Facility 1 -->
                <div class="facility-card" data-aos="fade-up">
                    <div class="facility-image">
                        <img src="assets/images/Pendopo.jpg" alt="Pendopo">
                        <div class="facility-overlay">
                            <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="facility-btn">
                                <i class="bi bi-calendar-plus"></i>
                                Booking
                            </a>
                        </div>
                    </div>
                    <div class="facility-content">
                        <div class="facility-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3>Pendopo</h3>
                        <p>Area serbaguna yang luas, cocok untuk kegiatan sosial, workshop, atau pertunjukan seni.</p>
                        <div class="facility-features">
                            <span><i class="bi bi-people-fill"></i> Kapasitas 70 orang</span>
                            <span><i class="bi bi-wifi"></i> Free WiFi</span>
                        </div>
                    </div>
                </div>

                <!-- Facility 2 -->
                <div class="facility-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="facility-image">
                        <img src="assets/images/Ruang_baca.jpg" alt="Ruang Baca">
                        <div class="facility-overlay">
                            <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="facility-btn">
                                <i class="bi bi-calendar-plus"></i>
                                Booking
                            </a>
                        </div>
                    </div>
                    <div class="facility-content">
                        <div class="facility-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <h3>Ruang Baca</h3>
                        <p>Tingkatkan literasi di ruang baca kami yang nyaman dengan koleksi buku yang beragam.</p>
                        <div class="facility-features">
                            <span><i class="bi bi-people-fill"></i> Kapasitas 35 orang</span>
                            <span><i class="bi bi-journal-bookmark"></i> 1000+ Buku</span>
                        </div>
                    </div>
                </div>

                <!-- Facility 3 -->
                <div class="facility-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="facility-image">
                        <img src="assets/images/Taman Bermain.jpg" alt="Taman Bermain">
                        <div class="facility-overlay">
                            <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="facility-btn">
                                <i class="bi bi-calendar-plus"></i>
                                Booking
                            </a>
                        </div>
                    </div>
                    <div class="facility-content">
                        <div class="facility-icon">
                            <i class="bi bi-tree"></i>
                        </div>
                        <h3>Taman Bermain</h3>
                        <p>Area bermain yang aman dan edukatif untuk anak-anak berekplorasi dan bersosialisasi.</p>
                        <div class="facility-features">
                            <span><i class="bi bi-people-fill"></i> Kapasitas 15 anak</span>
                            <span><i class="bi bi-shield-check"></i> Aman</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="facilities-cta">
                <a href="<?php echo $base_path; ?>about.php" class="btn-see-all">
                    Lihat Semua Fasilitas
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="galeri" class="gallery-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Galeri</span>
                <h2 class="section-title">Galeri Taman Cerdas</h2>
                <p class="section-subtitle">Abadikan momen berharga Anda di setiap sudut taman kami yang indah.</p>
            </div>

            <div class="gallery-grid">
                <?php
                $gallery_images = [
                    ['file' => 'foto_taman-cerdas_01122023-002934.png', 'title' => 'Taman Cerdas'],
                    ['file' => 'gardu_pandang.jpg', 'title' => 'Gardu Pandang'],
                    ['file' => 'Mushola.jpg', 'title' => 'Mushola'],
                    ['file' => 'Toilet.jpg', 'title' => 'Toilet'],
                    ['file' => 'Taman.jpg', 'title' => 'Taman'],
                    ['file' => 'Pendopo.jpg', 'title' => 'Pendopo'],
                    ['file' => 'Ruang_baca.jpg', 'title' => 'Ruang Baca'],
                    ['file' => 'umkm.jpg', 'title' => 'UMKM']
                ];

                foreach ($gallery_images as $index => $image):
                ?>
                <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?php echo $index * 50; ?>">
                    <img src="assets/images/<?php echo htmlspecialchars($image['file']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>">
                    <div class="gallery-overlay">
                        <div class="gallery-info">
                            <h4><?php echo htmlspecialchars($image['title']); ?></h4>
                            <button class="gallery-zoom" onclick="openLightbox('assets/images/<?php echo htmlspecialchars($image['file']); ?>')">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimoni" class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Testimoni</span>
                <h2 class="section-title">Apa Kata Mereka?</h2>
                <p class="section-subtitle">Dengarkan pengalaman dari para pengunjung kami.</p>
            </div>

            <div class="testimonials-slider">
                <div class="testimonials-track">
                    <!-- Testimonial 1 -->
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            <i class="bi bi-quote"></i>
                        </div>
                        <p class="testimonial-text">"Fasilitas perpustakaan yang ada sangat membantu untuk menumbuhkan minat baca sejak dini. Anak-anak saya sangat senang berkunjung ke sini."</p>
                        <div class="testimonial-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="assets/images/acc.png" alt="Ardiva">
                            </div>
                            <div class="author-info">
                                <h5>Ardiva</h5>
                                <span>Pengunjung</span>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            <i class="bi bi-quote"></i>
                        </div>
                        <p class="testimonial-text">"Tempat ini menyediakan lingkungan yang kondusif untuk belajar di luar kelas dan sangat menyenangkan. Recommended untuk keluarga!"</p>
                        <div class="testimonial-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="assets/images/acc.png" alt="Cessa">
                            </div>
                            <div class="author-info">
                                <h5>Cessa</h5>
                                <span>Pengunjung</span>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="testimonial-card">
                        <div class="testimonial-quote">
                            <i class="bi bi-quote"></i>
                        </div>
                        <p class="testimonial-text">"Saya senang melihat berbagai acara yang sering diadakan, seperti lomba melukis dan workshop keterampilan. Sangat edukatif!"</p>
                        <div class="testimonial-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <img src="assets/images/acc.png" alt="Hussein">
                            </div>
                            <div class="author-info">
                                <h5>Hussein</h5>
                                <span>Pengunjung</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Section -->
    <section id="lokasi" class="location-section">
        <div class="container">
            <div class="location-wrapper">
                <div class="location-info">
                    <span class="section-badge">Lokasi</span>
                    <h2 class="section-title">Temukan Kami</h2>
                    <p class="location-desc">Taman Cerdas berlokasi strategis di Sidorejo Lor, Kec. Sidorejo, Kota Salatiga, Jawa Tengah. Dekat dengan Fakultas Teknologi Informasi UKSW, kami mudah diakses oleh mahasiswa dan masyarakat umum.</p>

                    <div class="location-details">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="detail-text">
                                <h4>Alamat</h4>
                                <p>Sidorejo Lor, Kec. Sidorejo, Kota Salatiga, Jawa Tengah</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div class="detail-text">
                                <h4>Jam Operasional</h4>
                                <p>Senin - Minggu: 08.00 - 17.00 WIB</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="detail-text">
                                <h4>Kontak</h4>
                                <p>+62 812-3456-7890</p>
                            </div>
                        </div>
                    </div>

                    <a href="https://maps.google.com/?q=Taman+Cerdas+Salatiga" target="_blank" class="btn-directions">
                        <i class="bi bi-signpost-2-fill"></i>
                        Dapatkan Petunjuk Arah
                    </a>
                </div>
                <div class="location-map">
                    <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBJCDq0NABcYosN1fNy3cxUEtwRnSNZpmc&q=Taman+Cerdas+Salatiga&zoom=15" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Siap Untuk Berkunjung?</h2>
                <p>Reservasi sekarang dan nikmati pengalaman edukatif bersama keluarga.</p>
                <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-cta">
                    <i class="bi bi-calendar-check-fill"></i>
                    Reservasi Sekarang
                </a>
            </div>
        </div>
    </section>
</main>

<!-- Lightbox -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">
        <i class="bi bi-x-lg"></i>
    </button>
    <img src="" alt="Gallery Image" class="lightbox-image" id="lightboxImage">
</div>

<!-- Login Alert Modal -->
<div class="login-alert-overlay" id="loginAlertModal">
    <div class="login-alert-container">
        <!-- Close Button -->
        <button class="login-alert-close" onclick="closeLoginAlert()">
            <i class="bi bi-x-lg"></i>
        </button>

        <!-- Alert Content -->
        <div class="login-alert-content">
            <!-- Icon -->
            <div class="login-alert-icon">
                <div class="icon-circle">
                    <i class="bi bi-person-lock"></i>
                </div>
                <div class="icon-pulse"></div>
            </div>

            <!-- Text -->
            <h2 class="login-alert-title">Akses Terbatas</h2>
            <p class="login-alert-message">
                Untuk melakukan reservasi fasilitas, Anda perlu login terlebih dahulu.
                Silakan login atau buat akun baru jika belum memiliki akun.
            </p>

            <!-- Features Preview -->
            <div class="login-alert-features">
                <div class="feature-item">
                    <i class="bi bi-calendar-check"></i>
                    <span>Reservasi Fasilitas</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Booking</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-chat-dots"></i>
                    <span>Forum Diskusi</span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="login-alert-buttons">
                <a href="<?php echo $base_path; ?>login.php" class="btn-login-alert primary">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login Sekarang
                </a>
                <a href="<?php echo $base_path; ?>register.php" class="btn-login-alert secondary">
                    <i class="bi bi-person-plus"></i>
                    Buat Akun Baru
                </a>
            </div>

            <!-- Footer Text -->
            <p class="login-alert-footer">
                <i class="bi bi-shield-check"></i>
                Data Anda aman dan terlindungi
            </p>
        </div>
    </div>
</div>

<!-- Login Alert Styles -->
<style>
/* Login Alert Modal */
.login-alert-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    padding: 20px;
}

.login-alert-overlay.active {
    opacity: 1;
    visibility: visible;
}

.login-alert-container {
    background: white;
    border-radius: 24px;
    max-width: 440px;
    width: 100%;
    position: relative;
    transform: scale(0.9) translateY(20px);
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.login-alert-overlay.active .login-alert-container {
    transform: scale(1) translateY(0);
}

/* Close Button */
.login-alert-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    border: none;
    background: #f1f5f9;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    transition: all 0.3s ease;
    z-index: 10;
}

.login-alert-close:hover {
    background: #fee2e2;
    color: #ef4444;
    transform: rotate(90deg);
}

/* Content */
.login-alert-content {
    padding: 48px 32px 32px;
    text-align: center;
}

/* Icon */
.login-alert-icon {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 24px;
}

.icon-circle {
    width: 100%;
    height: 100%;
    background: #2563eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 2;
    animation: iconBounce 0.6s ease 0.3s both;
}

.icon-circle i {
    font-size: 2.5rem;
    color: white;
}

.icon-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    background: #2563eb;
    border-radius: 50%;
    opacity: 0.3;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes iconBounce {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes pulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
    50% { transform: translate(-50%, -50%) scale(1.3); opacity: 0; }
}

/* Title & Message */
.login-alert-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 12px;
    animation: fadeInUp 0.5s ease 0.2s both;
}

.login-alert-message {
    font-size: 1rem;
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 24px;
    animation: fadeInUp 0.5s ease 0.3s both;
}

/* Features */
.login-alert-features {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 32px;
    animation: fadeInUp 0.5s ease 0.4s both;
}

.feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: #f8fafc;
    border-radius: 16px;
    flex: 1;
    transition: all 0.3s ease;
}

.feature-item:hover {
    background: #eff6ff;
    transform: translateY(-3px);
}

.feature-item i {
    font-size: 1.5rem;
    color: #3b82f6;
}

.feature-item span {
    font-size: 0.8rem;
    font-weight: 600;
    color: #475569;
}

/* Buttons */
.login-alert-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    animation: fadeInUp 0.5s ease 0.5s both;
}

.btn-login-alert {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px 24px;
    border-radius: 14px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-login-alert.primary {
    background: #2563eb;
    color: white;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
}

.btn-login-alert.primary:hover {
    transform: translateY(-2px);
    background: #1d4ed8;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
    color: white;
}

.btn-login-alert.secondary {
    background: #f1f5f9;
    color: #475569;
    border: 2px solid #e2e8f0;
}

.btn-login-alert.secondary:hover {
    background: #e2e8f0;
    border-color: #cbd5e1;
    color: #1e293b;
}

/* Footer Text */
.login-alert-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
    font-size: 0.85rem;
    color: #94a3b8;
    animation: fadeInUp 0.5s ease 0.6s both;
}

.login-alert-footer i {
    color: #10b981;
}

/* Shake Animation for Attention */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

.login-alert-container.shake {
    animation: shake 0.5s ease;
}

/* Responsive */
@media (max-width: 480px) {
    .login-alert-content {
        padding: 40px 20px 24px;
    }

    .login-alert-title {
        font-size: 1.4rem;
    }

    .login-alert-message {
        font-size: 0.9rem;
    }

    .login-alert-features {
        flex-direction: column;
        gap: 10px;
    }

    .feature-item {
        flex-direction: row;
        justify-content: center;
        padding: 12px 16px;
    }

    .feature-item i {
        font-size: 1.2rem;
    }

    .btn-login-alert {
        padding: 14px 20px;
        font-size: 0.95rem;
    }

    .icon-circle {
        width: 80px;
        height: 80px;
    }

    .icon-circle i {
        font-size: 2rem;
    }

    .login-alert-icon {
        width: 80px;
        height: 80px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

<script>
// Counter Animation
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const updateCounter = () => {
            current += step;
            if (current < target) {
                counter.textContent = Math.floor(current) + '+';
                requestAnimationFrame(updateCounter);
            } else {
                counter.textContent = target + '+';
            }
        };

        updateCounter();
    });
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');

            // Trigger counter animation when hero stats are visible
            if (entry.target.classList.contains('hero-stats')) {
                animateCounters();
            }
        }
    });
}, observerOptions);

// Observe elements
document.querySelectorAll('.facility-card, .gallery-item, .testimonial-card, .hero-stats').forEach(el => {
    observer.observe(el);
});

// Lightbox functions
function openLightbox(src) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    lightboxImage.src = src;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.remove('active');
    document.body.style.overflow = '';
}

// Close lightbox on click outside
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLightbox();
    }
});

// Close lightbox on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ===== LOGIN ALERT MODAL =====
const loginAlertModal = document.getElementById('loginAlertModal');
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

// Function to open login alert
function openLoginAlert() {
    loginAlertModal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Add entrance animation sound effect (optional visual feedback)
    const container = loginAlertModal.querySelector('.login-alert-container');
    container.classList.remove('shake');
}

// Function to close login alert
function closeLoginAlert() {
    loginAlertModal.classList.remove('active');
    document.body.style.overflow = '';
}

// Function to shake modal (for emphasis)
function shakeModal() {
    const container = loginAlertModal.querySelector('.login-alert-container');
    container.classList.add('shake');
    setTimeout(() => container.classList.remove('shake'), 500);
}

// Handle click on reservation/booking buttons
document.addEventListener('click', function(e) {
    // Check if clicked element is a reservation/booking button
    const reservasiLink = e.target.closest('a[href*="reservasi"]');

    if (reservasiLink && !isLoggedIn) {
        e.preventDefault();
        e.stopPropagation();
        openLoginAlert();
    }
});

// Close modal when clicking outside
loginAlertModal.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLoginAlert();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && loginAlertModal.classList.contains('active')) {
        closeLoginAlert();
    }
});

// Shake modal if user tries to click outside too many times
let clickOutsideCount = 0;
loginAlertModal.addEventListener('click', function(e) {
    if (e.target === this) {
        clickOutsideCount++;
        if (clickOutsideCount >= 3) {
            shakeModal();
            clickOutsideCount = 0;
        }
    }
});
</script>

</body>
</html>
