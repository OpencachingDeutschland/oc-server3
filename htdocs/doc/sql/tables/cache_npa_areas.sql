SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_npa_areas`;
CREATE TABLE `cache_npa_areas` (
  `cache_id` int(10) unsigned NOT NULL,
  `npa_id` int(10) unsigned NOT NULL,
  `calculated` tinyint(1) NOT NULL,
  PRIMARY KEY  (`cache_id`,`npa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
