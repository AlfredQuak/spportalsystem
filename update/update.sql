ALTER TABLE `sp_modul_settings` CHANGE `modul_box_r` `modul_box_r` INT( 1 ) NOT NULL
ALTER TABLE `sp_modul_portal_cms_menue_main_side` ADD `toplink` BOOL NULL DEFAULT '0' AFTER `ID` ;
ALTER TABLE `sp_modul_settings` CHANGE `modul_box_r` `modul_box_r` TINYINT( 1 ) NOT NULL 
ALTER TABLE `sp_modul_portal_cms_page` ADD `page_ident_name` TEXT NOT NULL AFTER `page_domain` 

CREATE TABLE IF NOT EXISTS `sp_modul_portal_domain` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `domainLangs` text NOT NULL,
  PRIMARY KEY (`ID`)
);

DROP TABLE IF EXISTS `sp_thirdpt_tmp`;
CREATE TABLE IF NOT EXISTS `sp_thirdpt_tmp` (
  `name` text NOT NULL,
  `value` text NOT NULL,
  `time` text NOT NULL,
  PRIMARY KEY (`name`(700))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;