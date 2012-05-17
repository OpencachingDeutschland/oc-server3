SET NAMES 'utf8';
DROP TABLE IF EXISTS `user_statpic`;
CREATE TABLE `user_statpic` (
  `user_id` int(11) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY  (`user_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
