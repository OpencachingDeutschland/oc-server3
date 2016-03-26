SET NAMES 'utf8';
DROP TABLE IF EXISTS `nodes`;
CREATE TABLE `nodes` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `url` varchar(260) NOT NULL,
  `waypoint_prefix` char(2) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `waypoint_prefix` (`waypoint_prefix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
