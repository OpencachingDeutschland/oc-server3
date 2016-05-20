SET NAMES 'utf8';
DROP TABLE IF EXISTS `towns`;
CREATE TABLE `towns` (
  `country` char(2) NOT NULL,
  `name` varchar(40) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `coord_lat` double NOT NULL,
  `coord_long` double NOT NULL,
  `maplist` tinyint(1) NOT NULL DEFAULT '0',
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
