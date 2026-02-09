<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';

include '../includes/database.php';

$postId = $_GET['id'] ?? 0;
if (!filter_var($postId, FILTER_VALIDATE_INT) || $postId <= 0) {
    header("Location: index.php");
    exit();
}

$sql_post = "SELECT * FROM post WHERE id = ?";
$stmt_post = $conn->prepare($sql_post);
$stmt_post->bind_param("i", $postId);
$stmt_post->execute();
$result_post = $stmt_post->get_result();

if ($result_post->num_rows == 0) {
    header("Location: index.php");
    exit();
}
$post = $result_post->fetch_assoc();
$page_title = htmlspecialchars($post['title']) . ' - Forum';

// Cek apakah post milik user yang login & masih bisa diedit (24 jam)
$canEdit = false;
if ($isLoggedIn && $post['username'] === $_SESSION['username']) {
    $created = new DateTime($post['created_at']);
    $deadline = clone $created;
    $deadline->modify('+24 hours');
    $now = new DateTime();
    $canEdit = $now < $deadline;
    if ($canEdit) {
        $editRemaining = $now->diff($deadline);
    }
}

// Time ago helper
function timeAgo($datetime) {
    $created = new DateTime($datetime);
    $now = new DateTime();
    $diff = $now->diff($created);
    if ($diff->days === 0 && $diff->h === 0) {
        return max(1, $diff->i) . ' menit lalu';
    } elseif ($diff->days === 0) {
        return $diff->h . ' jam lalu';
    } elseif ($diff->days === 1) {
        return 'Kemarin, ' . date('H:i', strtotime($datetime));
    } elseif ($diff->days < 7) {
        return $diff->days . ' hari lalu';
    } else {
        return date('d M Y, H:i', strtotime($datetime));
    }
}

$post_time_ago = timeAgo($post['created_at']);

// Get replies
$sql_replies = "SELECT * FROM replies WHERE post_id = ? ORDER BY created_at ASC";
$stmt_replies = $conn->prepare($sql_replies);
$stmt_replies->bind_param("i", $postId);
$stmt_replies->execute();
$result_replies = $stmt_replies->get_result();
$replies = [];
while ($r = $result_replies->fetch_assoc()) {
    $replies[] = $r;
}
$reply_count = count($replies);

$post_initial = strtoupper(substr($post['username'], 0, 1));
$current_user_initial = $isLoggedIn ? strtoupper(substr($_SESSION['nama'], 0, 1)) : '';
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
                <nav class="breadcrumb-nav">
                    <a href="<?php echo $base_path; ?>index.php">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="index.php">Forum</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Postingan</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Post Detail -->
    <section class="forum-detail-section">
        <div class="container">
            <div class="forum-detail-layout">
                <!-- Main Content -->
                <div class="forum-detail-main">
                    <!-- Post Card -->
                    <article class="detail-post-card">
                        <div class="detail-post-header">
                            <div class="detail-post-author">
                                <div class="detail-author-avatar"><?php echo $post_initial; ?></div>
                                <div class="detail-author-info">
                                    <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                    <span><?php echo $post_time_ago; ?></span>
                                </div>
                            </div>
                            <div class="detail-post-actions-top">
                                <?php if ($canEdit): ?>
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn-edit-post">
                                        <i class="bi bi-pencil"></i>
                                        Edit
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h1 class="detail-post-title"><?php echo htmlspecialchars($post['title']); ?></h1>

                        <?php if ($canEdit): ?>
                            <div class="edit-time-hint" style="margin-bottom: 20px; display: inline-flex;">
                                <i class="bi bi-clock"></i>
                                Bisa diedit <?php echo $editRemaining->h; ?>j <?php echo $editRemaining->i; ?>m lagi
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($post['updated_at']) && $post['updated_at'] !== $post['created_at']): ?>
                            <span class="post-edited-badge" style="margin-bottom: 20px; display: inline-flex;">
                                <i class="bi bi-pencil-square"></i> diedit pada <?php echo date('d M Y, H:i', strtotime($post['updated_at'])); ?>
                            </span>
                        <?php endif; ?>

                        <div class="detail-post-body">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>

                        <div class="detail-post-footer">
                            <span class="footer-stat">
                                <i class="bi bi-chat-dots"></i>
                                <?php echo $reply_count; ?> balasan
                            </span>
                            <span class="footer-stat">
                                <i class="bi bi-calendar3"></i>
                                <?php echo date('d M Y, H:i', strtotime($post['created_at'])); ?>
                            </span>
                            <?php if ($isLoggedIn): ?>
                                <button type="button" class="btn-reply-to" onclick="showMainReplyForm()">
                                    <i class="bi bi-reply"></i>
                                    Balas
                                </button>
                            <?php endif; ?>
                        </div>
                    </article>

                    <!-- Replies Section -->
                    <div class="detail-replies-section">
                        <div class="replies-header">
                            <h2>
                                <i class="bi bi-chat-dots"></i>
                                Balasan
                                <span class="replies-count-badge"><?php echo $reply_count; ?></span>
                            </h2>
                        </div>

                        <?php if ($reply_count > 0): ?>
                            <div class="replies-list">
                                <?php foreach ($replies as $reply):
                                    $reply_initial = strtoupper(substr($reply['username'], 0, 1));
                                    $is_author = ($reply['username'] === $post['username']);
                                    $r_time = timeAgo($reply['created_at']);
                                    $has_reply_to = !empty($reply['reply_to_username']);
                                ?>
                                    <div class="reply-item <?php echo $is_author ? 'is-author' : ''; ?>" id="reply-<?php echo $reply['id']; ?>">
                                        <div class="reply-item-avatar <?php echo $is_author ? 'author' : ''; ?>">
                                            <?php echo $reply_initial; ?>
                                        </div>
                                        <div class="reply-item-content">
                                            <div class="reply-item-header">
                                                <strong><?php echo htmlspecialchars($reply['username']); ?></strong>
                                                <?php if ($is_author): ?>
                                                    <span class="author-tag">Penulis</span>
                                                <?php endif; ?>
                                                <?php if ($has_reply_to): ?>
                                                    <span class="reply-to-indicator">
                                                        <i class="bi bi-reply-fill"></i>
                                                        <?php echo htmlspecialchars($reply['reply_to_username']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="reply-time"><?php echo $r_time; ?></span>
                                            </div>
                                            <div class="reply-item-body">
                                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                            </div>
                                            <?php if ($isLoggedIn): ?>
                                                <div class="reply-item-actions">
                                                    <button type="button" class="btn-reply-to" onclick="replyTo(<?php echo $reply['id']; ?>, '<?php echo htmlspecialchars($reply['username'], ENT_QUOTES); ?>')">
                                                        <i class="bi bi-reply"></i>
                                                        Balas
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                            <!-- Inline reply form (hidden by default) -->
                                            <div class="inline-reply-form" id="inline-reply-<?php echo $reply['id']; ?>" style="display: none;">
                                                <form action="process_reply.php" method="POST" onsubmit="handleInlineSubmit(this)">
                                                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                                    <input type="hidden" name="parent_id" value="<?php echo $reply['id']; ?>">
                                                    <input type="hidden" name="reply_to_username" value="<?php echo htmlspecialchars($reply['username']); ?>">
                                                    <div class="inline-reply-context">
                                                        <i class="bi bi-reply-fill"></i>
                                                        Membalas <strong><?php echo htmlspecialchars($reply['username']); ?></strong>
                                                        <button type="button" class="inline-reply-close" onclick="closeReplyForm(<?php echo $reply['id']; ?>)">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
                                                    <div class="inline-reply-input-row">
                                                        <div class="inline-reply-avatar"><?php echo $current_user_initial; ?></div>
                                                        <textarea name="replyContent" class="inline-reply-textarea" rows="2" required placeholder="Tulis balasan..."></textarea>
                                                    </div>
                                                    <div class="inline-reply-actions">
                                                        <button type="button" class="btn-inline-cancel" onclick="closeReplyForm(<?php echo $reply['id']; ?>)">Batal</button>
                                                        <button type="submit" class="btn-inline-submit">
                                                            <i class="bi bi-send"></i>
                                                            Kirim
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="replies-empty">
                                <i class="bi bi-chat-square"></i>
                                <p>Belum ada balasan. Jadilah yang pertama menanggapi!</p>
                            </div>
                        <?php endif; ?>

                        <!-- Main Reply Form -->
                        <?php if ($isLoggedIn): ?>
                            <div class="reply-form-card" id="mainReplyForm">
                                <div class="reply-form-header">
                                    <div class="reply-form-avatar">
                                        <?php echo $current_user_initial; ?>
                                    </div>
                                    <span>Balas sebagai <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></span>
                                </div>
                                <form action="process_reply.php" method="POST" id="replyForm">
                                    <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                    <textarea
                                        name="replyContent"
                                        class="reply-textarea"
                                        id="mainReplyTextarea"
                                        rows="4"
                                        required
                                        placeholder="Tulis balasan Anda..."
                                    ></textarea>
                                    <div class="reply-form-actions">
                                        <button type="submit" class="btn-reply-submit" id="btnReply">
                                            <i class="bi bi-send"></i>
                                            Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="reply-login-prompt">
                                <i class="bi bi-lock"></i>
                                <p><a href="<?php echo $base_path; ?>login.php">Login</a> untuk meninggalkan balasan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="forum-detail-sidebar">
                    <a href="index.php" class="sidebar-back-link">
                        <i class="bi bi-arrow-left"></i>
                        Kembali ke Forum
                    </a>

                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-person-circle"></i>
                            <h3>Penulis</h3>
                        </div>
                        <div class="author-info">
                            <div class="author-avatar"><?php echo $post_initial; ?></div>
                            <div class="author-details">
                                <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                                <span>Anggota Forum</span>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <div class="sidebar-card-header">
                            <i class="bi bi-info-circle"></i>
                            <h3>Info Postingan</h3>
                        </div>
                        <div class="post-info-list">
                            <div class="post-info-item">
                                <span class="post-info-label">Dibuat</span>
                                <span class="post-info-value"><?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                            </div>
                            <div class="post-info-item">
                                <span class="post-info-label">Waktu</span>
                                <span class="post-info-value"><?php echo date('H:i', strtotime($post['created_at'])); ?> WIB</span>
                            </div>
                            <div class="post-info-item">
                                <span class="post-info-label">Balasan</span>
                                <span class="post-info-value"><?php echo $reply_count; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
$stmt_post->close();
$stmt_replies->close();
$conn->close();
include '../includes/footer.php';
?>

<script>
let activeReplyForm = null;

// Reply to a specific reply
function replyTo(replyId, username) {
    // Close any open inline form
    if (activeReplyForm !== null) {
        document.getElementById('inline-reply-' + activeReplyForm).style.display = 'none';
    }

    const form = document.getElementById('inline-reply-' + replyId);
    form.style.display = 'block';
    form.querySelector('textarea').focus();
    activeReplyForm = replyId;

    // Smooth scroll to inline form
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Close inline reply form
function closeReplyForm(replyId) {
    document.getElementById('inline-reply-' + replyId).style.display = 'none';
    activeReplyForm = null;
}

// Scroll to and focus main reply form
function showMainReplyForm() {
    const form = document.getElementById('mainReplyForm');
    const textarea = document.getElementById('mainReplyTextarea');
    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => textarea.focus(), 400);
}

// Handle inline form submit loading
function handleInlineSubmit(form) {
    const btn = form.querySelector('.btn-inline-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i>';
}

// Main reply form submit loading
const replyForm = document.getElementById('replyForm');
if (replyForm) {
    replyForm.addEventListener('submit', function() {
        const btn = document.getElementById('btnReply');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Mengirim...';
    });
}

// Auto-scroll to new reply if hash present
if (window.location.hash) {
    const target = document.querySelector(window.location.hash);
    if (target) {
        setTimeout(() => {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            target.classList.add('reply-highlight');
            setTimeout(() => target.classList.remove('reply-highlight'), 2500);
        }, 300);
    }
}
</script>

</body>
</html>
