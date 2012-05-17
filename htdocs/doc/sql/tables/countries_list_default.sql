SET NAMES 'utf8';
DROP TABLE IF EXISTS `countries_list_default`;
CREATE TABLE `countries_list_default` (
  `lang` varchar(2) NOT NULL,
  `show` varchar(2) NOT NULL,
  PRIMARY KEY  (`lang`,`show`),
  KEY `show` (`show`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='static content' ;
