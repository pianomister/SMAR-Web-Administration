-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 02. Mrz 2015 um 15:43
-- Server Version: 5.5.32
-- PHP-Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `smar`
--
CREATE DATABASE IF NOT EXISTS `smar` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `smar`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_delivery`
--

CREATE TABLE IF NOT EXISTS `smar_delivery` (
  `delivery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`order_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`delivery_id`),
	KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_delivery`:
--   `order_id`
--       `smar_order` -> `order_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_order_item`
--

CREATE TABLE IF NOT EXISTS `smar_delivery_item` (
  `delivery_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `amount` int(15) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`delivery_item_id`),
  KEY `unit_id` (`unit_id`),
  KEY `product_id` (`product_id`),
  KEY `delivery_id` (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_delivery_item`:
--   `delivery_id`
--       `smar_delivery` -> `delivery_id`
--   `unit_id`
--       `smar_unit` -> `unit_id`
--   `product_id`
--       `smar_product` -> `product_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_device`
--

CREATE TABLE IF NOT EXISTS `smar_device` (
  `device_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `hwaddress` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `activated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`device_id`),
  UNIQUE KEY `hwaddress` (`hwaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_map`
--

CREATE TABLE IF NOT EXISTS `smar_map` (
  `map_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `size_x` int(6) NOT NULL DEFAULT '0',
  `size_y` int(6) NOT NULL DEFAULT '0',
  `size_z` int(6) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_map_shelf`
--

CREATE TABLE IF NOT EXISTS `smar_map_shelf` (
  `map_shelf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shelf_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `position_x` int(6) NOT NULL DEFAULT '0',
  `position_y` int(6) NOT NULL DEFAULT '0',
  `position_z` int(6) NOT NULL DEFAULT '0',
  `rotation` int(3) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`map_shelf_id`),
  UNIQUE KEY `shelf_id` (`shelf_id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_map_shelf`:
--   `shelf_id`
--       `smar_shelf` -> `shelf_id`
--   `map_id`
--       `smar_map` -> `map_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_order`
--

CREATE TABLE IF NOT EXISTS `smar_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `date` datetime NOT NULL,
  `barcode` bigint(15) unsigned DEFAULT NULL,
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_order_item`
--

CREATE TABLE IF NOT EXISTS `smar_order_item` (
  `order_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `amount` int(15) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `unit_id` (`unit_id`),
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_order_item`:
--   `order_id`
--       `smar_order` -> `order_id`
--   `unit_id`
--       `smar_unit` -> `unit_id`
--   `product_id`
--       `smar_product` -> `product_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_product`
--

CREATE TABLE IF NOT EXISTS `smar_product` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_nr` varchar(30) DEFAULT NULL,
  `barcode` bigint(15) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` double NOT NULL DEFAULT '0',
	`size_x` int(3) unsigned NOT NULL DEFAULT '0',
  `size_y` int(3) NOT NULL DEFAULT '0',
  `size_z` int(3) NOT NULL DEFAULT '0',
	`stackable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `image` varchar(150) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_product_unit`
--

CREATE TABLE IF NOT EXISTS `smar_product_unit` (
  `product_unit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `barcode` bigint(15) unsigned DEFAULT NULL,
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_unit_id`),
  KEY `unit_id` (`unit_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_product_unit`:
--   `product_id`
--       `smar_product` -> `product_id`
--   `unit_id`
--       `smar_unit` -> `unit_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_section`
--

CREATE TABLE IF NOT EXISTS `smar_section` (
  `section_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shelf_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `capacity` int(15) unsigned NOT NULL DEFAULT '0',
	`min_capacity` int(15) unsigned NOT NULL DEFAULT '0',
  `size_x` int(3) NOT NULL DEFAULT '0',
  `size_y` int(3) NOT NULL DEFAULT '0',
  `position_x` int(5) NOT NULL DEFAULT '0',
  `position_y` int(5) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`section_id`),
  KEY `shelf_id` (`shelf_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_section`:
--   `product_id`
--       `smar_product` -> `product_id`
--   `shelf_id`
--       `smar_shelf` -> `shelf_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_shelf`
--

CREATE TABLE IF NOT EXISTS `smar_shelf` (
  `shelf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `barcode` bigint(15) unsigned DEFAULT NULL,
  `size_x` int(5) NOT NULL DEFAULT '0',
  `size_y` int(5) NOT NULL DEFAULT '0',
  `size_z` int(5) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shelf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_shelf_graphic`
--

CREATE TABLE IF NOT EXISTS `smar_shelf_graphic` (
  `shelf_graphic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shelf_id` int(10) unsigned NOT NULL,
  `graphic` text NOT NULL COMMENT 'XML+SVG',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shelf_graphic_id`),
  KEY `shelf_id` (`shelf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_shelf_graphic`:
--   `shelf_id`
--       `smar_shelf` -> `shelf_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_stock`
--

CREATE TABLE IF NOT EXISTS `smar_stock` (
  `stock_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `amount_warehouse` int(10) NOT NULL DEFAULT '0',
  `amount_shop` int(10) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`stock_id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONEN DER TABELLE `smar_stock`:
--   `product_id`
--       `smar_product` -> `product_id`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_unit`
--

CREATE TABLE IF NOT EXISTS `smar_unit` (
  `unit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `capacity` int(15) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_user`
--

CREATE TABLE IF NOT EXISTS `smar_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pnr` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `surname` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastname` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `role_web` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `role_device` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `password` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `salt` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password_device` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `name` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `smar_user`
--

INSERT INTO `smar_user` (`user_id`, `pnr`, `surname`, `lastname`, `username`, `role_web`, `role_device`, `password`, `salt`, `password_device`, `created`, `lastupdate`) VALUES
(1, '123456', 'Admin', 'istrator', 'admin', 70, 1, '2323c469df9947e4e3525ea5e8cf701ee72775e374304282a9fb47074283a0b3', '0fd1d92468325d2350699fce039d2b342a5a435740fc13b0f0bbd4bbf1e2c2fe', NULL, '2015-03-02 12:13:42', '2015-05-01 16:35:48');

--
-- Beispieldaten TODO
--
INSERT INTO `smar_product` (`product_id`, `article_nr`, `barcode`, `name`, `price`, `size_x`, `size_y`, `size_z`, `stackable`, `image`, `created`, `lastupdate`) VALUES
(1, 'ART953173', 953173, 'LekkaLekka Crunchy Bio-Chips', 2.99, 0, 0, 0, 0, 'NULL', '2015-04-03 00:16:12', '2015-04-02 22:16:12'),
(2, 'ART473623', 584372615, 'MAMF Pampelmusencreme Brotaufstrich', 4.39, 0, 0, 0, 0, 'NULL', '2015-04-04 14:01:22', '2015-04-04 12:01:22'),
(3, 'ART483721', 32847362, 'Förstermeister Kräuterlikör', 12.49, 12, 20, 4, 0, 'NULL', '2015-04-04 14:05:15', '2015-05-17 12:41:56'),
(4, 'ART213211', 342761552, 'EKIA Garten-Klappstuhl', 21.99, 0, 0, 0, 0, 'NULL', '2015-04-04 14:06:53', '2015-04-17 12:17:32'),
(5, 'ART230391', 2147483646, 'Vitalitasia Wasser still', 0.19, 0, 0, 0, 0, 'NULL', '2015-04-17 13:06:26', '2015-04-29 08:39:05'),
(6, 'ART493872', 2147483647, 'Rotwein', 3.39, 8, 32, 8, 1, 'NULL', '2015-05-16 18:56:30', '2015-05-17 12:42:14'),
(7, 'ART20000123', 2147483647, 'Holzuhr', 149, 5, 3, 10, 1, 'NULL', '2015-05-24 12:30:13', '2015-05-24 10:30:13'),
(8, 'ART23123488', 238472376, 'SSD Festplatte 128GB', 69.9, 15, 4, 20, 1, 'NULL', '2015-05-24 12:32:56', '2015-05-24 10:32:56');
INSERT INTO `smar_stock` (`stock_id`, `product_id`, `amount_warehouse`, `amount_shop`, `created`, `lastupdate`) VALUES
(1, 7, 0, 0, '2015-05-24 12:30:13', '2015-05-24 10:30:13'),
(2, 8, 2383, 23, '2015-05-24 12:32:56', '2015-05-24 10:32:56'),
(3, 1, 123, 4576, '2015-03-02 12:13:42', '2015-05-24 10:34:14'),
(4, 2, 423, 98, '2015-03-02 12:13:42', '2015-05-24 10:34:14'),
(5, 3, 0, 0, '2015-03-02 12:12:00', '2015-05-24 10:34:32'),
(6, 4, 0, 0, '2015-03-02 12:12:00', '2015-05-24 10:34:32'),
(7, 5, 45, 32, NULL, '2015-05-24 10:34:51'),
(8, 6, 153, 37, '2015-03-02 12:12:00', '2015-05-24 10:34:51');
INSERT INTO `smar_unit` (`unit_id`, `name`, `capacity`, `created`, `lastupdate`) VALUES
(1, 'Single', 1, '2015-04-17 14:51:33', '2015-04-17 12:52:56'),
(2, 'Box 50', 50, '2015-04-17 14:59:38', '2015-04-17 12:59:38'),
(3, 'Box 100', 100, '2015-04-17 14:59:46', '2015-04-17 12:59:46'),
(4, 'Box 500', 500, '2015-04-17 15:01:08', '2015-04-17 13:11:30');
INSERT INTO `smar_shelf` (`shelf_id`, `name`, `barcode`, `size_x`, `size_y`, `size_z`, `created`, `lastupdate`) VALUES
(1, 'Süßwaren', 123324235, 1200, 200, 50, '2015-04-19 13:04:01', '2015-04-22 00:01:25'),
(2, 'Softgetränke', 2147483647, 600, 180, 60, '2015-04-19 13:07:35', '2015-04-22 00:01:35'),
(3, 'Weine', 34636234, 400, 200, 40, '2015-04-19 16:32:03', '2015-04-19 14:32:03');
INSERT INTO `smar_section` (`section_id`, `shelf_id`, `product_id`, `name`, `capacity`, `size_x`, `size_y`, `position_x`, `position_y`, `created`, `lastupdate`) VALUES
(1, 1, 3, 'Likör', 30, 50, 30, 30, 0, '2015-04-22 01:00:00', '2015-04-25 12:57:25'),
(2, 1, 2, 'Aufstrich', 80, 80, 40, 200, 70, '2015-04-22 11:18:00', '2015-04-22 09:19:07'),
(4, 2, 4, 'Test-Sektion', 120, 40, 40, 0, 0, '2015-04-25 14:43:25', '2015-04-25 17:14:33'),
(5, 2, 3, 'Noch mehr Waldmeister', 20, 40, 60, 300, 0, '2015-04-25 19:05:25', '2015-04-25 17:05:25');





--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `smar_delivery`
--
ALTER TABLE `smar_delivery`
  ADD CONSTRAINT `smar_delivery_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `smar_order` (`order_id`);

--
-- Constraints der Tabelle `smar_delivery_item`
--
ALTER TABLE `smar_delivery_item`
  ADD CONSTRAINT `smar_delivery_item_ibfk_3` FOREIGN KEY (`delivery_id`) REFERENCES `smar_delivery` (`delivery_id`),
  ADD CONSTRAINT `smar_delivery_item_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `smar_unit` (`unit_id`),
  ADD CONSTRAINT `smar_delivery_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`);

--
-- Constraints der Tabelle `smar_map_shelf`
--
ALTER TABLE `smar_map_shelf`
  ADD CONSTRAINT `smar_map_shelf_ibfk_2` FOREIGN KEY (`shelf_id`) REFERENCES `smar_shelf` (`shelf_id`),
  ADD CONSTRAINT `smar_map_shelf_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `smar_map` (`map_id`);

--
-- Constraints der Tabelle `smar_order_item`
--
ALTER TABLE `smar_order_item`
  ADD CONSTRAINT `smar_order_item_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `smar_order` (`order_id`),
  ADD CONSTRAINT `smar_order_item_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `smar_unit` (`unit_id`),
  ADD CONSTRAINT `smar_order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`);

--
-- Constraints der Tabelle `smar_product_unit`
--
ALTER TABLE `smar_product_unit`
  ADD CONSTRAINT `smar_product_unit_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`),
  ADD CONSTRAINT `smar_product_unit_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `smar_unit` (`unit_id`);

--
-- Constraints der Tabelle `smar_section`
--
ALTER TABLE `smar_section`
  ADD CONSTRAINT `smar_section_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`),
  ADD CONSTRAINT `smar_section_ibfk_1` FOREIGN KEY (`shelf_id`) REFERENCES `smar_shelf` (`shelf_id`);

--
-- Constraints der Tabelle `smar_shelf_graphic`
--
ALTER TABLE `smar_shelf_graphic`
  ADD CONSTRAINT `smar_shelf_graphic_ibfk_1` FOREIGN KEY (`shelf_id`) REFERENCES `smar_shelf` (`shelf_id`);

--
-- Constraints der Tabelle `smar_stock`
--
ALTER TABLE `smar_stock`
  ADD CONSTRAINT `smar_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
