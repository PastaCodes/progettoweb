SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `isifitgems` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `isifitgems`;

DROP TABLE IF EXISTS `bundle`;
CREATE TABLE IF NOT EXISTS `bundle` (
  `code_name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `multiplier` float NOT NULL,
  PRIMARY KEY (`code_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `bundle`;
INSERT INTO `bundle` (`code_name`, `display_name`, `multiplier`) VALUES
('dragon_bundle', 'Dragon Bundle', 0.8),
('moai_bundle', 'Moai Bundle', 0.9);
DROP VIEW IF EXISTS `bundle_variant`;
CREATE TABLE IF NOT EXISTS `bundle_variant` (
`bundle` varchar(255)
,`code_suffix` varchar(255)
);

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `code_name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  PRIMARY KEY (`code_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `category`;
INSERT INTO `category` (`code_name`, `display_name`) VALUES
('bracelets', 'Bracelets'),
('earrings', 'Earrings'),
('necklaces', 'Necklaces'),
('pins', 'Pins'),
('rings', 'Rings');

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `notification`;DROP VIEW IF EXISTS `price_range`;
CREATE TABLE IF NOT EXISTS `price_range` (
`product` varchar(255)
,`price_min` decimal(10,2)
,`price_max` decimal(10,2)
);

DROP TABLE IF EXISTS `product_base`;
CREATE TABLE IF NOT EXISTS `product_base` (
  `code_name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `standalone` tinyint(1) NOT NULL,
  PRIMARY KEY (`code_name`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `product_base`;
INSERT INTO `product_base` (`code_name`, `category`, `display_name`, `short_description`, `standalone`) VALUES
('barbed_wire_bracelet', 'bracelets', 'Barbed Wire Bracelet', 'The classic symbol of alternative culture.', 1),
('chain_bracelet', 'bracelets', 'Chain Bracelet', 'Stylish and fashionable with a touch of edginess.', 1),
('chain_necklace', 'necklaces', 'Chain Necklace', 'A necklace. Made of chain.', 1),
('choker', 'necklaces', 'Choker', 'Why so serious?', 1),
('colorful_ring', 'rings', 'Colorful Ring', 'A ring which happens to be colorful.', 0),
('colorful_string_bracelet', 'bracelets', 'Colorful String Bracelet', 'A bracelet which is made of string and is colorful.', 0),
('dagger_earrings', 'earrings', 'Dagger Earrings', 'Not very effective for stabbing people.', 1),
('dragon_pendant', 'necklaces', 'Dragon Pendant', 'Dragon deez nuts.', 1),
('flame_earrings', 'earrings', 'Flame Earrings', 'Not made of actual fire.', 1),
('flower_bracelet', 'bracelets', 'Flower Bracelet', 'It is recommended to water the flowers daily to keep them from withering.', 0),
('gem_earrings', 'earrings', 'Gem Earrings', 'Whether you believe in their power, gems are sure to complement your attire.', 1),
('impostor_pin', 'pins', 'Impostor Pin', 'Might make you look a bit sus.', 1),
('leather_bracelet', 'bracelets', 'Leather Bracelet', 'Cows were definitely harmed to make this product.', 0),
('moai_pin', 'pins', 'Moai Pin', '?.', 1),
('pebble_bracelet', 'bracelets', 'Pebble Bracelet', 'Did you know a group of pebbles is called a stoner?', 0),
('raven_pendant', 'necklaces', 'Raven Pendant', 'The spirit animal of goth culture.', 1),
('simple_bracelet', 'bracelets', 'Simple Bracelet', 'Less is more.', 1),
('simple_ring', 'rings', 'Simple Ring', 'A classic minimal design, suitable for all occasions.', 1),
('skull_ring', 'rings', 'Skull Ring', 'A staple of macabre accessories.', 1),
('snake_ring', 'rings', 'Snake Ring', 'Ssssss, sssss sss ssssss.', 1);

DROP TABLE IF EXISTS `product_info`;
CREATE TABLE IF NOT EXISTS `product_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(255) NOT NULL,
  `variant` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product` (`product`,`variant`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `product_info`;
INSERT INTO `product_info` (`id`, `product`, `variant`, `price`) VALUES
(1, 'choker', NULL, 1.00),
(2, 'dagger_earrings', NULL, 1.00),
(3, 'flower_bracelet', NULL, 1.00),
(4, 'leather_bracelet', NULL, 1.00),
(5, 'moai_pin', NULL, 1000.00),
(6, 'pebble_bracelet', NULL, 1.00),
(7, 'raven_pendant', NULL, 7.00),
(8, 'colorful_ring', 'red', 1.00),
(9, 'colorful_ring', 'blue', 1.00),
(10, 'colorful_ring', 'green', 1.00),
(11, 'colorful_ring', 'pink', 1.00),
(12, 'colorful_ring', 'orange', 1.00),
(13, 'colorful_ring', 'yellow', 1.00),
(14, 'colorful_ring', 'black', 1.00),
(15, 'colorful_ring', 'white', 1.00),
(16, 'colorful_ring', 'purple', 1.00),
(17, 'colorful_ring', 'brown', 1.00),
(18, 'colorful_ring', 'cyan', 1.00),
(19, 'colorful_ring', 'lime', 1.00),
(20, 'colorful_string_bracelet', 'red', 1.00),
(21, 'colorful_string_bracelet', 'blue', 1.00),
(22, 'colorful_string_bracelet', 'green', 1.00),
(23, 'colorful_string_bracelet', 'pink', 1.00),
(24, 'colorful_string_bracelet', 'orange', 1.00),
(25, 'colorful_string_bracelet', 'yellow', 1.00),
(26, 'colorful_string_bracelet', 'black', 1.00),
(27, 'colorful_string_bracelet', 'white', 1.00),
(28, 'colorful_string_bracelet', 'purple', 1.00),
(29, 'colorful_string_bracelet', 'brown', 1.00),
(30, 'colorful_string_bracelet', 'cyan', 1.00),
(31, 'colorful_string_bracelet', 'lime', 1.00),
(32, 'impostor_pin', 'red', 1.00),
(33, 'impostor_pin', 'blue', 1.00),
(34, 'impostor_pin', 'green', 1.00),
(35, 'impostor_pin', 'pink', 1.00),
(36, 'impostor_pin', 'orange', 1.00),
(37, 'impostor_pin', 'yellow', 1.00),
(38, 'impostor_pin', 'black', 1.00),
(39, 'impostor_pin', 'white', 1.00),
(40, 'impostor_pin', 'purple', 1.00),
(41, 'impostor_pin', 'brown', 1.00),
(42, 'impostor_pin', 'cyan', 1.00),
(43, 'impostor_pin', 'lime', 1.00),
(44, 'barbed_wire_bracelet', 'black_steel', 1.00),
(45, 'barbed_wire_bracelet', 'chrome', 1.00),
(46, 'chain_bracelet', 'gunmetal', 1.00),
(47, 'chain_bracelet', 'copper', 1.00),
(48, 'chain_bracelet', 'black_steel', 1.00),
(49, 'chain_necklace', 'gunmetal', 2.00),
(50, 'chain_necklace', 'black_steel', 3.00),
(51, 'dragon_pendant', 'gunmetal', 2.00),
(52, 'dragon_pendant', 'copper', 1.00),
(53, 'dragon_pendant', 'black_steel', 2.00),
(54, 'flame_earrings', 'copper', 1.00),
(55, 'flame_earrings', 'black_steel', 1.00),
(56, 'simple_bracelet', 'gunmetal', 1.00),
(57, 'simple_bracelet', 'copper', 1.00),
(58, 'simple_bracelet', 'black_steel', 1.00),
(59, 'simple_ring', 'gunmetal', 1.00),
(60, 'simple_ring', 'copper', 1.00),
(61, 'simple_ring', 'black_steel', 1.00),
(62, 'skull_ring', 'gunmetal', 1.00),
(63, 'skull_ring', 'copper', 1.00),
(64, 'skull_ring', 'black_steel', 1.00),
(65, 'snake_ring', 'gunmetal', 1.00),
(66, 'snake_ring', 'black_steel', 1.00),
(67, 'gem_earrings', 'ruby', 1.00),
(68, 'gem_earrings', 'obsidian', 1.00),
(69, 'gem_earrings', 'demon_core', 0.00);

DROP TABLE IF EXISTS `product_in_bundle`;
CREATE TABLE IF NOT EXISTS `product_in_bundle` (
  `base` varchar(255) NOT NULL,
  `bundle` varchar(255) NOT NULL,
  PRIMARY KEY (`base`,`bundle`),
  KEY `bundle` (`bundle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `product_in_bundle`;
INSERT INTO `product_in_bundle` (`base`, `bundle`) VALUES
('dragon_pendant', 'dragon_bundle'),
('flame_earrings', 'dragon_bundle'),
('flower_bracelet', 'moai_bundle'),
('moai_pin', 'moai_bundle'),
('pebble_bracelet', 'moai_bundle');

DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE IF NOT EXISTS `product_variant` (
  `base` varchar(255) NOT NULL,
  `code_suffix` varchar(255) NOT NULL,
  `ordinal` smallint(6) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `color` char(6) NOT NULL,
  PRIMARY KEY (`base`,`code_suffix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `product_variant`;
INSERT INTO `product_variant` (`base`, `code_suffix`, `ordinal`, `display_name`, `color`) VALUES
('barbed_wire_bracelet', 'black_steel', 0, 'Black Steel', '181818'),
('barbed_wire_bracelet', 'chrome', 1, 'Chrome', 'A0A0A0'),
('chain_bracelet', 'black_steel', 2, 'Black Steel', '181818'),
('chain_bracelet', 'copper', 1, 'Copper', 'C68346'),
('chain_bracelet', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
('chain_necklace', 'black_steel', 1, 'Black Steel', '181818'),
('chain_necklace', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
('colorful_ring', 'black', 6, 'Black', '3F474E'),
('colorful_ring', 'blue', 1, 'Blue', '132ED1'),
('colorful_ring', 'brown', 9, 'Brown', '71491E'),
('colorful_ring', 'cyan', 10, 'Cyan', '38FADC'),
('colorful_ring', 'green', 2, 'Green', '117F2D'),
('colorful_ring', 'lime', 11, 'Lime', '50EF39'),
('colorful_ring', 'orange', 4, 'Orange', 'EF7D0D'),
('colorful_ring', 'pink', 3, 'Pink', 'ED54BA'),
('colorful_ring', 'purple', 8, 'Purple', '6B2FBB'),
('colorful_ring', 'red', 0, 'Red', 'C51111'),
('colorful_ring', 'white', 7, 'White', 'D6E0F0'),
('colorful_ring', 'yellow', 5, 'Yellow', 'F5F557'),
('colorful_string_bracelet', 'black', 6, 'Black', '3F474E'),
('colorful_string_bracelet', 'blue', 1, 'Blue', '132ED1'),
('colorful_string_bracelet', 'brown', 9, 'Brown', '71491E'),
('colorful_string_bracelet', 'cyan', 10, 'Cyan', '38FADC'),
('colorful_string_bracelet', 'green', 2, 'Green', '117F2D'),
('colorful_string_bracelet', 'lime', 11, 'Lime', '50EF39'),
('colorful_string_bracelet', 'orange', 4, 'Orange', 'EF7D0D'),
('colorful_string_bracelet', 'pink', 3, 'Pink', 'ED54BA'),
('colorful_string_bracelet', 'purple', 8, 'Purple', '6B2FBB'),
('colorful_string_bracelet', 'red', 0, 'Red', 'C51111'),
('colorful_string_bracelet', 'white', 7, 'White', 'D6E0F0'),
('colorful_string_bracelet', 'yellow', 5, 'Yellow', 'F5F557'),
('dragon_pendant', 'black_steel', 2, 'Black Steel', '181818'),
('dragon_pendant', 'copper', 1, 'Copper', 'C68346'),
('dragon_pendant', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
('flame_earrings', 'black_steel', 1, 'Black Steel', '181818'),
('flame_earrings', 'copper', 0, 'Copper', 'C68346'),
('gem_earrings', 'demon_core', 2, 'Demon Core', '808080'),
('gem_earrings', 'obsidian', 1, 'Obsidian', '1C1020'),
('gem_earrings', 'ruby', 0, 'Ruby', '800020'),
('impostor_pin', 'black', 6, 'Black', '3F474E'),
('impostor_pin', 'blue', 1, 'Blue', '132ED1'),
('impostor_pin', 'brown', 9, 'Brown', '71491E'),
('impostor_pin', 'cyan', 10, 'Cyan', '38FADC'),
('impostor_pin', 'green', 2, 'Green', '117F2D'),
('impostor_pin', 'lime', 11, 'Lime', '50EF39'),
('impostor_pin', 'orange', 4, 'Orange', 'EF7D0D'),
('impostor_pin', 'pink', 3, 'Pink', 'ED54BA'),
('impostor_pin', 'purple', 8, 'Purple', '6B2FBB'),
('impostor_pin', 'red', 0, 'Red', 'C51111'),
('impostor_pin', 'white', 7, 'White', 'D6E0F0'),
('impostor_pin', 'yellow', 5, 'Yellow', 'F5F557'),
('simple_bracelet', 'black_steel', 2, 'Black Steel', '181818'),
('simple_bracelet', 'copper', 1, 'Copper', 'C68346'),
('simple_bracelet', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
('simple_ring', 'black_steel', 2, 'Black Steel', '181818'),
('simple_ring', 'copper', 1, 'Copper', 'C68346'),
('simple_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
('skull_ring', 'black_steel', 2, 'Black Steel', '181818'),
('skull_ring', 'copper', 1, 'Copper', 'C68346'),
('skull_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C'),
('snake_ring', 'black_steel', 1, 'Black Steel', '181818'),
('snake_ring', 'gunmetal', 0, 'Gunmetal', '2C2C2C');
DROP TABLE IF EXISTS `bundle_variant`;

DROP VIEW IF EXISTS `bundle_variant`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `bundle_variant`  AS WITH product_count_in_bundle AS (SELECT `product_in_bundle`.`bundle` AS `bundle`, count(`product_in_bundle`.`base`) AS `product_count` FROM `product_in_bundle` GROUP BY `product_in_bundle`.`bundle`), variant_count_in_bundle AS (SELECT `product_in_bundle`.`bundle` AS `bundle`, `product_variant`.`code_suffix` AS `code_suffix`, count(`product_in_bundle`.`base`) AS `variant_count` FROM (`product_in_bundle` join `product_variant` on(`product_variant`.`base` = `product_in_bundle`.`base`)) GROUP BY `product_in_bundle`.`bundle`, `product_variant`.`code_suffix`) SELECT `variant_count_in_bundle`.`bundle` AS `bundle`, `variant_count_in_bundle`.`code_suffix` AS `code_suffix` FROM (`variant_count_in_bundle` join `product_count_in_bundle` on(`product_count_in_bundle`.`bundle` = `variant_count_in_bundle`.`bundle`)) WHERE `variant_count_in_bundle`.`variant_count` = `product_count_in_bundle`.`product_count` GROUP BY `variant_count_in_bundle`.`bundle`, `variant_count_in_bundle`.`code_suffix``code_suffix`  ;
DROP TABLE IF EXISTS `price_range`;

DROP VIEW IF EXISTS `price_range`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `price_range`  AS SELECT `product_info`.`product` AS `product`, min(`product_info`.`price`) AS `price_min`, max(`product_info`.`price`) AS `price_max` FROM `product_info` GROUP BY `product_info`.`product` ;


ALTER TABLE `product_base`
  ADD CONSTRAINT `product_base_ibfk_1` FOREIGN KEY (`category`) REFERENCES `category` (`code_name`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `product_info`
  ADD CONSTRAINT `product_info_ibfk_1` FOREIGN KEY (`product`) REFERENCES `product_base` (`code_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_info_ibfk_2` FOREIGN KEY (`product`,`variant`) REFERENCES `product_variant` (`base`, `code_suffix`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product_in_bundle`
  ADD CONSTRAINT `product_in_bundle_ibfk_1` FOREIGN KEY (`base`) REFERENCES `product_base` (`code_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_in_bundle_ibfk_2` FOREIGN KEY (`bundle`) REFERENCES `bundle` (`code_name`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_variant_ibfk_1` FOREIGN KEY (`base`) REFERENCES `product_base` (`code_name`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
