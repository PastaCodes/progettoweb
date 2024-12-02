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
  KEY `FOREIGN` (`product_choice`,`variant_choice`,`product`,`variant_chosen`,`variant_allowed`) USING BTREE,
  KEY `bruh_fk_product` (`product`),
  KEY `bruh_fk_variant_allowed` (`variant_allowed`),
  KEY `bruh_fk_variant_chosen` (`variant_chosen`)
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
INSERT INTO `category` (`code`, `display_name`) VALUES
('bracelets', 'Bracelets'),
('earrings', 'Earrings'),
('necklaces', 'Necklaces'),
('pins', 'Pins'),
('rings', 'Rings');

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `code` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(1023) NOT NULL,
  `bundle_only` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `FOREIGN` (`category`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product`;
INSERT INTO `product` (`code`, `display_name`, `description`, `bundle_only`, `category`) VALUES
('barbed_wire_bracelet', 'Barbed Wire Bracelet', 'The classic symbol of alternative culture.', 0, 'bracelets'),
('chain_bracelet', 'Chain Bracelet', 'Stylish and fashionable with a touch of edginess.', 0, 'bracelets'),
('chain_necklace', 'Chain Necklace', 'A necklace. Made of chain.', 0, 'necklaces'),
('choker', 'Choker', 'Why so serious?', 0, 'necklaces'),
('colorful_ring', 'Colorful Ring', 'A ring which happens to be colorful.', 1, 'rings'),
('colorful_string_bracelet', 'Colorful String Bracelet', 'A bracelet which is made of string and is colorful.', 1, 'bracelets'),
('dagger_earrings', 'Dagger Earrings', 'Not very effective for stabbing people.', 0, 'earrings'),
('dragon_pendant', 'Dragon Pendant', 'Dragon deez nuts.', 0, 'necklaces'),
('flame_earrings', 'Flame Earrings', 'Not made of actual fire.', 0, 'earrings'),
('flower_bracelet', 'Flower Bracelet', 'It is recommended to water the flowers daily to keep them from withering.', 1, 'bracelets'),
('gem_earrings', 'Gem Earrings', 'Whether you believe in their power, gems are sure to complement your attire.', 0, 'earrings'),
('impostor_pin', 'Impostor Pin', 'Might make you look a bit sus.', 0, 'pins'),
('leather_bracelet', 'Leather Bracelet', 'Cows were definitely harmed to make this product.', 1, 'bracelets'),
('moai_pin', 'Moai Pin', '🗿.', 0, 'pins'),
('pebble_bracelet', 'Pebble Bracelet', 'Did you know a group of pebbles is called a stoner?', 1, 'bracelets'),
('raven_pendant', 'Raven Pendant', 'The spirit animal of goth culture.', 0, 'necklaces'),
('simple_bracelet', 'Simple Bracelet', 'Less is more.', 0, 'bracelets'),
('simple_ring', 'Simple Ring', 'A classic minimal design, suitable for all occasions.', 0, 'rings'),
('skull_ring', 'Skull Ring', 'A staple of macabre accessories.', 0, 'rings'),
('snake_ring', 'Snake Ring', 'Ssssss, sssss sss ssssss.', 0, 'rings');

DROP TABLE IF EXISTS `product_choice`;
CREATE TABLE IF NOT EXISTS `product_choice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bundle` varchar(255) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FOREIGN` (`bundle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO `product_variant` (`product`, `variant`, `image`, `price`) VALUES
('barbed_wire_bracelet', 'black_steel', NULL, 1.00),
('barbed_wire_bracelet', 'gunmetal', NULL, 1.00),
('chain_bracelet', 'black_steel', NULL, 1.00),
('chain_bracelet', 'gunmetal', NULL, 1.00),
('choker', '', NULL, 1.00),
('colorful_ring', 'black', NULL, 1.00),
('colorful_ring', 'blue', NULL, 1.00),
('colorful_ring', 'brown', NULL, 1.00),
('colorful_ring', 'cyan', NULL, 1.00),
('colorful_ring', 'green', NULL, 1.00),
('colorful_ring', 'lime', NULL, 1.00),
('colorful_ring', 'orange', NULL, 1.00),
('colorful_ring', 'pink', NULL, 1.00),
('colorful_ring', 'purple', NULL, 1.00),
('colorful_ring', 'red', NULL, 1.00),
('colorful_ring', 'white', NULL, 1.00),
('colorful_ring', 'yellow', NULL, 1.00),
('colorful_string_bracelet', 'black', NULL, 1.00),
('colorful_string_bracelet', 'blue', NULL, 1.00),
('colorful_string_bracelet', 'brown', NULL, 1.00),
('colorful_string_bracelet', 'cyan', NULL, 1.00),
('colorful_string_bracelet', 'green', NULL, 1.00),
('colorful_string_bracelet', 'lime', NULL, 1.00),
('colorful_string_bracelet', 'orange', NULL, 1.00),
('colorful_string_bracelet', 'pink', NULL, 1.00),
('colorful_string_bracelet', 'purple', NULL, 1.00),
('colorful_string_bracelet', 'red', NULL, 1.00),
('colorful_string_bracelet', 'white', NULL, 1.00),
('colorful_string_bracelet', 'yellow', NULL, 1.00),
('dagger_earrings', 'black_steel', NULL, 1.00),
('dagger_earrings', 'gunmetal', NULL, 1.00),
('flame_earrings', 'black_steel', NULL, 1.00),
('flame_earrings', 'copper', NULL, 1.00),
('flower_bracelet', '', NULL, 1.00),
('gem_earrings', 'obsidian', NULL, 1.00),
('gem_earrings', 'ruby', NULL, 1.00),
('impostor_pin', 'black', NULL, 1.00),
('impostor_pin', 'blue', NULL, 1.00),
('impostor_pin', 'brown', NULL, 1.00),
('impostor_pin', 'cyan', NULL, 1.00),
('impostor_pin', 'green', NULL, 1.00),
('impostor_pin', 'lime', NULL, 1.00),
('impostor_pin', 'orange', NULL, 1.00),
('impostor_pin', 'pink', NULL, 1.00),
('impostor_pin', 'purple', NULL, 1.00),
('impostor_pin', 'red', NULL, 1.00),
('impostor_pin', 'white', NULL, 1.00),
('impostor_pin', 'yellow', NULL, 1.00),
('leather_bracelet', '', NULL, 1.00),
('moai_pin', '', NULL, 1000.00),
('pebble_bracelet', '', NULL, 1.00),
('simple_bracelet', 'black_steel', NULL, 1.00),
('simple_bracelet', 'copper', NULL, 1.00),
('simple_bracelet', 'gunmetal', NULL, 1.00),
('simple_ring', 'black_steel', NULL, 1.00),
('simple_ring', 'copper', NULL, 1.00),
('simple_ring', 'gunmetal', NULL, 1.00),
('skull_ring', 'black_steel', NULL, 1.00),
('skull_ring', 'copper', NULL, 1.00),
('skull_ring', 'gunmetal', NULL, 1.00),
('snake_ring', 'black_steel', NULL, 1.00),
('snake_ring', 'gunmetal', NULL, 1.00);

DROP TABLE IF EXISTS `variant`;
CREATE TABLE IF NOT EXISTS `variant` (
  `suffix` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`suffix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `variant`;
INSERT INTO `variant` (`suffix`, `display_name`) VALUES
('', NULL),
('black', 'Black'),
('black_steel', 'Black Steel'),
('blue', 'Blue'),
('brown', 'Brown'),
('copper', 'Copper'),
('cyan', 'Cyan'),
('green', 'Green'),
('gunmetal', 'Gunmetal'),
('lime', 'Lime'),
('obsidian', 'Obsidian'),
('orange', 'Orange'),
('pink', 'Pink'),
('purple', 'Purple'),
('red', 'Red'),
('ruby', 'Ruby'),
('white', 'White'),
('yellow', 'Yellow');

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
