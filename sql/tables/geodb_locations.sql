SET NAMES 'utf8';
DROP TABLE IF EXISTS `geodb_locations`;
CREATE TABLE `geodb_locations` (
  `loc_id` int(11) NOT NULL DEFAULT '0',
  `loc_type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loc_id`),
  KEY `loc_type_idx` (`loc_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
