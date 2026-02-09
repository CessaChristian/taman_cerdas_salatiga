-- ============================================
-- TAMAN CERDAS - Database Schema
-- ============================================
-- Server: MariaDB 10.4+
-- PHP: 8.1+
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taman_cerdas`
--
CREATE DATABASE IF NOT EXISTS `taman_cerdas` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `taman_cerdas`;

-- --------------------------------------------------------
-- Table: user
-- --------------------------------------------------------

CREATE TABLE `user` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('user','admin') NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: data_user
-- --------------------------------------------------------

CREATE TABLE `data_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `data_user_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: reservasi
-- --------------------------------------------------------

CREATE TABLE `reservasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `nama_penyewa` varchar(100) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `pendopo` tinyint(1) NOT NULL DEFAULT 0,
  `ruang_baca` tinyint(1) NOT NULL DEFAULT 0,
  `taman_bermain` tinyint(1) NOT NULL DEFAULT 0,
  `total_bayar` int(11) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: post (Forum)
-- --------------------------------------------------------

CREATE TABLE `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `post_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: replies (Forum)
-- --------------------------------------------------------

CREATE TABLE `replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `reply_to_username` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `username` (`username`),
  CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`username`) REFERENCES `user` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
