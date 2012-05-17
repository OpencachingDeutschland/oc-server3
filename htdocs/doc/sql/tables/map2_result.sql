SET NAMES 'utf8';
DROP TABLE IF EXISTS `map2_result`;
CREATE TABLE `map2_result` (
  `result_id` int(10) unsigned NOT NULL auto_increment,
  `slave_id` mediumint(9) NOT NULL,
  `sqlchecksum` int(10) unsigned NOT NULL,
  `sqlquery` text NOT NULL,
  `shared_counter` int(10) unsigned NOT NULL,
  `request_counter` int(10) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  `date_lastqueried` datetime NOT NULL,
  PRIMARY KEY  (`result_id`),
  KEY `sqlchecksum` (`sqlchecksum`),
  KEY `date_created` (`date_created`),
  KEY `date_lastqueried` (`date_lastqueried`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
