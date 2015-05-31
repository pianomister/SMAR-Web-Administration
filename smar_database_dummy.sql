-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 31. Mai 2015 um 23:07
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `smar_delivery_item`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `smar_order`
--

INSERT INTO `smar_order` (`order_id`, `name`, `date`, `barcode`, `created`, `lastupdate`) VALUES
(1, 'Bestellung B17362', '2015-05-30 10:54:00', 8534762552364, '2015-05-30 18:54:16', '2015-05-30 16:54:16');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `smar_order_item`
--

INSERT INTO `smar_order_item` (`order_item_id`, `order_id`, `product_id`, `unit_id`, `amount`, `created`, `lastupdate`) VALUES
(1, 1, 3, 3, 3, '2015-05-31 23:08:00', '2015-05-31 18:57:30'),
(2, 1, 7, 5, 8, '2015-05-31 23:08:00', '2015-05-31 18:57:30'),
(3, 1, 11, 4, 1, '2015-05-31 23:08:00', '2015-05-31 18:58:38'),
(4, 1, 12, 4, 1, '2015-05-31 23:08:00', '2015-05-31 18:58:38'),
(5, 1, 8, 3, 1, '2015-05-31 23:08:00', '2015-05-31 18:59:12');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Daten für Tabelle `smar_product`
--

INSERT INTO `smar_product` (`product_id`, `article_nr`, `barcode`, `name`, `price`, `size_x`, `size_y`, `size_z`, `stackable`, `image`, `created`, `lastupdate`) VALUES
(1, 'ART01001', 95317334, 'LekkaLekka Crunchy Bio-Chips', 2.99, 0, 0, 0, 0, NULL, '2015-04-03 00:16:12', '2015-05-31 21:05:33'),
(2, 'ART01002', 58437261, 'American Cookies', 4.39, 0, 0, 0, 0, NULL, '2015-04-04 14:01:22', '2015-05-31 21:05:31'),
(3, 'ART01003', 32847362, 'Doppelkekse', 1.99, 0, 0, 0, 0, NULL, '2015-04-04 14:05:15', '2015-05-31 21:05:30'),
(4, 'ART01004', 34276155, 'Eukalyptus-Bonbons', 2.49, 0, 0, 0, 0, NULL, '2015-04-04 14:06:53', '2015-05-31 21:05:27'),
(5, 'ART01005', 21474836, 'Tafel Schokolade', 0.99, 0, 0, 0, 0, NULL, '2015-04-17 13:06:26', '2015-05-31 21:05:25'),
(6, 'ART01006', 21494836, 'Butterkekse', 3.39, 0, 0, 0, 0, NULL, '2015-05-16 18:56:30', '2015-05-31 21:05:23'),
(7, 'ART01007', 22147483, 'Gummibärchen', 1.89, 0, 0, 0, 0, NULL, '2015-05-24 12:30:13', '2015-05-31 21:05:19'),
(8, 'ART02001', 38573625, 'Förstermeister Kräuterlikör', 12.49, 0, 0, 0, 0, NULL, '2015-05-31 20:27:18', '2015-05-31 21:05:35'),
(9, 'ART02002', 39251623, 'Portugieser Rotwein', 4.39, 0, 0, 0, 0, NULL, '2015-05-31 20:28:00', '2015-05-31 21:05:40'),
(10, 'ART02003', 58372615, 'Mineralwasser still', 0.19, 0, 0, 0, 0, NULL, '2015-05-31 20:28:40', '2015-05-31 21:05:46'),
(11, 'ART02004', 58372616, 'Mineralwasser medium', 0.19, 0, 0, 0, 0, NULL, '2015-05-31 20:29:01', '2015-05-31 21:05:44'),
(12, 'ART02005', 58372617, 'Mineralwasser classic', 0.19, 0, 0, 0, 0, NULL, '2015-05-31 20:29:52', '2015-05-31 21:05:42');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Daten für Tabelle `smar_product_unit`
--

INSERT INTO `smar_product_unit` (`product_unit_id`, `product_id`, `unit_id`, `barcode`, `created`, `lastupdate`) VALUES
(1, 1, 1, 23942398, '2015-05-25 18:00:00', '2015-05-31 18:46:30'),
(3, 6, 1, 34538475, '2015-05-25 21:04:14', '2015-05-31 18:46:30'),
(4, 3, 1, 45398475, '2015-05-25 21:28:32', '2015-05-31 18:46:30'),
(6, 1, 5, 12345678, '2015-05-25 21:57:47', '2015-05-31 18:56:10'),
(7, 2, 1, 91827364, '2015-05-31 20:48:24', '2015-05-31 18:48:24'),
(8, 5, 1, 18273642, '2015-05-31 20:48:24', '2015-05-31 18:48:24'),
(9, 7, 1, 88473654, '2015-05-31 20:48:24', '2015-05-31 18:48:24'),
(10, 4, 1, 99182734, '2015-05-31 20:48:24', '2015-05-31 18:48:24'),
(11, 8, 1, 55837123, '2015-05-31 20:49:27', '2015-05-31 18:49:27'),
(12, 9, 1, 55938273, '2015-05-31 20:49:27', '2015-05-31 18:49:27'),
(13, 10, 1, 55009283, '2015-05-31 20:49:27', '2015-05-31 18:49:27'),
(14, 11, 1, 55983723, '2015-05-31 20:49:27', '2015-05-31 18:49:27'),
(15, 12, 1, 55239384, '2015-05-31 20:49:27', '2015-05-31 18:49:27'),
(16, 9, 3, 33221923, '2015-05-31 20:51:06', '2015-05-31 18:51:06'),
(17, 8, 3, 33281723, '2015-05-31 20:51:06', '2015-05-31 18:51:06'),
(18, 2, 3, 66543821, '2015-05-31 20:51:06', '2015-05-31 18:51:06'),
(19, 3, 3, 77446352, '2015-05-31 20:51:06', '2015-05-31 18:51:06'),
(20, 5, 3, 77162583, '2015-05-31 20:51:06', '2015-05-31 18:51:06'),
(21, 10, 4, 11223345, '2015-05-31 20:52:44', '2015-05-31 18:55:26'),
(22, 11, 4, 11223346, '2015-05-31 20:52:44', '2015-05-31 18:55:25'),
(23, 12, 4, 11223347, '2015-05-31 20:52:44', '2015-05-31 18:55:25'),
(24, 6, 5, 11335577, '2015-05-31 20:56:10', '2015-05-31 18:56:10'),
(25, 7, 5, 19283344, '2015-05-31 20:56:10', '2015-05-31 18:56:10');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Daten für Tabelle `smar_section`
--

INSERT INTO `smar_section` (`section_id`, `shelf_id`, `product_id`, `name`, `capacity`, `min_capacity`, `size_x`, `size_y`, `position_x`, `position_y`, `created`, `lastupdate`) VALUES
(1, 2, 9, 'Rotwein', 30, 10, 100, 100, 0, 80, '2015-04-22 01:00:00', '2015-05-31 18:44:58'),
(2, 2, 10, 'Wasser still', 200, 40, 100, 180, 100, 0, '2015-04-22 11:18:00', '2015-05-31 18:44:58'),
(4, 2, 8, 'Likör', 120, 20, 100, 80, 0, 0, '2015-04-25 14:43:25', '2015-05-31 18:44:58'),
(8, 2, 11, 'Wasser medium', 200, 40, 100, 180, 200, 0, '2015-05-17 12:30:42', '2015-05-31 18:44:58'),
(9, 1, 1, 'Chips', 80, 10, 60, 200, 0, 0, '2015-05-31 20:32:57', '2015-05-31 18:59:11'),
(10, 1, 2, 'Cookies', 50, 10, 90, 130, 140, 70, '2015-05-31 20:33:52', '2015-05-31 18:59:11'),
(11, 1, 3, 'Kekse', 90, 20, 80, 140, 60, 0, '2015-05-31 20:34:20', '2015-05-31 18:59:11'),
(12, 1, 4, 'Bonbons', 90, 5, 80, 60, 60, 140, '2015-05-31 20:34:44', '2015-05-31 18:59:11'),
(13, 1, 5, 'Schokolade', 140, 5, 90, 70, 140, 0, '2015-05-31 20:35:21', '2015-05-31 18:59:11'),
(14, 2, 12, 'Wasser classic', 200, 40, 100, 180, 300, 0, '2015-05-31 20:42:36', '2015-05-31 18:44:58'),
(15, 1, 6, 'Butterkekse', 80, 20, 70, 100, 230, 100, '2015-05-31 20:59:41', '2015-05-31 19:00:36'),
(16, 1, 7, 'Gummibärchen', 100, 30, 70, 100, 230, 0, '2015-05-31 20:59:59', '2015-05-31 19:00:36');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `smar_shelf`
--

INSERT INTO `smar_shelf` (`shelf_id`, `name`, `barcode`, `size_x`, `size_y`, `size_z`, `created`, `lastupdate`) VALUES
(1, 'Süßwaren', 12332423, 300, 200, 50, '2015-04-19 13:04:01', '2015-05-31 18:31:26'),
(2, 'Getränke', 98725362, 400, 180, 60, '2015-04-19 13:07:35', '2015-05-31 18:44:32');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `smar_shelf_graphic`
--

INSERT INTO `smar_shelf_graphic` (`shelf_graphic_id`, `shelf_id`, `graphic`, `created`, `lastupdate`) VALUES
(1, 2, '<?xml version="1.0" encoding="UTF-8" standalone="no" ?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd"><svg width="400" height="180" viewBox="0 0 400 180" style="width:100%;height: auto;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">	<title>Shelf ''Getränke'' (ID: 2, last updated: ''31.05.2015 20:44:32'')</title>		<defs>		<style type="text/css">		<![CDATA[		text {fill: #333;font-family:Roboto;font-size: 8px;}		rect {fill:#ccc; stroke:#777; stroke-width: 2px;}		.section {fill:#ddd; stroke:#555; stroke-width: 1px; opacity:.8;}		.selected {fill:#16a082;stroke:#107861;}		.textselected {fill:#fff}		]]>		</style>	</defs>		<rect id="shelf2" x="0" y="0" width="400" height="180" />	<rect id="section1" class="section" x="0" y="80" width="100" height="100" />							 <text id="section1-text" x="5" y="95">1: Rotwein</text><rect id="section2" class="section" x="100" y="0" width="100" height="180" />							 <text id="section2-text" x="105" y="15">2: Wasser still</text><rect id="section4" class="section" x="0" y="0" width="100" height="80" />							 <text id="section4-text" x="5" y="15">4: Likör</text><rect id="section8" class="section" x="200" y="0" width="100" height="180" />							 <text id="section8-text" x="205" y="15">8: Wasser medium</text><rect id="section14" class="section" x="300" y="0" width="100" height="180" />							 <text id="section14-text" x="305" y="15">14: Wasser classic</text></svg>', '2015-05-30 19:42:02', '2015-05-31 21:04:45'),
(2, 1, '<?xml version="1.0" encoding="UTF-8" standalone="no" ?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd"><svg width="300" height="200" viewBox="0 0 300 200" style="width:100%;height: auto;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">	<title>Shelf ''Süßwaren'' (ID: 1, last updated: ''31.05.2015 20:31:26'')</title>		<defs>		<style type="text/css">		<![CDATA[		text {fill: #333;font-family:Roboto;font-size: 8px;}		rect {fill:#ccc; stroke:#777; stroke-width: 2px;}		.section {fill:#ddd; stroke:#555; stroke-width: 1px; opacity:.8;}		.selected {fill:#16a082;stroke:#107861;}		.textselected {fill:#fff}		]]>		</style>	</defs>		<rect id="shelf1" x="0" y="0" width="300" height="200" />	<rect id="section9" class="section" x="0" y="0" width="60" height="200" />							 <text id="section9-text" x="5" y="15">9: Chips</text><rect id="section10" class="section" x="140" y="70" width="90" height="130" />							 <text id="section10-text" x="145" y="85">10: Cookies</text><rect id="section11" class="section" x="60" y="0" width="80" height="140" />							 <text id="section11-text" x="65" y="15">11: Kekse</text><rect id="section12" class="section" x="60" y="140" width="80" height="60" />							 <text id="section12-text" x="65" y="155">12: Bonbons</text><rect id="section13" class="section" x="140" y="0" width="90" height="70" />							 <text id="section13-text" x="145" y="15">13: Schokolade</text><rect id="section15" class="section" x="230" y="100" width="70" height="100" />							 <text id="section15-text" x="235" y="115">15: Butterkekse</text><rect id="section16" class="section" x="230" y="0" width="70" height="100" />							 <text id="section16-text" x="235" y="15">16: Gummibärchen</text></svg>', '2015-05-31 20:31:28', '2015-05-31 19:00:39');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Daten für Tabelle `smar_stock`
--

INSERT INTO `smar_stock` (`stock_id`, `product_id`, `amount_warehouse`, `amount_shop`, `created`, `lastupdate`) VALUES
(1, 7, 200, 17, '2015-05-24 12:30:13', '2015-05-31 18:26:24'),
(3, 1, 150, 74, '2015-03-02 12:13:42', '2015-05-31 18:17:44'),
(4, 2, 450, 98, '2015-03-02 12:13:42', '2015-05-31 18:18:25'),
(5, 3, 200, 12, '2015-03-02 12:12:00', '2015-05-31 18:19:41'),
(6, 4, 80, 32, '2015-03-02 12:12:00', '2015-05-31 18:22:05'),
(7, 5, 330, 48, '2015-05-31 20:24:16', '2015-05-31 21:06:22'),
(8, 6, 153, 37, '2015-03-02 12:12:00', '2015-05-24 10:34:51'),
(9, 8, 60, 26, '2015-05-31 20:27:18', '2015-05-31 18:27:18'),
(10, 9, 90, 65, '2015-05-31 20:28:00', '2015-05-31 18:28:00'),
(11, 10, 500, 123, '2015-05-31 20:28:40', '2015-05-31 18:28:40'),
(12, 11, 320, 173, '2015-05-31 20:29:01', '2015-05-31 18:29:01'),
(13, 12, 670, 39, '2015-05-31 20:29:52', '2015-05-31 18:29:52');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `smar_unit`
--

INSERT INTO `smar_unit` (`unit_id`, `name`, `capacity`, `created`, `lastupdate`) VALUES
(1, 'Single', 1, '2015-04-17 14:51:33', '2015-04-17 12:52:56'),
(3, 'Box 100', 100, '2015-04-17 14:59:46', '2015-04-17 12:59:46'),
(4, 'Palette 500', 500, '2015-04-17 15:01:08', '2015-05-31 18:45:51'),
(5, 'Box 50', 50, '2015-05-24 18:11:46', '2015-05-24 16:11:46');

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
(1, '1582885', '', 'Administrator', 'admin', 90, 1, '2323c469df9947e4e3525ea5e8cf701ee72775e374304282a9fb47074283a0b3', '0fd1d92468325d2350699fce039d2b342a5a435740fc13b0f0bbd4bbf1e2c2fe', '5627a3351231fb54c654ec49b85196bc9db01f4a8b71d3f4fb3fc33bdc584a52', '2015-03-02 12:13:42', '2015-05-29 08:24:15');

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
  ADD CONSTRAINT `smar_delivery_item_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `smar_unit` (`unit_id`),
  ADD CONSTRAINT `smar_delivery_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`),
  ADD CONSTRAINT `smar_delivery_item_ibfk_3` FOREIGN KEY (`delivery_id`) REFERENCES `smar_delivery` (`delivery_id`);

--
-- Constraints der Tabelle `smar_map_shelf`
--
ALTER TABLE `smar_map_shelf`
  ADD CONSTRAINT `smar_map_shelf_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `smar_map` (`map_id`),
  ADD CONSTRAINT `smar_map_shelf_ibfk_2` FOREIGN KEY (`shelf_id`) REFERENCES `smar_shelf` (`shelf_id`);

--
-- Constraints der Tabelle `smar_order_item`
--
ALTER TABLE `smar_order_item`
  ADD CONSTRAINT `smar_order_item_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `smar_unit` (`unit_id`),
  ADD CONSTRAINT `smar_order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`),
  ADD CONSTRAINT `smar_order_item_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `smar_order` (`order_id`);

--
-- Constraints der Tabelle `smar_product_unit`
--
ALTER TABLE `smar_product_unit`
  ADD CONSTRAINT `smar_product_unit_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `smar_unit` (`unit_id`),
  ADD CONSTRAINT `smar_product_unit_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`);

--
-- Constraints der Tabelle `smar_section`
--
ALTER TABLE `smar_section`
  ADD CONSTRAINT `smar_section_ibfk_1` FOREIGN KEY (`shelf_id`) REFERENCES `smar_shelf` (`shelf_id`),
  ADD CONSTRAINT `smar_section_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `smar_product` (`product_id`);

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
