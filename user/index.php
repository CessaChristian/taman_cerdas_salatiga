<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Profil Pengguna - Taman Cerdas';

// Keamanan: Pastikan hanya user yang sudah login bisa mengakses halaman ini
if (!$isLoggedIn) {
    header("Location: " . $base_path . "login.php");
    exit();
}

// Database connection
include '../includes/database.php';
include '../includes/auto_cancel.php';

$username = $_SESSION['username'];
$nama = $_SESSION['nama'];

// Get user data
$sql_user = "SELECT * FROM data_user WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();

// Get reservation statistics
$sql_stats = "SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as rejected
    FROM reservasi WHERE username = ?";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("s", $username);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

// Get reservations
$sql_reservasi = "SELECT * FROM reservasi WHERE username = ? ORDER BY tanggal_mulai DESC";
$stmt_reservasi = $conn->prepare($sql_reservasi);
$stmt_reservasi->bind_param("s", $username);
$stmt_reservasi->execute();
$reservasi_result = $stmt_reservasi->get_result();

// Get forum posts count
$sql_posts = "SELECT COUNT(*) as total FROM post WHERE username = ?";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("s", $username);
$stmt_posts->execute();
$posts_count = $stmt_posts->get_result()->fetch_assoc()['total'];

// Member since (from first reservation or current date)
$member_since = date('F Y');
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
</head>
<body>

<?php include '../includes/header.php'; ?>

<main class="profile-page">
    <!-- Profile Header -->
    <section class="profile-header">
        <div class="profile-header-bg"></div>
        <div class="container">
            <div class="profile-header-content">
                <div class="profile-avatar">
                    <span class="avatar-text"><?php echo strtoupper(substr($nama, 0, 1)); ?></span>
                    <div class="avatar-badge">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo htmlspecialchars($nama); ?></h1>
                    <p class="profile-username">@<?php echo htmlspecialchars($username); ?></p>
                    <div class="profile-meta">
                        <span><i class="bi bi-envelope-fill"></i> <?php echo htmlspecialchars($user_data['email'] ?? 'email@example.com'); ?></span>
                        <span><i class="bi bi-calendar3"></i> Member sejak <?php echo $member_since; ?></span>
                    </div>
                </div>
                <div class="profile-actions">
                    <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-profile-action primary">
                        <i class="bi bi-plus-circle"></i>
                        Buat Reservasi
                    </a>
                    <button class="btn-profile-action secondary" onclick="showEditProfile()">
                        <i class="bi bi-pencil"></i>
                        Edit Profil
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="profile-stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $stats['total'] ?? 0; ?></span>
                        <span class="stat-label">Total Reservasi</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $stats['pending'] ?? 0; ?></span>
                        <span class="stat-label">Menunggu</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $stats['approved'] ?? 0; ?></span>
                        <span class="stat-label">Disetujui</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon posts">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div class="stat-content">
                        <span class="stat-number"><?php echo $posts_count; ?></span>
                        <span class="stat-label">Post Forum</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="profile-content">
        <div class="container">
            <div class="content-grid">
                <!-- Left Column - Reservations -->
                <div class="content-main">
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2><i class="bi bi-calendar2-week"></i> Reservasi Saya</h2>
                        </div>

                        <div class="reservations-list">
                            <?php if ($reservasi_result->num_rows > 0): ?>
                                <?php while($row = $reservasi_result->fetch_assoc()): ?>
                                    <?php
                                    $status = $row['status'];
                                    $status_class = 'pending';
                                    $status_icon = 'bi-hourglass-split';
                                    if ($status == 'disetujui') {
                                        $status_class = 'approved';
                                        $status_icon = 'bi-check-circle-fill';
                                    } else if ($status == 'ditolak') {
                                        $status_class = 'rejected';
                                        $status_icon = 'bi-x-circle-fill';
                                    }

                                    // Get facilities
                                    $facilities = [];
                                    if ($row['pendopo']) $facilities[] = 'Pendopo';
                                    if ($row['ruang_baca']) $facilities[] = 'Ruang Baca';
                                    if ($row['taman_bermain']) $facilities[] = 'Taman Bermain';
                                    ?>
                                    <div class="reservation-item">
                                        <div class="reservation-date">
                                            <span class="date-day"><?php echo date('d', strtotime($row['tanggal_mulai'])); ?></span>
                                            <span class="date-month"><?php echo date('M', strtotime($row['tanggal_mulai'])); ?></span>
                                        </div>
                                        <div class="reservation-details">
                                            <h4><?php echo implode(', ', $facilities); ?></h4>
                                            <p class="reservation-period">
                                                <i class="bi bi-calendar-range"></i>
                                                <?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?> - <?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?>
                                            </p>
                                            <p class="reservation-price">
                                                <i class="bi bi-cash"></i>
                                                Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?>
                                            </p>
                                        </div>
                                        <div class="reservation-status <?php echo $status_class; ?>">
                                            <i class="bi <?php echo $status_icon; ?>"></i>
                                            <span><?php echo ucfirst($status); ?></span>
                                        </div>
                                        <div class="reservation-actions">
                                            <?php if ($status == 'pending'): ?>
                                                <a href="<?php echo $base_path; ?>reservasi/edit_reservation.php?id=<?php echo $row['id']; ?>" class="action-btn edit" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($status == 'pending' && empty($row['bukti_transfer'])): ?>
                                                <a href="<?php echo $base_path; ?>reservasi/upload_payment.php?id=<?php echo $row['id']; ?>" class="action-btn upload" title="Upload Bukti Bayar">
                                                    <i class="bi bi-upload"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?php echo $base_path; ?>reservasi/delete_reservation.php?id=<?php echo $row['id']; ?>" class="action-btn delete" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus reservasi ini?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                        <?php if ($status == 'pending' && empty($row['bukti_transfer']) && !empty($row['created_at'])): ?>
                                            <?php
                                            $created = new DateTime($row['created_at']);
                                            $deadline = clone $created;
                                            $deadline->modify('+24 hours');
                                            $now = new DateTime();
                                            $remaining = $now->diff($deadline);
                                            $is_expired = $now > $deadline;
                                            ?>
                                            <div class="reservation-deadline <?php echo $is_expired ? 'expired' : ''; ?>">
                                                <i class="bi bi-clock"></i>
                                                <?php if ($is_expired): ?>
                                                    <span>Batas upload telah lewat</span>
                                                <?php else: ?>
                                                    <span>Batas upload: <strong><?php echo $remaining->h; ?>j <?php echo $remaining->i; ?>m lagi</strong></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($status == 'pending' && !empty($row['bukti_transfer'])): ?>
                                            <div class="reservation-deadline uploaded">
                                                <i class="bi bi-check-circle"></i>
                                                <span>Bukti sudah diupload, menunggu review admin</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-calendar-x"></i>
                                    </div>
                                    <h3>Belum Ada Reservasi</h3>
                                    <p>Anda belum melakukan reservasi apapun. Mulai buat reservasi pertama Anda!</p>
                                    <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="btn-empty-action">
                                        <i class="bi bi-plus-circle"></i>
                                        Buat Reservasi Pertama
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Info -->
                <div class="content-sidebar">
                    <!-- Quick Actions -->
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2><i class="bi bi-lightning-charge"></i> Aksi Cepat</h2>
                        </div>
                        <div class="quick-actions">
                            <a href="<?php echo $base_path; ?>reservasi/reservasi.php" class="quick-action-item">
                                <div class="quick-action-icon green">
                                    <i class="bi bi-calendar-plus"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h4>Buat Reservasi</h4>
                                    <p>Booking fasilitas baru</p>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="<?php echo $base_path; ?>forum/index.php" class="quick-action-item">
                                <div class="quick-action-icon blue">
                                    <i class="bi bi-chat-square-text"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h4>Forum Diskusi</h4>
                                    <p>Bergabung dalam diskusi</p>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="<?php echo $base_path; ?>forum/post.php" class="quick-action-item">
                                <div class="quick-action-icon purple">
                                    <i class="bi bi-pencil-square"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h4>Buat Postingan</h4>
                                    <p>Tulis postingan baru</p>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="<?php echo $base_path; ?>about.php" class="quick-action-item">
                                <div class="quick-action-icon orange">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <div class="quick-action-text">
                                    <h4>Info Fasilitas</h4>
                                    <p>Lihat detail fasilitas</p>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="content-card">
                        <div class="card-header-custom">
                            <h2><i class="bi bi-person-badge"></i> Info Akun</h2>
                        </div>
                        <div class="account-info">
                            <div class="info-item">
                                <span class="info-label">Username</span>
                                <span class="info-value"><?php echo htmlspecialchars($username); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nama Lengkap</span>
                                <span class="info-value"><?php echo htmlspecialchars($nama); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?php echo htmlspecialchars($user_data['email'] ?? '-'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Status</span>
                                <span class="info-value status-active">
                                    <i class="bi bi-circle-fill"></i> Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="help-card">
                        <div class="help-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3>Butuh Bantuan?</h3>
                        <p>Tim kami siap membantu Anda dengan pertanyaan seputar reservasi.</p>
                        <a href="<?php echo $base_path; ?>about.php#lokasi" class="help-link">
                            <i class="bi bi-telephone"></i>
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<style>
/* ===================================================
   PROFILE PAGE STYLES
   =================================================== */

.profile-page {
    background: #f8fafc;
    min-height: 100vh;
}

/* Profile Header */
.profile-header {
    position: relative;
    padding: 60px 0 100px;
    overflow: hidden;
}

.profile-header-bg {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #10b981 100%);
}

.profile-header-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.profile-header-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.profile-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-text {
    width: 120px;
    height: 120px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 800;
    color: #3b82f6;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    border: 4px solid rgba(255, 255, 255, 0.3);
}

.avatar-badge {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 32px;
    height: 32px;
    background: #10b981;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    border: 3px solid white;
}

.profile-info {
    flex: 1;
    color: white;
}

.profile-name {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 4px;
}

.profile-username {
    font-size: 1.1rem;
    opacity: 0.8;
    margin-bottom: 12px;
}

.profile-meta {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.profile-meta span {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.profile-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-profile-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-profile-action.primary {
    background: white;
    color: #1e3a8a;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.btn-profile-action.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

.btn-profile-action.secondary {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-profile-action.secondary:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Stats Section */
.profile-stats {
    margin-top: -50px;
    position: relative;
    z-index: 10;
    padding-bottom: 40px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.total {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #2563eb;
}

.stat-icon.pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #d97706;
}

.stat-icon.approved {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #059669;
}

.stat-icon.posts {
    background: linear-gradient(135deg, #e9d5ff, #d8b4fe);
    color: #7c3aed;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
}

.stat-label {
    font-size: 0.9rem;
    color: #64748b;
}

/* Content Section */
.profile-content {
    padding-bottom: 60px;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 30px;
    align-items: start;
}

.content-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-header-custom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
}

.card-header-custom h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.card-header-custom h2 i {
    color: #3b82f6;
}

.view-all-link {
    font-size: 0.9rem;
    color: #3b82f6;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.view-all-link:hover {
    gap: 10px;
    color: #2563eb;
}

/* Reservations List */
.reservations-list {
    padding: 8px;
}

.reservation-item {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding: 16px;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 8px;
}

.reservation-item:hover {
    background: #f8fafc;
}

.reservation-date {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.date-day {
    font-size: 1.5rem;
    font-weight: 800;
    line-height: 1;
}

.date-month {
    font-size: 0.75rem;
    text-transform: uppercase;
    opacity: 0.9;
}

.reservation-details {
    flex: 1;
    min-width: 0;
}

.reservation-details h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.reservation-period,
.reservation-price {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.reservation-period i,
.reservation-price i {
    font-size: 0.8rem;
}

.reservation-status {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    flex-shrink: 0;
}

.reservation-status.pending {
    background: #fef3c7;
    color: #d97706;
}

.reservation-status.approved {
    background: #d1fae5;
    color: #059669;
}

.reservation-status.rejected {
    background: #fee2e2;
    color: #dc2626;
}

.reservation-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.action-btn.edit {
    background: #dbeafe;
    color: #2563eb;
}

.action-btn.edit:hover {
    background: #2563eb;
    color: white;
}

.action-btn.upload {
    background: #d1fae5;
    color: #059669;
}

.action-btn.upload:hover {
    background: #059669;
    color: white;
}

.action-btn.delete {
    background: #fee2e2;
    color: #dc2626;
}

.action-btn.delete:hover {
    background: #dc2626;
    color: white;
}

/* Empty State */
.empty-state {
    padding: 60px 24px;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #f1f5f9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 2.5rem;
    color: #94a3b8;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 24px;
}

.btn-empty-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-empty-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    color: white;
}

/* Quick Actions */
.quick-actions {
    padding: 8px;
}

.quick-action-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 8px;
}

.quick-action-item:hover {
    background: #f8fafc;
}

.quick-action-item:hover .bi-chevron-right {
    transform: translateX(4px);
}

.quick-action-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.quick-action-icon.green {
    background: #d1fae5;
    color: #059669;
}

.quick-action-icon.blue {
    background: #dbeafe;
    color: #2563eb;
}

.quick-action-icon.purple {
    background: #e9d5ff;
    color: #7c3aed;
}

.quick-action-icon.orange {
    background: #fed7aa;
    color: #ea580c;
}

.quick-action-text {
    flex: 1;
}

.quick-action-text h4 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 2px;
}

.quick-action-text p {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0;
}

.quick-action-item > .bi-chevron-right {
    color: #94a3b8;
    transition: transform 0.3s ease;
}

/* Account Info */
.account-info {
    padding: 16px 24px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.9rem;
    color: #64748b;
}

.info-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
}

.info-value.status-active {
    color: #059669;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-value.status-active i {
    font-size: 0.5rem;
}

/* Help Card */
.help-card {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    border-radius: 20px;
    padding: 24px;
    text-align: center;
    color: white;
}

.help-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 1.5rem;
}

.help-card h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.help-card p {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 16px;
}

.help-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    color: #1e3a8a;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.help-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
    }

    .content-sidebar {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .help-card {
        grid-column: span 2;
    }
}

@media (max-width: 768px) {
    .profile-header {
        padding: 40px 0 80px;
    }

    .profile-header-content {
        flex-direction: column;
        text-align: center;
    }

    .profile-info {
        text-align: center;
    }

    .profile-meta {
        justify-content: center;
    }

    .profile-actions {
        justify-content: center;
        width: 100%;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .content-sidebar {
        grid-template-columns: 1fr;
    }

    .help-card {
        grid-column: span 1;
    }

    .reservation-item {
        flex-wrap: wrap;
    }

    .reservation-status {
        order: -1;
        width: 100%;
        justify-content: center;
        margin-bottom: 12px;
    }

    .reservation-actions {
        width: 100%;
        justify-content: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
    }
}

@media (max-width: 480px) {
    .profile-name {
        font-size: 1.5rem;
    }

    .avatar-text {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 16px;
    }

    .btn-profile-action {
        width: 100%;
        justify-content: center;
    }
}

/* Reservation Deadline Indicator */
.reservation-deadline {
    display: flex;
    align-items: center;
    gap: 6px;
    width: 100%;
    padding: 10px 14px;
    margin-top: -4px;
    border-radius: 8px;
    font-size: 0.8rem;
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
}

.reservation-deadline i {
    font-size: 0.85rem;
    flex-shrink: 0;
}

.reservation-deadline.expired {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}

.reservation-deadline.uploaded {
    background: #dcfce7;
    color: #16a34a;
    border-color: #bbf7d0;
}
</style>

<script>
function showEditProfile() {
    alert('Fitur edit profil akan segera hadir!');
}
</script>

</body>
</html>
<?php
$stmt_user->close();
$stmt_stats->close();
$stmt_reservasi->close();
$stmt_posts->close();
$conn->close();
?>
