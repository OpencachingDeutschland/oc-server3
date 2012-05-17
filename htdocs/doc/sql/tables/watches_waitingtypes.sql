SET NAMES 'utf8';
DROP TABLE IF EXISTS `watches_waitingtypes`;
CREATE TABLE `watches_waitingtypes` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `watchtype` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
