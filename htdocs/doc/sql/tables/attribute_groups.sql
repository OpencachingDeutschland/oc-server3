SET NAMES 'utf8';
DROP TABLE IF EXISTS `attribute_groups`;
CREATE TABLE `attribute_groups` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `category_id` tinyint(3) unsigned NOT NULL,
  `name` varchar(60) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
