SET NAMES 'utf8';
DROP TABLE IF EXISTS `xmlsession_data`;
CREATE TABLE `xmlsession_data` (
  `session_id` int(10) unsigned NOT NULL default '0',
  `object_type` tinyint(3) unsigned NOT NULL default '0',
  `object_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`session_id`,`object_type`,`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
