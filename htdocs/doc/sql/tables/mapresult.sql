SET NAMES 'utf8';
DROP TABLE IF EXISTS `mapresult`;
CREATE TABLE `mapresult` (
  `query_id` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY  (`query_id`),
  KEY `date_created` (`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
