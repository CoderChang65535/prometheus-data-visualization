CREATE SCHEMA `prom-vs` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE `prom-vs`.`CPU` (
  `time` DATETIME NOT NULL,
  `value` VARCHAR(45) NOT NULL,
  `node` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`time`));
