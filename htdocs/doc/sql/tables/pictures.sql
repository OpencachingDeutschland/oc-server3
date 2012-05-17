SET NAMES 'utf8';
DROP TABLE IF EXISTS `pictures`;
CREATE TABLE `pictures` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL default '0',
  `date_created` datetime NOT NULL COMMENT 'via Trigger (pictures)',
  `last_modified` datetime NOT NULL COMMENT 'via Trigger (pictures)',
  `url` varchar(255) NOT NULL,
  `title` varchar(250) NOT NULL,
  `last_url_check` datetime NOT NULL default '0000-00-00 00:00:00',
  `object_id` int(10) unsigned NOT NULL default '0',
  `object_type` tinyint(3) unsigned NOT NULL default '0',
  `thumb_url` varchar(255) NOT NULL,
  `thumb_last_generated` datetime NOT NULL default '0000-00-00 00:00:00',
  `spoiler` tinyint(1) NOT NULL default '0',
  `local` tinyint(1) NOT NULL default '1',
  `unknown_format` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `last_modified` (`last_modified`),
  KEY `object_type` (`object_type`,`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
