SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_logs`;
CREATE TABLE `cache_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL default '0',
  `date_created` datetime NOT NULL COMMENT 'via Trigger (cache_logs)',
  `last_modified` datetime NOT NULL COMMENT 'via Trigger (cache_logs)',
  `log_last_modified` datetime NOT NULL COMMENT 'via Triggers',
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `oc_team_comment` tinyint(1) NOT NULL default '0',
  `date` datetime NOT NULL,
  `text` mediumtext NOT NULL,
  `text_html` tinyint(1) NOT NULL default '1',
  `text_htmledit` tinyint(1) NOT NULL default '1',
  `owner_notified` tinyint(1) NOT NULL default '0',
  `picture` smallint(5) unsigned NOT NULL COMMENT 'via Trigger (picture)',
  /* Attention: modifications to this table may need to be applied also to
     cache_logs_archived, cache_logs_modified and trigger cacheLogsBeforeUpdate! */
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `owner_notified` (`owner_notified`),
  KEY `last_modified` (`last_modified`),
  KEY `type` (`type`,`cache_id`),
  KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`,`cache_id`),
  KEY `cache_id` (`cache_id`,`user_id`),
  KEY `date` (`cache_id`,`date`,`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
