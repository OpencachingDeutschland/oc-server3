SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_logs_archived`;
CREATE TABLE `cache_logs_archived` (
  `id` int(10) unsigned NOT NULL,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `cache_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `date` date NOT NULL,
  `text` mediumtext NOT NULL,
  `text_html` tinyint(1) NOT NULL,
  `text_htmledit` tinyint(1) NOT NULL,
  `owner_notified` tinyint(1) NOT NULL,
  `picture` smallint(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
