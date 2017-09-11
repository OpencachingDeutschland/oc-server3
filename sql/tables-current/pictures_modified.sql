SET NAMES 'utf8';
DROP TABLE IF EXISTS `pictures_modified`;
CREATE TABLE `pictures_modified` (
  `id` int(10) NOT NULL,
  `date_modified` datetime NOT NULL,
  `operation` char(1) NOT NULL,
  `date_created` datetime NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(250) NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `object_type` tinyint(3) unsigned NOT NULL,
  `spoiler` tinyint(1) NOT NULL,
  `unknown_format` tinyint(1) NOT NULL,
  `display` tinyint(1) NOT NULL,
  `original_id` int(10) NOT NULL,
  `restored_by` int(10) NOT NULL,
  UNIQUE KEY `id` (`id`,`operation`),
  KEY `object_type` (`object_type`,`object_id`,`date_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
