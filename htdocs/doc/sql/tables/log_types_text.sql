SET NAMES 'utf8';
DROP TABLE IF EXISTS `log_types_text`;
CREATE TABLE `log_types_text` (
  `id` int(10) NOT NULL auto_increment COMMENT 'obsolete',
  `log_types_id` int(10) NOT NULL default '0' COMMENT 'obsolete',
  `lang` char(2) NOT NULL COMMENT 'obsolete',
  `text_combo` varchar(255) NOT NULL COMMENT 'obsolete',
  `text_listing` varchar(255) NOT NULL COMMENT 'obsolete',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lang` (`lang`,`log_types_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='obsolete' ;
