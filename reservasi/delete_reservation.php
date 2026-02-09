<?php
session_start();
include '../includes/database.php'; // Menggunakan koneksi database terpusat

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $reservation_id = $_GET['id'];
    $username = $_SESSION['username']; // Hanya izinkan user menghapus reservasi miliknya sendiri

    $sql = "DELETE FROM reservasi WHERE id = ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $reservation_id, $username);

    if ($stmt->execute()) {
        header("Location: data.php"); // Kembali ke daftar reservasi
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: data.php");
}
exit();
?>