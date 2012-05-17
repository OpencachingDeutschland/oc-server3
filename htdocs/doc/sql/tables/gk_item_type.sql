SET NAMES 'utf8';
DROP TABLE IF EXISTS `gk_item_type`;
CREATE TABLE `gk_item_type` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
