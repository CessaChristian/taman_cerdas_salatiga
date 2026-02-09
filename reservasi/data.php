<?php
// Global Config
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$base_path = '../';
$page_title = 'Reservasi Saya - Taman Cerdas';

// Page-specific includes
include '../includes/database.php';

if (!$isLoggedIn) {
    header("Location: " . $base_path . "login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Global Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/components/user-dropdown.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<main>
    <section class="section-padding">
        <div class="container">
            <h1 class="fw-bold mb-4">Daftar Reservasi Saya</h1>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Penyewa</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $username = $_SESSION['username'];
                                $sql = "SELECT * FROM reservasi WHERE username = ? ORDER BY tanggal_mulai DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("s", $username);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_penyewa']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])); ?></td>
                                        <td><?php echo date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                                        <td>
                                            <?php
                                            $status = $row['status'];
                                            $badge_class = 'bg-secondary';
                                            if ($status == 'disetujui') $badge_class = 'bg-success';
                                            if ($status == 'pending') $badge_class = 'bg-warning text-dark';
                                            if ($status == 'ditolak') $badge_class = 'bg-danger';
                                            echo "<span class='badge {$badge_class}'>" . ucfirst($status) . "</span>";
                                            ?>
                                        </td>
                                        <td>
                                            <a href="edit_reservation.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="delete_reservation.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus reservasi ini?');">Hapus</a>
                                            <?php if ($status == 'disetujui' && empty($row['bukti_transfer'])): ?>
                                                <a href="upload_payment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">Upload Bukti Bayar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Anda belum memiliki reservasi.</td></tr>";
                                }
                                $stmt->close();
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
