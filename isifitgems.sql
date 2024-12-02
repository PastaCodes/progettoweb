SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `isifitgems` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `isifitgems`;

DROP TABLE IF EXISTS `additional_customization`;
CREATE TABLE IF NOT EXISTS `additional_customization` (
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `additional_customization`;
DROP TABLE IF EXISTS `bruh`;
CREATE TABLE IF NOT EXISTS `bruh` (
  `variant_choice` int(11) NOT NULL,
  `product_choice` int(11) NOT NULL,
  `product` varchar(255) NOT NULL,
  `variant_chosen` varchar(255) NOT NULL,
  `variant_allowed` varchar(255) NOT NULL,
  PRIMARY KEY (`variant_choice`,`product_choice`,`product`,`variant_chosen`,`variant_allowed`),
  KEY `FOREIGN` (`product_choice`,`variant_choice`,`product`,`variant_chosen`,`variant_allowed`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `bruh`;
DROP TABLE IF EXISTS `bundle`;
CREATE TABLE IF NOT EXISTS `bundle` (
  `code` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `bundle`;
DROP TABLE IF EXISTS `bundle_additional_customization`;
CREATE TABLE IF NOT EXISTS `bundle_additional_customization` (
  `bundle` varchar(255) NOT NULL,
  `additional_customization` varchar(255) NOT NULL,
  PRIMARY KEY (`bundle`,`additional_customization`),
  KEY `FOREIGN` (`additional_customization`,`bundle`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `bundle_additional_customization`;
DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `code` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `category`;
DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `code` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(1023) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `FOREIGN` (`category`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product`;
DROP TABLE IF EXISTS `product_choice`;
CREATE TABLE IF NOT EXISTS `product_choice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bundle` varchar(255) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FOREIGN` (`bundle`)
) ;

TRUNCATE TABLE `product_choice`;
DROP TABLE IF EXISTS `product_in_choice`;
CREATE TABLE IF NOT EXISTS `product_in_choice` (
  `product_choice` int(11) NOT NULL,
  `product` varchar(255) NOT NULL,
  PRIMARY KEY (`product_choice`,`product`),
  KEY `FOREIGN` (`product`,`product_choice`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product_in_choice`;
DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE IF NOT EXISTS `product_variant` (
  `product` varchar(255) NOT NULL,
  `variant` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`product`,`variant`),
  KEY `FOREIGN` (`variant`,`product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product_variant`;
DROP TABLE IF EXISTS `variant`;
CREATE TABLE IF NOT EXISTS `variant` (
  `suffix` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`suffix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `variant`;
DROP TABLE IF EXISTS `variant_choice`;
CREATE TABLE IF NOT EXISTS `variant_choice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bundle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FOREIGN` (`bundle`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `variant_choice`;
DROP TABLE IF EXISTS `variant_in_choice`;
CREATE TABLE IF NOT EXISTS `variant_in_choice` (
  `variant_choice` int(11) NOT NULL,
  `variant` varchar(255) NOT NULL,
  PRIMARY KEY (`variant_choice`,`variant`),
  KEY `FOREIGN` (`variant`,`variant_choice`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `variant_in_choice`;

ALTER TABLE `bruh`
  ADD CONSTRAINT `bruh_fk_product` FOREIGN KEY (`product`) REFERENCES `product` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bruh_fk_product_choice` FOREIGN KEY (`product_choice`) REFERENCES `product_choice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bruh_fk_variant_allowed` FOREIGN KEY (`variant_allowed`) REFERENCES `variant` (`suffix`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bruh_fk_variant_choice` FOREIGN KEY (`variant_choice`) REFERENCES `variant_choice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bruh_fk_variant_chosen` FOREIGN KEY (`variant_chosen`) REFERENCES `variant` (`suffix`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `bundle_additional_customization`
  ADD CONSTRAINT `bundle_additional_customization_fk_additional_customization` FOREIGN KEY (`additional_customization`) REFERENCES `additional_customization` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bundle_additional_customization_fk_bundle` FOREIGN KEY (`bundle`) REFERENCES `bundle` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product`
  ADD CONSTRAINT `product_fk_category` FOREIGN KEY (`category`) REFERENCES `category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `product_choice`
  ADD CONSTRAINT `product_choice_fk_bundle` FOREIGN KEY (`bundle`) REFERENCES `bundle` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product_in_choice`
  ADD CONSTRAINT `product_in_choice_fk_product` FOREIGN KEY (`product`) REFERENCES `product` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_in_choice_fk_product_choice` FOREIGN KEY (`product_choice`) REFERENCES `product_choice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_variant_fk_product` FOREIGN KEY (`product`) REFERENCES `product` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_variant_fk_variant` FOREIGN KEY (`variant`) REFERENCES `variant` (`suffix`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `variant_choice`
  ADD CONSTRAINT `variant_choice_fk_bundle` FOREIGN KEY (`bundle`) REFERENCES `bundle` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `variant_in_choice`
  ADD CONSTRAINT `variant_in_choice_fk_variant` FOREIGN KEY (`variant`) REFERENCES `variant` (`suffix`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `variant_in_choice_fk_variant_choice` FOREIGN KEY (`variant_choice`) REFERENCES `variant_choice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
