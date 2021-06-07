-- phpMyAdmin SQL Dump
-- version 3.3.7deb5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 29. Juni 2011 um 16:20
-- Server Version: 5.1.54
-- PHP-Version: 5.3.5-0.dotdeb.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `spportal_portal`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_admin_permissions`
--

CREATE TABLE IF NOT EXISTS `sp_modul_admin_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modul_settings` tinyint(1) NOT NULL,
  `permission_setting` tinyint(1) NOT NULL,
  `system_settings` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `sp_modul_admin_permissions`
--

INSERT INTO `sp_modul_admin_permissions` (`id`, `modul_settings`, `permission_setting`, `system_settings`) VALUES
(1, 1, 1, 1),
(2, 1, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_admin_settings`
--

CREATE TABLE IF NOT EXISTS `sp_modul_admin_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip1` int(11) NOT NULL,
  `ip2` int(11) NOT NULL,
  `ip3` int(11) NOT NULL,
  `ip4` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `sp_modul_admin_settings`
--

INSERT INTO `sp_modul_admin_settings` (`id`, `ip1`, `ip2`, `ip3`, `ip4`) VALUES
(1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_cms_menue_main_side`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_menue_main_side` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `page_id` int(10) NOT NULL,
  `link_text` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `position` int(2) NOT NULL,
  `box` int(11) DEFAULT NULL,
  `lang_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `sp_modul_portal_cms_menue_main_side`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_cms_menue_sub_side`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_menue_sub_side` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `page_id` int(10) NOT NULL,
  `link_text` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `position` int(10) NOT NULL,
  `mainlink_id` int(10) NOT NULL,
  `box` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `sp_modul_portal_cms_menue_sub_side`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_cms_news`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_news` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `news_date` text NOT NULL,
  `titel_text` text NOT NULL,
  `page_content` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `sp_modul_portal_cms_news`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_cms_page`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_page` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `titel_text` text NOT NULL,
  `page_content` text NOT NULL,
  `page_lang` int(11) NOT NULL,
  `page_relation` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `sp_modul_portal_cms_page`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_domain`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_domain` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `domainLangs` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `sp_modul_portal_domain`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_language`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_lang` varchar(30) NOT NULL,
  `lang_name` varchar(40) NOT NULL,
  `lang_picture` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_lang` (`lang_lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `sp_modul_portal_language`
--

INSERT INTO `sp_modul_portal_language` (`id`, `lang_lang`, `lang_name`, `lang_picture`) VALUES
(1, 'de', 'Germany', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_modul_menue`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_modul_menue` (
  `pos` int(2) NOT NULL,
  `name` varchar(32) NOT NULL,
  `modul_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`name`),
  UNIQUE KEY `pos` (`pos`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `sp_modul_portal_modul_menue`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_permissions`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_new` tinyint(1) NOT NULL,
  `box_edit` tinyint(1) NOT NULL,
  `side_new` tinyint(1) NOT NULL,
  `side_edit` tinyint(1) NOT NULL,
  `news_new` tinyint(1) NOT NULL,
  `news_edit` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `sp_modul_portal_permissions`
--

INSERT INTO `sp_modul_portal_permissions` (`id`, `box_new`, `box_edit`, `side_new`, `side_edit`, `news_new`, `news_edit`) VALUES
(2, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_rss_counter_user`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_rss_counter_user` (
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `sp_modul_portal_rss_counter_user`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_portal_settings`
--

CREATE TABLE IF NOT EXISTS `sp_modul_portal_settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `func_rss` tinyint(1) NOT NULL,
  `func_news` tinyint(1) NOT NULL,
  `func_contact` tinyint(1) NOT NULL,
  `rss_counter` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `sp_modul_portal_settings`
--

INSERT INTO `sp_modul_portal_settings` (`ID`, `func_rss`, `func_news`, `func_contact`, `rss_counter`) VALUES
(1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_modul_settings`
--

CREATE TABLE IF NOT EXISTS `sp_modul_settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `modul_name` varchar(32) NOT NULL,
  `modul_active` tinyint(1) NOT NULL,
  `modul_installed` tinyint(1) NOT NULL,
  `modul_admin_box` tinyint(1) NOT NULL,
  `modul_admin_box_r` tinyint(1) NOT NULL,
  `modul_box_activ` tinyint(1) NOT NULL,
  `modul_box_r` tinyint(1) NOT NULL,
  `modul_box_content` text NOT NULL,
  `modul_box_content_dyn` tinyint(1) NOT NULL,
  `modul_box_titel` varchar(32) NOT NULL,
  `modul_box_pos` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `sp_modul_settings`
--

INSERT INTO `sp_modul_settings` (`ID`, `modul_name`, `modul_active`, `modul_installed`, `modul_admin_box`, `modul_admin_box_r`, `modul_box_activ`, `modul_box_r`, `modul_box_content`, `modul_box_content_dyn`, `modul_box_titel`, `modul_box_pos`) VALUES
(1, 'admin', 1, 1, 0, 0, 0, 0, '', 0, '', 0),
(2, 'portal', 1, 1, 1, 1, 0, 0, '', 0, '', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_session`
--

CREATE TABLE IF NOT EXISTS `sp_session` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(12) NOT NULL,
  `user_sess_id` varchar(128) NOT NULL,
  `user_ip` char(128) NOT NULL,
  `session_time` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `sp_session`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_settings`
--

CREATE TABLE IF NOT EXISTS `sp_settings` (
  `id` int(1) NOT NULL,
  `start_modul` varchar(32) NOT NULL,
  `counter` int(1) NOT NULL,
  `b` varchar(1) NOT NULL,
  `c` varchar(1) NOT NULL,
  `d` varchar(1) NOT NULL,
  KEY `start_modul` (`start_modul`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `sp_settings`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_settings_counter`
--

CREATE TABLE IF NOT EXISTS `sp_settings_counter` (
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `sp_settings_counter`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_user`
--

CREATE TABLE IF NOT EXISTS `sp_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `real_firstname` varchar(32) NOT NULL,
  `real_lastname` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `user_groups` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Daten für Tabelle `sp_user`
--

INSERT INTO `sp_user` (`id`, `real_firstname`, `real_lastname`, `name`, `password`, `email`, `user_groups`, `active`) VALUES
(1, 'root', 'root', 'root', '098f6bcd4621d373cade4e832627b4f6', 'test@test.de', 'N;', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sp_user_group`
--

CREATE TABLE IF NOT EXISTS `sp_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupe_name` char(32) NOT NULL,
  `admin` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `groupe_name` (`groupe_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `sp_user_group`
--

INSERT INTO `sp_user_group` (`id`, `groupe_name`, `admin`) VALUES
(1, 'Administrator', 'a:3:{s:5:"admin";a:2:{s:5:"admin";s:4:"true";s:2:"id";i:2;}s:6:"portal";a:2:{s:2:"id";i:2;s:5:"admin";s:4:"true";}s:9:"fileShare";a:2:{s:2:"id";i:3;s:5:"admin";s:4:"true";}}');
