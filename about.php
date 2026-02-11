<?php
// Global Config
include 'includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = './';
$page_title = 'Tentang Kami - Taman Cerdas Salatiga';

include 'includes/database.php';
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
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/about.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <!-- Page Header -->
    <section class="about-header">
        <div class="container">
            <div class="about-header-content">
                <span class="page-badge">Tentang Kami</span>
                <h1>Mengenal Taman Cerdas Salatiga</h1>
                <p>Inovasi, edukasi, dan rekreasi di jantung Kota Salatiga.</p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>About Us</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="about-story">
        <div class="container">
            <div class="story-grid">
                <div class="story-text">
                    <span class="section-label">Cerita Kami</span>
                    <h2>Ruang Publik yang Menginspirasi</h2>
                    <p>Taman Cerdas di Kota Salatiga adalah sebuah inovasi yang memadukan elemen alam, teknologi, dan pendidikan. Ruang cerdas dan interaktif ini dirancang untuk memberikan pengalaman yang mendidik dan menghibur bagi pengunjung dari segala usia.</p>
                    <p>Dengan berbagai fasilitas modern seperti taman bermain edukatif dan spot informasi interaktif, kami bertujuan untuk menginspirasi minat dan pengetahuan anak-anak dalam berbagai disiplin ilmu, seraya menjadi pusat kegiatan komunitas yang dinamis.</p>
                    <div class="story-highlights">
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <strong>Berdiri Sejak 2020</strong>
                                <span>Melayani masyarakat Salatiga</span>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <strong>500+ Pengunjung</strong>
                                <span>Setiap bulannya</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="story-image">
                    <img src="<?php echo $base_path; ?>assets/images/Taman.jpg" alt="Suasana Taman Cerdas">
                    <div class="image-badge">
                        <i class="bi bi-pin-map-fill"></i>
                        <span>Salatiga, Jawa Tengah</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="about-vision">
        <div class="container">
            <div class="vision-grid">
                <div class="vision-card">
                    <div class="vision-icon">
                        <i class="bi bi-eye"></i>
                    </div>
                    <h3>Visi</h3>
                    <p>Menjadi ruang publik terdepan yang mengintegrasikan rekreasi, teknologi, dan pendidikan untuk menciptakan masyarakat yang cerdas, kreatif, dan berwawasan lingkungan.</p>
                    <ul class="vision-list">
                        <li><i class="bi bi-check2"></i> Ruang publik terdepan</li>
                        <li><i class="bi bi-check2"></i> Integrasi teknologi & pendidikan</li>
                        <li><i class="bi bi-check2"></i> Masyarakat cerdas & kreatif</li>
                    </ul>
                </div>
                <div class="vision-card">
                    <div class="vision-icon">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3>Misi</h3>
                    <p>Menyediakan fasilitas edukatif yang inovatif, menyelenggarakan acara komunitas yang inspiratif, dan mempromosikan gaya hidup berkelanjutan bagi seluruh warga Salatiga.</p>
                    <ul class="vision-list">
                        <li><i class="bi bi-check2"></i> Fasilitas edukatif inovatif</li>
                        <li><i class="bi bi-check2"></i> Acara komunitas inspiratif</li>
                        <li><i class="bi bi-check2"></i> Gaya hidup berkelanjutan</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Numbers Section -->
    <section class="about-numbers">
        <div class="container">
            <div class="numbers-grid">
                <div class="number-item">
                    <span class="number-value">8+</span>
                    <span class="number-label">Fasilitas Tersedia</span>
                </div>
                <div class="number-item">
                    <span class="number-value">500+</span>
                    <span class="number-label">Pengunjung / Bulan</span>
                </div>
                <div class="number-item">
                    <span class="number-value">50+</span>
                    <span class="number-label">Event / Tahun</span>
                </div>
                <div class="number-item">
                    <span class="number-value">4.8</span>
                    <span class="number-label">Rating Pengunjung</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="about-facilities">
        <div class="container">
            <div class="section-header-about">
                <span class="section-label">Fasilitas</span>
                <h2>Fasilitas Kami</h2>
                <p>Berbagai fasilitas yang tersedia untuk mendukung kegiatan Anda di Taman Cerdas.</p>
            </div>

            <div class="facilities-showcase-grid">
                <!-- Pendopo -->
                <div class="facility-showcase-card">
                    <div class="facility-showcase-image">
                        <img src="<?php echo $base_path; ?>assets/images/Pendopo.jpg" alt="Pendopo">
                        <span class="facility-showcase-badge">Populer</span>
                    </div>
                    <div class="facility-showcase-body">
                        <h3>Pendopo</h3>
                        <p>Area serbaguna yang cocok untuk berbagai acara, dari pertemuan komunitas hingga kegiatan budaya.</p>
                        <ul class="facility-showcase-features">
                            <li><i class="bi bi-check-circle-fill"></i> Kapasitas max 70 orang</li>
                            <li><i class="bi bi-check-circle-fill"></i> Free WiFi</li>
                            <li><i class="bi bi-check-circle-fill"></i> Akses Toilet</li>
                            <li><i class="bi bi-check-circle-fill"></i> Area serbaguna</li>
                        </ul>
                    </div>
                </div>

                <!-- Ruang Baca -->
                <div class="facility-showcase-card">
                    <div class="facility-showcase-image">
                        <img src="<?php echo $base_path; ?>assets/images/Ruang_baca.jpg" alt="Ruang Baca">
                        <span class="facility-showcase-badge">Favorit</span>
                    </div>
                    <div class="facility-showcase-body">
                        <h3>Ruang Baca</h3>
                        <p>Ruang baca nyaman dengan koleksi buku lengkap, ideal untuk kegiatan literasi dan diskusi.</p>
                        <ul class="facility-showcase-features">
                            <li><i class="bi bi-check-circle-fill"></i> Kapasitas max 35 orang</li>
                            <li><i class="bi bi-check-circle-fill"></i> Free WiFi</li>
                            <li><i class="bi bi-check-circle-fill"></i> 1000+ Koleksi Buku</li>
                            <li><i class="bi bi-check-circle-fill"></i> Akses Toilet</li>
                        </ul>
                    </div>
                </div>

                <!-- Taman Bermain -->
                <div class="facility-showcase-card">
                    <div class="facility-showcase-image">
                        <img src="<?php echo $base_path; ?>assets/images/Taman Bermain.jpg" alt="Taman Bermain">
                        <span class="facility-showcase-badge">Anak-anak</span>
                    </div>
                    <div class="facility-showcase-body">
                        <h3>Taman Bermain</h3>
                        <p>Area bermain aman dan edukatif untuk anak-anak dengan berbagai wahana menarik.</p>
                        <ul class="facility-showcase-features">
                            <li><i class="bi bi-check-circle-fill"></i> Kapasitas max 15 anak</li>
                            <li><i class="bi bi-check-circle-fill"></i> Area bermain aman</li>
                            <li><i class="bi bi-check-circle-fill"></i> Tempat tunggu orang tua</li>
                            <li><i class="bi bi-check-circle-fill"></i> Alat bermain edukatif</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="facilities-cta-link">
                <a href="<?php echo $base_path; ?>event.php" class="btn-see-pricing">
                    <i class="bi bi-tag"></i>
                    Lihat Harga & Reservasi
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="about-cta">
        <div class="container">
            <div class="cta-box">
                <div class="cta-text">
                    <h2>Tertarik Berkunjung?</h2>
                    <p>Reservasi fasilitas kami sekarang dan nikmati pengalaman edukatif bersama keluarga.</p>
                </div>
                <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-cta-about">
                    <i class="bi bi-calendar-check"></i>
                    Mulai Reservasi
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>
