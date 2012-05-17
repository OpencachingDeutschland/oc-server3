SET NAMES 'utf8';
DROP TABLE IF EXISTS `gk_move_waypoint`;
CREATE TABLE `gk_move_waypoint` (
  `id` int(11) NOT NULL,
  `wp` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`,`wp`),
  KEY `wp` (`wp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
