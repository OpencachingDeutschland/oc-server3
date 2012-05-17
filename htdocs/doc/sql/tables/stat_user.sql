SET NAMES 'utf8';
DROP TABLE IF EXISTS `stat_user`;
CREATE TABLE `stat_user` (
  `user_id` int(10) unsigned NOT NULL,
  `found` smallint(5) unsigned NOT NULL,
  `notfound` smallint(5) unsigned NOT NULL,
  `note` smallint(5) unsigned NOT NULL,
  `hidden` smallint(5) unsigned NOT NULL,
  `will_attend` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via trigger (user)' ;
