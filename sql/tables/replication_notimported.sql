SET NAMES 'utf8';
DROP TABLE IF EXISTS `replication_notimported`;
CREATE TABLE `replication_notimported` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_uuid` varchar(36) NOT NULL,
  `object_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='obsolete' ;
