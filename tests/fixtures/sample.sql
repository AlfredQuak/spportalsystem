-- Sample SQL for testing importFile
/* This is a comment block that should be stripped */

CREATE TABLE IF NOT EXISTS `dummy` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
);

-- Another comment line

INSERT INTO `dummy` (`name`) VALUES ('foo');
