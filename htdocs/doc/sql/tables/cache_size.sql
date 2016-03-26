SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_size`;
CREATE TABLE `cache_size` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `ordinal` tinyint(3) unsigned NOT NULL,
  `de` varchar(60) NOT NULL COMMENT 'obsolete',
  `en` varchar(60) NOT NULL COMMENT 'obsolete',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
