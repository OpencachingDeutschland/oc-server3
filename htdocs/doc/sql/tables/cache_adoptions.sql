SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_adoptions`;
CREATE TABLE `cache_adoptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cache_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cache_id` (`cache_id`,`date`),
  KEY `from_user_id` (`from_user_id`),
  KEY `to_user_id` (`to_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
