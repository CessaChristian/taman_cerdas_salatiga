<?php
// Global Config
include '../includes/security.php';
app_bootstrap_session();
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Forum - Taman Cerdas';

include '../includes/database.php';

// Get posts with reply count
$sql = "SELECT p.*, COUNT(r.id) as reply_count
        FROM post p
        LEFT JOIN replies r ON r.post_id = p.id
        GROUP BY p.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$total_posts = $result ? $result->num_rows : 0;
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
            <div class="forum-index-header">
                <div class="forum-page-header-content">
                    <span class="page-badge">Komunitas</span>
                    <h1>Forum Diskusi</h1>
                    <p>Tempat berbagi ide, pertanyaan, dan informasi seputar Taman Cerdas</p>
                </div>
                <a href="post.php" class="btn-new-post">
                    <i class="bi bi-plus-lg"></i>
                    Buat Postingan
                </a>
            </div>
        </div>
    </section>

    <!-- Forum Content -->
    <section class="forum-list-section">
        <div class="container">
            <!-- Stats Bar -->
            <div class="forum-stats-bar">
                <span class="forum-stats-count">
                    <i class="bi bi-chat-square-text"></i>
                    <strong><?php echo $total_posts; ?></strong> postingan
                </span>
            </div>

            <!-- Posts List -->
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="forum-posts-list">
                    <?php while($row = $result->fetch_assoc()):
                        // Time ago helper
                        $created = new DateTime($row['created_at']);
                        $now = new DateTime();
                        $diff = $now->diff($created);

                        if ($diff->days === 0 && $diff->h === 0) {
                            $time_ago = $diff->i . ' menit lalu';
                        } elseif ($diff->days === 0) {
                            $time_ago = $diff->h . ' jam lalu';
                        } elseif ($diff->days === 1) {
                            $time_ago = 'Kemarin';
                        } elseif ($diff->days < 7) {
                            $time_ago = $diff->days . ' hari lalu';
                        } else {
                            $time_ago = date('d M Y', strtotime($row['created_at']));
                        }

                        // Editable check
                        $canEditThis = false;
                        if ($isLoggedIn && $row['username'] === $_SESSION['username']) {
                            $deadline = clone $created;
                            $deadline->modify('+24 hours');
                            $canEditThis = $now < $deadline;
                        }

                        $initial = strtoupper(substr($row['username'], 0, 1));
                    ?>
                        <a href="post_details.php?id=<?php echo $row['id']; ?>" class="forum-post-item">
                            <div class="post-item-avatar">
                                <span><?php echo $initial; ?></span>
                            </div>
                            <div class="post-item-content">
                                <h3 class="post-item-title">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                    <?php if (!empty($row['updated_at']) && $row['updated_at'] !== $row['created_at']): ?>
                                        <span class="post-edited-badge"><i class="bi bi-pencil-square"></i> diedit</span>
                                    <?php endif; ?>
                                </h3>
                                <p class="post-item-excerpt"><?php echo htmlspecialchars(substr($row['content'], 0, 180)); ?>...</p>
                                <div class="post-item-meta">
                                    <span class="meta-author">
                                        <i class="bi bi-person"></i>
                                        <?php echo htmlspecialchars($row['username']); ?>
                                    </span>
                                    <span class="meta-time">
                                        <i class="bi bi-clock"></i>
                                        <?php echo $time_ago; ?>
                                    </span>
                                    <span class="meta-replies">
                                        <i class="bi bi-chat-dots"></i>
                                        <?php echo $row['reply_count']; ?> balasan
                                    </span>
                                </div>
                            </div>
                            <div class="post-item-actions" onclick="event.stopPropagation();">
                                <?php if ($canEditThis): ?>
                                    <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="btn-edit-post-sm" title="Edit" onclick="event.stopPropagation();">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                <?php endif; ?>
                                <div class="post-item-reply-count">
                                    <span><?php echo $row['reply_count']; ?></span>
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="forum-empty">
                    <div class="forum-empty-icon">
                        <i class="bi bi-chat-square-text"></i>
                    </div>
                    <h3>Belum Ada Postingan</h3>
                    <p>Forum masih sepi. Jadilah yang pertama memulai diskusi!</p>
                    <a href="post.php" class="btn-new-post">
                        <i class="bi bi-plus-lg"></i>
                        Buat Postingan Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
$conn->close();
include '../includes/footer.php';
?>

</body>
</html>
