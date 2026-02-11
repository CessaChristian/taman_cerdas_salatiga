<?php
// Global Config
include '../includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Buat Postingan Baru - Forum';

// Page-specific includes
include '../includes/database.php';

// Jika pengguna belum login, arahkan ke halaman login
if (!$isLoggedIn) {
    header("Location: " . $base_path . "login.php");
    exit();
}

$username = $_SESSION['username'];
$userName = $_SESSION['nama'];
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
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/forum.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<main>
    <!-- Page Header -->
    <section class="forum-page-header">
        <div class="container">
            <div class="forum-page-header-content">
                <span class="page-badge">Forum</span>
                <h1>Buat Postingan Baru</h1>
                <p>Bagikan pikiran, pertanyaan, atau informasi dengan komunitas</p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="index.php">Forum</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Buat Postingan</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="forum-form-section">
        <div class="container">
            <div class="forum-create-layout">
                <!-- Main Content -->
                <div class="forum-create-main">
                    <div class="forum-create-card">
                        <div class="forum-create-card-header">
                            <div class="card-header-icon">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h2>Tulis Postingan</h2>
                                <p>Isi judul dan konten postingan Anda</p>
                            </div>
                        </div>

                        <form action="process_post.php" method="POST" id="postForm" class="forum-create-form">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <!-- Title -->
                            <div class="form-field">
                                <label for="postTitle" class="form-field-label">
                                    <i class="bi bi-type-h1"></i>
                                    Judul Postingan
                                </label>
                                <input
                                    type="text"
                                    id="postTitle"
                                    name="postTitle"
                                    class="form-field-input"
                                    placeholder="Tulis judul yang menarik..."
                                    maxlength="255"
                                    required
                                >
                                <div class="form-field-hint">
                                    <span id="titleCount">0</span>/255 karakter
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="form-field">
                                <label for="postContent" class="form-field-label">
                                    <i class="bi bi-text-paragraph"></i>
                                    Isi Postingan
                                </label>
                                <div class="editor-toolbar">
                                    <button type="button" class="toolbar-btn" onclick="insertFormat('bold')" title="Bold">
                                        <i class="bi bi-type-bold"></i>
                                    </button>
                                    <button type="button" class="toolbar-btn" onclick="insertFormat('italic')" title="Italic">
                                        <i class="bi bi-type-italic"></i>
                                    </button>
                                    <div class="toolbar-divider"></div>
                                    <button type="button" class="toolbar-btn" onclick="insertFormat('list')" title="List">
                                        <i class="bi bi-list-ul"></i>
                                    </button>
                                    <button type="button" class="toolbar-btn" onclick="insertFormat('quote')" title="Quote">
                                        <i class="bi bi-quote"></i>
                                    </button>
                                </div>
                                <textarea
                                    id="postContent"
                                    name="postContent"
                                    class="form-field-textarea"
                                    placeholder="Tulis isi postingan Anda di sini..."
                                    rows="12"
                                    required
                                ></textarea>
                                <div class="form-field-hint">
                                    <span id="contentCount">0</span> karakter
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="forum-create-actions">
                                <a href="index.php" class="btn-forum-cancel">
                                    <i class="bi bi-arrow-left"></i>
                                    Kembali
                                </a>
                                <button type="submit" class="btn-forum-submit" id="btnSubmit">
                                    <i class="bi bi-send"></i>
                                    Kirim Postingan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="forum-create-sidebar">
                    <!-- Author Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-person-circle"></i>
                            <h3>Posting Sebagai</h3>
                        </div>
                        <div class="author-info">
                            <div class="author-avatar">
                                <?php echo strtoupper(substr($userName, 0, 1)); ?>
                            </div>
                            <div class="author-details">
                                <strong><?php echo htmlspecialchars($userName); ?></strong>
                                <span>@<?php echo htmlspecialchars($username); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-info-circle"></i>
                            <h3>Panduan Posting</h3>
                        </div>
                        <ul class="guidelines-list">
                            <li>
                                <i class="bi bi-check2"></i>
                                <span>Gunakan judul yang jelas dan deskriptif</span>
                            </li>
                            <li>
                                <i class="bi bi-check2"></i>
                                <span>Tulis konten yang informatif dan sopan</span>
                            </li>
                            <li>
                                <i class="bi bi-check2"></i>
                                <span>Hindari spam atau konten tidak pantas</span>
                            </li>
                            <li>
                                <i class="bi bi-check2"></i>
                                <span>Hormati pendapat dan privasi orang lain</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Preview Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-eye"></i>
                            <h3>Preview</h3>
                        </div>
                        <div class="post-preview">
                            <h4 class="preview-title" id="previewTitle">Judul postingan Anda...</h4>
                            <div class="preview-meta">
                                <span><i class="bi bi-person"></i> <?php echo htmlspecialchars($userName); ?></span>
                                <span><i class="bi bi-clock"></i> Baru saja</span>
                            </div>
                            <p class="preview-content" id="previewContent">Isi postingan akan muncul di sini...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<script>
// Character counters
const titleInput = document.getElementById('postTitle');
const contentInput = document.getElementById('postContent');
const titleCount = document.getElementById('titleCount');
const contentCount = document.getElementById('contentCount');
const previewTitle = document.getElementById('previewTitle');
const previewContent = document.getElementById('previewContent');

titleInput.addEventListener('input', function() {
    titleCount.textContent = this.value.length;
    previewTitle.textContent = this.value || 'Judul postingan Anda...';
    previewTitle.classList.toggle('placeholder', !this.value);
});

contentInput.addEventListener('input', function() {
    contentCount.textContent = this.value.length;
    const text = this.value.substring(0, 150);
    previewContent.textContent = text ? (text + (this.value.length > 150 ? '...' : '')) : 'Isi postingan akan muncul di sini...';
    previewContent.classList.toggle('placeholder', !this.value);
});

// Simple text formatting helpers
function insertFormat(type) {
    const textarea = contentInput;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end);
    let insert = '';

    switch(type) {
        case 'bold':
            insert = '**' + (selected || 'teks tebal') + '**';
            break;
        case 'italic':
            insert = '*' + (selected || 'teks miring') + '*';
            break;
        case 'list':
            insert = '\n- ' + (selected || 'item list');
            break;
        case 'quote':
            insert = '\n> ' + (selected || 'kutipan');
            break;
    }

    textarea.value = textarea.value.substring(0, start) + insert + textarea.value.substring(end);
    textarea.focus();
    textarea.selectionStart = start + insert.length;
    textarea.selectionEnd = start + insert.length;
    contentInput.dispatchEvent(new Event('input'));
}

// Submit loading state
document.getElementById('postForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Mengirim...';
});
</script>

</body>
</html>
