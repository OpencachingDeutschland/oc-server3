SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_reports`;
CREATE TABLE `cache_reports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_created` datetime DEFAULT NULL,
  `cacheid` int(11) unsigned NOT NULL,
  `userid` int(11) unsigned NOT NULL,
  `reason` tinyint(3) unsigned NOT NULL,
  `note` longtext NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `adminid` int(11) DEFAULT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `comment` mediumtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`,`adminid`),
  KEY `status_2` (`adminid`,`status`),
  KEY `userid` (`userid`),
  KEY `cacheid` (`cacheid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='submitted reports on caches' ;
