SET NAMES 'utf8';
DROP TABLE IF EXISTS `pictures_modified`;
CREATE TABLE `pictures_modified` (
  `id` int(10) NOT NULL,
  `date_modified` datetime NOT NULL COMMENT 'no time! see restorecaches.php',
  `operation` char(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(250) NOT NULL,
  `object_id` int(10) unsigned NOT NULL default '0',
  `object_type` tinyint(3) unsigned NOT NULL default '0',
  `spoiler` tinyint(1) NOT NULL default '0',
  `unknown_format` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) NOT NULL default '1',
  `original_id` int(10) NOT NULL,
  UNIQUE KEY `id` (`id`,`operation`),
  KEY `object_type` (`object_type`,`object_id`,`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
