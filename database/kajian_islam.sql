-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2026 at 02:54 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kajian_islam`
--
CREATE DATABASE IF NOT EXISTS `kajian_islam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `kajian_islam`;

-- --------------------------------------------------------

--
-- Table structure for table `kajian`
--

DROP TABLE IF EXISTS `kajian`;
CREATE TABLE IF NOT EXISTS `kajian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `tema` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` time NOT NULL,
  `ustaz_id` int(11) NOT NULL,
  `masjid_id` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ustaz_id` (`ustaz_id`),
  KEY `masjid_id` (`masjid_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kajian`
--

INSERT IGNORE INTO `kajian` (`id`, `judul`, `tema`, `tanggal`, `jam`, `ustaz_id`, `masjid_id`, `deskripsi`, `created_at`, `updated_at`) VALUES
(3, 'Pengajian Akbar', 'Malam Jum,at', '2026-07-16', '20:17:00', 7, 7, 'ayoo', '2026-07-09 09:10:38', '2026-07-10 16:34:47'),
(6, 'Ilmu agama', 'Sholawat', '2026-07-17', '14:47:00', 6, 14, '', '2026-07-10 16:44:14', '2026-07-10 16:44:14');

-- --------------------------------------------------------

--
-- Table structure for table `masjid`
--

DROP TABLE IF EXISTS `masjid`;
CREATE TABLE IF NOT EXISTS `masjid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_masjid` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `kecamatan` varchar(100) NOT NULL,
  `kota` varchar(100) NOT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `masjid`
--

INSERT IGNORE INTO `masjid` (`id`, `nama_masjid`, `alamat`, `kecamatan`, `kota`, `no_telepon`, `created_at`, `updated_at`) VALUES
(7, 'Masjid Al-Hendri', 'Jl. Kaliurang Km 10', 'subah', 'batang', '0274654321', '2026-07-08 21:18:16', '2026-07-10 16:40:32'),
(11, 'Masjid Al-Falah', 'Jl.mangga', 'peklongan', 'batang', '0987654433', '2026-07-09 09:09:13', '2026-07-10 16:32:53'),
(12, 'Mushola Nurrul', 'Jl.Belimbing', 'Batang', 'Pekalongan', '01898982189', '2026-07-10 16:40:11', '2026-07-10 16:40:11'),
(13, 'Masjid Al-Baqora', 'Jl.loma', 'Batang', 'batang', '009098989490', '2026-07-10 16:41:55', '2026-07-10 16:41:55'),
(14, 'Mushola Wagna', 'Jl.Mangonsri', 'Batang', 'Batang', '099876545', '2026-07-10 16:42:50', '2026-07-10 16:42:50'),
(16, 'Masjid.agung', 'Jl.maguwo', 'Sleman', 'yogya', '094390683869', '2026-07-11 09:55:47', '2026-07-11 09:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT IGNORE INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$8KqyUq3Y6F5q5J5p5J5p5O5Q5R5S5T5U5V5W5X5Y5Z5A5B5C5D5E5F', 'Administrator', 'admin@kajian.com', 'admin', '2026-07-08 21:18:16', '2026-07-08 21:18:16'),
(4, 'hendri', '$2y$10$sThPVkb2oyklfAhwUJIBF.xWzRqKs9iB9C8Z4ON2Q1G4uYPFoI1pW', 'Hendri', 'hendrisetyawan0812@gmail.com', 'user', '2026-07-08 21:43:21', '2026-07-08 21:43:21'),
(5, 'Hem', '$2y$10$ehhXG3qVJVUoIfjEEvkyQOUiUY6eUchNh3yNtC/LeHS2Ey9VWMorK', 'Hendri', 'setyawanhendri253@gmail.com', 'user', '2026-07-11 09:28:09', '2026-07-11 09:28:09'),
(6, 'setyawan', '$2y$10$g4jKlEF.WN4nmHtRga/c7ec7KFub.gW5Q0OPYhCFCEW9VP5puKgby', 'HEndri Setyawan', 'setyawan15@gmail.com', 'user', '2026-07-11 09:43:08', '2026-07-11 09:43:08');

-- --------------------------------------------------------

--
-- Table structure for table `ustaz`
--

DROP TABLE IF EXISTS `ustaz`;
CREATE TABLE IF NOT EXISTS `ustaz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_ustaz` varchar(255) NOT NULL,
  `bidang_keilmuan` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ustaz`
--

INSERT IGNORE INTO `ustaz` (`id`, `nama_ustaz`, `bidang_keilmuan`, `no_hp`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'Ustaz Hendri.SKOM', 'Tafsir Al-Quran', '081234567891', 'Jl. Merapi No. 1, Batang', '2026-07-08 21:05:59', '2026-07-10 16:50:25'),
(2, 'Ustaz Alba, Lc', 'Hadits', '081234567892', 'Jl. Merbabu No. 2, Batang', '2026-07-08 21:05:59', '2026-07-10 16:38:38'),
(6, 'Ustaz Nadi, M.', 'Tafsir Al-Quran', '081234567891', 'Jl. Merapi No. 1, Kemiri timur', '2026-07-08 21:18:16', '2026-07-10 16:39:12'),
(7, 'Ustaz Adit, Lc', 'Hadits Ilmu', '081234567892', 'Jl. Merbabu No. 2, Batang', '2026-07-08 21:18:16', '2026-07-10 16:49:50'),
(10, 'Ustaz Arif M.A', 'Fiqih Muamalah', '081234567895', 'Jl. Bromo No. 5, Batang', '2026-07-08 21:18:16', '2026-07-10 16:38:56'),
(13, 'dani', 'sholawat', '0939092895', 'Jl.mangga', '2026-07-11 09:54:13', '2026-07-11 09:54:13');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kajian`
--
ALTER TABLE `kajian`
  ADD CONSTRAINT `kajian_ibfk_1` FOREIGN KEY (`ustaz_id`) REFERENCES `ustaz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kajian_ibfk_2` FOREIGN KEY (`masjid_id`) REFERENCES `masjid` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
