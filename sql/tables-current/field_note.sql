SET NAMES 'utf8';
DROP TABLE IF EXISTS `field_note`;
CREATE TABLE `field_note` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `geocache_id` int(11) DEFAULT NULL,
  `type` smallint(6) NOT NULL,
  `date` datetime NOT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DC7193AEA76ED395` (`user_id`),
  KEY `IDX_DC7193AE67030974` (`geocache_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
