<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];
    $content = trim($_POST['replyContent']);
    $username = $_SESSION['username'];
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $reply_to_username = !empty($_POST['reply_to_username']) ? $_POST['reply_to_username'] : null;

    if (!empty($content) && !empty($post_id)) {
        $sql = "INSERT INTO replies (post_id, username, content, parent_id, reply_to_username) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issis", $post_id, $username, $content, $parent_id, $reply_to_username);

        if ($stmt->execute()) {
            $new_reply_id = $conn->insert_id;
            header("Location: post_details.php?id=" . $post_id . "#reply-" . $new_reply_id);
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        header("Location: post_details.php?id=" . $post_id);
    }
}

$conn->close();
?>
