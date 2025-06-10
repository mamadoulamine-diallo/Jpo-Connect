-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 10 juin 2025 à 09:07
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `jpo_connect`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id_admin`, `first_name`, `last_name`, `email`, `password`, `role`) VALUES
(1, 'Jean', 'Dupont', 'admin@example.com', '$2y$10$XGoSmwTR2P01XkaZoNWIku800.sn7iWS3az2bgRwy5awlv/aa5TiS', 'Directeur'),
(2, 'Test', 'User', 'test@example.com', 'motdepasse', 'Assistant');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id_comments` int NOT NULL,
  `jpo_fk` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_comments`),
  KEY `jpo_fk` (`jpo_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

DROP TABLE IF EXISTS `inscriptions`;
CREATE TABLE IF NOT EXISTS `inscriptions` (
  `id_inscription` int NOT NULL AUTO_INCREMENT,
  `jpo_fk` int NOT NULL,
  `visitor_fk` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inscription`),
  KEY `jpo_fk` (`jpo_fk`),
  KEY `visitor_fk` (`visitor_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jpo`
--

DROP TABLE IF EXISTS `jpo`;
CREATE TABLE IF NOT EXISTS `jpo` (
  `id_jpo` int NOT NULL AUTO_INCREMENT,
  `site_fk` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `date_jpo` date NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jpo`),
  KEY `jpo_ibfk_1` (`site_fk`),
  KEY `jpo_ibfk_2` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `jpo`
--

INSERT INTO `jpo` (`id_jpo`, `site_fk`, `created_by`, `date_jpo`, `title`, `description`, `created_at`) VALUES
(1, 1, 1, '2025-09-01', 'JPO école 2025', 'Visite de l\'école.', '2025-06-08 11:55:29'),
(2, 2, 1, '2025-08-10', 'JPO Paris', '/', '2025-06-09 09:26:49'),
(3, 3, 1, '2025-10-11', 'JPO Cannes', '/', '2025-06-09 09:56:37'),
(4, 4, 1, '2025-11-12', 'JPO Martigues', '/', '2025-06-09 09:56:37'),
(5, 5, 1, '2025-07-13', 'JPO Toulon', '/', '2025-06-09 09:57:41'),
(6, 6, 1, '2025-12-14', 'JPO Brignoles', '/', '2025-06-09 09:57:41'),
(7, 1, 1, '2025-10-26', 'JPO Marseille', '/', '2025-06-09 14:00:31');

-- --------------------------------------------------------

--
-- Structure de la table `site`
--

DROP TABLE IF EXISTS `site`;
CREATE TABLE IF NOT EXISTS `site` (
  `id_site` int NOT NULL,
  `city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cp` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `site`
--

INSERT INTO `site` (`id_site`, `city`, `cp`, `phone`, `address`) VALUES
(1, 'Marseille', '13002', '0484894369', '8 rue d\'Hozier'),
(2, 'Puteaux', '92800', '0484894369', '8 Terr. Bellini'),
(3, 'Cannes', '06400', '0484894369', '107 Boulevard de la République'),
(4, 'Martigues', '13500', '0484894369', 'Place du 8 mai 1945'),
(5, 'Toulon', '83100', '0484894369', '131 Avenue Franklin Roosevelt'),
(6, 'Brignoles', '83170', '0484894369', '47 Rue de la République');

-- --------------------------------------------------------

--
-- Structure de la table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
CREATE TABLE IF NOT EXISTS `visitors` (
  `id_visitors` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_visitors`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `visitors`
--

INSERT INTO `visitors` (`id_visitors`, `first_name`, `last_name`, `email`) VALUES
(1, 'Marie', 'Curie', 'marie@example.com');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`jpo_fk`) REFERENCES `jpo` (`id_jpo`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`jpo_fk`) REFERENCES `jpo` (`id_jpo`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`visitor_fk`) REFERENCES `visitors` (`id_visitors`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jpo`
--
ALTER TABLE `jpo`
  ADD CONSTRAINT `jpo_ibfk_1` FOREIGN KEY (`site_fk`) REFERENCES `site` (`id_site`),
  ADD CONSTRAINT `jpo_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
