SET NAMES 'utf8';
DROP TABLE IF EXISTS `log_types`;
CREATE TABLE `log_types` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) unsigned NOT NULL,
  `permission` char(1) NOT NULL COMMENT 'obsolete',
  `cache_status` tinyint(1) NOT NULL DEFAULT '0',
  `de` varchar(60) NOT NULL COMMENT 'obsolete',
  `en` varchar(60) NOT NULL,
  `icon_small` varchar(255) NOT NULL,
  `allow_rating` tinyint(1) NOT NULL,
  `require_password` tinyint(1) NOT NULL,
  `maintenance_logs` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
