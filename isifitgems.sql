SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `isifitgems` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `isifitgems`;

DROP TABLE IF EXISTS `bundle`;
CREATE TABLE IF NOT EXISTS `bundle` (
  `code` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `multiplier` float NOT NULL DEFAULT 1,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `bundle`;
INSERT INTO `bundle` (`code`, `display_name`, `multiplier`) VALUES
('dragon_bundle', 'Dragon Bundle', 0.8);

DROP TABLE IF EXISTS `bundle_content`;
CREATE TABLE IF NOT EXISTS `bundle_content` (
  `bundle` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `variant` varchar(255) NOT NULL,
  PRIMARY KEY (`bundle`,`product`,`variant`) USING BTREE,
  KEY `FOREIGN` (`product`,`bundle`,`variant`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `bundle_content`;
INSERT INTO `bundle_content` (`bundle`, `product`, `variant`) VALUES
('dragon_bundle', 'dragon_pendant', 'copper'),
('dragon_bundle', 'flame_earrings', 'copper');

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
DROP VIEW IF EXISTS `price_range`;
CREATE TABLE IF NOT EXISTS `price_range` (
`product` varchar(255)
,`price_min` decimal(10,2)
,`price_max` decimal(10,2)
);

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `code` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(1023) NOT NULL,
  `bundle_only` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`code`),
  KEY `FOREIGN` (`category`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product`;
INSERT INTO `product` (`code`, `category`, `display_name`, `description`, `bundle_only`) VALUES
('barbed_wire_bracelet', 'bracelets', 'Barbed Wire Bracelet', 'The classic symbol of alternative culture.', 0),
('chain_bracelet', 'bracelets', 'Chain Bracelet', 'Stylish and fashionable with a touch of edginess.', 0),
('chain_necklace', 'necklaces', 'Chain Necklace', 'A necklace. Made of chain.', 0),
('choker', 'necklaces', 'Choker', 'Why so serious?', 0),
('colorful_ring', 'rings', 'Colorful Ring', 'A ring which happens to be colorful.', 1),
('colorful_string_bracelet', 'bracelets', 'Colorful String Bracelet', 'A bracelet which is made of string and is colorful.', 1),
('dagger_earrings', 'earrings', 'Dagger Earrings', 'Not very effective for stabbing people.', 0),
('dragon_pendant', 'necklaces', 'Dragon Pendant', 'Dragon deez nuts.', 0),
('flame_earrings', 'earrings', 'Flame Earrings', 'Not made of actual fire.', 0),
('flower_bracelet', 'bracelets', 'Flower Bracelet', 'It is recommended to water the flowers daily to keep them from withering.', 1),
('gem_earrings', 'earrings', 'Gem Earrings', 'Whether you believe in their power, gems are sure to complement your attire.', 0),
('impostor_pin', 'pins', 'Impostor Pin', 'Might make you look a bit sus.', 0),
('leather_bracelet', 'bracelets', 'Leather Bracelet', 'Cows were definitely harmed to make this product.', 1),
('moai_pin', 'pins', 'Moai Pin', '🗿.', 0),
('pebble_bracelet', 'bracelets', 'Pebble Bracelet', 'Did you know a group of pebbles is called a stoner?', 1),
('raven_pendant', 'necklaces', 'Raven Pendant', 'The spirit animal of goth culture.', 0),
('simple_bracelet', 'bracelets', 'Simple Bracelet', 'Less is more.', 0),
('simple_ring', 'rings', 'Simple Ring', 'A classic minimal design, suitable for all occasions.', 0),
('skull_ring', 'rings', 'Skull Ring', 'A staple of macabre accessories.', 0),
('snake_ring', 'rings', 'Snake Ring', 'Ssssss, sssss sss ssssss.', 0);

DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE IF NOT EXISTS `product_variant` (
  `suffix` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `variant_display_name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `color` char(6) NOT NULL DEFAULT 'FFFFFF',
  PRIMARY KEY (`suffix`,`product`),
  KEY `FOREIGN` (`product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product_variant`;
INSERT INTO `product_variant` (`suffix`, `product`, `variant_display_name`, `image`, `price`, `color`) VALUES
('', 'choker', NULL, NULL, 1.00, 'FFFFFF'),
('', 'flower_bracelet', NULL, NULL, 1.00, 'FFFFFF'),
('', 'leather_bracelet', NULL, NULL, 1.00, 'FFFFFF'),
('', 'moai_pin', NULL, NULL, 1000.00, 'FFFFFF'),
('', 'pebble_bracelet', NULL, NULL, 1.00, 'FFFFFF'),
('', 'raven_pendant', NULL, NULL, 7.00, 'FFFFFF'),
('black', 'colorful_ring', 'Black', NULL, 1.00, 'FFFFFF'),
('black', 'colorful_string_bracelet', 'Black', NULL, 1.00, 'FFFFFF'),
('black', 'impostor_pin', 'Black', NULL, 1.00, 'FFFFFF'),
('black_steel', 'barbed_wire_bracelet', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'chain_bracelet', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'chain_necklace', 'Black Steel', NULL, 3.00, 'FFFFFF'),
('black_steel', 'dagger_earrings', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'dragon_pendant', 'Black Steel', NULL, 2.00, 'FFFFFF'),
('black_steel', 'flame_earrings', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'simple_bracelet', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'simple_ring', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'skull_ring', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('black_steel', 'snake_ring', 'Black Steel', NULL, 1.00, 'FFFFFF'),
('blue', 'colorful_ring', 'Blue', NULL, 1.00, 'FFFFFF'),
('blue', 'colorful_string_bracelet', 'Blue', NULL, 1.00, 'FFFFFF'),
('blue', 'impostor_pin', 'Blue', NULL, 1.00, 'FFFFFF'),
('brown', 'colorful_ring', 'Brown', NULL, 1.00, 'FFFFFF'),
('brown', 'colorful_string_bracelet', 'Brown', NULL, 1.00, 'FFFFFF'),
('brown', 'impostor_pin', 'Brown', NULL, 1.00, 'FFFFFF'),
('copper', 'dragon_pendant', 'Copper', NULL, 2.00, 'C68346'),
('copper', 'flame_earrings', 'Copper', NULL, 1.00, 'C68346'),
('copper', 'simple_bracelet', 'Copper', NULL, 1.00, 'C68346'),
('copper', 'simple_ring', 'Copper', NULL, 1.00, 'C68346'),
('copper', 'skull_ring', 'Copper', NULL, 1.00, 'C68346'),
('cyan', 'colorful_ring', 'Cyan', NULL, 1.00, 'FFFFFF'),
('cyan', 'colorful_string_bracelet', 'Cyan', NULL, 1.00, 'FFFFFF'),
('cyan', 'impostor_pin', 'Cyan', NULL, 1.00, 'FFFFFF'),
('green', 'colorful_ring', 'Green', NULL, 1.00, 'FFFFFF'),
('green', 'colorful_string_bracelet', 'Green', NULL, 1.00, 'FFFFFF'),
('green', 'impostor_pin', 'Green', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'barbed_wire_bracelet', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'chain_bracelet', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'chain_necklace', 'Gunmetal', NULL, 2.00, 'FFFFFF'),
('gunmetal', 'dagger_earrings', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'dragon_pendant', 'Gunmetal', NULL, 2.00, 'FFFFFF'),
('gunmetal', 'simple_bracelet', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'simple_ring', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'skull_ring', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('gunmetal', 'snake_ring', 'Gunmetal', NULL, 1.00, 'FFFFFF'),
('lime', 'colorful_ring', 'Lime', NULL, 1.00, 'FFFFFF'),
('lime', 'colorful_string_bracelet', 'Lime', NULL, 1.00, 'FFFFFF'),
('lime', 'impostor_pin', 'Lime', NULL, 1.00, 'FFFFFF'),
('obsidian', 'gem_earrings', 'Obsidian', NULL, 1.00, 'FFFFFF'),
('orange', 'colorful_ring', 'Orange', NULL, 1.00, 'FFFFFF'),
('orange', 'colorful_string_bracelet', 'Orange', NULL, 1.00, 'FFFFFF'),
('orange', 'impostor_pin', 'Orange', NULL, 1.00, 'FFFFFF'),
('pink', 'colorful_ring', 'Pink', NULL, 1.00, 'FFFFFF'),
('pink', 'colorful_string_bracelet', 'Pink', NULL, 1.00, 'FFFFFF'),
('pink', 'impostor_pin', 'Pink', NULL, 1.00, 'FFFFFF'),
('purple', 'colorful_ring', 'Purple', NULL, 1.00, 'FFFFFF'),
('purple', 'colorful_string_bracelet', 'Purple', NULL, 1.00, 'FFFFFF'),
('purple', 'impostor_pin', 'Purple', NULL, 1.00, 'FFFFFF'),
('red', 'colorful_ring', 'Red', NULL, 1.00, 'FFFFFF'),
('red', 'colorful_string_bracelet', 'Red', NULL, 1.00, 'FFFFFF'),
('red', 'impostor_pin', 'Red', NULL, 1.00, 'FFFFFF'),
('ruby', 'gem_earrings', 'Ruby', NULL, 1.00, 'FFFFFF'),
('white', 'colorful_ring', 'White', NULL, 1.00, 'FFFFFF'),
('white', 'colorful_string_bracelet', 'White', NULL, 1.00, 'FFFFFF'),
('white', 'impostor_pin', 'White', NULL, 1.00, 'FFFFFF'),
('yellow', 'colorful_ring', 'Yellow', NULL, 1.00, 'FFFFFF'),
('yellow', 'colorful_string_bracelet', 'Yellow', NULL, 1.00, 'FFFFFF'),
('yellow', 'impostor_pin', 'Yellow', NULL, 1.00, 'FFFFFF');
DROP TABLE IF EXISTS `price_range`;

DROP VIEW IF EXISTS `price_range`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `price_range`  AS   (select `product_variant`.`product` AS `product`,min(`product_variant`.`price`) AS `price_min`,max(`product_variant`.`price`) AS `price_max` from `product_variant` group by `product_variant`.`product`)  ;


ALTER TABLE `bundle_content`
  ADD CONSTRAINT `bundle_content_fk_bundle` FOREIGN KEY (`bundle`) REFERENCES `bundle` (`code`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bundle_content_fk_product` FOREIGN KEY (`product`) REFERENCES `product_variant` (`product`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bundle_content_fk_variant` FOREIGN KEY (`variant`) REFERENCES `product_variant` (`suffix`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product`
  ADD CONSTRAINT `product_fk_category` FOREIGN KEY (`category`) REFERENCES `category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_variant_fk_product` FOREIGN KEY (`product`) REFERENCES `product` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
