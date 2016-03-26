SET NAMES 'utf8';
DROP TABLE IF EXISTS `gns_search`;
CREATE TABLE `gns_search` (
  `uni_id` int(11) NOT NULL default '0',
  `sort` varchar(255) NOT NULL,
  `simple` varchar(255) NOT NULL,
  `simplehash` int(11) unsigned NOT NULL default '0',
  KEY `simplehash` (`simplehash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
