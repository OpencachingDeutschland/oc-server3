SET NAMES 'utf8';
DROP TABLE IF EXISTS `gk_move`;
CREATE TABLE `gk_move` (
  `id` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `datemoved` datetime NOT NULL,
  `datelogged` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `logtypeid` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `itemid` (`itemid`,`datemoved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
