SET NAMES 'utf8';
DROP TABLE IF EXISTS `geodb_search`;
CREATE TABLE `geodb_search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_id` int(11) NOT NULL DEFAULT '0',
  `sort` varchar(255) NOT NULL,
  `simple` varchar(255) NOT NULL,
  `simplehash` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`(250)),
  KEY `simple` (`simple`(250)),
  KEY `simplehash` (`simplehash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content, not in use' ;
