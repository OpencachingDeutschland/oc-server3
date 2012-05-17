SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_attrib`;
CREATE TABLE `cache_attrib` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `icon` varchar(30) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `group_id` tinyint(3) unsigned NOT NULL,
  `selectable` tinyint(1) NOT NULL default '1',
  `category` tinyint(1) unsigned NOT NULL default '0',
  `search_default` tinyint(1) unsigned NOT NULL,
  `default` tinyint(1) unsigned NOT NULL default '0',
  `icon_large` varchar(80) NOT NULL COMMENT 'obsolete',
  `icon_no` varchar(80) NOT NULL COMMENT 'obsolete',
  `icon_undef` varchar(80) NOT NULL COMMENT 'obsolete',
  `html_desc` text NOT NULL,
  `html_desc_trans_id` int(10) unsigned NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category` (`category`,`id`),
  KEY `default` (`default`),
  KEY `selectable` (`selectable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
