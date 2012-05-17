SET NAMES 'utf8';
DROP TABLE IF EXISTS `replication_notimported`;
CREATE TABLE `replication_notimported` (
  `id` int(11) NOT NULL auto_increment,
  `object_uuid` varchar(36) NOT NULL,
  `object_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='obsolete' ;
