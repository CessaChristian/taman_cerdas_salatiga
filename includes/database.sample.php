<?php
// ============================================
// DATABASE CONFIGURATION
// ============================================
// Salin file ini menjadi database.php dan sesuaikan kredensial
// cp database.sample.php database.php
// ============================================

date_default_timezone_set('Asia/Jakarta');

$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$dbname     = "taman_cerdas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Tidak dapat terhubung ke database. Silakan coba lagi nanti.");
}
?>
