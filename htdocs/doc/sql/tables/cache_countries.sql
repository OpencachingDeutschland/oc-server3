SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_countries`;
CREATE TABLE `cache_countries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `country` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`,`date_created`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via Trigger (caches)' ;
