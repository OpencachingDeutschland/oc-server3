SET NAMES 'utf8';
DROP TABLE IF EXISTS `geodb_textdata`;
CREATE TABLE `geodb_textdata` (
  `loc_id` int(11) NOT NULL DEFAULT '0',
  `text_val` varchar(255) NOT NULL DEFAULT '',
  `text_type` int(11) NOT NULL DEFAULT '0',
  `text_locale` varchar(5) DEFAULT NULL,
  `is_native_lang` smallint(1) DEFAULT NULL,
  `is_default_name` smallint(1) DEFAULT NULL,
  `valid_since` date DEFAULT NULL,
  `date_type_since` int(11) DEFAULT NULL,
  `valid_until` date NOT NULL DEFAULT '0000-00-00',
  `date_type_until` int(11) NOT NULL DEFAULT '0',
  KEY `text_lid_idx` (`loc_id`),
  KEY `text_val_idx` (`text_val`(250)),
  KEY `text_type_idx` (`text_type`),
  KEY `text_locale_idx` (`text_locale`),
  KEY `text_native_idx` (`is_native_lang`),
  KEY `text_default_idx` (`is_default_name`),
  KEY `text_since_idx` (`valid_since`),
  KEY `text_until_idx` (`valid_until`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='static content' ;
