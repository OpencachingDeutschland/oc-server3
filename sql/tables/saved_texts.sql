SET NAMES 'utf8';
DROP TABLE IF EXISTS `saved_texts`;
CREATE TABLE `saved_texts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date_created` datetime NOT NULL,
  `object_type` tinyint(3) NOT NULL,
  `object_id` int(10) NOT NULL,
  `subtype` tinyint(2) NOT NULL,
  `text` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `object_id` (`object_type`,`object_id`,`subtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
