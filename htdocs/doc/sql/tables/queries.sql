SET NAMES 'utf8';
DROP TABLE IF EXISTS `queries`;
CREATE TABLE `queries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(60) NOT NULL,
  `options` blob NOT NULL,
  `last_queried` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
