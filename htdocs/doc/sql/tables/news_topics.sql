SET NAMES 'utf8';
DROP TABLE IF EXISTS `news_topics`;
CREATE TABLE `news_topics` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
