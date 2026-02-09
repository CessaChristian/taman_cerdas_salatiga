<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../includes/database.php';

// Deteksi apakah request dari AJAX (event.php) atau form biasa (reservasi.php)
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startDate = $_POST['startDate'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $fasilitas = $_POST['fasilitas'] ?? [];

    // Validasi input
    if (empty($startDate) || empty($endDate)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => 'Tanggal mulai dan selesai harus diisi.']);
            exit;
        }
        echo "Tanggal mulai dan selesai harus diisi.";
        $conn->close();
        exit;
    }

    if (empty($fasilitas)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => 'Pilih minimal satu fasilitas.']);
            exit;
        }
        echo "Pilih minimal satu fasilitas.";
        $conn->close();
        exit;
    }

    // Validasi tanggal
    if ($startDate > $endDate) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => 'Tanggal mulai tidak boleh lebih dari tanggal selesai.']);
            exit;
        }
        echo "Tanggal mulai tidak boleh lebih dari tanggal selesai.";
        $conn->close();
        exit;
    }

    $today = date('Y-m-d');
    if ($startDate < $today) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => 'Tanggal mulai tidak boleh kurang dari hari ini.']);
            exit;
        }
        echo "Tanggal mulai tidak boleh kurang dari hari ini.";
        $conn->close();
        exit;
    }

    $pendopo = in_array('pendopo', $fasilitas) ? 1 : 0;
    $ruangBaca = in_array('ruang_baca', $fasilitas) ? 1 : 0;
    $tamanBermain = in_array('taman_bermain', $fasilitas) ? 1 : 0;

    $isAvailable = true;
    $conflict_facilities = [];
    $available_facilities = [];

    // Harga per hari
    $price_pendopo = 50000;
    $price_ruangBaca = 60000;
    $price_tamanBermain = 45000;
    $total_bayar = 0;

    // Hitung durasi
    $datetime1 = new DateTime($startDate);
    $datetime2 = new DateTime($endDate);
    $interval = $datetime1->diff($datetime2);
    $duration_days = $interval->days + 1;

    // Cek ketersediaan Pendopo
    if ($pendopo) {
        $check_sql = "SELECT COUNT(*) FROM reservasi WHERE pendopo = 1 AND status NOT IN ('ditolak', 'dibatalkan') AND
                      ((tanggal_mulai <= ? AND tanggal_selesai >= ?) OR
                       (tanggal_mulai >= ? AND tanggal_mulai <= ?))";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ssss", $endDate, $startDate, $startDate, $endDate);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $isAvailable = false;
            $conflict_facilities[] = "Pendopo";
        } else {
            $total_bayar += ($price_pendopo * $duration_days);
            $available_facilities[] = ['nama' => 'Pendopo', 'harga' => $price_pendopo * $duration_days];
        }
    }

    // Cek ketersediaan Ruang Baca
    if ($ruangBaca) {
        $check_sql = "SELECT COUNT(*) FROM reservasi WHERE ruang_baca = 1 AND status NOT IN ('ditolak', 'dibatalkan') AND
                      ((tanggal_mulai <= ? AND tanggal_selesai >= ?) OR
                       (tanggal_mulai >= ? AND tanggal_mulai <= ?))";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ssss", $endDate, $startDate, $startDate, $endDate);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $isAvailable = false;
            $conflict_facilities[] = "Ruang Baca";
        } else {
            $total_bayar += ($price_ruangBaca * $duration_days);
            $available_facilities[] = ['nama' => 'Ruang Baca', 'harga' => $price_ruangBaca * $duration_days];
        }
    }

    // Cek ketersediaan Taman Bermain
    if ($tamanBermain) {
        $check_sql = "SELECT COUNT(*) FROM reservasi WHERE taman_bermain = 1 AND status NOT IN ('ditolak', 'dibatalkan') AND
                      ((tanggal_mulai <= ? AND tanggal_selesai >= ?) OR
                       (tanggal_mulai >= ? AND tanggal_mulai <= ?))";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ssss", $endDate, $startDate, $startDate, $endDate);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $isAvailable = false;
            $conflict_facilities[] = "Taman Bermain";
        } else {
            $total_bayar += ($price_tamanBermain * $duration_days);
            $available_facilities[] = ['nama' => 'Taman Bermain', 'harga' => $price_tamanBermain * $duration_days];
        }
    }

    // Response
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => false,
            'available' => $isAvailable,
            'total_bayar' => $total_bayar,
            'total_bayar_formatted' => 'Rp ' . number_format($total_bayar, 0, ',', '.'),
            'duration_days' => $duration_days,
            'conflict_facilities' => $conflict_facilities,
            'available_facilities' => $available_facilities,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        $conn->close();
        exit;
    }

    // Response untuk form biasa (backward compatibility dari reservasi.php)
    if ($isAvailable) {
        $name = $_POST['name'] ?? '';
        echo "Reservasi tersedia. Total biaya: Rp " . number_format($total_bayar, 0, ',', '.');
        $_SESSION['temp_reservation'] = [
            'nama_penyewa' => $name,
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'pendopo' => $pendopo,
            'ruang_baca' => $ruangBaca,
            'taman_bermain' => $tamanBermain,
            'total_bayar' => $total_bayar
        ];
    } else {
        echo "Reservasi tidak tersedia untuk fasilitas: " . implode(", ", $conflict_facilities);
    }
} else {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => 'Permintaan tidak valid.']);
        exit;
    }
    echo "Permintaan tidak valid.";
}

$conn->close();
?>
