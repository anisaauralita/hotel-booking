<?php
// Memastikan session dimulai hanya sekali dan pengaturan cookie dilakukan dengan aman
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_booking');

// Base URL utama proyek
define('BASE_URL', '/HOTEL-BOOKING/');

// Path untuk assets
define('ASSETS_PATH', __DIR__ . '/../assets/');
define('IMAGES_PATH', ASSETS_PATH . 'images/');
define('ROOM_IMAGES_PATH', IMAGES_PATH . 'rooms/');

// Koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fungsi untuk mencegah SQL injection
if (!function_exists('sanitize')) {
    function sanitize($data) {
        global $conn;
        return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags($data)));
    }
}
?>
