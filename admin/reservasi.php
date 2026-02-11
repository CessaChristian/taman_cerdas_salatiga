<?php
// Global Config
include '../includes/security.php';
app_bootstrap_session();
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
    csrf_validate_or_abort("reservasi?error=csrf");

    $reservation_id = filter_var($_POST['reservation_id'], FILTER_VALIDATE_INT);
    $new_status = $_POST['new_status'];
    $allowed_status = ['pending', 'disetujui', 'ditolak'];

    if (!$reservation_id || !in_array($new_status, $allowed_status, true)) {
        header("Location: reservasi?error=invalid_input");
        exit();
    }

    $update_sql = "UPDATE reservasi SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $reservation_id);
    $update_stmt->execute();
    $update_stmt->close();
    header("Location: reservasi?updated=1");
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
$approved_count = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'disetujui'")->fetch_assoc()['total'];
$rejected_count = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'ditolak'")->fetch_assoc()['total'];
$pending_without_proof = $conn->query("SELECT COUNT(*) AS total FROM reservasi WHERE status = 'pending' AND (bukti_transfer IS NULL OR bukti_transfer = '')")->fetch_assoc()['total'];
$success_message = isset($_GET['updated']) ? 'Status reservasi berhasil diperbarui.' : '';
$csrf_token = csrf_token();

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
    <?php if (!empty($success_message)): ?>
    <div class="admin-alert success">
        <i class="bi bi-check-circle-fill"></i>
        <span><?php echo $success_message; ?></span>
    </div>
    <?php endif; ?>

    <div class="admin-mini-stats">
        <div class="mini-stat">
            <span class="mini-stat-label">Total</span>
            <strong><?php echo count($reservations); ?></strong>
        </div>
        <div class="mini-stat pending">
            <span class="mini-stat-label">Pending</span>
            <strong><?php echo $pending_count; ?></strong>
        </div>
        <div class="mini-stat approved">
            <span class="mini-stat-label">Disetujui</span>
            <strong><?php echo $approved_count; ?></strong>
        </div>
        <div class="mini-stat rejected">
            <span class="mini-stat-label">Ditolak</span>
            <strong><?php echo $rejected_count; ?></strong>
        </div>
        <div class="mini-stat warning">
            <span class="mini-stat-label">Belum Bukti Bayar</span>
            <strong><?php echo $pending_without_proof; ?></strong>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="bi bi-calendar-check"></i> Semua Reservasi</h2>
            <span class="header-count" id="visibleCount"><?php echo count($reservations); ?> data</span>
        </div>

        <?php if (!empty($reservations)): ?>
        <div class="admin-toolbar">
            <div class="toolbar-search">
                <i class="bi bi-search"></i>
                <input type="search" id="searchInput" placeholder="Cari ID, nama penyewa, atau username...">
            </div>
            <div class="toolbar-filters">
                <select id="statusFilter" class="toolbar-select">
                    <option value="all">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
                <select id="facilityFilter" class="toolbar-select">
                    <option value="all">Semua Fasilitas</option>
                    <option value="pendopo">Pendopo</option>
                    <option value="ruang_baca">Ruang Baca</option>
                    <option value="taman_bermain">Taman Bermain</option>
                </select>
                <select id="sortFilter" class="toolbar-select">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="start_asc">Mulai Terdekat</option>
                    <option value="start_desc">Mulai Terjauh</option>
                </select>
                <button type="button" id="resetFilters" class="toolbar-reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Penyewa</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="reservationsTableBody">
                    <?php foreach ($reservations as $row): ?>
                    <?php
                        $status = $row['status'];
                        $facilities = [];
                        if ($row['pendopo']) $facilities[] = 'Pendopo';
                        if ($row['ruang_baca']) $facilities[] = 'Ruang Baca';
                        if ($row['taman_bermain']) $facilities[] = 'Taman Bermain';
                        $facilities_key = [];
                        if ($row['pendopo']) $facilities_key[] = 'pendopo';
                        if ($row['ruang_baca']) $facilities_key[] = 'ruang_baca';
                        if ($row['taman_bermain']) $facilities_key[] = 'taman_bermain';
                        $search_text = strtolower('#' . $row['id'] . ' ' . $row['nama_penyewa'] . ' ' . $row['username']);
                    ?>
                    <tr
                        data-id="<?php echo $row['id']; ?>"
                        data-start="<?php echo $row['tanggal_mulai']; ?>"
                        data-status="<?php echo $status; ?>"
                        data-facilities="<?php echo htmlspecialchars(implode('|', $facilities_key)); ?>"
                        data-search="<?php echo htmlspecialchars($search_text); ?>"
                    >
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
                            <?php if (!empty($row['bukti_transfer'])): ?>
                                <span class="proof-chip yes">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Ada
                                </span>
                            <?php else: ?>
                                <span class="proof-chip no">
                                    <i class="bi bi-x-circle-fill"></i>
                                    Belum
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <?php if ($status === 'pending'): ?>
                                <form action="reservasi" method="POST" class="quick-action-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="new_status" value="disetujui">
                                    <button type="submit" class="btn-quick approve" title="Setujui cepat">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <form action="reservasi" method="POST" class="quick-action-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="new_status" value="ditolak">
                                    <button type="submit" class="btn-quick reject" title="Tolak cepat">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            <button type="button" class="btn-detail" onclick="openModal(<?php echo $row['id']; ?>)">
                                <i class="bi bi-eye"></i>
                                Detail
                            </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="emptyFilterState" class="admin-empty" style="display:none;">
            <i class="bi bi-funnel"></i>
            <h3>Tidak ada hasil</h3>
            <p>Coba ubah kata kunci atau reset filter.</p>
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

    const allowedStatus = ['pending', 'disetujui', 'ditolak'];
    const normalizedStatus = allowedStatus.includes(data.status) ? data.status : 'pending';
    const statusClass = normalizedStatus;
    const statusLabel = normalizedStatus.charAt(0).toUpperCase() + normalizedStatus.slice(1);
    const safeName = escapeHtml(data.nama_penyewa);
    const safeUsername = escapeHtml(data.username);

    let facilitiesHtml = data.facilities.map(f =>
        `<span class="modal-facility-tag"><i class="bi bi-check-circle-fill"></i> ${escapeHtml(f)}</span>`
    ).join('');

    let buktiHtml = '';
    if (data.bukti_transfer) {
        buktiHtml = `
            <div class="modal-section">
                <h4><i class="bi bi-image"></i> Bukti Transfer</h4>
                <div class="bukti-preview">
                    <img src="../reservasi/uploads/${data.bukti_transfer}" alt="Bukti Transfer" onclick="window.open(this.src, '_blank')">
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
                        <span class="info-value">${safeName}</span>
                    </div>
                    <div class="modal-info-row">
                        <span class="info-label">Username</span>
                        <span class="info-value">@${safeUsername}</span>
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
            <form action="reservasi" method="POST" class="modal-status-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="reservation_id" value="${data.id}">
                <div class="modal-status-options">
                    <label class="status-option">
                        <input type="radio" name="new_status" value="pending" ${normalizedStatus === 'pending' ? 'checked' : ''}>
                        <div class="status-option-card pending">
                            <i class="bi bi-hourglass-split"></i>
                            <span>Pending</span>
                        </div>
                    </label>
                    <label class="status-option">
                        <input type="radio" name="new_status" value="disetujui" ${normalizedStatus === 'disetujui' ? 'checked' : ''}>
                        <div class="status-option-card disetujui">
                            <i class="bi bi-check-circle"></i>
                            <span>Setujui</span>
                        </div>
                    </label>
                    <label class="status-option">
                        <input type="radio" name="new_status" value="ditolak" ${normalizedStatus === 'ditolak' ? 'checked' : ''}>
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

function escapeHtml(value) {
    const str = String(value ?? '');
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Tutup modal saat klik overlay
document.getElementById('modalOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Tutup modal saat tekan Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// Table filter/search/sort
const tableBody = document.getElementById('reservationsTableBody');
const rows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
const searchInput = document.getElementById('searchInput');
const statusFilter = document.getElementById('statusFilter');
const facilityFilter = document.getElementById('facilityFilter');
const sortFilter = document.getElementById('sortFilter');
const resetFilters = document.getElementById('resetFilters');
const visibleCount = document.getElementById('visibleCount');
const emptyFilterState = document.getElementById('emptyFilterState');

function sortRows(list, mode) {
    return list.sort((a, b) => {
        const idA = Number(a.dataset.id);
        const idB = Number(b.dataset.id);
        const startA = new Date(a.dataset.start);
        const startB = new Date(b.dataset.start);

        if (mode === 'oldest') return idA - idB;
        if (mode === 'start_asc') return startA - startB;
        if (mode === 'start_desc') return startB - startA;
        return idB - idA;
    });
}

function applyFilters() {
    const searchTerm = (searchInput?.value || '').trim().toLowerCase();
    const statusTerm = statusFilter?.value || 'all';
    const facilityTerm = facilityFilter?.value || 'all';
    const sortTerm = sortFilter?.value || 'latest';

    let filtered = rows.filter((row) => {
        const searchMatch = !searchTerm || row.dataset.search.includes(searchTerm);
        const statusMatch = statusTerm === 'all' || row.dataset.status === statusTerm;
        const facilityMatch = facilityTerm === 'all' || row.dataset.facilities.includes(facilityTerm);
        return searchMatch && statusMatch && facilityMatch;
    });

    filtered = sortRows(filtered, sortTerm);
    rows.forEach((row) => row.style.display = 'none');
    filtered.forEach((row) => {
        row.style.display = '';
        tableBody.appendChild(row);
    });

    if (visibleCount) {
        visibleCount.textContent = `${filtered.length} data`;
    }

    if (emptyFilterState) {
        emptyFilterState.style.display = filtered.length === 0 ? '' : 'none';
    }
}

if (searchInput) searchInput.addEventListener('input', applyFilters);
if (statusFilter) statusFilter.addEventListener('change', applyFilters);
if (facilityFilter) facilityFilter.addEventListener('change', applyFilters);
if (sortFilter) sortFilter.addEventListener('change', applyFilters);
if (resetFilters) {
    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = 'all';
        facilityFilter.value = 'all';
        sortFilter.value = 'latest';
        applyFilters();
    });
}
if (rows.length) applyFilters();
</script>

</body>
</html>
