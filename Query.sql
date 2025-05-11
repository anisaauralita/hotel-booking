-- Buat database hotel_booking
CREATE DATABASE hotel_booking;

-- Gunakan database hotel_booking
USE hotel_booking;

-- Buat tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat tabel rooms
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_kamar VARCHAR(10) NOT NULL UNIQUE,
    tipe ENUM('single', 'double', 'suite') NOT NULL,
    harga DECIMAL(10, 2) NOT NULL,
    status ENUM('tersedia', 'dipesan') DEFAULT 'tersedia',
    foto VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Buat tabel reservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'canceled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Tambahkan admin default (password: admin123)
INSERT INTO users (nama, email, password, role) 
VALUES ('Admin', 'admin@hotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Tambahkan beberapa kamar contoh
INSERT INTO rooms (nomor_kamar, tipe, harga, foto) VALUES
('101', 'single', 350000, 'single1.jpg'),
('102', 'single', 350000, 'single2.jpg'),
('201', 'double', 500000, 'double1.jpg'),
('202', 'double', 500000, 'double2.jpg'),
('301', 'suite', 1000000, 'suite1.jpg'),
('302', 'suite', 1200000, 'suite2.jpg');