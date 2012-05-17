SET NAMES 'utf8';
DROP TABLE IF EXISTS `geodb_search`;
CREATE TABLE `geodb_search` (
  `id` int(11) NOT NULL auto_increment,
  `loc_id` int(11) NOT NULL default '0',
  `sort` varchar(255) NOT NULL,
  `simple` varchar(255) NOT NULL,
  `simplehash` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sort` (`sort`),
  KEY `simple` (`simple`),
  KEY `simplehash` (`simplehash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
