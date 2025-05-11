-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for hotel_booking
CREATE DATABASE IF NOT EXISTS `hotel_booking` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `hotel_booking`;

-- Dumping structure for table hotel_booking.reservations
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `room_id` int NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `status` enum('pending','confirmed','canceled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table hotel_booking.reservations: ~1 rows (approximately)
INSERT INTO `reservations` (`id`, `user_id`, `room_id`, `check_in`, `check_out`, `status`, `created_at`) VALUES
	(1, 2, 2, '2025-05-08', '2025-05-10', 'pending', '2025-05-08 06:00:35'),
	(3, 2, 1, '2025-05-11', '2025-05-13', 'pending', '2025-05-09 17:00:58'),
	(4, 5, 7, '2025-05-11', '2025-05-13', 'pending', '2025-05-10 07:38:35');

-- Dumping structure for table hotel_booking.rooms
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_kamar` varchar(10) NOT NULL,
  `tipe` enum('single','double','suite') NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `status` enum('tersedia','dipesan') DEFAULT 'tersedia',
  `foto` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_kamar` (`nomor_kamar`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table hotel_booking.rooms: ~9 rows (approximately)
INSERT INTO `rooms` (`id`, `nomor_kamar`, `tipe`, `harga`, `status`, `foto`, `created_at`, `updated_at`) VALUES
	(1, '001', 'double', 350000.00, 'tersedia', 'room_681eb24fd65fb.jpeg', '2025-05-08 05:33:40', '2025-05-11 13:16:15'),
	(2, '002', 'single', 350000.00, 'dipesan', 'room_681eb239632e7.jpg', '2025-05-08 05:33:40', '2025-05-11 13:16:26'),
	(3, '009', 'single', 500000.00, 'tersedia', 'room_681eb22a6fe28.jpg', '2025-05-08 05:33:40', '2025-05-11 13:18:17'),
	(4, '008', 'double', 500000.00, 'tersedia', 'room_681eb2184177c.jpg', '2025-05-08 05:33:40', '2025-05-11 13:18:05'),
	(5, '007', 'suite', 1000000.00, 'tersedia', 'room_681eb1dc6c41e.jpg', '2025-05-08 05:33:40', '2025-05-11 13:17:31'),
	(6, '005', 'suite', 1200000.00, 'tersedia', 'room_681eb1c936b91.jpg', '2025-05-08 05:33:40', '2025-05-11 13:17:11'),
	(7, '006', 'double', 330000.00, 'dipesan', 'room_681eb1b999cc4.jpg', '2025-05-09 02:36:04', '2025-05-11 13:17:20'),
	(8, '003', 'suite', 700000.00, 'tersedia', 'room_681eb1aa43f8f.jpg', '2025-05-09 17:22:58', '2025-05-10 01:53:46'),
	(9, '004', 'single', 500000.00, 'tersedia', 'room_681eb1ffee91f.jpg', '2025-05-10 01:55:11', '2025-05-10 01:55:11');

-- Dumping structure for table hotel_booking.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table hotel_booking.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
	(1, 'Admin', 'admin@hotel.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-05-08 05:33:40'),
	(2, 'Anisa Auralita', 'anisaauralita20@gmail.com', '$2y$10$l/OYPWGe3VnLgR4QWCELP.bUX7ewdCF69RH4e9oew2os0n69uHkNu', 'user', '2025-05-08 05:59:52'),
	(3, 'Super Admin', 'superadmin@hotel.com', '$2y$10$LWpc.o/DQ/VKd1U9ByUNju/wKZewR2FJqnXnQTPRAZdXNt.0iErB.', 'admin', '2025-05-08 06:03:14'),
	(4, 'Ines Farah', 'farahsebian@gmail.com', '$2y$10$vGRzt.l2LO4ly0kjl2P3SekITnAWZzsM3L4/oYXDtD6zsNu6JlQ4S', 'user', '2025-05-08 06:48:35'),
	(5, 'Cajya wafiq ', 'wafiqazizah@gmail.com', '$2y$10$.Xbrd0479l.rEwk6bOZ6f.z.qG8aEdPWIFlXpUTqq3NTqXdBRw5K2', 'user', '2025-05-10 07:36:35');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
