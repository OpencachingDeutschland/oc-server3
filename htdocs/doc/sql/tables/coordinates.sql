SET NAMES 'utf8';
DROP TABLE IF EXISTS `coordinates`;
CREATE TABLE `coordinates` (
  `id` int(11) NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `type` int(11) NOT NULL,
  `subtype` int(11) default NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `cache_id` int(11) NOT NULL,
  `user_id` int(11) default NULL,
  `log_id` int(11) default NULL,
  `description` text,
  PRIMARY KEY  (`id`),
  KEY `cache_id` (`cache_id`),
  KEY `user_id` (`user_id`),
  KEY `log_id` (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
