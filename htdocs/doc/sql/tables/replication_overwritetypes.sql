SET NAMES 'utf8';
DROP TABLE IF EXISTS `replication_overwritetypes`;
CREATE TABLE `replication_overwritetypes` (
  `id` int(11) NOT NULL auto_increment,
  `table` varchar(60) NOT NULL,
  `field` varchar(60) NOT NULL,
  `uuid_fieldname` varchar(36) NOT NULL,
  `backupfirst` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='obsolete' ;
