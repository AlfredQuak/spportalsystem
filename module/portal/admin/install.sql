 CREATE TABLE IF NOT EXISTS `sp_modul_portal_rss_counter_user` (
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM  ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_modul_menue` (
  `pos` int(2) NOT NULL,
  `name` varchar(32) NOT NULL,
  `modul_active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`name`),
  UNIQUE KEY `pos` (`pos`)
) ENGINE=MyISAM  ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_menue_main_side` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `page_id` int(10) NOT NULL,
  `link_text` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `position` int(2) NOT NULL,
  `box` int(11) DEFAULT NULL,
  `lang_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_page` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `titel_text` text NOT NULL,
  `page_content` text NOT NULL,
  `page_lang` int(11) NOT NULL,
  `page_relation` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_menue_sub_side` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `page_id` int(10) NOT NULL,
  `link_text` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `position` int(10) NOT NULL,
  `mainlink_id` int(10) NOT NULL,
  `box` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_cms_news` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `news_date` text NOT NULL,
  `titel_text` text NOT NULL,
  `page_content` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM   ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_settings` (
  `ID` int(11) NOT NULL auto_increment,
  `func_rss` tinyint(1) NOT NULL,
  `func_news` tinyint(1) NOT NULL,
  `func_contact` tinyint(1) NOT NULL,
  `rss_counter` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM   ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `box_new` tinyint(1) NOT NULL,
  `box_edit` tinyint(1) NOT NULL,
  `side_new` tinyint(1) NOT NULL,
  `side_edit` tinyint(1) NOT NULL,
  `news_new` tinyint(1) NOT NULL,
  `news_edit` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM   ;
CREATE TABLE IF NOT EXISTS `sp_modul_portal_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_lang` varchar(30) NOT NULL,
  `lang_name` varchar(40) NOT NULL,
  `lang_picture` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_lang` (`lang_lang`)
) ENGINE=MyISAM   ;
INSERT INTO `sp_modul_settings` ( `modul_name`, `modul_active`, `modul_installed`, `modul_admin_box`, `modul_admin_box_r`, `modul_box_activ`, `modul_box_r`, `modul_box_content`, `modul_box_content_dyn`, `modul_box_titel`, `modul_box_pos`) VALUES ( 'portal', 1, 1, 1, 1, 0, 0, '', 0, '', 0);
INSERT INTO `sp_modul_portal_settings` (`ID`, `func_rss`, `func_news`, `func_contact`, `rss_counter`) VALUES (1, 0, 0, 0, 0);
INSERT INTO `sp_modul_portal_language` (`id`, `lang_lang`, `lang_name`, `lang_picture`) VALUES (1, 'de', 'Germany', '');