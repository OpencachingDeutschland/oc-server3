SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_reports`;
CREATE TABLE `cache_reports` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `date_created` datetime default NULL,
  `cacheid` int(11) unsigned NOT NULL,
  `userid` int(11) unsigned NOT NULL,
  `reason` tinyint(3) unsigned NOT NULL,
  `note` mediumtext NOT NULL,
  `status` tinyint(3) unsigned NOT NULL default '1',
  `adminid` int(11) default NULL,
  `lastmodified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `status` (`status`,`adminid`),
  KEY `status_2` (`adminid`,`status`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='submitted reports on caches' ;
