SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `isifitgems` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `isifitgems`;

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
  `category` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` varchar(1023) NOT NULL,
  PRIMARY KEY (`code`),
  KEY `FOREIGN` (`category`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product`;
INSERT INTO `product` (`code`, `category`, `display_name`, `description`) VALUES
('barbed_wire_bracelet', 'bracelets', 'Barbed Wire Bracelet', 'The classic symbol of alternative culture.'),
('chain_bracelet', 'bracelets', 'Chain Bracelet', 'Stylish and fashionable with a touch of edginess.'),
('chain_necklace', 'necklaces', 'Chain Necklace', 'A necklace. Made of chain.'),
('choker', 'necklaces', 'Choker', 'Why so serious?'),
('colorful_ring', 'rings', 'Colorful Ring', 'A ring which happens to be colorful.'),
('colorful_string_bracelet', 'bracelets', 'Colorful String Bracelet', 'A bracelet which is made of string and is colorful.'),
('dagger_earrings', 'earrings', 'Dagger Earrings', 'Not very effective for stabbing people.'),
('dragon_pendant', 'necklaces', 'Dragon Pendant', 'Dragon deez nuts.'),
('flame_earrings', 'earrings', 'Flame Earrings', 'Not made of actual fire.'),
('flower_bracelet', 'bracelets', 'Flower Bracelet', 'It is recommended to water the flowers daily to keep them from withering.'),
('gem_earrings', 'earrings', 'Gem Earrings', 'Whether you believe in their power, gems are sure to complement your attire.'),
('impostor_pin', 'pins', 'Impostor Pin', 'Might make you look a bit sus.'),
('leather_bracelet', 'bracelets', 'Leather Bracelet', 'Cows were definitely harmed to make this product.'),
('moai_pin', 'pins', 'Moai Pin', '🗿.'),
('pebble_bracelet', 'bracelets', 'Pebble Bracelet', 'Did you know a group of pebbles is called a stoner?'),
('raven_pendant', 'necklaces', 'Raven Pendant', 'The spirit animal of goth culture.'),
('simple_bracelet', 'bracelets', 'Simple Bracelet', 'Less is more.'),
('simple_ring', 'rings', 'Simple Ring', 'A classic minimal design, suitable for all occasions.'),
('skull_ring', 'rings', 'Skull Ring', 'A staple of macabre accessories.'),
('snake_ring', 'rings', 'Snake Ring', 'Ssssss, sssss sss ssssss.');

DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE IF NOT EXISTS `product_variant` (
  `suffix` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `variant_display_name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `bundle_only` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`suffix`,`product`),
  KEY `FOREIGN` (`product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

TRUNCATE TABLE `product_variant`;
INSERT INTO `product_variant` (`suffix`, `product`, `variant_display_name`, `image`, `price`, `bundle_only`) VALUES
('base', 'choker', '', NULL, 1.00, 0),
('base', 'flower_bracelet', '', NULL, 1.00, 1),
('base', 'leather_bracelet', '', NULL, 1.00, 1),
('base', 'moai_pin', '', NULL, 1000.00, 0),
('base', 'pebble_bracelet', '', NULL, 1.00, 1),
('black', 'colorful_ring', 'Black', NULL, 1.00, 1),
('black', 'colorful_string_bracelet', 'Black', NULL, 1.00, 1),
('black', 'impostor_pin', 'Black', NULL, 1.00, 0),
('black_steel', 'barbed_wire_bracelet', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'chain_bracelet', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'dagger_earrings', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'flame_earrings', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'simple_bracelet', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'simple_ring', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'skull_ring', 'Black Steel', NULL, 1.00, 0),
('black_steel', 'snake_ring', 'Black Steel', NULL, 1.00, 0),
('blue', 'colorful_ring', 'Blue', NULL, 1.00, 1),
('blue', 'colorful_string_bracelet', 'Blue', NULL, 1.00, 1),
('blue', 'impostor_pin', 'Blue', NULL, 1.00, 0),
('brown', 'colorful_ring', 'Brown', NULL, 1.00, 1),
('brown', 'colorful_string_bracelet', 'Brown', NULL, 1.00, 1),
('brown', 'impostor_pin', 'Brown', NULL, 1.00, 0),
('copper', 'flame_earrings', 'Copper', NULL, 1.00, 0),
('copper', 'simple_bracelet', 'Copper', NULL, 1.00, 0),
('copper', 'simple_ring', 'Copper', NULL, 1.00, 0),
('copper', 'skull_ring', 'Copper', NULL, 1.00, 0),
('cyan', 'colorful_ring', 'Cyan', NULL, 1.00, 1),
('cyan', 'colorful_string_bracelet', 'Cyan', NULL, 1.00, 1),
('cyan', 'impostor_pin', 'Cyan', NULL, 1.00, 0),
('green', 'colorful_ring', 'Green', NULL, 1.00, 1),
('green', 'colorful_string_bracelet', 'Green', NULL, 1.00, 1),
('green', 'impostor_pin', 'Green', NULL, 1.00, 0),
('gunmetal', 'barbed_wire_bracelet', 'Gunmetal', NULL, 1.00, 0),
('gunmetal', 'chain_bracelet', 'Gunmetal', NULL, 1.00, 0),
('gunmetal', 'dagger_earrings', 'Gunmetal', NULL, 1.00, 0),
('gunmetal', 'simple_bracelet', 'Gunmetal', NULL, 1.00, 0),
('gunmetal', 'simple_ring', 'Gunmetal', NULL, 1.00, 0),
('gunmetal', 'skull_ring', 'Gunmetal', NULL, 1.00, 0),
('gunmetal', 'snake_ring', 'Gunmetal', NULL, 1.00, 0),
('lime', 'colorful_ring', 'Lime', NULL, 1.00, 1),
('lime', 'colorful_string_bracelet', 'Lime', NULL, 1.00, 1),
('lime', 'impostor_pin', 'Lime', NULL, 1.00, 0),
('obsidian', 'gem_earrings', 'Obsidian', NULL, 1.00, 0),
('orange', 'colorful_ring', 'Orange', NULL, 1.00, 1),
('orange', 'colorful_string_bracelet', 'Orange', NULL, 1.00, 1),
('orange', 'impostor_pin', 'Orange', NULL, 1.00, 0),
('pink', 'colorful_ring', 'Pink', NULL, 1.00, 1),
('pink', 'colorful_string_bracelet', 'Pink', NULL, 1.00, 1),
('pink', 'impostor_pin', 'Pink', NULL, 1.00, 0),
('purple', 'colorful_ring', 'Purple', NULL, 1.00, 1),
('purple', 'colorful_string_bracelet', 'Purple', NULL, 1.00, 1),
('purple', 'impostor_pin', 'Purple', NULL, 1.00, 0),
('red', 'colorful_ring', 'Red', NULL, 1.00, 1),
('red', 'colorful_string_bracelet', 'Red', NULL, 1.00, 1),
('red', 'impostor_pin', 'Red', NULL, 1.00, 0),
('ruby', 'gem_earrings', 'Ruby', NULL, 1.00, 0),
('white', 'colorful_ring', 'White', NULL, 1.00, 1),
('white', 'colorful_string_bracelet', 'White', NULL, 1.00, 1),
('white', 'impostor_pin', 'White', NULL, 1.00, 0),
('yellow', 'colorful_ring', 'Yellow', NULL, 1.00, 1),
('yellow', 'colorful_string_bracelet', 'Yellow', NULL, 1.00, 1),
('yellow', 'impostor_pin', 'Yellow', NULL, 1.00, 0);


ALTER TABLE `product`
  ADD CONSTRAINT `product_fk_category` FOREIGN KEY (`category`) REFERENCES `category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `product_variant`
  ADD CONSTRAINT `product_variant_fk_product` FOREIGN KEY (`product`) REFERENCES `product` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
