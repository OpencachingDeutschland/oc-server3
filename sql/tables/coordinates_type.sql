SET NAMES 'utf8';
DROP TABLE IF EXISTS `coordinates_type`;
CREATE TABLE `coordinates_type` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `image` varchar(60) NOT NULL,
  `preposition` varchar(20) NOT NULL,
  `pp_trans_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
