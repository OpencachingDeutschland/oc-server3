SET NAMES 'utf8';
DROP TABLE IF EXISTS `user_options`;
CREATE TABLE `user_options` (
  `user_id` int(10) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `option_visible` tinyint(1) NOT NULL DEFAULT '1',
  `option_value` longtext,
  PRIMARY KEY (`user_id`,`option_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='user options' ;
