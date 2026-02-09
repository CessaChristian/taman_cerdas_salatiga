<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Dashboard';

// Keamanan: Pastikan hanya admin yang bisa mengakses halaman ini
if (!$isLoggedIn || $_SESSION['level'] !== 'admin') {
    header("Location: " . $base_path . "login.php");
    exit();
}

include '../includes/database.php';

// Ambil data statistik untuk dashboard
$total_reservasi = $conn->query("SELECT COUNT(*) AS total FROM reservasi")->fetch_assoc()['total'];
$pending_reservasi = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'pending'")->fetch_assoc()['total'];
$disetujui_reservasi = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'disetujui'")->fetch_assoc()['total'];
$ditolak_reservasi = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'ditolak'")->fetch_assoc()['total'];
$total_user = $conn->query("SELECT COUNT(*) AS total FROM user WHERE level = 'user'")->fetch_assoc()['total'];
$total_post = $conn->query("SELECT COUNT(*) AS total FROM post")->fetch_assoc()['total'];

// Reservasi terbaru untuk tabel
$recent_sql = "SELECT * FROM reservasi ORDER BY id DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);

// Pending count untuk sidebar badge
$pending_count = $pending_reservasi;

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Admin Taman Cerdas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/admin.css">
</head>
<body class="admin-body">

<?php include '../includes/admin_header.php'; ?>

<main class="admin-content">
    <!-- Stats -->
    <div class="stats-row">
        <div class="admin-stat-card">
            <div class="stat-icon-box blue">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $total_reservasi; ?></h3>
                <p>Total Reservasi</p>
                <a href="reservasi.php" class="stat-link">Lihat detail →</a>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-icon-box yellow">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $pending_reservasi; ?></h3>
                <p>Pending</p>
                <a href="reservasi.php" class="stat-link">Lihat detail →</a>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-icon-box green">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $disetujui_reservasi; ?></h3>
                <p>Disetujui</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-icon-box red">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $ditolak_reservasi; ?></h3>
                <p>Ditolak</p>
            </div>
        </div>
    </div>

    <div class="stats-row" style="grid-template-columns: repeat(2, 1fr);">
        <div class="admin-stat-card">
            <div class="stat-icon-box purple">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $total_user; ?></h3>
                <p>Jumlah Pengguna</p>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-icon-box blue">
                <i class="bi bi-chat-dots"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo $total_post; ?></h3>
                <p>Total Post Forum</p>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="bi bi-clock-history"></i> Reservasi Terbaru</h2>
            <a href="reservasi.php" class="stat-link">Lihat semua →</a>
        </div>
        <?php if ($recent_result && $recent_result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Penyewa</th>
                        <th>Tanggal</th>
                        <th>Fasilitas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $recent_result->fetch_assoc()): ?>
                    <?php
                        $facilities = [];
                        if ($row['pendopo']) $facilities[] = 'Pendopo';
                        if ($row['ruang_baca']) $facilities[] = 'R. Baca';
                        if ($row['taman_bermain']) $facilities[] = 'T. Bermain';

                        $status = $row['status'];
                        $status_class = $status;
                    ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_penyewa']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></td>
                        <td>
                            <div class="facility-tags">
                                <?php foreach($facilities as $f): ?>
                                    <span class="facility-tag"><?php echo $f; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="admin-empty">
            <i class="bi bi-calendar-x"></i>
            <h3>Belum Ada Reservasi</h3>
            <p>Data reservasi akan muncul di sini.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
