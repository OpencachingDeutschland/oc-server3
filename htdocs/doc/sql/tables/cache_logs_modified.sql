SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_logs_modified`;
CREATE TABLE `cache_logs_modified` (
  `id` int(10) unsigned NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `log_last_modified` datetime NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `oc_team_comment` tinyint(1) NOT NULL,
  `date` datetime NOT NULL,
  `text` mediumtext NOT NULL,
  `text_html` tinyint(1) NOT NULL,
  `modify_date` datetime default NULL,
  KEY `id` (`id`, `modify_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
