SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_lists`;
CREATE TABLE `cache_lists` (
  `id` int(10) NOT NULL auto_increment,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL default '0',
  `user_id` int(10) NOT NULL,
  `date_created` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `last_added` datetime default NULL,
  `last_state_change` datetime default NULL,
  `name` varchar(80) NOT NULL,
  `is_public` tinyint(1) NOT NULL default '0',
  `description` mediumtext NOT NULL,
  `desc_htmledit` tinyint(1) unsigned NOT NULL default '1',
  `password` varchar(80) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
