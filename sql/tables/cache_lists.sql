SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_lists`;
CREATE TABLE `cache_lists` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) NOT NULL,
  `node` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL,
  `date_created` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `last_added` datetime DEFAULT NULL,
  `last_state_change` datetime DEFAULT NULL,
  `name` varchar(80) NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `description` longtext NOT NULL,
  `desc_htmledit` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `password` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `name` (`name`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
