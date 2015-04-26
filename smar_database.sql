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
  `barcode` int(15) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` double NOT NULL DEFAULT '0',
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
  `barcode` int(15) DEFAULT NULL,
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
  `barcode` int(15) unsigned DEFAULT NULL,
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
  `created` datetime NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `name` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `smar_user`
--

INSERT INTO `smar_user` (`user_id`, `pnr`, `surname`, `lastname`, `username`, `role_web`, `role_device`, `password`, `salt`, `created`, `lastupdate`) VALUES
(1, '123456', 'Admin', 'istrator', 'admin', 1, 0, '2323c469df9947e4e3525ea5e8cf701ee72775e374304282a9fb47074283a0b3', '0fd1d92468325d2350699fce039d2b342a5a435740fc13b0f0bbd4bbf1e2c2fe', '2015-03-02 12:13:42', '2015-03-02 11:16:36');

--
-- Constraints der exportierten Tabellen
--

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
