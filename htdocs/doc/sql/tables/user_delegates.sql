SET NAMES 'utf8';
DROP TABLE IF EXISTS `user_delegates`;
CREATE TABLE `user_delegates` (
  `user_id` int(10) unsigned NOT NULL,
  `node` tinyint(3) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY  (`user_id`,`node`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
