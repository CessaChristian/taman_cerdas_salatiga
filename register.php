<?php
// Global Config
include 'includes/security.php';
app_bootstrap_session();
$base_path = './';

include 'includes/database.php';

// Hanya proses POST, selain itu redirect ke login
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php?mode=register");
    exit();
}

csrf_validate_or_abort("login.php?mode=register&register_error=" . urlencode("Sesi formulir tidak valid. Silakan coba lagi."));

$nama = $_POST['nama'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$level = 'user';

// Cek apakah username sudah ada
$check_sql = "SELECT * FROM user WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $check_stmt->close();
    $conn->close();
    header("Location: login.php?mode=register&register_error=" . urlencode("Username sudah terdaftar."));
    exit();
}
$check_stmt->close();

// Cek apakah email sudah ada
$check_email_sql = "SELECT * FROM data_user WHERE email = ?";
$check_email_stmt = $conn->prepare($check_email_sql);
$check_email_stmt->bind_param("s", $email);
$check_email_stmt->execute();
$check_email_result = $check_email_stmt->get_result();

if ($check_email_result->num_rows > 0) {
    $check_email_stmt->close();
    $conn->close();
    header("Location: login.php?mode=register&register_error=" . urlencode("Email sudah terdaftar."));
    exit();
}
$check_email_stmt->close();

// Mulai transaksi
$conn->begin_transaction();
try {
    $sql_user = "INSERT INTO user (username, password, level) VALUES (?, ?, ?)";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("sss", $username, $hashed_password, $level);
    $stmt_user->execute();

    $sql_data_user = "INSERT INTO data_user (username, nama, email) VALUES (?, ?, ?)";
    $stmt_data_user = $conn->prepare($sql_data_user);
    $stmt_data_user->bind_param("sss", $username, $nama, $email);
    $stmt_data_user->execute();

    $conn->commit();

    $stmt_user->close();
    $stmt_data_user->close();
    $conn->close();

    header("Location: login.php?register=success");
    exit();
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    if (isset($stmt_user)) $stmt_user->close();
    if (isset($stmt_data_user)) $stmt_data_user->close();
    $conn->close();

    header("Location: login.php?mode=register&register_error=" . urlencode("Registrasi gagal. Silakan coba lagi."));
    exit();
}
?>
