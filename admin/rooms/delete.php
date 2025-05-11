<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$room_id = sanitize($_GET['id']);

// Get room data to delete photo
$sql = "SELECT foto FROM rooms WHERE id='$room_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $room = $result->fetch_assoc();
    
    // Delete photo if not default
    if ($room['foto'] != 'default.jpg') {
        $target_dir = "../../assets/images/";
        unlink($target_dir . $room['foto']);
    }
    
    // Delete room from database
    $sql = "DELETE FROM rooms WHERE id='$room_id'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Kamar berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Terjadi kesalahan: " . $conn->error;
    }
}

header("Location: index.php");
exit();
?>