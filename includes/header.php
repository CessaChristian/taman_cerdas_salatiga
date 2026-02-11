<?php
// Header dinamis terpusat
include_once __DIR__ . '/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$userName = $_SESSION['nama'] ?? 'Pengguna';
$base_path = $base_path ?? './';
?>
<header>
    <nav class="navbar-custom">
        <div class="navbar-wrapper">
            <!-- Brand - Kiri -->
            <a class="navbar-brand-custom" href="<?php echo $base_path; ?>index.php">
                <span class="brand-icon">
                    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="20" fill="url(#brandGradient)" opacity="0.1"/>
                        <path d="M20 8C20 8 13 14 13 20C13 23.5 15 26 18 27.2V30C18 30.55 18.45 31 19 31H21C21.55 31 22 30.55 22 30V27.2C25 26 27 23.5 27 20C27 14 20 8 20 8Z" fill="url(#leafGradient)"/>
                        <path d="M20 8C20 8 13 14 13 20C13 23.5 15 26 18 27.2V30C18 30.55 18.45 31 19 31H20V8Z" fill="url(#leafGradientDark)" opacity="0.15"/>
                        <path d="M20 14V27" stroke="white" stroke-width="1.2" stroke-linecap="round" opacity="0.5"/>
                        <path d="M17 19.5C17 19.5 19 18 20 16" stroke="white" stroke-width="1" stroke-linecap="round" opacity="0.4"/>
                        <path d="M23 21C23 21 21 19.5 20 18" stroke="white" stroke-width="1" stroke-linecap="round" opacity="0.4"/>
                        <defs>
                            <linearGradient id="brandGradient" x1="0" y1="0" x2="40" y2="40">
                                <stop offset="0%" stop-color="#059669"/>
                                <stop offset="100%" stop-color="#2563eb"/>
                            </linearGradient>
                            <linearGradient id="leafGradient" x1="13" y1="8" x2="27" y2="31">
                                <stop offset="0%" stop-color="#10b981"/>
                                <stop offset="100%" stop-color="#059669"/>
                            </linearGradient>
                            <linearGradient id="leafGradientDark" x1="13" y1="8" x2="20" y2="31">
                                <stop offset="0%" stop-color="#064e3b"/>
                                <stop offset="100%" stop-color="#064e3b"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span class="brand-text">
                    <span class="brand-text-primary">Taman</span>
                    <span class="brand-text-accent">Cerdas</span>
                </span>
            </a>

            <!-- Menu Navigasi - Tengah (position absolute) -->
            <ul class="navbar-menu" id="navMenu">
                <li><a href="<?php echo $base_path; ?>index.php" class="nav-link-custom">Home</a></li>
                <li><a href="<?php echo $base_path; ?>about.php" class="nav-link-custom">About Us</a></li>
                <li><a href="<?php echo $base_path; ?>event.php" class="nav-link-custom">Event</a></li>
                <li><a href="<?php echo $base_path; ?>forum/index.php" class="nav-link-custom">Forum</a></li>
            </ul>

            <!-- Auth Section - Kanan -->
            <div class="navbar-auth">
                <?php if ($isLoggedIn): ?>
                    <!-- User Dropdown -->
                    <div class="user-menu">
                        <button class="user-menu-trigger" id="userMenuTrigger">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($userName, 0, 1)); ?>
                            </div>
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"></polyline>
                            </svg>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-header">
                                <div class="user-avatar large">
                                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                                </div>
                                <div class="user-info">
                                    <span class="user-fullname"><?php echo htmlspecialchars($userName); ?></span>
                                    <span class="user-role">Member</span>
                                </div>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo $base_path; ?>user/index.php" class="dropdown-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo $base_path; ?>logout.php" class="dropdown-item logout">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16,17 21,12 16,7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Login Button -->
                    <a href="<?php echo $base_path; ?>login.php" class="btn-login">
                        <span>Login</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10,17 15,12 10,7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Toggle -->
            <button class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileOverlay">
    <div class="mobile-menu-content">
        <ul class="mobile-nav-list">
            <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
            <li><a href="<?php echo $base_path; ?>about.php">About Us</a></li>
            <li><a href="<?php echo $base_path; ?>event.php">Event</a></li>
            <li><a href="<?php echo $base_path; ?>forum/index.php">Forum</a></li>
        </ul>
        <?php if ($isLoggedIn): ?>
            <div class="mobile-user-section">
                <div class="mobile-user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                    <span><?php echo htmlspecialchars($userName); ?></span>
                </div>
                <a href="<?php echo $base_path; ?>user/index.php" class="mobile-link">Profile</a>
                <a href="<?php echo $base_path; ?>logout.php" class="mobile-link logout">Logout</a>
            </div>
        <?php else: ?>
            <a href="<?php echo $base_path; ?>login.php" class="btn-login mobile">Login</a>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Dropdown Toggle
    const userTrigger = document.getElementById('userMenuTrigger');
    const userDropdown = document.getElementById('userDropdown');

    if (userTrigger && userDropdown) {
        userTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
            userTrigger.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!userTrigger.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
                userTrigger.classList.remove('active');
            }
        });
    }

    // Mobile Menu Toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (mobileToggle && mobileOverlay) {
        mobileToggle.addEventListener('click', function() {
            mobileToggle.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
            document.body.style.overflow = mobileOverlay.classList.contains('active') ? 'hidden' : '';
        });

        mobileOverlay.addEventListener('click', function(e) {
            if (e.target === mobileOverlay) {
                mobileToggle.classList.remove('active');
                mobileOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Navbar scroll effect
    const navbar = document.querySelector('.navbar-custom');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});
</script>
