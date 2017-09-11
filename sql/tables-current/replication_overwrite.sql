SET NAMES 'utf8';
DROP TABLE IF EXISTS `replication_overwrite`;
CREATE TABLE `replication_overwrite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0',
  `value` varchar(255) NOT NULL,
  `uuid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='obsolete' ;
