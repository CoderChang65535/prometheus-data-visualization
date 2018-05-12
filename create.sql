CREATE SCHEMA `prom-vs` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE `prom-vs`.`CPU` (
  `queryID` varchar(45) NOT NULL,
  `time` datetime NOT NULL,
  `value` varchar(45) NOT NULL,
  `node` varchar(45) NOT NULL,
  PRIMARY KEY (`time`,`node`,`queryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `prom-vs`.`BasicInfo` (
  `queryID` VARCHAR(45) NOT NULL,
  `time` VARCHAR(45) NOT NULL,
  `type` VARCHAR(45) NOT NULL,
  `node` VARCHAR(45) NOT NULL,
  `value` VARCHAR(45) NULL,
  PRIMARY KEY (`queryID`, `time`, `type`, `node`));
