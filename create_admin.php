<?php
require_once __DIR__ . '/includes/config.php';

$nama = "Super Admin";
$email = "superadmin@hotel.com";
$password_plain = "admin123";
$role = "admin";

// Hash password
$password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

// Cek apakah user sudah ada
$sql_check = "SELECT id FROM users WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    echo "Admin dengan email tersebut sudah ada.";
} else {
    // Insert admin baru
    $sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama, $email, $password_hashed, $role);

    if ($stmt->execute()) {
        echo "Akun admin berhasil dibuat.<br>";
        echo "Email: $email<br>";
        echo "Password: $password_plain";
    } else {
        echo "Gagal membuat akun admin: " . $stmt->error;
    }
}
?>
