<?php
// Global Config
include '../includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Edit Postingan - Forum';

include '../includes/database.php';

if (!$isLoggedIn) {
    header("Location: " . $base_path . "login.php");
    exit();
}

$postId = $_GET['id'] ?? 0;
if (!filter_var($postId, FILTER_VALIDATE_INT) || $postId <= 0) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$userName = $_SESSION['nama'];

// Ambil data post
$sql = "SELECT * FROM post WHERE id = ? AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $postId, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();
$stmt->close();

// Cek deadline 24 jam
$created = new DateTime($post['created_at']);
$deadline = clone $created;
$deadline->modify('+24 hours');
$now = new DateTime();

if ($now > $deadline) {
    header("Location: post_details.php?id=" . $postId);
    exit();
}

$remaining = $now->diff($deadline);

// Handle POST update
$edit_message = '';
$edit_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate_or_abort("post_details.php?id=" . $postId);

    $newTitle = trim($_POST['postTitle'] ?? '');
    $newContent = trim($_POST['postContent'] ?? '');

    if (empty($newTitle) || empty($newContent)) {
        $edit_message = 'Judul dan isi postingan tidak boleh kosong.';
        $edit_type = 'error';
    } else {
        $update_sql = "UPDATE post SET title = ?, content = ?, updated_at = NOW() WHERE id = ? AND username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssis", $newTitle, $newContent, $postId, $username);

        if ($update_stmt->execute()) {
            header("Location: post_details.php?id=" . $postId . "&edited=1");
            exit();
        } else {
            $edit_message = 'Gagal menyimpan perubahan. Silakan coba lagi.';
            $edit_type = 'error';
        }
        $update_stmt->close();
    }

    // Update local data for form
    $post['title'] = $newTitle;
    $post['content'] = $newContent;
}

$conn->close();
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
                <h1>Edit Postingan</h1>
                <p>Perbarui judul atau isi postingan Anda</p>
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="index.php">Forum</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="post_details.php?id=<?php echo $postId; ?>">Postingan</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Edit</span>
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
                            <div class="card-header-icon edit">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <h2>Edit Postingan</h2>
                                <p>Ubah judul atau isi postingan #<?php echo $postId; ?></p>
                            </div>
                        </div>

                        <?php if (!empty($edit_message)): ?>
                        <div class="forum-alert <?php echo $edit_type; ?>">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span><?php echo $edit_message; ?></span>
                        </div>
                        <?php endif; ?>

                        <form action="edit_post.php?id=<?php echo $postId; ?>" method="POST" id="editForm" class="forum-create-form">
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
                                    value="<?php echo htmlspecialchars($post['title']); ?>"
                                    required
                                >
                                <div class="form-field-hint">
                                    <span id="titleCount"><?php echo strlen($post['title']); ?></span>/255 karakter
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
                                ><?php echo htmlspecialchars($post['content']); ?></textarea>
                                <div class="form-field-hint">
                                    <span id="contentCount"><?php echo strlen($post['content']); ?></span> karakter
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="forum-create-actions">
                                <a href="post_details.php?id=<?php echo $postId; ?>" class="btn-forum-cancel">
                                    <i class="bi bi-arrow-left"></i>
                                    Batal
                                </a>
                                <button type="submit" class="btn-forum-submit" id="btnSubmit">
                                    <i class="bi bi-check-lg"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="forum-create-sidebar">
                    <!-- Edit Deadline -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-clock"></i>
                            <h3>Batas Edit</h3>
                        </div>
                        <div class="edit-deadline-info">
                            <div class="deadline-countdown">
                                <div class="countdown-item">
                                    <span class="countdown-number"><?php echo $remaining->h; ?></span>
                                    <span class="countdown-label">Jam</span>
                                </div>
                                <span class="countdown-separator">:</span>
                                <div class="countdown-item">
                                    <span class="countdown-number"><?php echo str_pad($remaining->i, 2, '0', STR_PAD_LEFT); ?></span>
                                    <span class="countdown-label">Menit</span>
                                </div>
                            </div>
                            <p class="deadline-note">Postingan hanya dapat diedit dalam 24 jam setelah dibuat.</p>
                        </div>
                    </div>

                    <!-- Author Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-person-circle"></i>
                            <h3>Author</h3>
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

                    <!-- Post Info -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-info-circle"></i>
                            <h3>Info Postingan</h3>
                        </div>
                        <div class="post-info-list">
                            <div class="post-info-item">
                                <span class="post-info-label">ID Postingan</span>
                                <span class="post-info-value">#<?php echo $postId; ?></span>
                            </div>
                            <div class="post-info-item">
                                <span class="post-info-label">Dibuat</span>
                                <span class="post-info-value"><?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?></span>
                            </div>
                            <div class="post-info-item">
                                <span class="post-info-label">Batas Edit</span>
                                <span class="post-info-value"><?php echo $deadline->format('d M Y, H:i'); ?></span>
                            </div>
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

titleInput.addEventListener('input', function() {
    titleCount.textContent = this.value.length;
});

contentInput.addEventListener('input', function() {
    contentCount.textContent = this.value.length;
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
document.getElementById('editForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Menyimpan...';
});
</script>

</body>
</html>
