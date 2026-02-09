<?php
session_start();
include '../includes/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION['temp_reservation'])) {
    header("Location: reservasi.php");
    exit();
}

$temp_data = $_SESSION['temp_reservation'];

// Pastikan semua data yang dibutuhkan ada
if (
    !isset($temp_data['nama_penyewa']) ||
    !isset($temp_data['tanggal_mulai']) ||
    !isset($temp_data['tanggal_selesai']) ||
    !isset($temp_data['pendopo']) ||
    !isset($temp_data['ruang_baca']) ||
    !isset($temp_data['taman_bermain']) ||
    !isset($temp_data['total_bayar'])
) {
    header("Location: reservasi.php?error=incomplete");
    exit();
}

$username = $_SESSION['username'];
$nama_penyewa = $temp_data['nama_penyewa'];
$tanggal_mulai = $temp_data['tanggal_mulai'];
$tanggal_selesai = $temp_data['tanggal_selesai'];
$pendopo = $temp_data['pendopo'];
$ruang_baca = $temp_data['ruang_baca'];
$taman_bermain = $temp_data['taman_bermain'];
$total_bayar = $temp_data['total_bayar'];
$status = 'pending';

// Cek apakah ada file upload (opsional)
$has_file = isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === UPLOAD_ERR_OK;

if ($has_file) {
    $file = $_FILES['bukti_transfer'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 2 * 1024 * 1024; // 2MB

    // Validasi tipe file
    if (!in_array($file['type'], $allowed_types)) {
        header("Location: reservasi.php?error=invalid_type");
        exit();
    }

    // Validasi ekstensi
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
        header("Location: reservasi.php?error=invalid_ext");
        exit();
    }

    // Validasi ukuran
    if ($file['size'] > $max_size) {
        header("Location: reservasi.php?error=too_large");
        exit();
    }
}

// INSERT reservasi
$sql = "INSERT INTO reservasi (username, nama_penyewa, tanggal_mulai, tanggal_selesai, status, pendopo, ruang_baca, taman_bermain, total_bayar, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiiii", $username, $nama_penyewa, $tanggal_mulai, $tanggal_selesai, $status, $pendopo, $ruang_baca, $taman_bermain, $total_bayar);

if ($stmt->execute()) {
    $reservation_id = $conn->insert_id;

    // Upload file jika ada
    if ($has_file) {
        $safe_username = preg_replace('/[^a-zA-Z0-9_-]/', '', $username);
        $new_filename = $reservation_id . '-' . $safe_username . '.' . $ext;
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $sql_update = "UPDATE reservasi SET bukti_transfer = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $new_filename, $reservation_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    unset($_SESSION['temp_reservation']);
    header("Location: ../user/index.php?status=success_reserve");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
exit();
?>
