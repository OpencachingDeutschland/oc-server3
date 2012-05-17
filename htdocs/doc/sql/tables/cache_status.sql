SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_status`;
CREATE TABLE `cache_status` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `de` varchar(60) NOT NULL COMMENT 'obsolete',
  `en` varchar(60) NOT NULL COMMENT 'obsolete',
  `allow_user_view` tinyint(1) NOT NULL,
  `allow_owner_edit_status` tinyint(1) NOT NULL,
  `allow_user_log` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
