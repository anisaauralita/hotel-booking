<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';


if (!isLoggedIn() || isAdmin()) {
    header("Location: ../../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$reservation_id = sanitize($_GET['id']);
$user_id = $_SESSION['user_id'];

// Dapatkan data reservasi
$sql = "SELECT res.id, res.room_id, res.status 
        FROM reservations res
        WHERE res.id='$reservation_id' AND res.user_id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: ../dashboard.php");
    exit();
}

$reservation = $result->fetch_assoc();

// Hanya bisa membatalkan jika status pending
if ($reservation['status'] == 'pending') {
    // Update status kamar kembali ke tersedia
    $conn->query("UPDATE rooms SET status='tersedia' WHERE id='{$reservation['room_id']}'");
    
    // Hapus reservasi
    $conn->query("DELETE FROM reservations WHERE id='$reservation_id'");
    
    $_SESSION['success'] = "Reservasi berhasil dibatalkan.";
} else {
    $_SESSION['error'] = "Reservasi tidak dapat dibatalkan.";
}

header("Location: ../dashboard.php");
exit();
?>