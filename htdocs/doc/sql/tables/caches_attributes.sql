SET NAMES 'utf8';
DROP TABLE IF EXISTS `caches_attributes`;
CREATE TABLE `caches_attributes` (
  `cache_id` int(10) unsigned NOT NULL,
  `attrib_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`cache_id`,`attrib_id`),
  KEY `attrib_id` (`attrib_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
