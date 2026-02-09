<?php
// Admin Header/Sidebar Component
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$adminName = $_SESSION['nama'] ?? 'Admin';
$base_path = $base_path ?? '../';

// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <span class="brand-icon">🌳</span>
        <span class="brand-text">Taman Cerdas</span>
        <span class="brand-badge">Admin</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-title">Menu Utama</span>
            <a href="<?php echo $base_path; ?>admin/index.php" class="nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo $base_path; ?>admin/reservasi.php" class="nav-item <?php echo $current_page === 'reservasi.php' ? 'active' : ''; ?>">
                <i class="bi bi-calendar-check"></i>
                <span>Reservasi</span>
                <?php if (isset($pending_count) && $pending_count > 0): ?>
                    <span class="nav-badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">Lainnya</span>
            <a href="<?php echo $base_path; ?>index.php" class="nav-item" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat Situs</span>
            </a>
            <a href="<?php echo $base_path; ?>forum/index.php" class="nav-item" target="_blank">
                <i class="bi bi-chat-square-text"></i>
                <span>Forum</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="admin-profile">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($adminName, 0, 1)); ?>
            </div>
            <div class="admin-info">
                <strong><?php echo htmlspecialchars($adminName); ?></strong>
                <span>Administrator</span>
            </div>
        </div>
        <a href="<?php echo $base_path; ?>logout.php" class="btn-admin-logout">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>

<!-- Admin Topbar -->
<header class="admin-topbar" id="adminTopbar">
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
    <div class="topbar-title">
        <h1><?php echo $page_title ?? 'Admin Panel'; ?></h1>
    </div>
    <div class="topbar-right">
        <span class="topbar-greeting">Halo, <?php echo htmlspecialchars($adminName); ?></span>
        <div class="topbar-avatar">
            <?php echo strtoupper(substr($adminName, 0, 1)); ?>
        </div>
    </div>
</header>

<!-- Mobile Overlay -->
<div class="admin-overlay" id="adminOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('adminOverlay');

    toggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
});
</script>
