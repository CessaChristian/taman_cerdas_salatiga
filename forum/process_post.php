<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Gunakan koneksi database terpusat
include '../includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['postTitle'];
    $content = $_POST['postContent'];
    $username = $_SESSION['username'];

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO post (username, title, content) VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $title, $content);
        
        if ($stmt->execute()) {
            $new_post_id = $conn->insert_id;
            header("Location: post_details.php?id=" . $new_post_id);
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Judul dan isi postingan tidak boleh kosong.";
    }
}

$conn->close();
?>
