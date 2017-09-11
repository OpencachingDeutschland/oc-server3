SET NAMES 'utf8';
DROP TABLE IF EXISTS `mp3`;
CREATE TABLE `mp3` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL COMMENT 'via Trigger (mp3)',
  `last_modified` datetime NOT NULL COMMENT 'via Trigger (mp3)',
  `url` varchar(255) NOT NULL,
  `title` varchar(250) NOT NULL,
  `last_url_check` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `local` tinyint(1) NOT NULL DEFAULT '1',
  `unknown_format` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `last_modified` (`last_modified`),
  KEY `url` (`url`(250)),
  KEY `title` (`title`),
  KEY `object_id` (`object_id`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
