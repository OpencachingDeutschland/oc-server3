SET NAMES 'utf8';
DROP TABLE IF EXISTS `attribute_categories`;
CREATE TABLE `attribute_categories` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `color` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
