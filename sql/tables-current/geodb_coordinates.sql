SET NAMES 'utf8';
DROP TABLE IF EXISTS `geodb_coordinates`;
CREATE TABLE `geodb_coordinates` (
  `loc_id` int(11) NOT NULL DEFAULT '0',
  `lon` double DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `coord_type` int(11) NOT NULL DEFAULT '0',
  `coord_subtype` int(11) DEFAULT NULL,
  `valid_since` date DEFAULT NULL,
  `date_type_since` int(11) DEFAULT NULL,
  `valid_until` date NOT NULL DEFAULT '0000-00-00',
  `date_type_until` int(11) NOT NULL DEFAULT '0',
  KEY `coord_loc_id_idx` (`loc_id`),
  KEY `coord_lon_idx` (`lon`),
  KEY `coord_lat_idx` (`lat`),
  KEY `coord_type_idx` (`coord_type`),
  KEY `coord_stype_idx` (`coord_subtype`),
  KEY `coord_since_idx` (`valid_since`),
  KEY `coord_until_idx` (`valid_until`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
