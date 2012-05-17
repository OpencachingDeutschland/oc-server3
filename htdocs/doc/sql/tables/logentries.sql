SET NAMES 'utf8';
DROP TABLE IF EXISTS `logentries`;
CREATE TABLE `logentries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date_created` datetime NOT NULL COMMENT 'via Trigger (logentries)',
  `module` varchar(30) NOT NULL,
  `eventid` tinyint(3) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `objectid1` int(10) unsigned NOT NULL default '0',
  `objectid2` int(10) unsigned NOT NULL default '0',
  `logtext` mediumtext NOT NULL,
  `details` blob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
