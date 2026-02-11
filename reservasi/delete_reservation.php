<?php
include '../includes/security.php';
app_bootstrap_session();
include '../includes/database.php'; // Menggunakan koneksi database terpusat

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $return_to = $_POST['return_to'] ?? 'user';
    $redirect_target = $return_to === 'data' ? 'data' : '../user/index.php';

    csrf_validate_or_abort($redirect_target . "?status=csrf_error");

    $reservation_id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    if (!$reservation_id) {
        header("Location: " . $redirect_target);
        exit();
    }

    $username = $_SESSION['username']; // Hanya izinkan user menghapus reservasi miliknya sendiri

    $sql = "DELETE FROM reservasi WHERE id = ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $reservation_id, $username);

    if ($stmt->execute()) {
        header("Location: " . $redirect_target . "?status=delete_success");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../user/index.php");
}
exit();
?>
