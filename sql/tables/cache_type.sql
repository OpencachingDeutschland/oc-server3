SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_type`;
CREATE TABLE `cache_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) NOT NULL,
  `ordinal` tinyint(3) unsigned NOT NULL,
  `short` varchar(10) NOT NULL,
  `de` varchar(60) NOT NULL,
  `en` varchar(60) NOT NULL,
  `icon_large` varchar(60) NOT NULL,
  `short2` varchar(15) NOT NULL,
  `short2_trans_id` int(10) NOT NULL,
  `kml_name` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
