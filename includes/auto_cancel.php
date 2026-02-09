<?php
/**
 * Auto-cancel reservasi yang sudah lewat 24 jam tanpa bukti transfer.
 * Include file ini setelah database.php di halaman yang perlu pengecekan.
 */
if (isset($conn) && $conn instanceof mysqli) {
    $cancel_sql = "UPDATE reservasi SET status = 'ditolak' WHERE status = 'pending' AND (bukti_transfer IS NULL OR bukti_transfer = '') AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $conn->query($cancel_sql);
}
?>
