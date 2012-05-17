SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_logqueue`;
CREATE TABLE `watches_logqueue` (
  `log_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`log_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
