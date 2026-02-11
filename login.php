<?php
// Global Config
include 'includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = './';
$page_title = 'Login - Taman Cerdas Salatiga';

// Page-specific includes
include 'includes/database.php';

$error_message = '';
$register_error = '';
$register_success = '';

// Jika pengguna sudah login, arahkan ke halaman yang sesuai
if (isset($_SESSION['username'])) {
    if ($_SESSION['level'] == 'admin') {
        header("Location: admin/reservasi.php");
    } else {
        header("Location: user/index.php");
    }
    exit();
}

// Handle login POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    csrf_validate_or_abort("login.php");

    $user = $_POST['username'];
    $pass = $_POST['password'];
    $rateLimit = app_login_rate_limit_check($user);

    if (!$rateLimit['allowed']) {
        $retryMinutes = max(1, (int) ceil(($rateLimit['retry_after'] ?? 60) / 60));
        $error_message = "Terlalu banyak percobaan login. Coba lagi dalam {$retryMinutes} menit.";
    } else {

        $sql = "SELECT user.username, user.password, user.level, data_user.nama
                FROM user
                JOIN data_user ON user.username = data_user.username
                WHERE user.username = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if (password_verify($pass, $hashed_password)) {
                app_login_rate_limit_clear($user);
                session_regenerate_id(true);
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['level'] = $row['level'];

                if ($row['level'] == 'admin') {
                    header("Location: admin/reservasi.php");
                } else {
                    header("Location: user/index.php");
                }
                exit();
            } else {
                app_login_rate_limit_record_failure($user);
                $error_message = "Username atau Password yang Anda masukkan salah.";
            }
        } else {
            app_login_rate_limit_record_failure($user);
            $error_message = "Username atau Password yang Anda masukkan salah.";
        }
        $stmt->close();
        $conn->close();
    }
}

// Handle redirect params from register.php
if (isset($_GET['register.php']) && $_GET['register.php'] === 'success') {
    $register_success = "Registrasi berhasil! Silakan login dengan akun baru Anda.";
}
if (isset($_GET['register_error'])) {
    $register_error = htmlspecialchars($_GET['register_error']);
}

// Determine initial mode
$initial_mode = (isset($_GET['mode']) && $_GET['mode'] === 'register.php') || !empty($register_error) ? 'register.php' : 'login.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/auth.css">
</head>
<body class="auth-body">

<div class="auth-container<?php echo $initial_mode === 'register.php' ? ' register-mode' : ''; ?>" id="authContainer">

    <!-- ============================================
         FORM PANELS (behind the overlay)
         ============================================ -->

    <!-- LOGIN FORM — sits on the RIGHT half -->
    <div class="auth-panel auth-panel-login">
        <div class="auth-panel-inner">
            <div class="auth-mobile-brand">
                <div class="auth-brand-icon">🌳</div>
                <span class="auth-brand-text">Taman Cerdas</span>
            </div>

            <a href="<?php echo $base_path; ?>index.php" class="auth-back-link">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Beranda
            </a>

            <div class="auth-form-header">
                <h1>Selamat Datang</h1>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <?php if (!empty($error_message)): ?>
            <div class="auth-alert error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span><?php echo $error_message; ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($register_success)): ?>
            <div class="auth-alert success">
                <i class="bi bi-check-circle-fill"></i>
                <span><?php echo $register_success; ?></span>
            </div>
            <?php endif; ?>

            <form action="<?php echo $base_path; ?>login.php" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="auth-input-group">
                    <label for="login-username">Username</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" class="auth-input" id="login-username" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label for="login-password">Password</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="auth-input" id="login-password" name="password" placeholder="Masukkan password" required>
                        <button type="button" class="btn-password-toggle" onclick="togglePassword('login-password', 'loginToggleIcon')">
                            <i class="bi bi-eye" id="loginToggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Masuk</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

                <div class="auth-divider"><span>atau</span></div>

                <p class="auth-form-footer">
                    Belum punya akun? <a href="#" onclick="switchToRegister(event)">Daftar sekarang</a>
                </p>
            </form>
        </div>
    </div>

    <!-- REGISTER FORM — sits on the LEFT half -->
    <div class="auth-panel auth-panel-register">
        <div class="auth-panel-inner">
            <div class="auth-mobile-brand">
                <div class="auth-brand-icon">🌳</div>
                <span class="auth-brand-text">Taman Cerdas</span>
            </div>

            <a href="<?php echo $base_path; ?>index.php" class="auth-back-link">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Beranda
            </a>

            <div class="auth-form-header">
                <h1>Buat Akun Baru</h1>
                <p>Isi data di bawah untuk mendaftar</p>
            </div>

            <?php if (!empty($register_error)): ?>
            <div class="auth-alert error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span><?php echo $register_error; ?></span>
            </div>
            <?php endif; ?>

            <form action="<?php echo $base_path; ?>register.php" method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="auth-input-row">
                    <div class="auth-input-group">
                        <label for="reg-nama">Nama Lengkap</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="auth-input" id="reg-nama" name="nama" placeholder="Nama lengkap" required>
                        </div>
                    </div>
                    <div class="auth-input-group">
                        <label for="reg-email">Email</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="auth-input" id="reg-email" name="email" placeholder="email@contoh.com" required>
                        </div>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label for="reg-username">Username</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-at input-icon"></i>
                        <input type="text" class="auth-input" id="reg-username" name="username" placeholder="Pilih username" required>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label for="reg-password">Password</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="auth-input" id="reg-password" name="password" placeholder="Buat password" required>
                        <button type="button" class="btn-password-toggle" onclick="togglePassword('reg-password', 'regToggleIcon')">
                            <i class="bi bi-eye" id="regToggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span>Daftar Sekarang</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

                <div class="auth-divider"><span>atau</span></div>

                <p class="auth-form-footer">
                    Sudah punya akun? <a href="#" onclick="switchToLogin(event)">Masuk di sini</a>
                </p>
            </form>
        </div>
    </div>

    <!-- ============================================
         SLIDING OVERLAY (the image panel)
         ============================================ -->
    <div class="auth-overlay" id="authOverlay">
        <img src="<?php echo $base_path; ?>assets/images/Taman.jpg" alt="Taman Cerdas Salatiga" class="auth-overlay-img">
        <div class="auth-overlay-shade"></div>

        <!-- Brand Logo -->
        <div class="auth-overlay-brand">
            <div class="auth-brand-icon">🌳</div>
            <span class="auth-brand-text">Taman Cerdas</span>
        </div>

        <!-- Login overlay content (visible when overlay is LEFT = login mode) -->
        <div class="auth-overlay-content auth-overlay-login">
            <h2>Jelajahi Taman Cerdas Salatiga</h2>
            <p>Ruang publik edukatif untuk belajar, bermain, dan berkumpul bersama keluarga di jantung Kota Salatiga.</p>
            <div class="auth-overlay-features">
                <span class="auth-overlay-feature"><i class="bi bi-building"></i> Pendopo</span>
                <span class="auth-overlay-feature"><i class="bi bi-book"></i> Ruang Baca</span>
                <span class="auth-overlay-feature"><i class="bi bi-tree"></i> Taman Bermain</span>
            </div>
        </div>

        <!-- Register overlay content (visible when overlay is RIGHT = register mode) -->
        <div class="auth-overlay-content auth-overlay-register">
            <h2>Bergabung dengan Komunitas Kami</h2>
            <p>Daftar dan nikmati kemudahan reservasi fasilitas Taman Cerdas Salatiga secara online.</p>
            <div class="auth-overlay-features">
                <span class="auth-overlay-feature"><i class="bi bi-calendar-check"></i> Reservasi Online</span>
                <span class="auth-overlay-feature"><i class="bi bi-chat-dots"></i> Forum Diskusi</span>
                <span class="auth-overlay-feature"><i class="bi bi-shield-check"></i> Aman & Mudah</span>
            </div>
        </div>
    </div>

    <!-- Mobile Mode Toggle -->
    <div class="auth-mobile-tabs" id="mobileTabs">
        <button class="auth-mobile-tab active" data-mode="login.php" onclick="switchToLogin(event)">
            <i class="bi bi-box-arrow-in-right"></i> Masuk
        </button>
        <button class="auth-mobile-tab" data-mode="register.php" onclick="switchToRegister(event)">
            <i class="bi bi-person-plus"></i> Daftar
        </button>
    </div>

</div>

<script>
const container = document.getElementById('authContainer');

function switchToRegister(e) {
    if (e) e.preventDefault();
    container.classList.add('register-mode');
    updateMobileTabs('register.php');
}

function switchToLogin(e) {
    if (e) e.preventDefault();
    container.classList.remove('register-mode');
    updateMobileTabs('login.php');
}

function updateMobileTabs(mode) {
    document.querySelectorAll('.auth-mobile-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.mode === mode);
    });
}

function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Set initial mobile tabs state
<?php if ($initial_mode === 'register.php'): ?>
updateMobileTabs('register.php');
<?php endif; ?>
</script>

</body>
</html>
