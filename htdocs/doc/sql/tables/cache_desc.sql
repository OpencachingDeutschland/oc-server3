SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_desc`;
CREATE TABLE `cache_desc` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL default '0',
  `date_created` datetime NOT NULL COMMENT 'via Trigger (cache_desc)',
  `last_modified` datetime NOT NULL COMMENT 'via Trigger (cache_desc)',
  `cache_id` int(10) unsigned NOT NULL,
  `language` char(2) NOT NULL,
  `desc` mediumtext NOT NULL,
  `desc_html` tinyint(1) NOT NULL default '1' COMMENT 'obsolete',
  `desc_htmledit` tinyint(1) NOT NULL default '1',
  `hint` mediumtext NOT NULL,
  `short_desc` varchar(120) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cache_id` (`cache_id`,`language`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `last_modified` (`last_modified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
