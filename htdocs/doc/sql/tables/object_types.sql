SET NAMES 'utf8';
DROP TABLE IF EXISTS `object_types`;
CREATE TABLE `object_types` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
