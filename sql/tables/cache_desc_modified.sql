SET NAMES 'utf8';
DROP TABLE IF EXISTS `cache_desc_modified`;
CREATE TABLE `cache_desc_modified` (
  `cache_id` int(10) unsigned NOT NULL,
  `language` char(2) NOT NULL,
  `date_modified` date NOT NULL COMMENT 'no time! see restorecaches.php',
  `date_created` datetime NOT NULL,
  `desc` longtext,
  `desc_html` tinyint(1) NOT NULL DEFAULT '0',
  `desc_htmledit` tinyint(1) NOT NULL DEFAULT '0',
  `hint` longtext,
  `short_desc` varchar(120) NOT NULL,
  `restored_by` int(10) NOT NULL,
  UNIQUE KEY `cache_id` (`cache_id`,`date_modified`,`language`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;
