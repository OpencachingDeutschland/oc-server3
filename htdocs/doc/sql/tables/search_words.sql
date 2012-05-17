SET NAMES 'utf8';
DROP TABLE IF EXISTS `search_words`;
CREATE TABLE `search_words` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `word` varchar(255) NOT NULL,
  `simple` varchar(30) NOT NULL,
  `hash` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `hash` (`hash`,`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='via cronjob' ;
