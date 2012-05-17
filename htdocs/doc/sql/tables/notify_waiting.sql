SET NAMES 'utf8';
DROP TABLE IF EXISTS `notify_waiting`;
CREATE TABLE `notify_waiting` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cache_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cache_user` (`cache_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
