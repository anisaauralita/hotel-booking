<?php
require_once __DIR__ . '/config.php'; // Gunakan '/' bukan './' agar pasti benar

// Fungsi login
function login($email, $password) {
    global $conn;

    $email = sanitize($email); // Amankan input email

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if (!$stmt->execute()) {
        return false;
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
    }

    return false;
}

// Fungsi registrasi
function register($nama, $email, $password, $role = 'user') {
    global $conn;

    $nama = sanitize($nama);
    $email = sanitize($email);
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nama, $email, $password_hashed, $role);

    return $stmt->execute();
}

// Cek apakah user login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Cek apakah user adalah admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>
