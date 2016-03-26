SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_location`;
CREATE TABLE `cache_location` (
  `cache_id` int(10) unsigned NOT NULL,
  `last_modified` datetime NOT NULL COMMENT 'via Trigger (cache_location)',
  `adm1` varchar(120) default NULL,
  `adm2` varchar(120) default NULL,
  `adm3` varchar(120) default NULL,
  `adm4` varchar(120) default NULL,
  `code1` varchar(2) default NULL,
  `code2` varchar(3) default NULL,
  `code3` varchar(4) default NULL,
  `code4` varchar(5) default NULL,
  PRIMARY KEY  (`cache_id`),
  KEY `code1` (`code1`,`code2`,`code3`,`code4`),
  KEY `adm1` (`adm1`,`adm2`),
  KEY `adm1_2` (`adm1`,`code1`),
  KEY `code1_2` (`code1`,`adm3`),
  KEY `adm1_3` (`adm1`,`adm3`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via cronjob' ;
