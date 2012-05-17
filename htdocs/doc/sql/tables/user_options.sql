SET NAMES 'utf8';
DROP TABLE IF EXISTS `user_options`;
CREATE TABLE `user_options` (
  `user_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `option_visible` tinyint(1) NOT NULL default '1',
  `option_value` mediumtext,
  PRIMARY KEY  (`user_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user options' ;
