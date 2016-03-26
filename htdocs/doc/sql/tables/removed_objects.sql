SET NAMES 'utf8';
DROP TABLE IF EXISTS `removed_objects`;
CREATE TABLE `removed_objects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `localID` int(10) unsigned NOT NULL default '0',
  `uuid` varchar(36) NOT NULL,
  `type` tinyint(3) unsigned NOT NULL default '0',
  `removed_date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'via Trigger (removed_objects)',
  `node` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `UUID` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via Trigger (caches, cache_logs, cache_dec, pictures, user)' ;
