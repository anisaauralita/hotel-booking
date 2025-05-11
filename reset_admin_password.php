<?php
require_once __DIR__ . '/includes/config.php';

$email = "superadmin@hotel.com";
$password_baru = "admin123"; // Ganti dengan password baru yang kamu inginkan
$password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $password_hashed, $email);

if ($stmt->execute()) {
    echo "Password admin berhasil direset.<br>";
    echo "Email: $email<br>";
    echo "Password baru: $password_baru";
} else {
    echo "Gagal reset password: " . $stmt->error;
}
?>
