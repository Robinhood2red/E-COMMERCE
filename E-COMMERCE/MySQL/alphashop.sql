-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 23 mars 2026 à 13:31
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `alphashop`
--

-- --------------------------------------------------------

--
-- Structure de la table `add_product_history`
--

DROP TABLE IF EXISTS `add_product_history`;
CREATE TABLE IF NOT EXISTS `add_product_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EDEB7BDE4584665A` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `add_product_history`
--

INSERT INTO `add_product_history` (`id`, `created_at`, `product_id`, `quantity`) VALUES
(1, '2026-03-03 13:40:09', 5, 2),
(2, '2026-03-12 14:10:26', 6, 5),
(3, '2026-03-12 14:19:05', 7, 2),
(4, '2026-03-13 07:52:38', 8, 99),
(5, '2026-03-16 12:38:29', 9, 1);

-- --------------------------------------------------------

--
-- Structure de la table `alpha_camp`
--

DROP TABLE IF EXISTS `alpha_camp`;
CREATE TABLE IF NOT EXISTS `alpha_camp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_EACAFF5312469DE2` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `alpha_camp`
--

INSERT INTO `alpha_camp` (`id`, `name`, `category_id`) VALUES
(3, 'Le hurlement primale Alpha', 10),
(5, 'Jiu jitsu à la plage', 12),
(6, 'La poudre de Pierick', 14),
(7, 'Andrew Tate', 15),
(8, 'Giga Chad', 15);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_497DD6345E237E06` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `name`) VALUES
(10, 'Alpha scream'),
(12, 'Alpha naked fight'),
(14, 'Alpha Market'),
(15, 'Idoles');

-- --------------------------------------------------------

--
-- Structure de la table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `shipping_cost` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `city`
--

INSERT INTO `city` (`id`, `name`, `shipping_cost`) VALUES
(1, 'Bordeaux', 5),
(2, 'Paris', 3),
(3, 'Tours', 5);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260224150049', '2026-02-24 15:01:15', 54),
('DoctrineMigrations\\Version20260225103516', '2026-02-26 07:05:42', 85),
('DoctrineMigrations\\Version20260225123333', '2026-02-26 07:05:42', 4),
('DoctrineMigrations\\Version20260302084443', '2026-03-02 08:44:54', 38),
('DoctrineMigrations\\Version20260302124718', '2026-03-02 12:47:39', 149),
('DoctrineMigrations\\Version20260302142322', '2026-03-02 14:23:34', 115),
('DoctrineMigrations\\Version20260303103517', '2026-03-03 10:35:31', 104),
('DoctrineMigrations\\Version20260303103911', '2026-03-03 10:39:24', 96),
('DoctrineMigrations\\Version20260303130405', '2026-03-03 13:04:17', 165),
('DoctrineMigrations\\Version20260303131541', '2026-03-03 13:15:51', 132),
('DoctrineMigrations\\Version20260303131804', '2026-03-03 13:18:21', 117),
('DoctrineMigrations\\Version20260303145807', '2026-03-03 14:58:16', 152),
('DoctrineMigrations\\Version20260316125354', '2026-03-16 12:54:01', 188),
('DoctrineMigrations\\Version20260316143137', '2026-03-16 14:31:56', 178),
('DoctrineMigrations\\Version20260317093305', '2026-03-17 09:33:17', 160),
('DoctrineMigrations\\Version20260317130552', '2026-03-17 13:07:23', 195),
('DoctrineMigrations\\Version20260323074006', '2026-03-23 07:40:17', 210),
('DoctrineMigrations\\Version20260323085115', '2026-03-23 08:51:29', 249),
('DoctrineMigrations\\Version20260323090015', '2026-03-23 09:00:19', 211);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `city_id` int DEFAULT NULL,
  `pay_on_delivery` tinyint NOT NULL,
  `total_price` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F52993988BAC62AF` (`city_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `first_name`, `last_name`, `phone`, `address`, `created_at`, `city_id`, `pay_on_delivery`, `total_price`) VALUES
(25, 'louis', 'boutet', '0666666666', '5 Rue Pierre de Coubertin', '2026-03-23 10:58:48', 1, 1, 1004),
(26, 'louis', 'boutet', '0666666666', '5 Rue Pierre de Coubertin', '2026-03-23 10:59:34', 1, 1, 1004),
(24, 'louis', 'boutet', '0666666666', '5 Rue Pierre de Coubertin', '2026-03-23 10:58:08', 1, 1, 1004),
(23, 'louis', 'boutet', '0666666666', '5 Rue Pierre de Coubertin', '2026-03-23 10:55:10', 1, 1, 74),
(22, 'louis', 'boutet', '0666666666', '5 Rue Pierre de Coubertin', '2026-03-23 10:53:09', 1, 1, 74),
(21, 'louis', 'boutet', '0666666666', '5 Rue Pierre de Coubertin', '2026-03-23 10:50:00', 1, 1, 74);

-- --------------------------------------------------------

--
-- Structure de la table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
CREATE TABLE IF NOT EXISTS `order_products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `qte` int NOT NULL,
  `_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5242B8EBA35F2858` (`_order_id`),
  KEY `IDX_5242B8EB4584665A` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `order_products`
--

INSERT INTO `order_products` (`id`, `qte`, `_order_id`, `product_id`) VALUES
(33, 1, 26, 8),
(32, 1, 25, 8),
(31, 1, 24, 8),
(30, 1, 23, 9),
(29, 1, 22, 9),
(28, 1, 21, 9);

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(180) NOT NULL,
  `product_description` longtext,
  `price` int NOT NULL,
  `images` varchar(180) DEFAULT NULL,
  `stock` int NOT NULL,
  `categorie_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D34A04AD5E237E06` (`name`),
  KEY `IDX_D34A04ADBCF5E72D` (`categorie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `product_description`, `price`, `images`, `stock`, `categorie_id`) VALUES
(4, 'Alpha Poudre', 'Demander à Pierick', 60, 'miso-poudre-69b2bbfa5810b.png', 23, NULL),
(3, 'Alpha Fight Club', 'La 1erre règle du bagarre club c\'est il faut pas parler du bagarre club', 100, 'club-alpha-69a92b20cd258.png', 0, NULL),
(7, 'Cris Primâle', 'Thérapie du cris primale entre hommes', 99, 'cris-primale-69b2cb591db04.png', 2, NULL),
(8, 'Andrew Tate', 'Rencontre le meilleur des alpah mâle', 999, 'Andrew-Tate-69b3c2468c7e4.jpg', 99, NULL),
(9, 'Giga Chad', 'Rencontrez le plus beau et musclé des alpha : GigaChad, l\'icône absolue de la perfection masculine sur Internet. Avec sa mâchoire de granit et sa musculature sculptée au scalpel, il incarne une confiance inébranlable et un calme olympien. Plus qu\'un simple mème, c\'est le symbole du \"Chad\" ultime qui assume ses choix avec un charisme désarmant.', 69, 'giga-chad-69b7f9c56c175.jpg', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `product_alpha_camp`
--

DROP TABLE IF EXISTS `product_alpha_camp`;
CREATE TABLE IF NOT EXISTS `product_alpha_camp` (
  `product_id` int NOT NULL,
  `alpha_camp_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`alpha_camp_id`),
  KEY `IDX_79ADA0784584665A` (`product_id`),
  KEY `IDX_79ADA078E02E83EF` (`alpha_camp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `product_alpha_camp`
--

INSERT INTO `product_alpha_camp` (`product_id`, `alpha_camp_id`) VALUES
(1, 5),
(2, 5),
(3, 5),
(4, 6),
(5, 6),
(6, 3),
(7, 3),
(8, 7),
(9, 8);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `lastname` varchar(180) NOT NULL,
  `firstname` varchar(180) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `lastname`, `firstname`) VALUES
(1, 'louisboutetcamille@gmail.com', '[\"ROLE_ADMIN\", \"ROLE_EDITOR\", \"ROLE_USER\"]', '$2y$13$OekFXn3Q3KJf93ics0mqBuADdoP0DCMZpFDnTdJZJAD9MrMM/.1la', 'boutet', 'louis'),
(2, 'louis.boutet2000@gmail.com', '[\"ROLE_EDITOR\", \"ROLE_USER\"]', '$2y$13$fjrFK1xTux69u8SSeIk.8.lnz5xMAu85aCIsGEE2dG22Nuwci2/Sq', 'robin', 'camille'),
(8, 'test1@gmail.com', '[]', '$2y$13$ax8X6xBVrEA4LWyw.X1cGeb3sOValFnlEhrg2.J8pA25yHZ1q4R5K', 'boutet', 'louis');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
