SET NAMES 'utf8';
DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `short` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `trans_id` int(10) NOT NULL,
  `de` varchar(128) NOT NULL COMMENT 'obsolete',
  `en` varchar(128) NOT NULL,
  `list_default_de` int(1) NOT NULL DEFAULT '0' COMMENT 'obsolete',
  `sort_de` varchar(128) NOT NULL COMMENT 'obsolete',
  `list_default_en` int(1) NOT NULL DEFAULT '0' COMMENT 'obsolete',
  `sort_en` varchar(128) NOT NULL COMMENT 'obsolete',
  PRIMARY KEY (`short`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
