SET NAMES 'utf8';
DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE `sysconfig` (
  `name` varchar(60) NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='obsolete' ;
