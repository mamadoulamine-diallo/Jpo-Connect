-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 11, 2025 at 12:25 PM
-- Server version: 8.0.41
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jpo_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `permissions` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `first_name`, `last_name`, `email`, `password`, `role`, `permissions`) VALUES
(1, 'Jean', 'Dupont', 'admin@example.com', '$2y$10$XGoSmwTR2P01XkaZoNWIku800.sn7iWS3az2bgRwy5awlv/aa5TiS', 'Directeur', '{\"jpo_edit\": true, \"jpo_create\": true, \"jpo_delete\": true, \"stats_view\": true, \"role_manage\": true, \"comment_moderate\": true}'),
(2, 'Test', 'User', 'test@example.com', 'motdepasse', 'Responsable', '{\"jpo_edit\": true, \"jpo_create\": true, \"stats_view\": true, \"comment_moderate\": true}'),
(3, 'Claire', 'Martin', 'claire@example.com', '$2y$10$pbSFVFM0pFc/8d/D/YUVF.hu7TW6eIarKTVjINuAMEtEQlJisYc2W', 'Responsable', '{\"jpo_edit\": true, \"jpo_create\": true, \"stats_view\": true, \"comment_moderate\": true}'),
(4, 'Yanis', 'Durand', 'yanis@example.com', '$2y$10$pbSFVFM0pFc/8d/D/YUVF.hu7TW6eIarKTVjINuAMEtEQlJisYc2W', 'Salarié', '{\"stats_view\": true, \"comment_reply\": true}');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id_comments` int NOT NULL,
  `jpo_fk` int NOT NULL,
  `comment` text COLLATE utf8mb4_general_ci,
  `visitor_fk` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_approved` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id_comments`, `jpo_fk`, `comment`, `visitor_fk`, `created_at`, `is_approved`) VALUES
(1, 1, 'Super JPO, tr?s accueillant !', 1, '2025-06-09 09:10:26', 1),
(3, 1, '?v?nement bien organis? !', 2, '2025-06-09 09:44:43', 1),
(4, 1, 'Excellent ?v?nement !', 2, '2025-06-09 10:00:24', 1),
(5, 1, 'JPO tr?s informative !', 1, '2025-06-09 11:07:55', 0),
(6, 1, '?v?nement top !', 2, '2025-06-09 11:28:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `comment_replies`
--

CREATE TABLE `comment_replies` (
  `id_reply` int NOT NULL,
  `comment_fk` int NOT NULL,
  `visitor_fk` int DEFAULT NULL,
  `reply` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_fk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comment_replies`
--

INSERT INTO `comment_replies` (`id_reply`, `comment_fk`, `visitor_fk`, `reply`, `created_at`, `admin_fk`) VALUES
(1, 3, 1, 'Merci pour l organisation', '2025-06-09 11:40:13', 0),
(2, 1, NULL, 'Merci pour votre avis !', '2025-06-11 09:59:42', 4);

-- --------------------------------------------------------

--
-- Table structure for table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id_inscription` int NOT NULL,
  `jpo_fk` int NOT NULL,
  `visitor_fk` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inscriptions`
--

INSERT INTO `inscriptions` (`id_inscription`, `jpo_fk`, `visitor_fk`, `created_at`) VALUES
(8, 3, 1, '2025-06-10 08:54:04');

-- --------------------------------------------------------

--
-- Table structure for table `jpo`
--

CREATE TABLE `jpo` (
  `id_jpo` int NOT NULL,
  `site_fk` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `date_jpo` date NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `capacity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jpo`
--

INSERT INTO `jpo` (`id_jpo`, `site_fk`, `created_by`, `date_jpo`, `title`, `description`, `created_at`, `capacity`) VALUES
(1, 1, 1, '2025-10-01', 'JPO ?cole 2025', 'Visite de l??cole.', '2025-06-08 11:55:29', 50),
(3, 3, 1, '2025-12-05', 'JPO Martigues 2025 Modifi?e', 'D?couverte approfondie Martigues', '2025-06-09 07:45:49', 30),
(4, 4, 1, '2026-01-15', 'JPO Paris 2026', 'Portes ouvertes Paris', '2025-06-09 07:45:49', 50);

-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE `site` (
  `id_site` int NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cp` int DEFAULT NULL,
  `phone` int DEFAULT NULL,
  `address` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site`
--

INSERT INTO `site` (`id_site`, `city`, `cp`, `phone`, `address`) VALUES
(1, 'Marseille', 13001, 123456789, '123 Rue Exemple'),
(2, 'Cannes', 6400, 987654321, '456 Rue Exemple'),
(3, 'Martigues', 13500, 555555555, '789 Rue Exemple'),
(4, 'Paris', 75001, 111222333, '101 Rue Exemple');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id_visitors` int NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id_visitors`, `first_name`, `last_name`, `email`) VALUES
(1, 'Marie', 'Curie', 'marie@example.com'),
(2, 'Admin', 'Directeur', 'admin@example.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id_comments`),
  ADD KEY `jpo_fk` (`jpo_fk`),
  ADD KEY `comments_ibfk_1` (`visitor_fk`);

--
-- Indexes for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD PRIMARY KEY (`id_reply`),
  ADD KEY `comment_fk` (`comment_fk`),
  ADD KEY `visitor_fk` (`visitor_fk`);

--
-- Indexes for table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id_inscription`),
  ADD KEY `jpo_fk` (`jpo_fk`),
  ADD KEY `visitor_fk` (`visitor_fk`);

--
-- Indexes for table `jpo`
--
ALTER TABLE `jpo`
  ADD PRIMARY KEY (`id_jpo`),
  ADD KEY `jpo_ibfk_1` (`site_fk`),
  ADD KEY `jpo_ibfk_2` (`created_by`);

--
-- Indexes for table `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`id_site`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id_visitors`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id_comments` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `comment_replies`
--
ALTER TABLE `comment_replies`
  MODIFY `id_reply` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id_inscription` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jpo`
--
ALTER TABLE `jpo`
  MODIFY `id_jpo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id_visitors` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`visitor_fk`) REFERENCES `visitors` (`id_visitors`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`jpo_fk`) REFERENCES `jpo` (`id_jpo`);

--
-- Constraints for table `comment_replies`
--
ALTER TABLE `comment_replies`
  ADD CONSTRAINT `comment_replies_ibfk_1` FOREIGN KEY (`comment_fk`) REFERENCES `comments` (`id_comments`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_replies_ibfk_2` FOREIGN KEY (`visitor_fk`) REFERENCES `visitors` (`id_visitors`) ON DELETE CASCADE;

--
-- Constraints for table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`jpo_fk`) REFERENCES `jpo` (`id_jpo`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`visitor_fk`) REFERENCES `visitors` (`id_visitors`) ON DELETE CASCADE;

--
-- Constraints for table `jpo`
--
ALTER TABLE `jpo`
  ADD CONSTRAINT `jpo_ibfk_1` FOREIGN KEY (`site_fk`) REFERENCES `site` (`id_site`),
  ADD CONSTRAINT `jpo_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
