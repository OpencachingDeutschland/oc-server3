SET NAMES 'utf8';
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'via trigger (news)',
  `content` mediumtext NOT NULL,
  `topic` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `display` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `display` (`display`,`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='obsolete' ;
