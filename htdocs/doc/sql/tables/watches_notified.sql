SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_notified`;
CREATE TABLE `watches_notified` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL default '0',
  `object_type` tinyint(3) unsigned NOT NULL default '0',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'via Trigger (watches_notified)',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`object_id`,`object_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
