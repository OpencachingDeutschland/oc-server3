SET NAMES 'utf8';
DROP TABLE IF EXISTS `sys_menu`;
CREATE TABLE `sys_menu` (
  `id` smallint(6) NOT NULL auto_increment,
  `id_string` varchar(80) NOT NULL,
  `title` varchar(80) NOT NULL,
  `title_trans_id` int(10) unsigned NOT NULL,
  `menustring` varchar(80) NOT NULL,
  `menustring_trans_id` int(10) unsigned NOT NULL,
  `access` tinyint(3) unsigned NOT NULL,
  `href` varchar(80) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `parent` smallint(6) NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  `color` varchar(7) NOT NULL,
  `sitemap` tinyint(1) NOT NULL,
  `only_if_parent` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_string` (`id_string`),
  KEY `parent` (`parent`,`position`),
  KEY `href` (`href`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
