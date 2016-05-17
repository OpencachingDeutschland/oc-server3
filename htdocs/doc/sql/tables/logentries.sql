SET NAMES 'utf8';
DROP TABLE IF EXISTS `logentries`;
CREATE TABLE `logentries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL COMMENT 'via Trigger (logentries)',
  `module` varchar(30) NOT NULL,
  `eventid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `userid` int(10) unsigned NOT NULL DEFAULT '0',
  `objectid1` int(10) unsigned NOT NULL DEFAULT '0',
  `objectid2` int(10) unsigned NOT NULL DEFAULT '0',
  `logtext` longtext NOT NULL,
  `details` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`,`objectid1`,`module`),
  KEY `date` (`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
