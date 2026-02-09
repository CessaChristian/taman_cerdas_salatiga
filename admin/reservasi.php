<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Manajemen Reservasi';

// Keamanan: Pastikan hanya admin yang bisa mengakses halaman ini
if (!$isLoggedIn || $_SESSION['level'] !== 'admin') {
    header("Location: " . $base_path . "login.php");
    exit();
}

include '../includes/database.php';
include '../includes/auto_cancel.php';

// Logika untuk mengubah status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id']) && isset($_POST['new_status'])) {
    $reservation_id = $_POST['reservation_id'];
    $new_status = $_POST['new_status'];

    $update_sql = "UPDATE reservasi SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $reservation_id);
    $update_stmt->execute();
    $update_stmt->close();
    header("Location: reservasi.php");
    exit();
}

// Ambil semua reservasi
$sql = "SELECT * FROM reservasi ORDER BY id DESC";
$result = $conn->query($sql);

// Simpan data ke array supaya bisa dipakai untuk tabel dan modal
$reservations = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
}

// Pending count untuk sidebar badge
$pending_count = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'pending'")->fetch_assoc()['total'];

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
    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="bi bi-calendar-check"></i> Semua Reservasi</h2>
            <span class="header-count"><?php echo count($reservations); ?> data</span>
        </div>

        <?php if (!empty($reservations)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Penyewa</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $row): ?>
                    <?php
                        $status = $row['status'];
                        $facilities = [];
                        if ($row['pendopo']) $facilities[] = 'Pendopo';
                        if ($row['ruang_baca']) $facilities[] = 'Ruang Baca';
                        if ($row['taman_bermain']) $facilities[] = 'Taman Bermain';
                    ?>
                    <tr>
                        <td><span class="id-cell">#<?php echo $row['id']; ?></span></td>
                        <td>
                            <div class="penyewa-cell">
                                <strong><?php echo htmlspecialchars($row['nama_penyewa']); ?></strong>
                                <span>@<?php echo htmlspecialchars($row['username']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="date-cell">
                                <span><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></span>
                                <span class="date-separator">→</span>
                                <span><?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $status; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn-detail" onclick="openModal(<?php echo $row['id']; ?>)">
                                <i class="bi bi-eye"></i>
                                Detail
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="admin-empty">
            <i class="bi bi-calendar-x"></i>
            <h3>Belum Ada Reservasi</h3>
            <p>Belum ada data reservasi yang masuk.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Detail Modal / Lightbox -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-container" id="modalContainer">
        <div class="modal-header">
            <h3 id="modalTitle">Detail Reservasi</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Diisi via JavaScript -->
        </div>
    </div>
</div>

<!-- Data reservasi untuk JavaScript -->
<script>
const reservations = <?php echo json_encode(array_map(function($r) {
    $facilities = [];
    if ($r['pendopo']) $facilities[] = 'Pendopo';
    if ($r['ruang_baca']) $facilities[] = 'Ruang Baca';
    if ($r['taman_bermain']) $facilities[] = 'Taman Bermain';

    $datetime1 = new DateTime($r['tanggal_mulai']);
    $datetime2 = new DateTime($r['tanggal_selesai']);
    $duration = $datetime1->diff($datetime2)->days + 1;

    return [
        'id' => $r['id'],
        'username' => $r['username'],
        'nama_penyewa' => $r['nama_penyewa'],
        'tanggal_mulai' => date('d M Y', strtotime($r['tanggal_mulai'])),
        'tanggal_selesai' => date('d M Y', strtotime($r['tanggal_selesai'])),
        'duration' => $duration,
        'status' => $r['status'],
        'facilities' => $facilities,
        'pendopo' => (int)$r['pendopo'],
        'ruang_baca' => (int)$r['ruang_baca'],
        'taman_bermain' => (int)$r['taman_bermain'],
        'total_bayar' => (int)$r['total_bayar'],
        'total_bayar_formatted' => 'Rp ' . number_format($r['total_bayar'], 0, ',', '.'),
        'bukti_transfer' => $r['bukti_transfer'] ?? ''
    ];
}, $reservations)); ?>;

function openModal(id) {
    const data = reservations.find(r => r.id == id);
    if (!data) return;

    const overlay = document.getElementById('modalOverlay');
    const title = document.getElementById('modalTitle');
    const body = document.getElementById('modalBody');

    title.textContent = 'Detail Reservasi #' + data.id;

    const statusClass = data.status;
    const statusLabel = data.status.charAt(0).toUpperCase() + data.status.slice(1);

    let facilitiesHtml = data.facilities.map(f =>
        `<span class="modal-facility-tag"><i class="bi bi-check-circle-fill"></i> ${f}</span>`
    ).join('');

    let buktiHtml = '';
    if (data.bukti_transfer) {
        buktiHtml = `
            <div class="modal-section">
                <h4><i class="bi bi-image"></i> Bukti Transfer</h4>
                <div class="bukti-preview">
                    <img src="../uploads/${data.bukti_transfer}" alt="Bukti Transfer" onclick="window.open(this.src, '_blank')">
                    <span class="bukti-hint">Klik untuk memperbesar</span>
                </div>
            </div>
        `;
    }

    body.innerHTML = `
        <div class="modal-status-bar ${statusClass}">
            <i class="bi bi-${statusClass === 'disetujui' ? 'check-circle-fill' : statusClass === 'ditolak' ? 'x-circle-fill' : 'hourglass-split'}"></i>
            <span>Status: ${statusLabel}</span>
        </div>

        <div class="modal-grid">
            <div class="modal-section">
                <h4><i class="bi bi-person"></i> Informasi Penyewa</h4>
                <div class="modal-info-list">
                    <div class="modal-info-row">
                        <span class="info-label">Nama Penyewa</span>
                        <span class="info-value">${data.nama_penyewa}</span>
                    </div>
                    <div class="modal-info-row">
                        <span class="info-label">Username</span>
                        <span class="info-value">@${data.username}</span>
                    </div>
                </div>
            </div>

            <div class="modal-section">
                <h4><i class="bi bi-calendar3"></i> Jadwal Reservasi</h4>
                <div class="modal-info-list">
                    <div class="modal-info-row">
                        <span class="info-label">Tanggal Mulai</span>
                        <span class="info-value">${data.tanggal_mulai}</span>
                    </div>
                    <div class="modal-info-row">
                        <span class="info-label">Tanggal Selesai</span>
                        <span class="info-value">${data.tanggal_selesai}</span>
                    </div>
                    <div class="modal-info-row">
                        <span class="info-label">Durasi</span>
                        <span class="info-value">${data.duration} hari</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-section">
            <h4><i class="bi bi-building"></i> Fasilitas yang Disewa</h4>
            <div class="modal-facilities">
                ${facilitiesHtml}
            </div>
        </div>

        <div class="modal-section">
            <h4><i class="bi bi-cash-stack"></i> Pembayaran</h4>
            <div class="modal-payment">
                <span class="payment-label">Total Biaya</span>
                <span class="payment-amount">${data.total_bayar_formatted}</span>
            </div>
        </div>

        ${buktiHtml}

        <div class="modal-section">
            <h4><i class="bi bi-gear"></i> Ubah Status</h4>
            <form action="reservasi.php" method="POST" class="modal-status-form">
                <input type="hidden" name="reservation_id" value="${data.id}">
                <div class="modal-status-options">
                    <label class="status-option">
                        <input type="radio" name="new_status" value="pending" ${data.status === 'pending' ? 'checked' : ''}>
                        <div class="status-option-card pending">
                            <i class="bi bi-hourglass-split"></i>
                            <span>Pending</span>
                        </div>
                    </label>
                    <label class="status-option">
                        <input type="radio" name="new_status" value="disetujui" ${data.status === 'disetujui' ? 'checked' : ''}>
                        <div class="status-option-card disetujui">
                            <i class="bi bi-check-circle"></i>
                            <span>Setujui</span>
                        </div>
                    </label>
                    <label class="status-option">
                        <input type="radio" name="new_status" value="ditolak" ${data.status === 'ditolak' ? 'checked' : ''}>
                        <div class="status-option-card ditolak">
                            <i class="bi bi-x-circle"></i>
                            <span>Tolak</span>
                        </div>
                    </label>
                </div>
                <button type="submit" class="btn-modal-save">
                    <i class="bi bi-check-lg"></i>
                    Simpan Perubahan
                </button>
            </form>
        </div>
    `;

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const overlay = document.getElementById('modalOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

// Tutup modal saat klik overlay
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Tutup modal saat tekan Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

</body>
</html>
