CREATE TABLE IF NOT EXISTS `sp_logging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_ip` text NOT NULL,
  `log_functionlvl` text NOT NULL,
  `log_usrlvl` int(11) NOT NULL,
  `log_callMethod` text NOT NULL,
  `log_info` text NOT NULL,
  `log_user` text NOT NULL,
  `log_message` text NOT NULL,
  PRIMARY KEY (`id`)
);
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
);
CREATE TABLE IF NOT EXISTS `sp_session` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(12) NOT NULL,
  `user_sess_id` varchar(128) NOT NULL,
  `user_ip` char(128) NOT NULL,
  `session_time` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
);
CREATE TABLE IF NOT EXISTS `sp_settings` (
  `id` int(1) NOT NULL,
  `start_modul` varchar(32) NOT NULL,
  `counter` int(11) NOT NULL,
  `b` varchar(1) NOT NULL,
  `c` varchar(1) NOT NULL,
  `d` varchar(1) NOT NULL,
  KEY `start_modul` (`start_modul`)
);
CREATE TABLE IF NOT EXISTS `sp_settings_counter` (
  `ip` varchar(15) NOT NULL default '',
  `time` datetime NOT NULL
);
CREATE TABLE IF NOT EXISTS `sp_user` (
  `id` int(10) NOT NULL auto_increment,
  `real_firstname` varchar(32) NOT NULL,
  `real_lastname` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `user_groups` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `sp_user_group` (
  `id` int(11) NOT NULL auto_increment,
  `groupe_name` char(32) NOT NULL,
  `admin` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `groupe_name` (`groupe_name`)
);
CREATE TABLE IF NOT EXISTS `sp_modul_admin_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `modul_settings` tinyint(1) NOT NULL,
  `permission_setting` tinyint(1) NOT NULL,
  `system_settings` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
);
CREATE TABLE IF NOT EXISTS `sp_modul_admin_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip1` int(11) NOT NULL,
  `ip2` int(11) NOT NULL,
  `ip3` int(11) NOT NULL,
  `ip4` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
INSERT INTO `sp_modul_admin_settings`  (`ip1` ,`ip2` ,`ip3` ,`ip4`)VALUES ('0', '0', '0', '0');
INSERT INTO `sp_modul_admin_permissions` (`modul_settings`, `permission_setting`, `system_settings`) VALUES (1, 1, 1);
INSERT INTO `sp_modul_settings` (`ID`, `modul_name`, `modul_active`, `modul_installed`, `modul_admin_box`, `modul_admin_box_r`, `modul_box_activ`, `modul_box_r`, `modul_box_content`, `modul_box_content_dyn`, `modul_box_titel`) VALUES (1, 'admin', 1, 1, 0, 0, 0, 0, '', 0, '');
INSERT INTO `sp_settings` (`id`, `start_modul`, `counter`, `b`, `c`, `d`) VALUES (0, 'noModul', '0', '', '', '');